
  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>




<script>
  // On form submit, disable all inputs in inactive tab
  document.querySelector('form').addEventListener('submit', function (e) {
    // Get all tab panes
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabPanes.forEach(pane => {
      if (!pane.classList.contains('active')) {
        // Disable all inputs inside inactive pane
        pane.querySelectorAll('input, select, textarea').forEach(input => {
          input.disabled = true;
        });
      }
    });
  });
</script>



</body>

</html>