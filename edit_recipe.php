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

// Check if recipe ID is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: index.php");
    exit;
}

// Get recipe ID from the URL
$recipe_id = $_GET['id'];

// Fetch recipe details from the database
$sql = "SELECT * FROM recipes WHERE RecipeID = ? AND UserID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $recipe_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Check if recipe exists and belongs to the user
if (mysqli_num_rows($result) != 1) {
    header("location: view_reciepe.php");
    exit;
}

$row = mysqli_fetch_assoc($result);

// Handle recipe update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];

    // Check if a new image file is uploaded
    if ($_FILES['image']['error'] == 0) {
        // Upload new image file
        $target_dir = "recipe_images/";
        $image_name = basename($_FILES['image']['name']);
        $target_file = $target_dir . $image_name;
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
    } else {
        // Keep the existing image if no new image is uploaded
        $target_file = $row['ImageURL'];
    }

    // Update recipe details in the database
    $sql = "UPDATE recipes SET Title = ?, Description = ?, CategoryID = ?, ImageURL = ? WHERE RecipeID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssisi", $title, $description, $category, $target_file, $recipe_id);
    mysqli_stmt_execute($stmt);

    // Redirect to my recipes page after update
    header("location: view_reciepe.php");
    exit;
}

// Fetch categories from the database
$sql = "SELECT * FROM categories";
$result = mysqli_query($conn, $sql);
$categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Recipe</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5 mb-5">
    <h1>Edit Recipe</h1>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Title:</label>
            <input type="text" class="form-control" name="title" value="<?php echo $row['Title']; ?>" required>
        </div>
        <div class="form-group">
            <label>Description:</label>
            <textarea class="form-control" name="description" rows="5" required><?php echo $row['Description']; ?></textarea>
        </div>
        <div class="form-group">
            <label>Category:</label>
            <select class="form-control" name="category" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" <?php if ($category['id'] == $row['CategoryID']) echo 'selected'; ?>><?php echo $category['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Current Image:</label><br>
            <img src="<?php echo $row['ImageURL']; ?>" alt="Recipe Image" style="max-width: 200px;"><br>
            <label>New Image:</label>
            <input type="file" class="form-control-file" name="image">
        </div>
        <button type="submit" class="btn btn-primary">Update Recipe</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
