<?php
// ------ CONFIG ------
$FACEPP_KEY    = "Wyo50FpK5bck97B9TRGdG2Vx7Oz6vVS1";
$FACEPP_SECRET = "KwhCsPYtzJU6j56W9AWlnvxqbQuGOIlX";
$FACESET_TOKEN = "303d872c0d2dfb7f58abd75b20724fba";

$host = "srv1412.hstgr.io";
$user = "u499793037_attendance";
$pass = "XF:VY7+zD";
$db   = "u499793037_attendance";

// --------------------
function httpPost($url, $data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $response = curl_exec($ch);
    if ($response === false) die("cURL Error: ".curl_error($ch));
    curl_close($ch);
    return $response;
}

$conn = new mysqli($host,$user,$pass,$db);
if($conn->connect_error) die("DB Connection failed: ".$conn->connect_error);

// --------------------
// ------------- Registration -------------
$regMessage = "";
if(isset($_POST['register'])){
    $fullname=$_POST['fullname'] ?? '';
    $address=$_POST['address'] ?? '';
    $contact=$_POST['contact'] ?? '';
    $course=$_POST['course'] ?? '';

    if(isset($_FILES['face']) && $_FILES['face']['error']===UPLOAD_ERR_OK){
        $uploadDir="faces/";
        if(!is_dir($uploadDir)) mkdir($uploadDir,0777,true);
        $fileTmp=$_FILES['face']['tmp_name'];
        $fileName=time()."_".basename($_FILES['face']['name']);
        $target=$uploadDir.$fileName;
        if(move_uploaded_file($fileTmp,$target)){
            $cfile=new CURLFile(realpath($target),mime_content_type($target),$fileName);
            $detectResp=httpPost("https://api-us.faceplusplus.com/facepp/v3/detect",[
                "api_key"=>$FACEPP_KEY,"api_secret"=>$FACEPP_SECRET,"image_file"=>$cfile
            ]);
            $detectJson=json_decode($detectResp,true);
            $face_token=$detectJson['faces'][0]['face_token'] ?? null;
            if($face_token){
                httpPost("https://api-us.faceplusplus.com/facepp/v3/faceset/addface",[
                    "api_key"=>$FACEPP_KEY,"api_secret"=>$FACEPP_SECRET,
                    "faceset_token"=>$FACESET_TOKEN,"face_tokens"=>$face_token
                ]);
                $stmt=$conn->prepare("INSERT INTO users (name,address,contact_number,course,face_image,face_token) VALUES (?,?,?,?,?,?)");
                $stmt->bind_param("ssssss",$fullname,$address,$contact,$course,$target,$face_token);
                if($stmt->execute()){
                    $regMessage="<div class='alert alert-success'>✅ Registration successful!<br><img src='$target' class='img-thumbnail mt-2' width='200'></div>";
                }else $regMessage="<div class='alert alert-danger'>❌ Database insert failed.</div>";
                $stmt->close();
            }else $regMessage="<div class='alert alert-warning'>❌ No face detected. Try another image.</div>";
        }else $regMessage="<div class='alert alert-danger'>❌ Failed to save uploaded file.</div>";
    }else $regMessage="<div class='alert alert-danger'>❌ No face image uploaded.</div>";
}

// ------------- File Recognition -------------
$recMessage="";
if(isset($_POST['recognize']) && isset($_FILES['image'])){
    $imgPath=$_FILES['image']['tmp_name'];
    $imageData=file_get_contents($imgPath);
    $imageBase64=base64_encode($imageData);

    $detectResp=httpPost("https://api-us.faceplusplus.com/facepp/v3/detect",[
        "api_key"=>$FACEPP_KEY,"api_secret"=>$FACEPP_SECRET,"image_base64"=>$imageBase64
    ]);
    $detectJson=json_decode($detectResp,true);

    if(!isset($detectJson['faces'][0]['face_token'])){
        $recMessage="<div class='alert alert-danger'>❌ No face detected.</div>";
    }else{
        $face_token=$detectJson['faces'][0]['face_token'];
        $searchResp=httpPost("https://api-us.faceplusplus.com/facepp/v3/search",[
            "api_key"=>$FACEPP_KEY,"api_secret"=>$FACEPP_SECRET,
            "face_token"=>$face_token,"faceset_token"=>$FACESET_TOKEN
        ]);
        $searchJson=json_decode($searchResp,true);
        if(isset($searchJson['results'][0])){
            $confidence=$searchJson['results'][0]['confidence'];
            $matchedToken=$searchJson['results'][0]['face_token'];

            $stmt=$conn->prepare("SELECT * FROM users WHERE face_token=?");
            $stmt->bind_param("s",$matchedToken);
            $stmt->execute();
            $userData=$stmt->get_result()->fetch_assoc();
            $stmt->close();

            if($userData){
                $color=$confidence>75?'success':'danger';
                $recMessage="<div class='alert alert-$color'>
                    <h5>✅ Recognition Result</h5>
                    <p><b>Name:</b> ".htmlspecialchars($userData['name'])."</p>
                    <p><b>Address:</b> ".htmlspecialchars($userData['address'])."</p>
                    <p><b>Contact:</b> ".htmlspecialchars($userData['contact_number'])."</p>
                    <p><b>Course:</b> ".htmlspecialchars($userData['course'])."</p>
                    <p><b>Confidence:</b> $confidence</p>
                    <p>".($confidence>75?"This is a match":"Not confident enough")."</p>
                    <img src='data:image/jpeg;base64,$imageBase64' class='img-thumbnail mt-2' width='200'>
                </div>";
            }else $recMessage="<div class='alert alert-warning'>❌ Face matched but user not found.</div>";
        }else $recMessage="<div class='alert alert-warning'>❌ No match found in FaceSet.</div>";
    }
}

// ------------- Update (Edit) User -------------
$updateMessage = "";
if (isset($_POST['update_user'])) {
    $id = intval($_POST['user_id'] ?? 0);
    $fullname = $_POST['fullname'] ?? '';
    $address  = $_POST['address'] ?? '';
    $contact  = $_POST['contact'] ?? '';
    $course   = $_POST['course'] ?? '';
    $finger_id = $_POST['finger_id'] ?? null;

    // Get current user row
    $oldRow = $conn->query("SELECT face_image, face_token FROM users WHERE id=".$id)->fetch_assoc();
    $oldFaceToken = $oldRow['face_token'] ?? null;
    $oldFacePath  = $oldRow['face_image'] ?? null;

    $newFaceToken = null;
    $newFacePath = null;

    // If new face uploaded, process it
    if (isset($_FILES['edit_face']) && $_FILES['edit_face']['error'] === UPLOAD_ERR_OK) {
        $uploadDir="faces/";
        if(!is_dir($uploadDir)) mkdir($uploadDir,0777,true);
        $fileTmp = $_FILES['edit_face']['tmp_name'];
        $fileName = "edit_".time()."_".basename($_FILES['edit_face']['name']);
        $target = $uploadDir.$fileName;
        if(move_uploaded_file($fileTmp,$target)){
            $cfile = new CURLFile(realpath($target), mime_content_type($target), $fileName);
            $detectResp = httpPost("https://api-us.faceplusplus.com/facepp/v3/detect", [
                "api_key"=>$FACEPP_KEY, "api_secret"=>$FACEPP_SECRET, "image_file"=>$cfile
            ]);
            $detectJson = json_decode($detectResp, true);
            $newFaceToken = $detectJson['faces'][0]['face_token'] ?? null;
            if ($newFaceToken) {
                // add new face to faceset
                httpPost("https://api-us.faceplusplus.com/facepp/v3/faceset/addface", [
                    "api_key"=>$FACEPP_KEY, "api_secret"=>$FACEPP_SECRET,
                    "faceset_token"=>$FACESET_TOKEN, "face_tokens"=>$newFaceToken
                ]);
                $newFacePath = $target;
            } else {
                // failed detect on new image - remove saved file
                if(file_exists($target)) unlink($target);
                $updateMessage = "<div class='alert alert-warning'>❌ New face not detected. Update aborted for face image, other fields may still be updated.</div>";
                // continue — we won't use newFaceToken
                $newFaceToken = null;
                $newFacePath = null;
            }
        } else {
            $updateMessage = "<div class='alert alert-danger'>❌ Failed to save uploaded face image.</div>";
        }
    }

    // If we have a new face token, remove old face token from faceset and delete old image file
    if (!empty($newFaceToken) && !empty($oldFaceToken)) {
        httpPost("https://api-us.faceplusplus.com/facepp/v3/faceset/removeface", [
            "api_key"=>$FACEPP_KEY, "api_secret"=>$FACEPP_SECRET,
            "faceset_token"=>$FACESET_TOKEN, "face_tokens"=>$oldFaceToken
        ]);
        if ($oldFacePath && file_exists($oldFacePath)) {
            @unlink($oldFacePath);
        }
    }

    // Build update query depending on whether new face was uploaded and detected
    if (!empty($newFaceToken) && !empty($newFacePath)) {
        $stmt = $conn->prepare("UPDATE users SET name=?, address=?, contact_number=?, course=?, face_image=?, face_token=?, finger_id=? WHERE id=?");
        $stmt->bind_param("sssssssi", $fullname, $address, $contact, $course, $newFacePath, $newFaceToken, $finger_id, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, address=?, contact_number=?, course=?, finger_id=? WHERE id=?");
        $stmt->bind_param("sssssi", $fullname, $address, $contact, $course, $finger_id, $id);
    }

    if ($stmt->execute()) {
        if (empty($updateMessage)) $updateMessage = "<div class='alert alert-success'>✅ User updated successfully.</div>";
    } else {
        $updateMessage = "<div class='alert alert-danger'>❌ Update failed.</div>";
    }
    $stmt->close();
}

// ------------- Delete User -------------
$deleteMessage="";
if(isset($_GET['delete_id'])){
    $id=intval($_GET['delete_id']);
    $row=$conn->query("SELECT face_image,face_token FROM users WHERE id=$id")->fetch_assoc();
    if($row){
        $faceToken=$row['face_token'];
        $imagePath=$row['face_image'];
        $postData=["api_key"=>$FACEPP_KEY,"api_secret"=>$FACEPP_SECRET,"faceset_token"=>$FACESET_TOKEN,"face_tokens"=>$faceToken];
        httpPost("https://api-us.faceplusplus.com/facepp/v3/faceset/removeface",$postData);
        if(file_exists($imagePath)) unlink($imagePath);
        $conn->query("DELETE FROM users WHERE id=$id");
        $deleteMessage="<div class='alert alert-success'>✅ User deleted.</div>";
    }else $deleteMessage="<div class='alert alert-danger'>❌ User not found.</div>";
}

// ------------- Attendance -------------
$attMessage="";
if((isset($_POST['attendance_camera']) || isset($_POST['attendance_file'])) && isset($_FILES['attendance_image'])){
    $imgPath=$_FILES['attendance_image']['tmp_name'];
    $imageData=file_get_contents($imgPath);
    $imageBase64=base64_encode($imageData);

    $detectResp=httpPost("https://api-us.faceplusplus.com/facepp/v3/detect",[
        "api_key"=>$FACEPP_KEY,"api_secret"=>$FACEPP_SECRET,"image_base64"=>$imageBase64
    ]);
    $detectJson=json_decode($detectResp,true);

    if(!isset($detectJson['faces'][0]['face_token'])){
        $attMessage="<div class='alert alert-danger'>❌ No face detected.</div>";
    }else{
        $face_token=$detectJson['faces'][0]['face_token'];
        $searchResp=httpPost("https://api-us.faceplusplus.com/facepp/v3/search",[
            "api_key"=>$FACEPP_KEY,"api_secret"=>$FACEPP_SECRET,
            "face_token"=>$face_token,"faceset_token"=>$FACESET_TOKEN
        ]);
        $searchJson=json_decode($searchResp,true);

        if(isset($searchJson['results'][0]) && $searchJson['results'][0]['confidence']>75){
            $matchedToken=$searchJson['results'][0]['face_token'];
            $stmt=$conn->prepare("SELECT * FROM users WHERE face_token=?");
            $stmt->bind_param("s",$matchedToken);
            $stmt->execute();
            $userData=$stmt->get_result()->fetch_assoc();
            $stmt->close();

            if($userData){
                $userId=$userData['id'];
                $today=date("Y-m-d");
                $log=$conn->query("SELECT * FROM attendance_logs WHERE user_id=$userId AND DATE(login_time)='$today' ORDER BY id DESC LIMIT 1")->fetch_assoc();
                if(!$log || ($log && $log['logout_time']!==NULL)){
                    $conn->query("INSERT INTO attendance_logs(user_id,login_time) VALUES($userId,NOW())");
                    $attMessage="<div class='alert alert-success'>✅ ".htmlspecialchars($userData['name'])." logged in at ".date("H:i:s")."</div>";
                }else{
                    $conn->query("UPDATE attendance_logs SET logout_time=NOW() WHERE id=".$log['id']);
                    $attMessage="<div class='alert alert-warning'>✅ ".htmlspecialchars($userData['name'])." logged out at ".date("H:i:s")."</div>";
                }
            }
        }else $attMessage="<div class='alert alert-danger'>❌ No confident match.</div>";
    }
}

// ------------- Fetch Users & Logs -------------
$users=$conn->query("SELECT * FROM users ORDER BY id DESC");
$logs=$conn->query("SELECT l.*, u.name FROM attendance_logs l JOIN users u ON u.id=l.user_id ORDER BY l.id DESC LIMIT 50");

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Face Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
video{width:100%;max-width:400px;}
canvas{display:none;}
</style>
</head>
<body class="bg-light">
<div class="container mt-5">
<div class="card shadow-lg mb-4">
<div class="card-header bg-primary text-white text-center"><h4>Face Management Dashboard</h4></div>
<div class="card-body">
<?php
if(!empty($deleteMessage)) echo $deleteMessage;
if(!empty($updateMessage)) echo $updateMessage;
if(!empty($regMessage)) echo $regMessage;
?>
<ul class="nav nav-tabs mb-3" id="tabMenu" role="tablist">
<li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#camera">Camera Recognition</button></li>
<li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#recognize">File Recognition</button></li>
<li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#register">Register User</button></li>
<li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#attendance">Attendance</button></li>
<li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#users">Registered Users</button></li>
</ul>
<div class="tab-content">

<!-- Camera Recognition -->
<div class="tab-pane fade show active" id="camera">
<h5>Camera Recognition</h5>
<div id="cameraResult"><?php if(!empty($recMessage)) echo $recMessage; ?></div>
<video id="video" autoplay></video>
<button id="snapBtn" class="btn btn-primary mt-2">Capture & Recognize</button>
<canvas id="canvas"></canvas>
</div>

<!-- File Recognition -->
<div class="tab-pane fade" id="recognize">
<?php if(!empty($recMessage)) echo $recMessage; ?>
<form method="POST" enctype="multipart/form-data">
<div class="mb-3"><label class="form-label">Upload Image</label><input type="file" name="image" class="form-control" accept="image/*" required></div>
<button type="submit" name="recognize" class="btn btn-primary">Recognize</button>
</form>
</div>

<!-- Register User -->
<div class="tab-pane fade" id="register">
<?php if(!empty($regMessage)) echo $regMessage; ?>
<form method="POST" enctype="multipart/form-data">
<div class="mb-3"><label class="form-label">Full Name</label><input type="text" name="fullname" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Address</label><input type="text" name="address" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Contact</label><input type="text" name="contact" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Course</label><input type="text" name="course" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Face Image</label><input type="file" name="face" class="form-control" accept="image/*" required></div>
<button type="submit" name="register" class="btn btn-success">Register</button>
</form>
</div>

<!-- Attendance -->
<div class="tab-pane fade" id="attendance">
<h5>Attendance</h5>
<?php if(!empty($attMessage)) echo $attMessage; ?>
<form method="POST" enctype="multipart/form-data">
<div class="mb-3"><label class="form-label">Upload Image for Attendance</label><input type="file" name="attendance_image" class="form-control" accept="image/*" required></div>
<button type="submit" name="attendance_file" class="btn btn-primary">Submit Attendance (File)</button>
</form>

<h6 class="mt-4">Live Camera Attendance</h6>
<video id="attendanceVideo" autoplay></video>
<button id="attendanceSnapBtn" class="btn btn-success mt-2">Capture & Submit</button>
<canvas id="attendanceCanvas"></canvas>

<h6 class="mt-4">Recent Attendance Logs</h6>
<div class="table-responsive">
<table class="table table-bordered table-striped">
<thead class="table-dark"><tr><th>Name</th><th>Login Time</th><th>Logout Time</th></tr></thead>
<tbody>
<?php if($logs && $logs->num_rows>0): while($row=$logs->fetch_assoc()): ?>
<tr><td><?= htmlspecialchars($row['name']) ?></td><td><?= $row['login_time'] ?></td><td><?= $row['logout_time'] ?? "-" ?></td></tr>
<?php endwhile; else: ?>
<tr><td colspan="3" class="text-center">No attendance logs yet.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>

<!-- Registered Users -->
<div class="tab-pane fade" id="users">
<h5>Registered Users</h5>
<div class="table-responsive">
<table class="table table-bordered table-striped align-middle">
<thead class="table-dark"><tr>
<th>ID</th><th>Name</th><th>Address</th><th>Contact</th><th>Course</th><th>Finger ID</th><th>Face</th><th>Face Token</th><th>Action</th>
</tr></thead>
<tbody>
<?php if($users && $users->num_rows>0): while($row=$users->fetch_assoc()): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['name']) ?></td>
<td><?= htmlspecialchars($row['address']) ?></td>
<td><?= htmlspecialchars($row['contact_number']) ?></td>
<td><?= htmlspecialchars($row['course']) ?></td>
<td><?= htmlspecialchars($row['finger_id']) ?></td>
<td><img src="<?= htmlspecialchars($row['face_image']) ?>" width="60" class="img-thumbnail"></td>
<td><?= htmlspecialchars($row['face_token']) ?></td>
<td>
<!-- Edit button (opens modal) -->
<button class="btn btn-sm btn-warning editBtn"
    data-id="<?= $row['id'] ?>"
    data-name="<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>"
    data-address="<?= htmlspecialchars($row['address'], ENT_QUOTES) ?>"
    data-contact="<?= htmlspecialchars($row['contact_number'], ENT_QUOTES) ?>"
    data-course="<?= htmlspecialchars($row['course'], ENT_QUOTES) ?>"
    data-finger="<?= htmlspecialchars($row['finger_id'], ENT_QUOTES) ?>"
    data-face="<?= htmlspecialchars($row['face_image'], ENT_QUOTES) ?>"
> Edit </button>

<!-- Delete -->
<a href="?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</a>
</td>
</tr>
<?php endwhile; else: ?>
<tr><td colspan="9" class="text-center">No users registered yet.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>

</div>
</div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" enctype="multipart/form-data" id="editForm">
        <div class="modal-header bg-warning">
          <h5 class="modal-title text-dark">Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="user_id" id="edit_user_id">
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="fullname" id="edit_fullname" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Address</label>
            <input type="text" name="address" id="edit_address" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Contact</label>
            <input type="text" name="contact" id="edit_contact" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Course</label>
            <input type="text" name="course" id="edit_course" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Finger ID</label>
            <input type="text" name="finger_id" id="edit_finger" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Replace Face Image (optional)</label>
            <input type="file" name="edit_face" id="edit_face" class="form-control" accept="image/*">
            <div id="currentFacePreview" class="mt-2"></div>
            <small class="text-muted">If you upload a new face, it will be added to FaceSet and the old face will be removed.</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="update_user" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Camera Recognition
const video=document.getElementById('video');
const canvas=document.getElementById('canvas');
const snapBtn=document.getElementById('snapBtn');
const cameraResult=document.getElementById('cameraResult');
if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
    navigator.mediaDevices.getUserMedia({video:true}).then(stream=>video.srcObject=stream).catch(err=>{/*console.error(err)*/});
}

if(snapBtn){
  snapBtn.addEventListener('click',()=>{
      const ctx=canvas.getContext('2d');
      canvas.width=video.videoWidth; canvas.height=video.videoHeight;
      ctx.drawImage(video,0,0,canvas.width,canvas.height);
      canvas.toBlob(blob=>{
          const formData=new FormData();
          formData.append('image',blob,'camera.jpg');
          formData.append('recognize','1');
          fetch('',{method:'POST',body:formData}).then(r=>r.text()).then(html=>cameraResult.innerHTML=html);
      },'image/jpeg',0.95);
  });
}

// Attendance Camera
const attVideo=document.getElementById('attendanceVideo');
const attCanvas=document.getElementById('attendanceCanvas');
const attSnapBtn=document.getElementById('attendanceSnapBtn');
if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
    navigator.mediaDevices.getUserMedia({video:true}).then(stream=>attVideo.srcObject=stream).catch(err=>{/*console.error(err)*/});
}
if(attSnapBtn){
  attSnapBtn.addEventListener('click',()=>{
      const ctx=attCanvas.getContext('2d');
      attCanvas.width=attVideo.videoWidth; attCanvas.height=attVideo.videoHeight;
      ctx.drawImage(attVideo,0,0,attCanvas.width,attCanvas.height);
      attCanvas.toBlob(blob=>{
          const formData=new FormData();
          formData.append('attendance_image',blob,'attendance.jpg');
          formData.append('attendance_camera','1');
          fetch('',{method:'POST',body:formData}).then(r=>r.text()).then(html=>document.getElementById('attendance').innerHTML=html);
      },'image/jpeg',0.95);
  });
}

// Edit modal populate
document.querySelectorAll('.editBtn').forEach(btn => {
  btn.addEventListener('click', (e) => {
    const id = btn.getAttribute('data-id');
    const name = btn.getAttribute('data-name');
    const address = btn.getAttribute('data-address');
    const contact = btn.getAttribute('data-contact');
    const course = btn.getAttribute('data-course');
    const finger = btn.getAttribute('data-finger');
    const face = btn.getAttribute('data-face');

    document.getElementById('edit_user_id').value = id;
    document.getElementById('edit_fullname').value = name;
    document.getElementById('edit_address').value = address;
    document.getElementById('edit_contact').value = contact;
    document.getElementById('edit_course').value = course;
    document.getElementById('edit_finger').value = finger || '';

    const preview = document.getElementById('currentFacePreview');
    preview.innerHTML = face ? '<img src="'+face+'" class="img-thumbnail" width="120">' : '';

    var editModal = new bootstrap.Modal(document.getElementById('editModal'));
    editModal.show();
  });
});
</script>
</body>
</html>
