<?php
// verify_face.php
header('Content-Type: application/json');

// === FACE++ API CONFIG ===
$api_key = 'gCB6xe-0lpTtGHWVsSBReG3f3paQvUf8';
$api_secret = 'FaT-sI6uN8aLRSldToQIZX9st9LKFy_S';

// Include database connection
include 'layout/connection.php'; // adjust path if needed

// Get JSON input from frontend
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['image']) || !isset($input['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

$student_id = $input['student_id'];
$image_data = $input['image'];
$facial_token = $input['facial_token'] ?? null;

if (!$facial_token) {
    // Fetch from DB if not sent
    $stmt = $conn->prepare("SELECT facial_token FROM biometrics WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'No face token found for student']);
        exit;
    }
    $row = $result->fetch_assoc();
    $facial_token = $row['facial_token'];
    $stmt->close();
}

// Convert base64 image to binary
$image_data = preg_replace('#^data:image/\w+;base64,#i', '', $image_data);
$image_binary = base64_decode($image_data);

// Save temporary image
$temp_file = tempnam(sys_get_temp_dir(), 'face_') . '.jpg';
file_put_contents($temp_file, $image_binary);

// === Face++ Compare API ===
$api_url = 'https://api-us.faceplusplus.com/facepp/v3/compare';
$post_fields = [
    'api_key' => $api_key,
    'api_secret' => $api_secret,
    'image_file1' => new CURLFile($temp_file),
    'face_token2' => $facial_token // ✅ Corrected
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Clean up temp file
unlink($temp_file);

if (!$response) {
    echo json_encode(['success' => false, 'message' => 'Face++ API error']);
    exit;
}

$result = json_decode($response, true);

if (isset($result['confidence']) && $result['confidence'] > 80) { // threshold adjustable
    echo json_encode([
        'success' => true,
        'message' => 'Face matched successfully!',
        'confidence' => $result['confidence']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Face does not match',
        'confidence' => $result['confidence'] ?? 'N/A'
    ]);
}

$conn->close();
?>
