<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Attendance Monitoring System</title>
  <meta content="Attendance monitoring with real-time tracking and analytics" name="description">
  <meta content="attendance, monitoring, system, login, signup" name="keywords">

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

/* Dark semi-transparent overlay with optional blur */
#hero::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.65); /* Darker overlay */
  backdrop-filter: blur(2px); /* Optional: subtle blur */
  z-index: 1;
}


    /* Title Styling */
    #hero h1 {
      color: white;
      font-size: 3rem;
      font-weight: bold;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6); /* Adds shadow for better readability */
      margin-bottom: 20px;
    }

    #hero p {
      color: white;
      font-size: 1.25rem;
      margin-bottom: 40px;
    }
    #hero .container {
  position: relative;
  z-index: 2;
}

#hero .container {
  background: rgba(0, 0, 0, 0.4);
  padding: 30px;
  border-radius: 10px;
}

    .hero-btns a {
      margin: 0 10px;
    }

    .hero-btns .btn {
      padding: 12px 30px;
      font-size: 1.1rem;
    }
  </style>
</head>

<body>
  <!-- ======= Hero Section ======= -->
  <section id="hero">
    <div class="container text-white">
      <h1 class="fw-bold display-4 mb-3">Attendance Monitoring System</h1>
      <p class="lead mb-5">Track attendance efficiently with real-time insights and secure dashboards.</p>
      <div class="hero-btns">
        <a href="login.php" class="btn btn-light btn-lg shadow px-4 py-2">
          <i class="bi bi-box-arrow-in-right"></i> Login
        </a>
        <a href="signup.php" class="btn btn-outline-light btn-lg shadow px-4 py-2">
          <i class="bi bi-person-plus"></i> Sign Up
        </a>
      </div>
    </div>
  </section>
  <!-- End Hero -->

  <!-- ======= Features Section ======= -->
  <main id="main">
    <section class="features py-5 bg-light">
      <div class="container">
        <div class="row g-4 text-center">
          <div class="col-lg-4 col-md-6">
            <div class="card shadow-sm border-0 h-100 p-4">
              <div class="icon mb-3 text-primary"><i class="bi bi-people fs-1"></i></div>
              <h5 class="card-title">Easy Attendance</h5>
              <p class="card-text">Quickly log and track attendance with just a few clicks.</p>
            </div>
          </div>
          <div class="col-lg-4 col-md-6">
            <div class="card shadow-sm border-0 h-100 p-4">
              <div class="icon mb-3 text-success"><i class="bi bi-bar-chart-line fs-1"></i></div>
              <h5 class="card-title">Analytics</h5>
              <p class="card-text">View detailed insights and reports in real time.</p>
            </div>
          </div>
          <div class="col-lg-4 col-md-6">
            <div class="card shadow-sm border-0 h-100 p-4">
              <div class="icon mb-3 text-warning"><i class="bi bi-shield-lock fs-1"></i></div>
              <h5 class="card-title">Secure System</h5>
              <p class="card-text">Protected with authentication and encryption for safety.</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  <!-- End Features Section -->

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
