<?php
    include 'layout/header.php';
    include 'layout/navigation.php';
    include 'layout/connection.php';
?>


<?php
$student_id = null;
$request_id = null;

if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];
    $request_id = intval($student_id);
}
?>


<main id="main" class="main">

  <div class="pagetitle">
    <h1>Schedule Form</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">Schedule</a></li>
        <li class="breadcrumb-item active">Form</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row">
      <!-- Form Column -->
      <div class="col-lg-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Schedule Profile Identification</h5>

<?php
$userData = null;
$facial_recognition = '';

if (!empty($request_id)) {
    // Fetch user details
    $stmt = $conn->prepare("SELECT id_number, firstname, lastname, role, username FROM user WHERE id_number = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
    $stmt->close();

    // Fetch facial_recognition image from biometrics table
    $student_id = $userData['id_number'];
    $stmt = $conn->prepare("SELECT facial_recognition FROM biometrics WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $stmt->bind_result($facial_recognition);
    $stmt->fetch();
    $stmt->close();
}
?>
<form id="scheduleForm" method="POST" action="process/insert_profile_image.php" enctype="multipart/form-data">
  <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_id ?? ''); ?>">

  <?php if ($userData): ?>
    <div class="mb-3">
      <label class="form-label">Student Profile Picture</label>

      <?php if (!empty($facial_recognition) && file_exists('' . $facial_recognition)): ?>
        <!-- ✅ If profile picture exists -->
        <div class="mb-2">
          <img src="<?= htmlspecialchars($facial_recognition); ?>"
               alt="Profile Picture"
               class="img-thumbnail"
               style="width: 150px; height: 150px; object-fit: cover;">
        </div>
        <!-- <small class="text-muted d-block mb-2">Current Profile Picture</small>
        <label class="form-label">Change Profile Picture</label>
        <input type="file" id="profileImageInput" name="profile_image" accept="image/*" class="form-control"> -->

      <?php else: ?>
        <!-- 🚀 If no profile picture yet -->
        <input type="file" id="profileImageInput" name="profile_image" accept="image/*" class="form-control">
        <small class="text-muted">No profile picture uploaded yet.</small>
      <?php endif; ?>

      <!-- Live preview when selecting new file -->
      <div id="imagePreview" class="mt-2"></div>
    </div>

    <div class="mb-3">
      <label class="form-label">Student Name</label>
      <input type="text" class="form-control"
             value="<?= htmlspecialchars($userData['firstname'] . ' ' . $userData['lastname']); ?>" disabled>
    </div>

    <div class="mb-3">
      <label class="form-label">Username</label>
      <input type="text" class="form-control"
             value="<?= htmlspecialchars($userData['username']); ?>" disabled>
    </div>

    <div class="mb-3">
      <label class="form-label">Role</label>
      <input type="text" class="form-control"
             value="<?= htmlspecialchars($userData['role']); ?>" disabled>
    </div>

    <div class="d-grid">
      <button type="submit" class="btn btn-success">Update Profile Photo</button>
    </div>
  <?php else: ?>
    <div class="alert alert-warning">⚠️ No user found for ID <?= htmlspecialchars($request_id); ?></div>
  <?php endif; ?>
</form>
          </div>
        </div>
      </div>

      <!-- Table Column -->
      <div class="col-lg-8">
        <div class="card">
          <div class="card-body">

                              <h5 class="card-title">Camera Preview</h5>
                                <button id="openCameraBtn" class="btn btn-primary">Open Camera</button>
<div id="cameraResult"></div>
<video id="video" autoplay playsinline width="100%" style="max-height:300px;"></video>
<div class="d-flex justify-content-center mt-2">
  <button id="snapBtn" class="btn btn-success" style="display: none;">Capture</button>
</div>
<canvas id="canvas" style="display:none;"></canvas>

          </div>
        </div>
      </div>
    </div>
  </section>

</main><!-- End #main -->
<?php
include 'layout/footer.php';
?>
<script>
// Load table on page loa
document.addEventListener('DOMContentLoaded', () => {
  const openCameraBtn = document.getElementById('openCameraBtn');
  const video = document.getElementById('video');
  const canvas = document.getElementById('canvas');
  const snapBtn = document.getElementById('snapBtn');
  const cameraResult = document.getElementById('cameraResult');
  const profileImageInput = document.getElementById('profileImageInput');
const imagePreview = document.getElementById('imagePreview');



  let streamStarted = false;

  openCameraBtn.addEventListener('click', () => {
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
      navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
          video.srcObject = stream;
          streamStarted = true;
          snapBtn.style.display = 'inline-block'; // Show the capture button
        })
        .catch(err => {
          cameraResult.innerHTML = `<div class="alert alert-danger">🚫 Camera access denied.</div>`;
        });
    } else {
      cameraResult.innerHTML = `<div class="alert alert-warning">⚠️ Camera not supported in this browser.</div>`;
    }
  });


  snapBtn.addEventListener('click', () => {
  if (!streamStarted) return;

  const ctx = canvas.getContext('2d');
  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

  canvas.toBlob(blob => {
    // Show preview
    const img = document.createElement('img');
    img.src = URL.createObjectURL(blob);
    img.className = 'img-fluid rounded border';
    img.style.maxHeight = '200px';
    imagePreview.innerHTML = '';
    imagePreview.appendChild(img);

    // Convert blob to File and assign to hidden input
    const file = new File([blob], 'profile.jpg', { type: 'image/jpeg' });
    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(file);
    profileImageInput.files = dataTransfer.files;

    // Optional: also send to server immediately
    /*
    const formData = new FormData();
    formData.append('image', file);
    fetch('recognize.php', { method: 'POST', body: formData })
      .then(response => response.text())
      .then(html => {
        cameraResult.innerHTML = html;
      })
      .catch(err => {
        cameraResult.innerHTML = `<div class="alert alert-danger">❌ Recognition failed.</div>`;
      });
    */
  }, 'image/jpeg', 0.95);
});

snapBtn.addEventListener('click', () => {
  if (!streamStarted) return;

  const ctx = canvas.getContext('2d');

  // ✅ Force a good size (Face++ prefers at least 480x480)
  canvas.width = 640;
  canvas.height = 480;
  ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

  canvas.toBlob(blob => {
    const file = new File([blob], 'profile.jpg', { type: 'image/jpeg' });
    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(file);
    profileImageInput.files = dataTransfer.files;

    // ✅ show preview
    const img = document.createElement('img');
    img.src = URL.createObjectURL(blob);
    img.className = 'img-fluid rounded border';
    img.style.maxHeight = '200px';
    imagePreview.innerHTML = '';
    imagePreview.appendChild(img);
  }, 'image/jpeg', 1.0); // high quality, no compression
});
});

document.getElementById('profileImageInput').addEventListener('change', function(event) {
  const preview = document.getElementById('imagePreview');
  preview.innerHTML = '';
  const file = event.target.files[0];
  if (file) {
    const img = document.createElement('img');
    img.src = URL.createObjectURL(file);
    img.style.width = '150px';
    img.style.height = '150px';
    img.style.objectFit = 'cover';
    img.classList.add('img-thumbnail');
    preview.appendChild(img);
  }
});
</script>
