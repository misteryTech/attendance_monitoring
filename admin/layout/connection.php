
<?php
    $localhost = "localhost";
    $username = "root";
    $password = "";
    $database = "attendance";


    mysqli_connect($localhost, $username, $password, $database) or die("Connection Failed");
    $conn = mysqli_connect($localhost, $username, $password, $database);
  ?>
