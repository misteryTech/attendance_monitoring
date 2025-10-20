<?php
  include 'layout/header.php';
    include 'layout/navigation.php';
    include 'layout/connection.php';
?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>

<?php
$query = mysqli_query($conn, "SELECT * FROM biometrics WHERE student_id = '$student_id'");
$row = mysqli_fetch_assoc($query);

if (
    !$row || // No record at all
    empty($row['facial_recognition']) ||
    empty($row['finger_print']) ||
    empty($row['pincode']) ||
    empty($row['year']) ||
    empty($row['section'])
) {
    // Block access and show modal
    echo '
    <div class="modal fade" id="biometricModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="biometricModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="biometricModalLabel">Complete Biometric Registration</h5>
          </div>
          <div class="modal-body">
            You must complete all biometric fields before accessing the dashboard.
          </div>
          <div class="modal-footer">
            <a href="layout/logout.php" class="btn btn-danger">Logout</a>
            <a href="biometric_registration.php" class="btn btn-primary">Update Biometrics</a>
          </div>
        </div>
      </div>
    </div>

    <script>
      var biometricModal = new bootstrap.Modal(document.getElementById("biometricModal"));
      biometricModal.show();
    </script>
    ';
}
?>

    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">

        <!-- Left side columns -->
        <div class="col-lg-8">
          <div class="row">


            <!-- Revenue Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card revenue-card">


                <div class="card-body">
                  <h5 class="card-title">Attendance <span>| Total</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-people"></i>
                    </div>
                    <div class="ps-3">
                      <h6>

                      </h6>

                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Revenue Card -->

            <!-- Customers Card -->
            <div class="col-xxl-4 col-xl-12">

              <div class="card info-card customers-card">


                <div class="card-body">
                  <h5 class="card-title">Teachers <span>| Total</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-people"></i>
                    </div>
                    <div class="ps-3">
                      <h6>

                        <?php

                        $select_parent = "SELECT COUNT(*) AS total_parents FROM user WHERE role = 'parents'";
                        $result_parent = $conn->query($select_parent);
                        $data_parent = $result_parent->fetch_assoc();
                          $total_parent  = $data_parent['total_parents'];
                       ?>
                        <?= $total_parent ?>


                      </h6>


                    </div>
                  </div>

                </div>
              </div>

            </div><!-- End Customers Card -->

                   <!-- Customers Card -->
            <div class="col-xxl-4 col-xl-12">

              <div class="card info-card customers-card">

                <div class="card-body">
                  <h5 class="card-title">Absent <span>| Total</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-list"></i>
                    </div>
                    <div class="ps-3">
                      <h6>




                      </h6>

                    </div>
                  </div>

                </div>
              </div>

            </div><!-- End Customers Card -->



            <!-- Recent Sales -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">

                <div class="card-body">
                  <h5 class="card-title">Attendance History <span>| Logs</span></h5>

                  <table class="table table-borderless datatable">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">ID Number</th>
                        <th scope="col">Full Name</th>
                        <th scope="col">Username</th>
                        <th scope="col">Role</th>
                        <th scope="col">Status</th>
                        <th scope="col">Profile</th>
                      </tr>
                    </thead>
                    <tbody>

                    </tbody>
                  </table>

                </div>

              </div>
            </div><!-- End Recent Sales -->

          </div>
        </div><!-- End Left side columns -->

        <!-- Right side columns -->
        <div class="col-lg-4">

          <!-- Recent Activity -->
          <div class="card">
            <div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6>Filter</h6>
                </li>

                <li><a class="dropdown-item" href="#">Today</a></li>
                <li><a class="dropdown-item" href="#">This Month</a></li>
                <li><a class="dropdown-item" href="#">This Year</a></li>
              </ul>
            </div>

            <div class="card-body">
              <h5 class="card-title">Recent Activity <span>| Today</span></h5>

              <div class="activity">

                <div class="activity-item d-flex">
                  <div class="activite-label">32 min</div>
                  <i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>
                  <div class="activity-content">
                    Quia quae rerum <a href="#" class="fw-bold text-dark">explicabo officiis</a> beatae
                  </div>
                </div><!-- End activity item-->

                <div class="activity-item d-flex">
                  <div class="activite-label">56 min</div>
                  <i class='bi bi-circle-fill activity-badge text-danger align-self-start'></i>
                  <div class="activity-content">
                    Voluptatem blanditiis blanditiis eveniet
                  </div>
                </div><!-- End activity item-->

                <div class="activity-item d-flex">
                  <div class="activite-label">2 hrs</div>
                  <i class='bi bi-circle-fill activity-badge text-primary align-self-start'></i>
                  <div class="activity-content">
                    Voluptates corrupti molestias voluptatem
                  </div>
                </div><!-- End activity item-->

                <div class="activity-item d-flex">
                  <div class="activite-label">1 day</div>
                  <i class='bi bi-circle-fill activity-badge text-info align-self-start'></i>
                  <div class="activity-content">
                    Tempore autem saepe <a href="#" class="fw-bold text-dark">occaecati voluptatem</a> tempore
                  </div>
                </div><!-- End activity item-->

                <div class="activity-item d-flex">
                  <div class="activite-label">2 days</div>
                  <i class='bi bi-circle-fill activity-badge text-warning align-self-start'></i>
                  <div class="activity-content">
                    Est sit eum reiciendis exercitationem
                  </div>
                </div><!-- End activity item-->

                <div class="activity-item d-flex">
                  <div class="activite-label">4 weeks</div>
                  <i class='bi bi-circle-fill activity-badge text-muted align-self-start'></i>
                  <div class="activity-content">
                    Dicta dolorem harum nulla eius. Ut quidem quidem sit quas
                  </div>
                </div><!-- End activity item-->

              </div>

            </div>
          </div><!-- End Recent Activity -->


        </div><!-- End Right side columns -->

      </div>
    </section>

  </main><!-- End #main -->


<?php
include 'layout/footer.php';
?>

    <!-- Trigger Modal -->
    <script>
      var biometricModal = new bootstrap.Modal(document.getElementById("biometricModal"));
      biometricModal.show();
    </script>