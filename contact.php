<?php
session_start();
include 'dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    $stmt = $conn->prepare("INSERT INTO contact (full_name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $full_name, $email, $subject, $message);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: contact.php?success=1");
        exit();
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact Us | AutoHive</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet"> 
  <link rel="stylesheet" href="css/contact.css">
  <link rel="stylesheet" href="css/footer.css">
  <link rel="stylesheet" href="css/navbar.css">
  <style>

        .hero {
        margin: 0;
    }

             @media (max-width: 768px) {
      .hero {
        margin-top: 90px;
      }}
        .btn-contact {
            display: inline-block;
            background-color: #E0101B;
            color: #ffffff;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-contact:hover {
            background-color: #E0101B;
            color: #ffffff;
            transform: translateY(-2px);
        }
        
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<!-- ✅ Hero Section with Animation -->
<section class="hero" data-aos="fade-down">
  <div class="container text-white">
    <h1 class="display-4 fw-bold">CONTACT | AUTOHIVE</h1>
    <p class="lead">We're here to assist you with buying, selling, or trading your next vehicle. Let's connect.</p>
  </div>
</section>

<!-- ✅ Contact Section -->
<section class="contact-section">
  <div class="container">
    <div class="row g-5">
      <!-- ✅ Contact Info -->
      <div class="col-lg-5" data-aos="fade-right">
        <div class="contact-card h-100 contact-info">
          <h4 class="mb-4">Contact Details</h4>
          <p><i class="bi bi-geo-alt-fill"></i> 123 Car Lane, Motor City, NY 10001</p>
          <p><i class="bi bi-telephone-fill"></i> +1 (800) 123-4567</p>
          <p><i class="bi bi-envelope-fill"></i> team.autohive@gmail.com</p>
          <hr />
          <h6 class="fw-bold mb-2">Working Hours</h6>
          <p class="mb-1">Mon - Fri: 9:00 AM – 6:00 PM</p>
          <p>Sat - Sun: 10:00 AM – 4:00 PM</p>
        </div>
      </div>

      <!-- ✅ Contact Form -->
      <div class="col-lg-7" data-aos="fade-left">
        <div class="contact-card">
          <h4 class="mb-4">Send Us a Message</h4>
          <form method="POST">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label" for="name">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Your Name" required />
              </div>
              <div class="col-md-6">
                <label class="form-label" for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required />
              </div>
              <div class="col-12">
                <label class="form-label" for="subject">Subject</label>
                <input type="text" class="form-control" id="subject" name="subject" placeholder="Vehicle Inquiry" required />
              </div>
              <div class="col-12">
                <label class="form-label" for="message">Message</label>
                <textarea class="form-control" id="message" name="message" rows="5" placeholder="Tell us how we can help..." required></textarea>
              </div>
            </div>
            <div class="mt-4">
              <button type="submit" class="btn btn-danger">Submit Message</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- ✅ Map Section -->
    <div class="row mt-5">
      <div class="col-12 map-box" data-aos="zoom-in">
        <div class="contact-card p-0 overflow-hidden">
          <iframe src="https://maps.google.com/maps?q=New%20York%20City&t=&z=13&ie=UTF8&iwloc=&output=embed" allowfullscreen></iframe>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ✅ SweetAlert for success -->
<?php if (isset($_GET['success'])): ?>
  <script>
    Swal.fire({
      title: "Message Sent!",
      text: "We have received your message and will get back to you shortly.",
      icon: "success",
      confirmButtonColor: "#E0101B",
      allowOutsideClick: false
    }).then(() => {
      if (window.history.replaceState) {
        const url = new URL(window.location);
        url.searchParams.delete("success");
        window.history.replaceState({}, document.title, url.pathname);
      }
    });
  </script>
<?php endif; ?>

<?php include 'footer.php'; ?>

<!-- ✅ AOS JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 1000, 
    once: true       
  });
</script>

</body>
</html>