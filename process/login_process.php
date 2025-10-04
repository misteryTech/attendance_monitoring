<?php
include '../layout/connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Use prepared statement to avoid SQL injection
    $stmt = $conn->prepare("SELECT id, username, password, role FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // ✅ Verify password (assuming you stored it hashed)
        if (password_verify($password, $row['password'])) {
            // Store session variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $role_assigned = $_SESSION['role'] = $row['role'];
            // Redirect based on role
            if ($role_assigned === 'admin') {
                header("Location: ../admin/dashboard.php");
                exit;
            } elseif ($role_assigned === 'student') {
                header("Location: ../student/dashboard.php");
                exit;
            } elseif ($role_assigned === 'parent') {
                header("Location: ../parent/dashboard.php");
                exit;
            }


        } else {
            header("Location: ../login.php?status=invalid");
            exit;
        }
    } else {
        header("Location: ../login.php?status=invalid");
        exit;
    }
}
?>
