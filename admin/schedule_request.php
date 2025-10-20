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
          <li class="breadcrumb-item"><a href="index.html">Request Schedule</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">
                   <!-- Customers Card -->
            <!-- Recent Sales -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">

                <div class="card-body">
                  <h5 class="card-title">Request Schedule <span>| Dashboard</span></h5>

                                  <table class="table table-borderless datatable">
                      <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">Student ID</th>
                          <th scope="col">Date Requested</th>
                          <th scope="col">Scheduled Time</th>
                          <th scope="col">Status</th>
                          <th scope="col">Message</th>
                          <th scope="col">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $query = "SELECT * FROM schedule_picture ORDER BY date_request ASC, time ASC";
                        $result_sched = mysqli_query($conn, $query);

                        if (mysqli_num_rows($result_sched) > 0) {
                            $i = 1;
                            while ($row_sched = mysqli_fetch_assoc($result_sched)) {
                                echo "<tr>";
                                echo "<th scope='row'>" . $i++ . "</th>";
                                echo "<td>" . htmlspecialchars($row_sched['student_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row_sched['date_request']) . "</td>";
                                echo "<td>" . htmlspecialchars($row_sched['time']) . "</td>";

                                $status = htmlspecialchars($row_sched['status']);
                                $badge_class = ($status === 'Pending') ? 'bg-warning text-dark' : 'bg-success';
                                echo "<td><span class='badge $badge_class'>$status</span></td>";

                                echo "<td>" . htmlspecialchars($row_sched['message']) . "</td>";

                                // Respond button
                                echo "<td>";
                                echo "<a href='respond_request.php?student_id=" . urlencode($row_sched['student_id']) . "' class='btn btn-sm btn-primary'>Respond</a>";
                                echo "</td>";

                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center text-muted'>No pending schedules found</td></tr>";
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