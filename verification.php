<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Verification</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      padding: 40px;
      background-color: #f8f9fa;
    }
    .verification-step {
      margin-bottom: 30px;
    }
    .hidden {
      display: none;
    }
    video {
      width: 100%;
      max-width: 320px;
      border-radius: 8px;
      border: 1px solid #ccc;
      margin-bottom: 10px;
    }
    #verified-image {
      width: 100%;
      max-width: 320px;
      border-radius: 8px;
      border: 3px solid #28a745;
      margin-top: 10px;
      display: none;
    }
  </style>
</head>
<body>

<div class="container">
  <h1 class="mb-4 text-center">Student Verification</h1>

  <div id="verification-steps">

    <!-- Facial Verification -->
    <div class="card verification-step" id="facial-step">
      <div class="card-body text-center">
        <h5 class="card-title">Step 1: Facial Verification</h5>
        <p class="card-text">Allow access to your camera and verify your face.</p>
        <video id="video" autoplay muted></video>
        <button class="btn btn-primary mb-2" onclick="captureAndVerifyFace()">Verify Face</button>
        <p id="facial-status" class="mt-2"></p>
        <img id="verified-image" alt="Verified Image">
      </div>
    </div>

    <!-- PIN Verification -->
    <div class="card verification-step hidden" id="pin-step">
      <div class="card-body">
        <h5 class="card-title">Step 2: Enter PIN</h5>
        <input type="password" id="pin-input" class="form-control mb-2" placeholder="Enter 4-digit PIN" maxlength="4">
        <button class="btn btn-primary" onclick="verifyPIN()">Submit PIN</button>
        <p id="pin-status" class="mt-2 text-danger"></p>
      </div>
    </div>

    <!-- Biometric Verification -->
    <div class="card verification-step hidden" id="biometric-step">
      <div class="card-body text-center">
        <h5 class="card-title">Step 3: Biometric Verification</h5>
        <p class="card-text">Click below to verify your fingerprint (simulation).</p>
        <button class="btn btn-primary" onclick="verifyBiometric()">Verify Biometric</button>
        <p id="biometric-status" class="mt-2 text-success"></p>
      </div>
    </div>

  </div>

  <!-- Verification Success -->
  <div class="card hidden" id="verification-success">
    <div class="card-body text-center">
      <h2 class="card-title text-success">Verification Successful</h2>
      <p class="card-text">Student ID: <strong id="student-id-display"></strong></p>
      <p class="card-text">You have been successfully verified.</p>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Get student ID and face token from URL
  const urlParams = new URLSearchParams(window.location.search);
  const studentId = urlParams.get('student_id');
  const facial_token = urlParams.get('facial_token');
  const video = document.getElementById('video');
  const verifiedImage = document.getElementById('verified-image');

  // Open camera
  async function startCamera() {
    try {
      const stream = await navigator.mediaDevices.getUserMedia({ video: true });
      video.srcObject = stream;
    } catch (err) {
      alert("Camera access denied or not available.");
      console.error(err);
    }
  }

  startCamera();

  async function captureAndVerifyFace() {
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    const imageData = canvas.toDataURL('image/jpeg');

    try {
      const response = await fetch('verify_face.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          image: imageData,
          student_id: studentId,
          facial_token: facial_token
        })
      });

      const result = await response.json();
      const statusEl = document.getElementById('facial-status');
      statusEl.classList.remove('text-danger', 'text-success');

      if (result.success) {
        statusEl.textContent = `Face verified! (Confidence: ${result.confidence || 'N/A'})`;
        statusEl.classList.add('text-success');

        verifiedImage.src = imageData;
        verifiedImage.style.display = 'block';

        setTimeout(() => {
          document.getElementById('facial-step').classList.add('hidden');
          document.getElementById('pin-step').classList.remove('hidden');
        }, 1500);
      } else {
        statusEl.textContent = result.message || "Face does not match. Try again.";
        statusEl.classList.add('text-danger');
        verifiedImage.style.display = 'none';
      }

    } catch (err) {
      console.error(err);
      const statusEl = document.getElementById('facial-status');
      statusEl.textContent = "Verification failed. Try again.";
      statusEl.classList.add('text-danger');
      verifiedImage.style.display = 'none';
    }
  }

  // ✅ PIN verification with database check
  async function verifyPIN() {
    const pin = document.getElementById('pin-input').value.trim();
    const statusEl = document.getElementById('pin-status');
    statusEl.classList.remove('text-success', 'text-danger');
    statusEl.textContent = "Checking PIN...";

    if (!pin) {
      statusEl.textContent = "Please enter your PIN.";
      statusEl.classList.add('text-danger');
      return;
    }

    try {
      const response = await fetch('verify_pin.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          student_id: studentId,
          pin: pin
        })
      });

      const result = await response.json();
      statusEl.classList.remove('text-success', 'text-danger');

      if (result.success) {
        statusEl.textContent = "PIN verified!";
        statusEl.classList.add('text-success');
        document.getElementById('pin-step').classList.add('hidden');
        document.getElementById('biometric-step').classList.remove('hidden');
      } else {
        statusEl.textContent = result.message;
        statusEl.classList.add('text-danger');
      }
    } catch (error) {
      console.error(error);
      statusEl.textContent = "Error verifying PIN.";
      statusEl.classList.add('text-danger');
    }
  }

  function verifyBiometric() {
    const statusEl = document.getElementById('biometric-status');
    statusEl.textContent = "Biometric verified!";
    statusEl.classList.add('text-success');

    document.getElementById('biometric-step').classList.add('hidden');
    document.getElementById('student-id-display').textContent = studentId || "Unknown";
    document.getElementById('verification-success').classList.remove('hidden');
  }
</script>

</body>
</html>
