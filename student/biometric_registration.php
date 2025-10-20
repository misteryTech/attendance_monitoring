<?php
  include 'layout/header.php';
    include 'layout/navigation.php';
    include 'layout/connection.php';
?>




  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Profile</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Users</li>
          <li class="breadcrumb-item active">Profile</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section profile">
      <div class="row">
        <div class="col-xl-4">

          <div class="card">
            <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

                  <?php

                    // Assuming $conn is your database connection and $user_id is defined
                    $query = mysqli_query($conn, "SELECT facial_recognition FROM biometrics WHERE student_id = '$user_id'");
                    $row = mysqli_fetch_assoc($query);

                    // Check if image exists in the database
                    if ($row && !empty($row['facial_recognition'])) {
                        $imageSrc = $row['facial_recognition']; // Path stored in DB
                    } else {
                        $imageSrc = 'user.png'; // Path to default image

                    }
                    ?>

              <img src="<?php echo htmlspecialchars($imageSrc); ?>" alt="Profile" class="rounded-circle">
              <h2><?= $fullname; ?></h2>
              <h3>Student Id: <?= $student_id; ?></h3>
              <div class=" mt-2">
                    <?php
                      if ($row === !empty($row['facial_recognition'])){
                              echo '<p class=" h3 text-success">Validated</p>';

                      }else{
                           echo '<p class=" h3 text-danger">Not Validated</p>';
                      }
                    ?>
            </div>
            </div>
          </div>

        </div>

        <div class="col-xl-8">

          <div class="card">
            <div class="card-body pt-3">
              <!-- Bordered Tabs -->
              <ul class="nav nav-tabs nav-tabs-bordered">

                <li class="nav-item">
                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Biometrics</button>
                </li>


              </ul>
              <div class="tab-content pt-2">
                  <div class="tab-pane fade show active profile-overview" id="profile-overview">
                    <h5 class="card-title">About</h5>
                    <p class="small fst-italic">
                      Sunt est soluta temporibus accusantium neque nam maiores cumque temporibus. Tempora libero non est unde veniam est qui dolor. Ut sunt iure rerum quae quisquam autem eveniet perspiciatis odit. Fuga sequi sed ea saepe at unde.
                    </p>

                    <?php
                    // Fetch user details
                    $query_student = mysqli_query($conn, "SELECT * FROM user WHERE id='$user_id'");
                    $row_student = mysqli_fetch_assoc($query_student);

                    // Fetch biometrics details
                    $query_biometrics = mysqli_query($conn, "SELECT pincode, year, section FROM biometrics WHERE student_id = '{$row_student['id_number']}'");
                    $row_biometrics = mysqli_fetch_assoc($query_biometrics);

                    // Set biometric values or fallback to "Required"
                    $pincode = $row_biometrics['pincode'] ?? '<span class="text-danger">Required</span>';
                    $year = $row_biometrics['year'] ?? '<span class="text-danger">Required</span>';
                    $section = $row_biometrics['section'] ?? '<span class="text-danger">Required</span>';
                    ?>

                    <h5 class="card-title">Profile Details</h5>

                    <div class="row">
                      <div class="col-lg-3 col-md-4 label">Full Name</div>
                      <div class="col-lg-9 col-md-8"><?= $row_student['firstname'] . ' ' . $row_student['lastname']; ?></div>
                    </div>

                    <div class="row">
                      <div class="col-lg-3 col-md-4 label">Student ID</div>
                      <div class="col-lg-9 col-md-8"><?= $row_student['id_number']; ?></div>
                    </div>

                    <div class="row">
                      <div class="col-lg-3 col-md-4 label">Role</div>
                      <div class="col-lg-9 col-md-8"><?= $row_student['role']; ?></div>
                    </div>

                    <div class="row">
                      <div class="col-lg-3 col-md-4 label">Username</div>
                      <div class="col-lg-9 col-md-8"><?= $row_student['username']; ?></div>
                    </div>

                    <div class="row">
                      <div class="col-lg-3 col-md-4 label">Status</div>
                      <div class="col-lg-9 col-md-8"><p class="h3 text-primary"><?= $row_student['verification']; ?></p></div>
                    </div>

                    <div class="row">
                      <div class="col-lg-3 col-md-4 label">Biometric Pin Code</div>
                    <div class="col-lg-9 col-md-8">
                      <?= !empty($pincode) && $pincode !== '<span class="text-danger">*</span>' ? str_repeat('*', strlen($pincode)) : $pincode; ?>
                    </div>
                    </div>

                    <div class="row">
                      <div class="col-lg-3 col-md-4 label">Year</div>
                      <div class="col-lg-9 col-md-8"><?= $year; ?></div>
                    </div>

                    <div class="row">
                      <div class="col-lg-3 col-md-4 label">Section</div>
                      <div class="col-lg-9 col-md-8"><?= $section; ?></div>
                    </div>
                  </div>

                <div class="tab-pane fade profile-edit pt-3" id="profile-edit">

                  <!-- Profile Edit Form -->
                  <form id="profileForm">
                    <div class="row mb-3">

                      <input type="text" name="student_id" value="<?= $student_id; ?>" hidden>
                      <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Image Verification</label>
                      <div class="col-md-8 col-lg-9">
                                        <?php
                              // Check if facial recognition image exists
                              if ($row && !empty($row['facial_recognition'])) {
                                  $imageSrc = $row['facial_recognition']; // Path stored in DB
                                  $showScheduleButton = false;
                              } else {
                                  $imageSrc = 'user.png'; // Default image
                                  $showScheduleButton = true;
                              }
                              ?>


                              <!-- Display Profile Image -->
                              <img src="<?php echo htmlspecialchars($imageSrc); ?>" alt="Profile" class="rounded-circle mb-3">

                              <!-- Show Schedule Picture Button if image is missing -->
                              <?php if ($showScheduleButton): ?>
                                <a href="schedule_picture.php" class="btn btn-primary">Contact Admin</a>
                              <?php endif; ?>
                      </div>

                    </div>



                    <div class="row mb-3">
                      <label for="pincode" class="col-md-4 col-lg-3 col-form-label">Finger Print Registration</label>

                                                  <?php
                            // Fetch fingerprint data from the biometrics table
                            $query = mysqli_query($conn, "SELECT finger_print FROM biometrics WHERE student_id = '$user_id'");
                            $row = mysqli_fetch_assoc($query);

                            // Determine fingerprint status
                            if ($row && !empty($row['finger_print'])) {
                                $fingerprintStatus = $row['finger_print']; // Display actual fingerprint status or ID
                                $showRegisterButton = false;
                            } else {
                                $fingerprintStatus = 'Not Yet Registered'; // Default status if no data
                                $showRegisterButton = true;
                            }
                            ?>

                            <div class="col-md-8 col-lg-3">
                              <h6>Fingerprint Status</h6>

                              <p class="h6 text-danger"><?php echo htmlspecialchars($fingerprintStatus); ?></p>
                              <?php if ($showRegisterButton): ?>
                                <a href="biometric_registration.php" class="btn btn-warning btn-sm">Go to Biometrics Registration</a>
                              <?php endif; ?>
                            </div>


                    </div>
                        <?php
                        // Fetch biometrics data for the logged-in user
                        $query = mysqli_query($conn, "SELECT pincode, year, section FROM biometrics WHERE student_id = '$user_id'");
                        $row = mysqli_fetch_assoc($query);

                        // Set default values
                        $pincode = $row['pincode'] ?? '';
                        $year = $row['year'] ?? '';
                        $section = $row['section'] ?? '';
                        ?>

                        <div class="row mb-3">
                          <label for="pincode" class="col-md-4 col-lg-3 col-form-label">Pin Code</label>
                          <div class="col-md-8 col-lg-3">
                                              <?php
                          $maskedPincode = !empty($pincode) ? str_repeat('*', strlen($pincode)) : '';
                          ?>
                          <input name="pincode" type="text" class="form-control" id="pincode" value="<?php echo $maskedPincode; ?>">

                          </div>
                        </div>

                        <div class="row mb-3">
                          <label for="year" class="col-md-4 col-lg-3 col-form-label">Year</label>
                          <div class="col-md-8 col-lg-3">
                            <select class="form-select" name="year" id="year">
                              <option value="">Select Year</option>
                              <option value="1st Year" <?php if ($year == '1st Year') echo 'selected'; ?>>1st Year</option>
                              <option value="2nd Year" <?php if ($year == '2nd Year') echo 'selected'; ?>>2nd Year</option>
                              <option value="3rd Year" <?php if ($year == '3rd Year') echo 'selected'; ?>>3rd Year</option>
                              <option value="4th Year" <?php if ($year == '4th Year') echo 'selected'; ?>>4th Year</option>
                            </select>
                          </div>
                        </div>

                        <div class="row mb-3">
                          <label for="section" class="col-md-4 col-lg-3 col-form-label">Section</label>
                          <div class="col-md-8 col-lg-3">
                            <input name="section" type="text" class="form-control" id="section" value="<?php echo htmlspecialchars($section); ?>" placeholder="Enter Section">
                          </div>
                        </div>

                    <div class="text-center">
                      <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                  </form><!-- End Profile Edit Form -->

                </div>



                </div>

              </div><!-- End Bordered Tabs -->

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
document.getElementById("profileForm").addEventListener("submit", function(e) {
  e.preventDefault();

  const formData = new FormData(this);

  fetch("ajax/update_biometrics.php", {
    method: "POST",
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert("Biometrics saved successfully!");
    } else {
      alert("Save failed: " + data.message);
    }
  })
  .catch(error => {
    console.error("Error:", error);
    alert("An error occurred while saving.");
  });
});
</script>
