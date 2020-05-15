<?php
    require_once "config.php";

    //echo "<script>alert('Welcome');</script>";

    $username = $password = $confirm_password = $email = "";
    $username_err = $password_err = $confirm_password_err = $email_err = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(empty(trim($_POST["email"]))){
            $email_err = "Email field empty";
        }

        if(empty(trim($_POST["username"]))){
            $username_err = "Username field empty";
        } else{
            $query = "select * from users where username = ?";

            if($statement = mysqli_prepare($conn, $query)){
                mysqli_stmt_bind_param($statement, "s", $param_username);
                $param_username = trim($_POST["username"]);

                if(mysqli_stmt_execute($statement)){
                    mysqli_stmt_store_result($statement);

                    if(mysqli_stmt_num_rows($statement) == 1){
                        $username_err = "Username already taken";
                    } else{
                        $username = trim($_POST["username"]);
                    }
                } else{
                    echo "<script type='text/javascript'>alert('Sorry!! Something went wrong. Try again later');</script>";
                    
                }
                mysqli_stmt_close($statement);
            }
        }

        if(empty(trim($_POST["password"]))){
            $password_err = "Password field empty";
        } elseif(strlen(trim($_POST["password"])) < 6){
            $password_err = "Password must have at least 6 characters";
        } else{
            $password = trim($_POST["password"]);
        }

        if(empty(trim($_POST["confirm"]))){
            $confirm_password_err = "Confirm password must not be empty";
        } else{
            $confirm_password = trim($_POST["confirm"]);
            if(empty($password_err) && $password != $confirm_password){
                $confirm_password_err = "Passwords did not match";
            }
        }

        if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
            $query = "insert into users values(?, ?);";
            
            if($statement = mysqli_prepare($conn, $query)){
                mysqli_stmt_bind_param($statement,"ss",$param_username, $param_password);
                $param_username = $username;
                $param_password = password_hash($password, PASSWORD_DEFAULT);

                if(mysqli_stmt_execute($statement)){
                    header("location: Login_page.php");
                } else{
                    echo "<script type='text/javascript'>alert('Something went wrong. Try again later');</script>";
                }

                mysqli_stmt_close($statement);
            }
        } /*else{
            if(!empty($username_err)){
                echo "<script type='text/javascript'>alert('".$username_err."');</script>";
            }
            if(!empty($password_err)){
                echo "<script type='text/javascript'>alert('".$password_err."');</script>";
            }
            if(!empty($confirm_password_err)){
                echo "<script type='text/javascript'>alert('".$confirm_password_err."');</script>";
            }
            if(!empty($email_err)){
                echo "<script type='text/javascript'>alert('".$email_err."');</script>";
            }
        }*/
        mysqli_close($conn);
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Signup</title>
        <link rel="stylesheet" type="text/css" href="CSS/signup.css">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    </head>
    <body>
        <div class="background" style="overflow:hidden">
            <nav>
                <div class="navigation">
                    <a class="title" href="">Chefsite</a>
                    <a class="login" href="signup.php">Signup</a>
                    <a class="login" href="Login_page.php">Login</a>
                </div>
            </nav>
            <header>
                <div class="container">
                    <div class="inner">
                        <h3 class="signup">Welcome to Chefsite</h3>
                        <p class="signup">Please enter your details</p>
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                            <div class="error">
                                <p>
                                    <?php
                                        if(!empty($username_err)){
                                            echo "".$username_err;
                                        }
                                    ?>
                                </p>
                            </div>
                            <div class="main">
                                <input type="text" name="username" id="username" placeholder="Username">
                            </div>
                            <div class="error">
                                <p>
                                    <?php
                                        if(!empty($email_err)){
                                            echo "".$email_err;
                                        }
                                    ?>
                                </p>
                            </div>
                            <div class="main">
                                <input type="email" name="email" id="email" placeholder="E-mail">
                            </div>
                            <div class="error">
                                <p>
                                    <?php
                                        if(!empty($password_err)){
                                            echo "".$password_err;
                                        }
                                    ?>
                                </p>
                            </div>
                            <div class="main">
                                <input type="password" name="password" id="password" placeholder="Password">
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
                                <input type="submit" name="submit" id="submit" value="Signup">
                            </div>
                        </form>
                    </div>
                </div>
            </header>
        </div>
    </body>
</html>