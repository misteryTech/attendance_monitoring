<?php
include 'layout/header.php';
?>
<body>
<?php
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
                  <h5 class="card-title">Student <span>| Total</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-people"></i>
                    </div>
                    <div class="ps-3">
                      <h6>
                        <?php

                        $select_student = "SELECT COUNT(*) AS total_students FROM user WHERE role = 'student'";
                        $result = $conn->query($select_student);
                        $data = $result->fetch_assoc();
                          $total  = $data['total_students'];
                       ?>
                        <?= $total ?>
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
                  <h5 class="card-title">Parents <span>| Total</span></h5>

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
                  <h5 class="card-title">Request User <span>| Total</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-list"></i>
                    </div>
                    <div class="ps-3">
                      <h6>

                        <?php

                        $select_request = "SELECT COUNT(*) AS total_request FROM user WHERE verification = 'pending'";
                        $result_request = $conn->query($select_request);
                        $data_request = $result_request->fetch_assoc();
                        $total_request  = $data_request['total_request'];
                       ?>
                        <?= $total_request ?>




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
                  <h5 class="card-title">Student Request <span>| Dashboard</span></h5>

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
                      <?php
                      $query = "SELECT * FROM user WHERE verification = 'pending'";
                      $result = mysqli_query($conn, $query);


                      if (mysqli_num_rows($result) > 0) {
                          $i = 1;
                          while ($row = mysqli_fetch_assoc($result)) {
                              echo "<tr>";
                              echo "<th scope='row'>" . $i++ . "</th>";
                              echo "<td>" . htmlspecialchars($row['id_number']) . "</td>";
                              echo "<td>" . htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) . "</td>";
                              echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                              echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                              echo "<td><span class='badge bg-warning text-dark'>Pending</span></td>";


                              if ($row['verification'] === 'pending') {
                                  echo "<td><a href='view_profile.php?id_number=" . $row['id_number'] . "' class='btn btn-success btn-sm'>View Details</a></td>";
                              } else {
                                  echo "<td colspan='2'>N/A</td>";
                              }
                              echo "</tr>";
                          }
                      } else {
                          echo "<tr><td colspan='6' class='text-center text-muted'>No pending verifications</td></tr>";
                      }
                      ?>
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