<?php
// Include database connection
include 'admin/config/db.php';

$status = ""; // Status message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    // Insert data into database
    $sql = "INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $subject, $message);

    if ($stmt->execute()) {
        $status = "success"; // Success message
    } else {
        $status = "error"; // Error message
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <!-- Bootstrap CSS -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
</head>
<body>

<!-- Contact Form -->
<div class="col-lg-7">
    <div class="contact-form">
        <h3>Get In Touch</h3>

        <form method="post">
            <div class="row gy-4">
                <div class="col-md-6">
                    <input type="text" name="name" class="form-control" placeholder="Your Name" required>
                </div>

                <div class="col-md-6">
                    <input type="email" class="form-control" name="email" placeholder="Your Email" required>
                </div>

                <div class="col-12">
                    <input type="text" class="form-control" name="subject" placeholder="Subject" required>
                </div>

                <div class="col-12">
                    <textarea class="form-control" name="message" rows="6" placeholder="Message" required></textarea>
                </div>

                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="statusModalLabel">Message Status</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php
        if ($status === "success") {
            echo '<p class="text-success">Message Sent Successfully!</p>';
        } elseif ($status === "error") {
            echo '<p class="text-danger">Error: Unable to send message.</p>';
        }
        ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
<?php if ($status !== ""): ?>
// Show the modal if there is a status message
document.addEventListener("DOMContentLoaded", function() {
    var statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
    statusModal.show();
});
<?php endif; ?>
</script>

</body>
</html>
