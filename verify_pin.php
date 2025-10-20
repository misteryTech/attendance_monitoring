<?php
header('Content-Type: application/json');
include 'layout/connection.php'; // adjust path if needed

$input = json_decode(file_get_contents('php://input'), true);
$student_id = $input['student_id'] ?? '';
$entered_pin = $input['pin'] ?? '';

if (!$student_id || !$entered_pin) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters.']);
    exit;
}

// Fetch student's registered PIN
$stmt = $conn->prepare("SELECT pincode FROM biometrics WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'No PIN registered. Please update your profile.']);
    exit;
}

$row = $result->fetch_assoc();
$stored_pin = $row['pincode'];

if (!$stored_pin) {
    echo json_encode(['success' => false, 'message' => 'No PIN found. Please update your profile.']);
    exit;
}

if ($entered_pin === $stored_pin) {
    echo json_encode(['success' => true, 'message' => 'PIN verified successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Incorrect PIN. Try again.']);
}

$stmt->close();
$conn->close();
?>
