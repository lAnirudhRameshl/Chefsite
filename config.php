<?php
    define('DB_SERVER','localhost');
    define('DB_USERNAME','user');
    define('DB_PASSWORD','password');
    define('DB_NAME','chefsite');

    $conn = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_NAME);

    if($conn == false){
        die("Error: Could not connect to database" . mysqli_connect_error());
    }
?>