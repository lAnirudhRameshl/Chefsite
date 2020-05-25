<?php
    session_start();

    $conn = new MongoDB\Driver\Manager();
    $result = "";

    if(empty($_SESSION["search_name"])){
        $filter = array("username" => "".$_SESSION["username"]);
    } else{
        $filter = array("username" => "".$_SESSION["search_name"]);
    }
    $query = new MongoDb\Driver\Query($filter);
    $result = $conn -> executeQuery("iwp_project.recipe", $query);
    $res_array = $result -> toarray();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" type="text/css" href="CSS/profile.css">
    <title>Profile</title>
  </head>
  <body>
    <div class="navigation">
        <a class="title" href="profile.php">Chefsite</a>
        <div class="dropdown">
            <a class="dropdown_a"><?php echo $_SESSION["username"] ?></a>
            <div class="dropdown_content">
                <a class="profile" href="logout.php">Logout</a><br>
                <a href="main.php" class="profile">New recipe</a><br>
                <a href="reset_pass.php" class="profile">Reset Password</a><br>
                <a href="search.php" class="profile">Search</a><br>
                <a href="edit_recipe.php" class="profile">Edit Recipe</a><br>
                <a href="delete_recipe.php" class="profile">Delete Recipe</a><br>
            </div>    
        </div>
  </div>
    <div style="margin-left: 5vw;">
    <h2>Name: <?php echo $_SESSION["username"]; ?></h2>
    <br />
    <h3>Recipes</h3>
    <?php
        foreach($res_array as $recipe){
            echo '<button type="button" class="collapsible">'.$recipe -> name_of_recipe.'</button>';
            echo "<div class='content'>";
            echo "<img align='right' height=300px width=300px style='margin:10px;' src = '".$recipe -> image."' />"; 
            echo "<p style='white-space:pre-line;'>".$recipe -> recipe."</p>";
            echo "<video controls><source src='".$recipe -> video . "'type='video/mp4' >";
            echo "<video controls><source src='".$recipe -> video . "'type='video/ogg' >"; 
            echo "<video controls><source src='".$recipe -> video . "'type='video/webm' >";
            echo "Browser does not support video";
            echo "</video>";            
            echo "</div>";
        }

    ?>
    <script>
        var coll = document.getElementsByClassName("collapsible");
        var i;

        for (i = 0; i < coll.length; i++) {
            coll[i].addEventListener("click", function() {
                this.classList.toggle("active");
                var content = this.nextElementSibling;
                if (content.style.display === "block") {
                    content.style.display = "none";
                } else {
                    content.style.display = "block";
                }
            }); 
        } 
    </script>
  </div>
  </body>
</html>
