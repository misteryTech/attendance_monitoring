<?php
header('Content-Type: application/json');
include '../layout/connection.php';

$student_id = $_POST['student_id'] ?? '';
$date = $_POST['date'] ?? '';
$time = $_POST['time'] ?? '';
$message = $_POST['message'] ?? '';
$status = 'Pending';

if (empty($student_id) || empty($date) || empty($time) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO schedule_picture (student_id, date_request, `time`, `message`, `status`) VALUES (?, ?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("sssss", $student_id, $date, $time, $message, $status);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Insert failed: ' . $stmt->error]);
}
?>