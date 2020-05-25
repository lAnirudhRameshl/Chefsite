<?php
    session_start();

    $conn = new MongoDB\Driver\Manager();

    $number_of_steps = 1;
    $name = $recipe = $image = "";
    $name_err = $recipe_err = $image_err = $video_err = "";
    $success_message = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(empty(trim($_POST["name"]))){
            $name_err = "Enter the name of the recipe";
        } else{
            $name = trim($_POST["name"]);
            $filter = array("username" => "".$_SESSION["username"], "name_of_recipe" => "".$name);
            $query = new MongoDB\Driver\Query($filter);
            $result = $conn -> executeQuery("iwp_project.recipe",$query);
            $res_array = $result -> toarray();
            if(count($res_array) == 1){
                $name_err = "Recipe already exists";
            }
        }

        if(empty(trim($_POST["recipe"]))){
            $recipe_err = "Enter the recipe";
        } else{
            $recipe = $_POST["recipe"];
        }

        if(isset($_FILES["imageUpload"])){
            $accepted_images = array("JPG", "PNG", "JPEG");
            $file_extension = pathinfo($_FILES["imageUpload"]["name"], PATHINFO_EXTENSION);
            if(!in_array($file_extension, $accepted_images)){
                $image_err = "File uploaded is not an image";
            }
        } else{
            $image_err = "Please enter the image";
        }

        if(isset($_FILES["videoUpload"])){
            $accepted_video = array("WEBM", "OGG", "MP4");
            $file_extension_video = pathinfo($_FILES["videoUpload"]["name"], PATHINFO_EXTENSION);
            if(in_array($file_extension_video, $accepted_video)){
                $video_err = "File uploaded is not a video that is supported by browsers";
            }
        } else{
            $video_err = "Please enter the video";
        }

        if(empty($name_err) && empty($recipe_err) && empty($image_err)){
            $target_file = "User_images/" . $_SESSION["username"] . "_" . $name . "." . $file_extension;
            $target_file_video = "User_videos/" . $_SESSION["username"] . "_" . $name . "." . $file_extension_video;
            if(move_uploaded_file($_FILES["imageUpload"]["tmp_name"], $target_file) && move_uploaded_file($_FILES["videoUpload"]["tmp_name"], $target_file_video)){
                $bulk = new MongoDB\Driver\BulkWrite;
                $insert = array("username" => "".$_SESSION["username"], "name_of_recipe" => "".$name, "recipe" => "".$recipe, "image" => "".$target_file, "video" => "".$target_file_video);
                $bulk -> insert($insert);
                $res = $conn -> executeBulkWrite("iwp_project.recipe", $bulk);
                if($res -> isAcknowledged()){
                    $success_message = "New recipe added!!";
    
                } else{
                    echo "<script type='text/javascript'>alert('Something went wrong. Try again later1');</script>";
                }
            } else{
                echo "<script type='text/javascript'>alert('Something went wrong. Try again later`');</script>";
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
    <title>New recipe</title>
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
                <a href="edit_recipe.php" class="profile">Edit Recipe</a><br>
                <a href="delete_recipe.php" class="profile">Delete Recipe</a><br>
            </div>  
        </div>
    </div>
    <div style="margin-left: 5vw;"> 
        <h2>Food Recipes</h2>
    <h3>Enter a new recipe</h3>

    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
        <div class="error">
                <p>
                    <?php
                        if(!empty($name_err)){
                            echo "".$name_err;
                        }
                    ?>
                </p>
        </div>
        <input type="text" placeholder="Name" name="name" id="name"/>
        <br>
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
        <textarea rows=30 cols=60 name="recipe" placeholder="Enter the recipe"></textarea>
        <br><br>
        <h3>Image of recipe: </h3>
        <div class="error">
                <p>
                    <?php
                        if(!empty($image_err)){
                            echo "".$image_err;
                        }
                    ?>
                </p>
        </div>
        <br>
        <input type="file" name="imageUpload">
        <br><br>
        <h3>Video of recipe: </h3>
        <div class="error">
                <p>
                    <?php
                        if(!empty($video_err)){
                            echo "".$video_err;
                        }
                    ?>
                </p>
        </div>
        <br>
        <input type="file" name="videoUpload">
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
