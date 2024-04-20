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

// Fetch approved recipes from the database
$sql = "SELECT * FROM recipes WHERE UserID = ? AND ApprovalStatus = 'Approved'";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unpkg.com/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">

    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h1>Welcome to Your Dashboard</h1>
    <!-- Display approved recipes -->
    <div class="row mt-3">
        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="<?php echo $row['ImageURL']; ?>" class="card-img-top" alt="Recipe Image">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['Title']; ?></h5>
                        <p class="card-text"><?php echo $row['Description']; ?></p>
                        <!-- Add social media sharing icons -->
                        <div class="social-icons">
                            <span>Share To </span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://localhost/receipe/view_recipe.php?id=' . $row['RecipeID']); ?>" target="_blank"><i class='fab fa-facebook'></i></a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('http://localhost/receipe/view_recipe.php?id=' . $row['RecipeID']); ?>" target="_blank"><i class='fab fa-twitter'></i></a>
                            <a href="https://www.instagram.com/?url=<?php echo urlencode('http://localhost/receipe/view_recipe.php?id=' . $row['RecipeID']); ?>" target="_blank"><i class='fab fa-instagram'></i></a>
                        </div>
                        
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Font Awesome -->
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>
