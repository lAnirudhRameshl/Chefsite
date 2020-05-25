<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require "C:/Users/arvin/OneDrive/Documents/Anirudh Ramesh docs/study material/XAMPP/composer/vendor/autoload.php";
    $length = 8;
    $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $str = '';
    $email_err = "";
    $success_message = "";
    $max = mb_strlen($keyspace, '8bit') - 1;
    if ($max < 1) {
        throw new Exception('$keyspace must be at least two characters long');
    }
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[random_int(0, $max)];
    }

    $mail = new PHPMailer(TRUE);
    $mail -> isSMTP();
    $mail -> SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail -> Host = 'smtp.gmail.com';
    $mail -> Port = 587;
    $mail -> Username = "Your username";
    $mail -> Password = "Your password";
    $mail -> SMTPAuth = TRUE;

    $conn = new MongoDB\Driver\Manager();
    $bulk = new MongoDB\Driver\BulkWrite;
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(empty(trim($_POST["email"]))){
            $email_err = "Please enter the email";
        } else{
            $filter = array("email" => "".$_POST["email"]);
            $query = new MongoDB\Driver\Query($filter);
            $result = $conn -> executeQuery("iwp_project.users", $query);
            $res_array = $result -> toarray();
            if(count($res_array) == 0){
                $email_err = "No email registered";
            } else{
                $new_obj = array('$set' => array("password" => "".password_hash($str, PASSWORD_DEFAULT)));
                $bulk -> update($filter, $new_obj);
                $result = $conn -> executeBulkWrite("iwp_project.users", $bulk);
                try{
                    $mail -> setFrom('chefsite.site@gmail.com', 'Chefsite Admin');
                    $mail -> addAddress("".$_POST["email"]);
                    $mail -> Subject = "Password reset";
                    $mail -> Body = "Your new password is ".$str.". Please do not reply to this mail";
                    if(!$mail -> send()){
                        echo "<script>alert('Something went wrong. Try again later');</script>";
                    } else{
                        $success_message = "Check your mail to see the replaced password";
                    }
                }
                catch(Exception $e){
                    echo $e -> errorMessage();
                }
                catch(\Exception $e){
                    echo $e -> errorMessage();
                }
            }
        }
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
                    <div class="inner" style="height:40%">
                        <h3 class="signup">Welcome to Chefsite</h3>
                        <p class="signup">Please enter your email</p>
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
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
                            <div class="main">
                                <input type="submit" name="submit" id="submit" value="Reset">
                            </div>
                            <p class="login"><?php echo $success_message; ?></p>
                        </form>
                    </div>
                </div>
            </header>
        </div>
    </body>
</html>