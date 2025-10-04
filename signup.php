<?php
include 'layout/header.php';
?>
<main>
  <div class="container">

    <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-5 col-md-7 d-flex flex-column align-items-center justify-content-center">

            <div class="d-flex justify-content-center py-4">
              <a href="index.html" class="logo d-flex align-items-center w-auto">

                <span class="d-none d-lg-block"></span>
              </a>
            </div><!-- End Logo -->

            <div class="card mb-3">
              <div class="card-body">

                <div class="pt-4 pb-2">
                  <h5 class="card-title text-center pb-0 fs-4">Create an Account</h5>
                  <p class="text-center small">Enter your personal details to create an account</p>
                </div>

                <!-- Tabs for User Type -->
                <ul class="nav nav-pills mb-3 justify-content-center" id="pills-tab" role="tablist">
                  <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="student-tab" data-bs-toggle="pill" data-bs-target="#student" type="button" role="tab">Student</button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="parent-tab" data-bs-toggle="pill" data-bs-target="#parent" type="button" role="tab">Parent</button>
                  </li>
                </ul>

                <!-- Form Starts -->
                <form class="row g-3 needs-validation" novalidate action="process/signup_process.php" method="POST">
                            <!-- Show notification here -->
                <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                    <div class="alert alert-success">Successfully Registered! Try to <a href="login.php">Login</a></div>
                <?php elseif (isset($_GET['status']) && $_GET['status'] == 'error'): ?>
                    <div class="alert alert-danger">Something went wrong. Please try again.</div>
                <?php endif; ?>

                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="student" role="tabpanel">
                      <!-- Common Fields for Student -->
                      <?php include 'form-fields_student.php'; ?>
                    </div>

                    <div class="tab-pane fade" id="parent" role="tabpanel">
                      <!-- Common Fields for Parent -->
                      <?php include 'form-fields_parents.php'; ?>
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="form-check">
                      <input class="form-check-input" name="terms" type="checkbox" value="" id="acceptTerms" required>
                      <label class="form-check-label" for="acceptTerms">I agree and accept the <a href="#">terms and conditions</a></label>
                      <div class="invalid-feedback">You must agree before submitting.</div>
                    </div>
                  </div>
                  <div class="col-12">
                    <button  class="btn btn-primary w-100" type="submit">Create Account</button>
                  </div>
                  <div class="col-12">
                    <p class="small mb-0">Already have an account? <a href="login.php">Log in</a></p>
                  </div>
                </form>
                <!-- Form Ends -->

              </div>
            </div>

            <div class="credits">
               Developed by <a href="index.php">STUDENTS</a>
            </div>

          </div>
        </div>
      </div>
    </section>

  </div>
</main>
<?php include 'layout/footer.php'; ?>
