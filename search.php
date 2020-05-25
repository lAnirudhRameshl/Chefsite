<?php
    session_start();

    $conn = new MongoDB\Driver\Manager();

    $_SESSION["search_name"] = "";

    $search_username = "";
    $search_username_err = "";

    //echo "<script>alert('Welcome');</script>";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        
        if(!empty(trim($_POST["search"]))){
            $search_username = trim($_POST["search"]);
            
            $filter = array("username" => "".$search_username);
            $query = new MongoDB\Driver\Query($filter);
            $result = $conn -> executeQuery("iwp_project.recipe", $query);
            $res_array = $result -> toarray();
            //print_r($res_array);
            if(count($res_array) == 0){
                $username_err = "No user found";
            } else{
                $_SESSION["search_name"] = $search_username;
                echo "<script>alert('".$_SESSION["search_name"]."');</script>";
                header("location: profile.php");
                exit;
            }
        } else{
            $username_err = "Please enter the username";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Search</title>
        <link rel="stylesheet" type="text/css" href="CSS/pass_reset.css">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    </head>
    <body>
        <div class="background" style="overflow:hidden">
            <nav>
                <div class="navigation">
                    <div class="dropdown">
                        <a class="dropdown_a"><?php echo $_SESSION["username"] ?></a>
                        <div class="dropdown_content">
                            <a class="profile" href="logout.php">Logout</a><br>
                            <a href="profile.php" class="profile">Profile</a><br>
                            <a href="edit_recipe.php" class="profile">Edit recipe</a><br>
                            <a href="reset_pass.php" class="profile">Reset password</a><br>
                            <a href="main.php" class="profile">New Recipe</a><br>
                            <a href="delete_recipe.php" class="profile">Delete Recipe</a><br>
                        </div>  
                    </div>
                </div>
            </nav>
            <header>
                <div class="container">
                    <div class="inner">
                        <h3 class="signup">Welcome to Chefsite</h3>
                        <p class="signup">Search for the username</p>
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                            <div class="error">
                                <p>
                                    <?php
                                        if(!empty($search_username_err)){
                                            echo "".$search_username_err;
                                        }
                                    ?>
                                </p>
                            </div>
                            <div class="main">
                                <input type="text" name="search" id="search" placeholder="Username">
                            </div>
                            <div class="main">
                                <input type="submit" name="submit" id="submit" value="Search">
                            </div>
                        </form>
                    </div>
                </div>
            </header>
        </div>
    </body>
</html>