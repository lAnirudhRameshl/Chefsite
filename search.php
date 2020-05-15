<?php
    session_start();

    require_once "config.php";

    $_SESSION["search_name"] = "";

    $search_username = "";
    $search_username_err = "";

    $flag = 0;

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        
        if(!empty(trim($_POST["search"]))){
            $search_username = $_POST["search"];
        
            $query = "select * from recipe where username = ?";

            if($statement = mysqli_prepare($conn, $query)){
                mysqli_stmt_bind_param($statement, "s", $param_username);
                $param_username = trim($_POST["search"]);

                if(mysqli_stmt_execute($statement)){
                    mysqli_stmt_store_result($statement);

                    if(mysqli_stmt_num_rows($statement) == 0){
                        $username_err = "No user found";
                    } else{
                        $_SESSION["search_name"] = $search_username;
                        $flag = 1;
                    }
                } else{
                    echo "<script type='text/javascript'>alert('Sorry!! Something went wrong. Try again later');</script>";
                }
                mysqli_stmt_close($statement);
            }
        }
        mysqli_close($conn);
        if($flag == 1){
            header("location: profile.php");
            exit;
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
                <a class="title" href="profile.php">Chefsite</a>
                <a class="profile" href="logout.php">Logout</a>
                <a href="profile.php" class="profile">Profile</a>
                <a href="reset_pass.php" class="profile">Reset Password</a>
                <a href="search.php" class="profile">Search</a>
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