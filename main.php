<?php
    session_start();

    require_once "config.php";

    $name = $recipe = "";
    $name_err = $recipe_err = "";
    $success_message = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(empty(trim($_POST["name"]))){
            $name_err = "Enter the name of the recipe";
        } else{
            $name = trim($_POST["name"]);
        }

        if(empty(trim($_POST["recipe"]))){
            $recipe_err = "Enter the recipe";
        } else{
            $recipe = $_POST["recipe"];
        }

        if(empty($name_err) && empty($recipe_err)){
            $query = "insert into recipe values(?, ?, ?)";

            if($statement = mysqli_prepare($conn, $query)){
                mysqli_stmt_bind_param($statement, "sss", $param_username, $param_name, $param_recipe);
                $param_username = $_SESSION["username"];
                $param_name = $name;
                $param_recipe = $recipe;

                if(mysqli_stmt_execute($statement)){
                    $success_message = "New recipe added!!";
                } else{
                    echo "<script type='text/javascript'>alert('Something went wrong. Try again later');</script>";
                }
                mysqli_stmt_close($statement);
            }
        }
        mysqli_close($conn);
    }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" type="text/css" href="CSS/main.css">
    <title>New recipe</title>
  </head>
  <body>
    <div class="navigation">
        <a class="title" href="profile.php">Chefsite</a>
        <a class="profile" href="logout.php">Logout</a>
        <a href="profile.php" class="profile">Profile</a>
        <a href="reset_pass.php" class="profile">Reset Password</a>
        <a href="search.php" class="profile">Search</a>
    </div>
    <div style="margin-left: 5vw;"> 
        <h2>Food Recipes</h2>
    <h3>Enter a new recipe</h3>

    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
        <div class="error">
                <p>
                    <?php
                        if(!empty($name_err)){
                            echo "".$name_err;
                        }
                    ?>
                </p>
        </div>
        <input type="text" placeholder="Name" name="name"/>
        <br></br>
        <h3>Recipe</h3>
        <div class="error">
                <p>
                    <?php
                        if(!empty($recipe_err)){
                            echo "".$recipe_err;
                        }
                    ?>
                </p>
        </div>
        <br>
        <textarea rows="30" cols="60" name="recipe" name="recipe" placeholder="Recipe"></textarea>
        <br><br>
        <input type="submit" style="margin-bottom: 2vw;">
        <p style="font-size:15px;font-color:black;">
            <?php
                if(!empty($success_message)){
                    echo "".$success_message;
                } 
            ?>
        </p>
    </form>
    </div>
  </body>
</html>
