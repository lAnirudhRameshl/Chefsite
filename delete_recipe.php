<?php
    session_start();
    
    $conn = new MongoDB\Driver\Manager();

    $search_err = "";
    $success_message = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(empty(trim($_POST["name"]))){
            $search_err = "Please enter the recipe to delete";
        } else{
            $filter = array("username" => "".$_SESSION["username"], "name_of_recipe" => "".$_POST["name"]);
            $query = new MongoDB\Driver\Query($filter);
            $result = $conn -> executeQuery("iwp_project.recipe", $query);
            $res_array = $result -> toarray();
            if(count($res_array) == 1){
                unlink($res_array[0] -> image);
                unlink($res_array[0] -> video);
                $bulk = new MongoDB\Driver\BulkWrite;
                $filter = array("username" => "".$_SESSION["username"], "name_of_recipe" => "".$_POST["name"]);
                $bulk -> delete($filter);
                $result = $conn -> executeBulkWrite("iwp_project.recipe", $bulk);
                if($result -> isAcknowledged()){
                    $success_message = "Deleted successfully";
                }else{
                    $success_message = "Something went wrong. Try again later";
                }
            } else{
                $search_err = "No recipe found";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" type="text/css" href="CSS/profile.css">
    <title>Edit recipe</title>
  </head>
  <body>
    <div class="navigation">
        <a class="title" href="profile.php">Chefsite</a>
        <div class="dropdown">
            <a class="dropdown_a"><?php echo $_SESSION["username"] ?></a>
            <div class="dropdown_content">
                <a class="profile" href="logout.php">Logout</a><br>
                <a href="profile.php" class="profile">Profile</a><br>
                <a href="reset_pass.php" class="profile">Reset Password</a><br>
                <a href="search.php" class="profile">Search</a><br>
                <a href="main.php" class="profile">New Recipe</a><br>
                <a href="edit_recipe.php" class="profile">Edit Recipe</a><br>
            </div>  
        </div>
    </div>
    <div style="margin-left: 5vw;"> 
        <h2>Delete recipe</h2>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <h3>Enter the name of recipe to delete</h3>
                <div class="error">
                    <p>
                        <?php
                            if(!empty($search_err)){
                                echo "".$search_err;
                            }
                        ?>
                    </p>
                </div>
            <input type="text" placeholder="Name of recipe" name="name" id="name"/>
            <input type="submit" value="search" id="search" name="search" style="height:30px;" />
            <p><?php echo $success_message; ?></p>
        </form>
    </div>
  </body>
</html>
