<?php
include ('../layout/connection.php');

$id_number = $_POST['id_number'];


$query = "UPDATE user SET verification = 'yes' WHERE id_number = '$id_number'";
$query_run = mysqli_query($conn, $query);
if ($query_run) {
    // Redirect to the view_profile.php page with a success message
    header("Location: ../view_profile.php?id_number=$id_number&message=Status updated successfully");
    exit();
} else {
    // Redirect to the view_profile.php page with an error message
    header("Location: ../view_profile.php?id_number=$id_number&error=Failed to update status");
    exit();
}




?>