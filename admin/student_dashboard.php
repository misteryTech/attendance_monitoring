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
        <div class="col-lg-12">
          <div class="row">


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

                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $query = "SELECT * FROM user WHERE role = 'student' ORDER BY id DESC";
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

                              if ($row['verification'] === 'pending') {
                                      echo "<td><span class='badge bg-warning text-dark'>Pending</span></td>";
                              } else if($row['verification'] === 'yes')  {
                                  echo "<td><span class='badge bg-success text-light'>Approve</span></td>";
                              } else {
                                  echo "<td><span class='badge bg-danger text-dark'>Reject</span></td>";
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


      </div>
    </section>

  </main><!-- End #main -->


<?php
include 'layout/footer.php';
?>