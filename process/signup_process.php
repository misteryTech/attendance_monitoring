<?php
include '../layout/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role       = trim($_POST['role']);
    $id_number  = trim($_POST['id_number']);
    $first_name = trim($_POST['firstname']);
    $last_name  = trim($_POST['lastname']);
    $username   = trim($_POST['username']);
    $password   = trim($_POST['password']);
    $verification = "pending";

    // 🔐 Hash the password before storing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Use prepared statements to avoid SQL injection
    $stmt = $conn->prepare("INSERT INTO user (`role`, id_number, firstname, lastname, username, `password`, verification)
                            VALUES (?, ?, ?, ?, ?, ?, ? )");
    $stmt->bind_param("sisssss", $role, $id_number, $first_name, $last_name, $username, $hashed_password , $verification);

     // Execute the statement and check for success

    if ($stmt->execute()) {
        header("Location: ../signup.php?status=success");
        exit;
    } else {
        header("Location: ../signup.php?status=error");
        exit;
    }
}
?>
