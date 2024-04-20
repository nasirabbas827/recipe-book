<?php
include('config.php');

session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Get the user ID from the session
$user_id = $_SESSION["id"];

// Handle recipe deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $recipeId = $_POST['delete_id'];
    
    // Prepare a delete statement
    $sql = "DELETE FROM recipes WHERE RecipeID = ? AND UserID = ?";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "ii", $recipeId, $user_id);
        
        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Recipe deleted successfully
            echo '<script>alert("Recipe deleted successfully.");</script>';
        } else {
            // Error while deleting the recipe
            echo '<script>alert("Error deleting recipe. Please try again.");</script>';
        }

        // Close statement
        mysqli_stmt_close($stmt);
    } else {
        // Error while preparing the delete statement
        echo '<script>alert("Error deleting recipe. Please try again.");</script>';
    }
}

// Fetch user's recipes from the database
$sql = "SELECT recipes.*, categories.name AS category_name FROM recipes 
        LEFT JOIN categories ON recipes.CategoryID = categories.id 
        WHERE recipes.UserID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

// Search functionality
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT recipes.*, categories.name AS category_name FROM recipes 
            LEFT JOIN categories ON recipes.CategoryID = categories.id 
            WHERE recipes.UserID = $user_id AND (recipes.Title LIKE '%$search%' OR categories.name LIKE '%$search%')";
    $result = mysqli_query($conn, $sql);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Recipes</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h1>My Recipes</h1>
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" class="form-control" name="search" placeholder="Search by name or category" value="<?php if (isset($_GET['search'])) echo $_GET['search']; ?>">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit">Search</button>
            </div>
        </div>
    </form>
    <table class="table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Category</th>
                <th>Image</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>' . $row['Title'] . '</td>';
                echo '<td>' . $row['Description'] . '</td>';
                echo '<td>' . $row['category_name'] . '</td>';
                echo '<td><img src="' . $row['ImageURL'] . '" alt="Recipe Image" style="max-width: 100px;"></td>';
                echo '<td>' . $row['ApprovalStatus'] . '</td>';

                echo '<td>';
                echo '<a href="edit_recipe.php?id=' . $row['RecipeID'] . '" class="m-2 btn btn-primary">Edit</a>';
                echo '<form method="post" style="display: inline-block;">';
                echo '<input type="hidden" name="delete_id" value="' . $row['RecipeID'] . '">';
                echo '<button type="submit" class="btn btn-danger">Delete</button>';
                echo '</form>';
                echo '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
