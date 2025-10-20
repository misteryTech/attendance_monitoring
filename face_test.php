<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Face++ Test Upload</title>
</head>
<body>
<h2>Upload a Picture to Test Face++ Verification</h2>

<input type="file" id="imageInput" accept="image/*"><br><br>
<input type="text" id="studentId" placeholder="Enter Student ID"><br><br>
<button onclick="uploadFace()">Verify Face</button>

<p id="result"></p>

<script>
function uploadFace() {
    const fileInput = document.getElementById('imageInput');
    const studentId = document.getElementById('studentId').value;
    const result = document.getElementById('result');

    if (!fileInput.files.length || !studentId) {
        alert('Please select an image and enter a student ID');
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        const base64Image = e.target.result;

        fetch('verify_face.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                student_id: studentId,
                image: base64Image
            })
        })
        .then(res => res.json())
        .then(data => {
            result.textContent = data.message;
            result.style.color = data.success ? 'green' : 'red';
        })
        .catch(err => {
            result.textContent = 'Error: ' + err;
            result.style.color = 'red';
        });
    };

    reader.readAsDataURL(fileInput.files[0]);
}
</script>
</body>
</html>
