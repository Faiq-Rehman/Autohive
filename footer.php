<?php
include 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = trim($_POST['email']);

    if (!empty($email)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "invalid";
            exit;
        }

        $checkQuery = "SELECT id FROM newsletter_subscribers WHERE email = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            echo "exists";
        } else {
            $insertQuery = "INSERT INTO newsletter_subscribers (email) VALUES (?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("s", $email);

            if ($insertStmt->execute()) {
                echo "success";  // ✅ Ye frontend ko milega
            } else {
                if ($insertStmt->errno === 1062) {
                    echo "exists";
                } else {
                    echo "error";
                }
            }
        }
    } else {
        echo "empty";
    }
    exit;
}
?>

<!-- Footer Start -->
<footer class="footer">
  <div class="container">
    <div class="row text-start">
      <!-- Logo Section -->
      <div class="col-md-3 footer-logo">
        <img src="extraimages/logo.png" alt="AutoHive Logo" />
        <p>AutoHive is your trusted digital showroom to explore, buy, sell, or trade brand-new and certified pre-owned
          vehicles — anytime, anywhere.</p>
      </div>

      <!-- Quick Links -->
      <div class="col-md-3">
        <h5>Quick Links</h5>
        <a href="cars.php">All Cars</a>
        <a href="ev_tools.php">EV Tools</a>
        <a href="services.php">Services</a>
        <a href="about.php">About Us</a>
        <a href="contact.php">Contact Us</a>
        <a href="help.php">Help</a>
      </div>

      <!-- Contact Info + Social Icons -->
      <div class="col-md-3">
        <h5>Contact</h5>
        <p><i class="bi bi-geo-alt-fill me-2 text-danger"></i>123 Main Street, Your City</p>
        <p><i class="bi bi-envelope-fill me-2 text-danger"></i>team.autohive@gmail.com</p>
        <p><i class="bi bi-telephone-fill me-2 text-danger"></i>+123 456 7890</p>
        <div class="social-icons mt-3">
          <a href="#"><i class="bi bi-facebook"></i></a>
          <a href="#"><i class="bi bi-twitter"></i></a>
          <a href="#"><i class="bi bi-instagram"></i></a>
          <a href="#"><i class="bi bi-youtube"></i></a>
        </div>
      </div>

      <!-- Newsletter -->
      <div class="col-md-3">
        <h5>Stay Connected</h5>
        <form id="newsletterForm" class="mt-3">
          <div class="mb-3">
            <input type="email" class="form-control newsletter-input" id="newsletterEmail"
              placeholder="Enter your email" required>
          </div>
          <button type="submit" class="btn newsletter-btn w-100"
            style="background-color: #E0101B; color: #ffffff;">Subscribe</button>
        </form>
      </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom mt-4">
      &copy; 2025 <span class="text-danger">AutoHive</span>. All rights reserved.
    </div>
  </div>
</footer>
<!-- Footer End -->

<!-- ✅ SweetAlert2 Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.getElementById("newsletterForm").addEventListener("submit", function (e) {
    e.preventDefault();

    let email = document.getElementById("newsletterEmail").value.trim();

    fetch("<?php echo basename(__FILE__); ?>", {   // ✅ same file name send karein
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "email=" + encodeURIComponent(email)
    })
    .then(res => res.text())
    .then(data => {
        console.log("Server Response:", data);  // 👈 Debugging ke liye
        if (data === "success") {
            Swal.fire("🎉 Subscribed!", "You have successfully subscribed to our newsletter.", "success");
        } else if (data === "exists") {
            Swal.fire("📬 Already Subscribed", "This email is already subscribed.", "info");
        } else if (data === "invalid") {
            Swal.fire("⚠️ Invalid Email", "Please enter a valid email address.", "warning");
        } else if (data === "empty") {
            Swal.fire("⚠️ Empty Field", "Please enter your email address.", "warning");
        } else {
            Swal.fire("❌ Error", "Something went wrong. Please try again.", "error");
        }
        document.getElementById("newsletterEmail").value = "";
    })
    .catch(err => {
        console.error(err);
        Swal.fire("⚠️ Network Error", "Unable to subscribe right now.", "error");
    });
});
</script>