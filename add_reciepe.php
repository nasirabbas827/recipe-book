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

// Process form submission for adding a recipe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];
    $ingredients = $_POST['ingredients'];
    $approval_status = 'Pending';
    
    // Handle image upload
    $target_dir = "recipe_images/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        echo '<div class="alert alert-danger" role="alert">File is not an image.</div>';
        $uploadOk = 0;
    }
    // Check file size
    if ($_FILES["image"]["size"] > 5000000) {
        echo '<div class="alert alert-danger" role="alert">Sorry, your file is too large.</div>';
        $uploadOk = 0;
    }
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        echo '<div class="alert alert-danger" role="alert">Sorry, only JPG, JPEG, PNG & GIF files are allowed.</div>';
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo '<div class="alert alert-danger" role="alert">Sorry, your file was not uploaded.</div>';
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // File uploaded successfully
            $image_url = $target_file;
            
            // Insert recipe details into the database
            $sql = "INSERT INTO recipes (UserID, CategoryID, Title, Description, Ingredients, ImageURL, ApprovalStatus) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iisssss", $user_id, $category_id, $title, $description, $ingredients, $image_url, $approval_status);
            
            // Execute the prepared statement
            if(mysqli_stmt_execute($stmt)) {
                // Recipe added successfully
                echo '<div class="alert alert-success" role="alert">Recipe added successfully.</div>';
            } else {
                // Error adding recipe
                echo '<div class="alert alert-danger" role="alert">Error adding recipe: ' . mysqli_error($conn) . '</div>';
            }
            
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
        } else {
            // Error uploading file
            echo '<div class="alert alert-danger" role="alert">Sorry, there was an error uploading your file.</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5 mb-5">
    <h2>Add Recipe</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="4" required></textarea>
        </div>
        <div class="form-group">
            <label>Category</label>
            <select name="category_id" class="form-control" required>
                <?php
                // Fetch categories from the database
                $sql = "SELECT id, name FROM categories";
                $result = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($result)) {
                    echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label>Ingredients</label>
            <textarea name="ingredients" class="form-control" rows="4" required></textarea>
        </div>
        <div class="form-group">
            <label>Image</label>
            <input type="file" name="image" class="form-control-file" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
        <a class="btn btn-outline-dark" href="view_reciepe.php">View Reciepe</a>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
