<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch all recipes with corresponding user and category information
$sql = "SELECT recipes.RecipeID, recipes.UserID, users.username, recipes.Title, recipes.CategoryID, categories.name AS CategoryName, recipes.Description, recipes.ApprovalStatus FROM recipes
        INNER JOIN users ON recipes.UserID = users.id
        INNER JOIN categories ON recipes.CategoryID = categories.id";
$result = mysqli_query($conn, $sql);

// Handle recipe status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $recipe_id = $_POST['recipe_id'];
    $status = $_POST['status'];

    // Update recipe status in the database
    $update_sql = "UPDATE recipes SET ApprovalStatus = ? WHERE RecipeID = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "si", $status, $recipe_id);
    mysqli_stmt_execute($update_stmt);
    mysqli_stmt_close($update_stmt);
}

// Search functionality
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql .= " WHERE recipes.Title LIKE '%$search%' OR categories.name LIKE '%$search%'";
    $result = mysqli_query($conn, $sql);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Manage Recipes</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Manage Recipes</h2>
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" class="form-control" name="search" placeholder="Search by name or category" value="<?php if (isset($_GET['search'])) echo $_GET['search']; ?>">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit">Search</button>
            </div>
        </div>
    </form>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Title</th>
                <th>Category</th>
                <th>Description</th>
                <th>Status</th>
                <th>Approval Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?php echo $row['RecipeID']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['Title']; ?></td>
                    <td><?php echo $row['CategoryName']; ?></td>
                    <td><?php echo $row['Description']; ?></td>
                    <td><?php echo $row['ApprovalStatus']; ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="recipe_id" value="<?php echo $row['RecipeID']; ?>">
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="Approved" <?php if ($row['ApprovalStatus'] == 'Approved') echo 'selected'; ?>>Approved</option>
                                <option value="Pending" <?php if ($row['ApprovalStatus'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                <option value="Disapproved" <?php if ($row['ApprovalStatus'] == 'Disapproved') echo 'selected'; ?>>Disapproved</option>
                            </select>
                            <input type="hidden" name="update_status">
                        </form>
                    </td>
                    <td>
                        <a href="view_recipe.php?id=<?php echo $row['RecipeID']; ?>" class="btn btn-info">View</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
