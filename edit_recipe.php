<?php
    session_start();
    $search = "";
    $search_err = $recipe_err = $video_err = $image_err = "";

    $searched = 0;
    $res_array = array();

    $conn = new MongoDB\Driver\Manager();

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST["search"])){
            if(empty(trim($_POST["name"]))){
                $search_err = "Please enter the recipe you want to search";
            } else{
                $filter = array("username" => "".$_SESSION["username"], "name_of_recipe" => "".$_POST["name"]);
                $query = new MongoDB\Driver\Query($filter);
                $result = $conn -> executeQuery("iwp_project.recipe", $query);
                $res_array = $result -> toarray();
                if(count($res_array) == 1){
                    $_SESSION["searched"] = $_POST["name"];
                    $_SESSION["image_name"] = $res_array[0] -> image;
                    $_SESSION["video_name"] = $res_array[0] -> video;
                    $searched = 1;
                } else{
                    $search_err = "No recipe found";
                    $searched = 0;
                }
            }
        }
        if(empty($search_err)){
            if(isset($_POST["updateRecipe"])){
                if(isset($_FILES["imageUpload"])){
                    $accepted_images = array("JPG", "PNG", "JPEG");
                    $file_extension = pathinfo($_FILES["imageUpload"]["name"], PATHINFO_EXTENSION);
                    if(!in_array($file_extension, $accepted_images)){
                        $image_err = "File uploaded is not an image";
                    }else{
                        $target_file = "User_images/" . $_SESSION["username"] . "_" . $_SESSION["searched"] . "." . $file_extension;
                        unlink($_SESSION["image_name"]);
                        move_uploaded_file($_FILES["imageUpload"]["tmp_name"], $target_file);
                    }
                }

                if(isset($_FILES["videoUpload"])){
                    $accepted_video = array("WEBM", "OGG", "MP4");
                    $file_extension_video = pathinfo($_FILES["videoUpload"]["name"], PATHINFO_EXTENSION);
                    if(!in_array($file_extension_video, $accepted_video)){
                        $video_err = "File uploaded is not a video that is supported by browsers";
                    } else{
                        $target_file_video = "User_videos/" . $_SESSION["username"] . "_" . $_SESSION["searched"] . "." . $file_extension_video;    
                        unlink($_SESSION["video_name"]);
                        move_uploaded_file($_FILES["videoUpload"]["tmp_name"], $target_file_video);
                    }
                }

                if(empty(trim($_POST["recipe"]))){
                    $recipe_err = "Please enter the updated recipe";
                } else{
                    $bulk = new MongoDb\Driver\BulkWrite;
                    $filter = array("username" => "".$_SESSION["username"], "name_of_recipe" => "".$_SESSION["searched"]);
                    $new_obj = array('$set' => array("recipe" => "".$_POST["recipe"]));
                    $bulk -> update($filter, $new_obj);
                    $result = $conn -> executeBulkWrite("iwp_project.recipe", $bulk);
                    if($result -> isAcknowledged()){
                        header("location: profile.php");
                        $success_message = "Recipe Updated";
                    } else{
                        $success_message = "Sorry. Something went wrong try again later";
                    }
                }
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
                <a href="delete_recipe.php" class="profile">Delete Recipe</a><br>
            </div>  
        </div>
    </div>
    <div style="margin-left: 5vw;"> 
        <h2>Edit recipe</h2>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
            <h3>Enter the name of recipe to change</h3>
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
            <br>    
            <div id="hidden">
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
                <textarea rows=30 cols=60 name="recipe" placeholder="Enter the recipe"><?php echo $res_array[0] -> recipe; ?></textarea>
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
                <input type="submit" style="margin-bottom: 2vw;" name="updateRecipe">
                <p style="font-size:15px;font-color:black;">
                    <?php
                        if(!empty($success_message)){
                            echo "".$success_message;
                        } 
                    ?>
                </p>
            </div>
        </form>
    </div>
    <script>
        function disp(){
            var searched = <?php echo $searched ?>;
            if(searched == 0){
                document.getElementById("hidden").style.display = 'none';
            } else{
                document.getElementById("hidden").style.display = 'block';
            }
        }
        disp();
    </script>
  </body>
</html>
