<?php
    session_start();

    require_once "config.php";
    $result = "";

    $query = "select * from recipe where username = ?";

    if($statement = mysqli_prepare($conn, $query)){
        mysqli_stmt_bind_param($statement, "s", $param_username);
        if(empty($_SESSION["search_name"])){
            $param_username = $_SESSION["username"];
        } else{
            $param_username = $_SESSION["search_name"];
        }

         if(mysqli_stmt_execute($statement)){
            //mysqli_stmt_store_result($statement);

            $result = mysqli_stmt_get_result($statement);
        } else{
             echo "<script type='text/javascript'>alert('Sorry!! Something went wrong. Try again later');</script>";
        }
        mysqli_stmt_close($statement);
        mysqli_close($conn);
    }
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
      <a class="title" href="main.php">Chefsite</a>
        <a class="profile" href="logout.php">Logout</a>
        <a href="main.php" class="profile">New recipe</a>
        <a href="reset_pass.php" class="profile">Reset Password</a>
        <a href="search.php" class="profile">Search</a>
  </div>
    <div style="margin-left: 5vw;">
    <h2>Name: <?php echo $_SESSION["username"]; ?></h2>
    <br />
    <h3>Recipes</h3>
    <?php
        while($row = mysqli_fetch_assoc($result)){
            echo '<button type="button" class="collapsible">'.$row["name_of_recipe"].'</button>';
            echo "<div class='content'>";
            echo "<p>".$row['preparation']."</p>";
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
