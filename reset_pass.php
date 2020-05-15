<?php
    session_start();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] != true){
        header("location: Login_page.php");
        exit;
    } 

    require_once "config.php";

    $new_password = $confirm_password = "";
    $new_password_err = $confirm_password_err = "";

    if($_SERVER["REQUEST_METHOD"] = "POST"){
        if(empty(trim($_POST["password"]))){
            $new_password_err = "Please enter the password";
        } else{
            $new_password = trim($_POST["password"]);
        }

        if(empty(trim($_POST["confirm"]))){
            $confirm_password_err = "Please enter your password again to confirm";
        } else{
            $confirm_password = trim($_POST["confirm"]);
            if(empty($new_password_err) && ($new_password != $confirm_password)){
                $confirm_password_err = "Password do not match";
            }
        } 

        if(empty($new_password_err) && empty($confirm_password_err)){
            $query = "update users set password = ? where username = ?";

            if($statement = mysqli_prepare($conn, $query)){
                mysqli_stmt_bind_param($statement, "ss", $param_password, $param_username);

                $param_password = password_hash($new_password, PASSWORD_DEFAULT);
                $param_username = $_SESSION["username"];

                if(mysqli_stmt_execute($statement)){
                    session_destroy();
                    header("location: Login_page.php");
                    exit();
                } else{
                    echo "Something went wrong. Try again later";
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
        <title>Reset password</title>
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
                        <p class="signup">Please enter the following</p>
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                            <div class="error">
                                <p>
                                    <?php
                                        if(!empty($new_password_err)){
                                            echo "".$new_password_err;
                                        }
                                    ?>
                                </p>
                            </div>
                            <div class="main">
                                <input type="password" name="password" id="password" placeholder="New Password">
                            </div>
                            <div class="error">
                                <p>
                                    <?php
                                        if(!empty($confirm_password_err)){
                                            echo "".$confirm_password_err;
                                        }
                                    ?>
                                </p>
                            </div>
                            <div class="main">
                                <input type="password" name="confirm" id="confirm" placeholder="Confirm password">
                            </div>
                            <div class="main">
                                <input type="submit" name="submit" id="submit" value="Reset">
                            </div>
                        </form>
                    </div>
                </div>
            </header>
        </div>
    </body>
</html>