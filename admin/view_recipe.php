<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Check if recipe ID is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin_recipes.php");
    exit;
}

// Fetch recipe details from the database
$recipe_id = $_GET['id'];
$sql = "SELECT recipes.*, users.username, categories.name AS CategoryName FROM recipes
        INNER JOIN users ON recipes.UserID = users.id
        INNER JOIN categories ON recipes.CategoryID = categories.id
        WHERE RecipeID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $recipe_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

// Check if recipe exists
if (!$row) {
    header("Location: admin_recipes.php");
    exit;
}

// Close statement
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Recipe</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Recipe Details</h2>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><?php echo $row['Title']; ?></h5>
            <p class="card-text"><strong>Category:</strong> <?php echo $row['CategoryName']; ?></p>
            <p class="card-text"><strong>Description:</strong> <?php echo $row['Description']; ?></p>
            <p class="card-text"><strong>Ingredients:</strong> <?php echo $row['Ingredients']; ?></p>
            <p class="card-text"><strong>Approval Status:</strong> <?php echo $row['ApprovalStatus']; ?></p>
            <p class="card-text"><strong>Created At:</strong> <?php echo $row['CreatedAt']; ?></p>
            <p class="card-text"><strong>Updated At:</strong> <?php echo $row['UpdatedAt']; ?></p>
            <?php if (!empty($row['ImageURL'])) : ?>
                <p class="card-text"><strong>Image:</strong></p>
                <img src="../<?php echo $row['ImageURL']; ?>" class="img-fluid" alt="Recipe Image">
            <?php endif; ?>
            <a href="admin_recipes.php" class="btn btn-primary">Back to Recipes</a>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
