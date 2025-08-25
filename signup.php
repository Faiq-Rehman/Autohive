<?php
session_start();
include 'dbconnect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = md5($_POST['password']); // Can upgrade to password_hash() later
    $user_type = htmlspecialchars($_POST['user_type']);
    $phone = htmlspecialchars(trim($_POST['phone']));
    $gender = htmlspecialchars($_POST['gender']);

    // Basic phone validation (optional)
    if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
        $message = "⚠ Please enter a valid phone number!";
    } else {
        // Check if email already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "⚠ Email already registered!";
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, password, user_type, phone, gender, verified) 
                 VALUES (?, ?, ?, ?, ?, ?, 0)"
            );
            $stmt->bind_param("ssssss", $name, $email, $password, $user_type, $phone, $gender);

            if ($stmt->execute()) {
                // ✅ Auto-login after successful signup
                $user_id = $stmt->insert_id;
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_type'] = $user_type;
                $_SESSION['user_name'] = $name;

                // ✅ Redirect to index.php with success message
                $_SESSION['signup_success'] = "✅ Welcome, $name! Your account has been created. Please wait for admin verification.";
                header("Location: index.php");
                exit;
            } else {
                $message = "❌ Error: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Signup - AutoHive</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #000000;
      --secondary-color: #333333;
      --accent-color: #4361ee;
      --light-color: #f8f9fa;
      --dark-color: #212529;
    }
    
    body {
      background: linear-gradient(135deg, #ffffff 0%, #f1f1f1 50%, #e1e1e1 100%);
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0;
    }
    
    .signup-container {
      width: 100%;
      max-width: 500px;
      animation: fadeInUp 0.6s ease-out;
    }
    
    .signup-card {
      border: none;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      overflow: hidden;
      transition: all 0.3s ease;
    }
    
    .signup-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
    }
    
    .card-header {
      background-color: var(--primary-color);
      color: white;
      text-align: center;
      padding: 20px;
      border-bottom: none;
    }
    
    .logo {
      font-weight: 700;
      font-size: 2rem;
      margin-bottom: 0.5rem;
      color: white;
    }
    
    .card-body {
      padding: 1.5rem;
      background-color: white;
    }
    
    .form-control {
      border-radius: 8px;
      padding: 12px 15px;
      border: 1px solid #ddd;
      transition: all 0.3s;
    }
    
    .form-control:focus {
      border-color: var(--accent-color);
      box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
    }
    
    .form-label {
      font-weight: 500;
      margin-bottom: 0.5rem;
      color: #555;
    }
    
    .btn-primary {
      background-color: var(--primary-color);
      border: none;
      border-radius: 8px;
      padding: 12px;
      font-weight: 600;
      letter-spacing: 0.5px;
      transition: all 0.3s;
    }
    
    .btn-primary:hover {
      background-color: var(--secondary-color);
      transform: translateY(-2px);
    }
    
    .login-footer {
      text-align: center;
      margin-top: 1.5rem;
      color: #666;
    }
    
    .login-footer a {
      color: var(--primary-color);
      font-weight: 500;
      text-decoration: none;
    }
    
    .login-footer a:hover {
      text-decoration: underline;
    }
    
    .alert {
      border-radius: 8px;
    }
    
    select.form-select {
      appearance: none;
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23333' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right 0.75rem center;
      background-size: 16px 12px;
    }

    .password-toggle {
      position: absolute;
      top: 81.005%;
      right: 35px;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6c757d;
      font-size: 1.2rem;
    }
    
    /* Animations */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      20%, 60% { transform: translateX(-5px); }
      40%, 80% { transform: translateX(5px); }
    }
  </style>
</head>
<body>
<div class="signup-container">
  <div class="card signup-card">
    <div class="card-header">
      <div class="logo">AUTOHIVE</div>
      <h4 class="mb-0">Create Your Account</h4>
    </div>
    <div class="card-body">
      <?php if(!empty($message)) { echo "<div class='alert alert-info shake mb-4'>$message</div>"; } ?>
      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Full Name</label>
          <input type="text" name="name" class="form-control" placeholder="Enter your full name" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Phone Number</label>
          <input type="number" name="phone" class="form-control" placeholder="Enter your phone number" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Gender</label>
          <select name="gender" class="form-select" required>
            <option value="" selected disabled>Select gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
          </select>
        </div>
        <div class="mb-4">
          <label class="form-label">Register As</label>
          <select name="user_type" class="form-select" required>
            <option value="" selected disabled>Select account type</option>
            <option value="buyer">Buyer</option>
            <option value="seller">Seller</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" id="password" class="form-control" placeholder="Create a password" required>
          <i class="bi bi-eye password-toggle" id="togglePassword"></i>
        </div>
        <button type="submit" class="btn btn-primary w-100">Create Account</button>
        <div class="login-footer">
          Already have an account? <a href="login.php">Sign in</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
      <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');

        togglePassword.addEventListener('click', function () {
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;

            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
      </script>
</body>
</html>