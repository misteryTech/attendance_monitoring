<?php
    include 'layout/header.php';
    include 'layout/navigation.php';
    include 'layout/connection.php';
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

            <form id="scheduleForm">
              <input type="hidden" name="student_id" value="<?= $student_id; ?>">

              <div class="mb-3">
                <label for="inputDate" class="form-label">Date</label>
                <input type="date" class="form-control" name="date" id="inputDate" required>
              </div>

              <div class="mb-3">
                <label for="inputTime" class="form-label">Time</label>
                <input type="time" class="form-control" name="time" id="inputTime" required>
              </div>

              <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea class="form-control" name="message" id="message" rows="4" required></textarea>
              </div>

              <div class="d-grid">
                <button type="submit" class="btn btn-primary">Submit Form</button>
              </div>
            </form>

          </div>
        </div>
      </div>

      <!-- Table Column -->
      <div class="col-lg-8">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Submitted Schedule</h5>

            <?php
            $query = mysqli_query($conn, "SELECT date_request, time, status, message FROM schedule_picture WHERE student_id = '$student_id'");
            ?>

            <div class="table-responsive">
              <table class="table table-bordered table-striped" id="scheduleTable">
                <thead class="table-light">
                  <tr>
                    <th>Date Request</th>
                    <th>Time</th>
                    <th>Message</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (mysqli_num_rows($query) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($query)): ?>
                      <tr>
                        <td><?= htmlspecialchars($row['date_request']) ?></td>
                        <td><?= htmlspecialchars($row['time']) ?></td>
                        <td><?= htmlspecialchars($row['message']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="4" class="text-center text-muted">No schedule found.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>

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

document.getElementById("scheduleForm").addEventListener("submit", function(e) {
  e.preventDefault();

  const formData = new FormData(this);

  fetch("ajax/insert_schedule.php", {
    method: "POST",
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert("Schedule submitted successfully!");
      document.getElementById("scheduleForm").reset();
      location.reload();
    } else {
      alert("Submission failed: " + data.message);
    }
  })
  .catch(error => {
    console.error("Error:", error);
    alert("An error occurred while submitting.");
  });
});


</script>