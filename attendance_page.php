<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Attendance Monitoring System</title>
  <meta content="Attendance monitoring with real-time tracking and analytics" name="description">
  <meta content="attendance, monitoring, system" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|Nunito:300,400,600,700|Poppins:300,400,500,600,700"
    rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

  <!-- Custom Styles -->
  <style>
    #hero {
      min-height: 100vh;
      position: relative;
      background: url("photo/bg.jpg") no-repeat center center;
      background-size: cover;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      overflow: hidden;
    }

    #hero::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, 0.65);
      backdrop-filter: blur(2px);
      z-index: 1;
    }

    #hero .container {
      position: relative;
      z-index: 2;
    }

    #hero h1 {
      color: white;
      font-size: 3rem;
      font-weight: bold;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
      margin-bottom: 20px;
    }

    #hero p {
      color: white;
      font-size: 1.25rem;
      margin-bottom: 40px;
    }

    #attendanceForm input {
      max-width: 400px;
      margin: 0 auto;
      background: rgba(255, 255, 255, 0.85);
    }

    #status {
      margin-top: 10px;
      color: #ffdddd;
      font-weight: bold;
      font-size: 1.1rem;
    }
  </style>
</head>

<body>
  <section id="hero">
    <div class="container text-center">
      <h1 class="fw-bold display-4 mb-3">AURA</h1>
      <p class="lead mb-5">Tap your RFID/QR card below to log attendance.</p>
<?php
include 'layout/connection.php'; // adjust path if needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $card_uid = $_POST['card_uid'] ?? '';

    if (empty($card_uid)) {
        header("Location: attendance_page.php?error=1");
        exit;
    }

    // Check if user exists
    $stmt = $conn->prepare("SELECT facial_token, student_id FROM biometrics WHERE student_id = ?");
    $stmt->bind_param("s", $card_uid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Not registered
        header("Location: attendance_page.php?error=1");
    } else {
        // Registered — redirect to verification page with student ID and face token
        $user = $result->fetch_assoc();
        $student_id = $user['student_id'];
        $facial_token = $user['facial_token'];

        // URL encode both values
        $redirect_url = "verification.php?student_id=" . urlencode($student_id) . "&facial_token=" . urlencode($facial_token);
        header("Location: $redirect_url");
    }

    $stmt->close();
    $conn->close();
}
?>

      <!-- Tap Card Input -->
      <form id="attendanceForm" method="POST">
        <input type="text"
               name="card_uid"
               id="cardInput"
               placeholder="Tap your card here..."
               class="form-control form-control-lg text-center"
               autofocus
               autocomplete="off"
               required>
      </form>

      <!-- Status Message -->
      <div id="status">
        <?php
        // Show message if student not registered
        if(isset($_GET['error']) && $_GET['error'] == 1){
            echo "You are not currently verified.";
        }
        ?>
      </div>
    </div>
  </section>

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <script>
    // Auto-submit when card UID is scanned
    const cardInput = document.getElementById('cardInput');
    const form = document.getElementById('attendanceForm');
    const status = document.getElementById('status');

    cardInput.addEventListener('input', () => {
      if(cardInput.value.trim().length > 0){
        form.submit();
      }
    });

    // Keep input focused
    setInterval(() => cardInput.focus(), 500);
  </script>
</body>
</html>
