<?php
// process/insert_profile_image.php
include '../layout/connection.php';

$student_id = $_POST['student_id'] ?? '';

if (empty($student_id)) {
    die('Student ID missing.');
}

// ✅ Check uploaded file
if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
    die('No image uploaded or upload error.');
}

// ✅ Set upload directory
$uploadDir = '../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// ✅ Extract file extension (e.g., jpg, png)
$fileTmp = $_FILES['profile_image']['tmp_name'];
$fileExt = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
if (empty($fileExt)) $fileExt = 'jpg';

// ✅ New filename format: <student_id>_profile.<ext>
$fileName = $student_id . '_profile.' . $fileExt;
$filePath = $uploadDir . $fileName;

// ✅ Move uploaded file (overwrite if exists)
if (!move_uploaded_file($fileTmp, $filePath)) {
    die('Failed to move uploaded file.');
}

// ✅ Relative path for database
$imageLocation = 'uploads/' . $fileName;

// === FACE++ API CONFIG ===
$api_key = 'gCB6xe-0lpTtGHWVsSBReG3f3paQvUf8';
$api_secret = 'FaT-sI6uN8aLRSldToQIZX9st9LKFy_S';
$face_api_url = 'https://api-us.faceplusplus.com/facepp/v3/detect';

// ✅ Send image to Face++ for detection
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $face_api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'api_key' => $api_key,
    'api_secret' => $api_secret,
    'image_file' => new CURLFile(realpath($filePath))
]);

$response = curl_exec($ch);
$curl_error = curl_error($ch);
curl_close($ch);

if ($curl_error) {
    unlink($filePath);
    die("CURL Error: " . $curl_error);
}

$result = json_decode($response, true);

// ✅ Check if a face is detected
if (!isset($result['faces'][0]['face_token'])) {
    unlink($filePath);
    echo "<script>alert('⚠️ No face detected. Please ensure your face is clearly visible.'); history.back();</script>";
    exit;
}

// ✅ Get Face++ token
$face_token = $result['faces'][0]['face_token'];

// ✅ Update record in biometrics table (facial_recognition + face_token)
$stmt = $conn->prepare("
    UPDATE biometrics
    SET facial_recognition = ?, facial_token = ?
    WHERE student_id = ?
");
$stmt->bind_param("sss", $imageLocation, $face_token, $student_id);

if ($stmt->execute()) {
    echo "<script>alert('✅ Profile image and Face++ token updated successfully!'); window.location='../respond_request.php?student_id=$student_id';</script>";
} else {
    echo "<script>alert('❌ Failed to update image or face token. Please try again.'); history.back();</script>";
}

$stmt->close();
$conn->close();
?>
