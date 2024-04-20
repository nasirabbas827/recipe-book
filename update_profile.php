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

// Fetch user details from the database
$sql = "SELECT id, username, email, age, full_name, profile_picture, bio FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $fetched_id, $username, $email, $age, $full_name, $profile_picture, $bio);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Update user profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newUsername = $_POST["username"];
    $newFullName = $_POST["full_name"];
    $newEmail = $_POST["email"];
    $newAge = $_POST["age"];
    $newBio = $_POST["bio"];

    // Handle profile picture upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        if($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["profile_picture"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            echo "The file ". htmlspecialchars( basename( $_FILES["profile_picture"]["name"])). " has been uploaded.";
            $profile_picture = $target_file; // Update profile picture path
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    // Update user details in the database
    $update_query = "UPDATE users 
                     SET username = ?, full_name = ?, email = ?, age = ?, profile_picture = ?, bio = ? 
                     WHERE id = ?";
    
    $update_stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($update_stmt, "sssisss", $newUsername, $newFullName, $newEmail, $newAge, $profile_picture, $newBio, $user_id);
    
    if (mysqli_stmt_execute($update_stmt)) {
        echo "Profile updated successfully!";
        // Update session data if needed
        $_SESSION["username"] = $newUsername;
    } else {
        echo "Error updating profile: " . mysqli_error($conn);
    }

    mysqli_stmt_close($update_stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Profile</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5 mb-5">
    <h2>Update Profile</h2>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="profile_picture">Profile Picture:</label>
                <?php if (!empty($profile_picture)) : ?>
                    <img src="<?php echo $profile_picture; ?>" alt="Profile Picture" class="img-thumbnail" style="width: 200px; height: 200px;">
                <?php endif; ?>
                <input type="file" class="form-control-file" name="profile_picture">
            </div>
        </div>
       
        <div class="col-md-6">
            <div class="form-group">
                <label for="full_name">Full Name:</label>
                <input type="text" class="form-control" name="full_name" value="<?php echo $full_name; ?>" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" name="email" value="<?php echo $email; ?>" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="age">Age:</label>
                <input type="number" class="form-control" name="age" value="<?php echo $age; ?>" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" name="username" value="<?php echo $username; ?>" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="bio">Bio:</label>
                <textarea class="form-control" name="bio" rows="4"><?php echo $bio; ?></textarea>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Update Profile</button>
</form>


</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
