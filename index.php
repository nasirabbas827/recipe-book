<?php 
include('config.php');

// Fetch approved recipes with user information
$sql = "SELECT recipes.*, users.username 
        FROM recipes 
        INNER JOIN users ON recipes.UserID = users.id 
        WHERE recipes.ApprovalStatus = 'Approved'";

// Check if search query is provided
if(isset($_GET['search'])) {
    $search = $_GET['search'];
    // Filter recipes by name or category
    $sql .= " AND (recipes.Title LIKE '%$search%' OR recipes.Description LIKE '%$search%' OR recipes.Ingredients LIKE '%$search%')";
}

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>RecipeBook</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .jumbotron {
            height: 550px;
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('./images/hotel.jpg');
            background-size: cover;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .jumbotron h1 {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        .jumbotron p {
            font-size: 1.5rem;
        }
        .card {
            min-height: 350px; /* Adjust card height as needed */
        }
    </style>
</head>
<body>

<?php include('navbar.php'); ?>

<div class="jumbotron text-center">
    <h1>Welcome to RecipeBook</h1>
    <p>Discover Delicious Recipes Shared by Our Community of Food Enthusiasts</p>
    <a href="login.php" class="btn btn-primary btn-lg">Sign In to Share Your Recipes</a>
</div>


<div class="container">
    <h2 class="mb-4">Approved Recipes</h2>

    <!-- Search form -->
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" class="form-control" name="search" placeholder="Search by name or category" value="<?php if (isset($_GET['search'])) echo $_GET['search']; ?>">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit">Search</button>
            </div>
        </div>
    </form>

    <div class="row">
        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="<?php echo $row['ImageURL']; ?>" class="card-img-top" alt="Recipe Image">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $row['Title']; ?></h5>
                    <p class="card-text"><?php echo $row['Description']; ?></p>
                    <p class="card-text"><small class="text-muted">By <?php echo $row['username']; ?></small></p>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<footer class="mt-5 py-3 bg-light">
    <div class="container text-center">
        <p>&copy; 2024 RecipeBook. All rights reserved.</p>
    </div>
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>

</body>
</html>
