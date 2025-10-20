<?php
header('Content-Type: application/json');
include '../layout/connection.php';

session_start();
$user_id = $_SESSION['user_id'];

if (empty($user_id)) {
    echo json_encode(['success' => false, 'message' => 'User ID not found in session.']);
    exit;
}

$student_id = $_POST['student_id'] ?? '';
$pincode = $_POST['pincode'] ?? '';
$year = $_POST['year'] ?? '';
$section = $_POST['section'] ?? '';

if (empty($pincode) || empty($year) || empty($section)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

// Check if record already exists
$check = $conn->prepare("SELECT pincode, year, section FROM biometrics WHERE student_id = ?");
$check->bind_param("s", $student_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    // Record exists — check if values have changed
    $existing = $result->fetch_assoc();

    if (
        $existing['pincode'] !== $pincode ||
        $existing['year'] !== $year ||
        $existing['section'] !== $section
    ) {
        // Update record
        $update = $conn->prepare("UPDATE biometrics SET pincode = ?, year = ?, section = ? WHERE student_id = ?");
        $update->bind_param("ssss", $pincode, $year, $section, $student_id);

        if ($update->execute()) {
            echo json_encode(['success' => true, 'message' => 'Biometrics updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Update failed: ' . $update->error]);
        }
    } else {
        // No changes
        echo json_encode(['success' => true, 'message' => 'No changes detected.']);
    }
} else {
    // Insert new record
    $insert = $conn->prepare("INSERT INTO biometrics (student_id, pincode, year, section) VALUES (?, ?, ?, ?)");
    if (!$insert) {
        echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
        exit;
    }

    $insert->bind_param("ssss", $student_id, $pincode, $year, $section);

    if ($insert->execute()) {
        echo json_encode(['success' => true, 'message' => 'Biometrics registered successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Insert failed: ' . $insert->error]);
    }
}
?>