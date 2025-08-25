<?php
session_start();
include 'dbconnect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = md5($_POST['password']); // must match signup hashing
    $login_type = isset($_POST['login_type']) ? $_POST['login_type'] : '';

    // Prepare statement to avoid SQL injection
    $stmt = $conn->prepare("SELECT id, name, user_type, verified FROM users WHERE email=? AND password=? LIMIT 1");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if ((int)$user['verified'] === 1) {
            if ($login_type && $login_type !== $user['user_type']) {
                $message = "❌ You are registered as a " . ucfirst($user['user_type']) . ". Please select correct login type.";
            } else {
                // Login allowed
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_type'] = $user['user_type'];

                // Redirect based on user type
                header("Location: index.php");
                exit();
            }
        } else {
            $message = "⚠ Your account is not verified yet. Please wait for admin approval.";
        }
    } else {
        $message = "❌ Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - AutoHive</title>
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
      padding: 20px;
    }
    
    .login-container {
      width: 100%;
      max-width: 400px; /* Reduced from 500px to 400px */
      animation: fadeInUp 0.6s ease-out;
    }
    
    .login-card {
      border: none;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      overflow: hidden;
      transition: all 0.3s ease;
    }
    
    .login-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
    }
    
    .card-header {
      background-color: var(--primary-color);
      color: white;
      text-align: center;
      padding: 1.5rem;
      border-bottom: none;
    }
    
    .logo {
      font-weight: 700;
      font-size: 2rem;
      margin-bottom: 0.5rem;
      color: white;
    }
    
    .card-body {
      padding: 1.5rem; /* Slightly reduced padding */
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
    
    .btn-login {
      background-color: var(--primary-color);
      color: white;
      border: none;
      border-radius: 8px;
      padding: 12px;
      font-weight: 600;
      letter-spacing: 0.5px;
      transition: all 0.3s;
    }
    
    .btn-login:hover {
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
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='%23333' stroke='%23333' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right 0.75rem center;
      background-size: 16px 12px;
    }

    .password-toggle {
      position: absolute;
      top: 55.3%;
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
<div class="login-container">
  <div class="card login-card">
    <div class="card-header">
      <div class="logo">AUTOHIVE</div>
      <h4 class="mb-0">Login to Your Account</h4>
    </div>
    <div class="card-body">
      <?php if(!empty($message)) { echo "<div class='alert alert-info shake mb-4'>$message</div>"; } ?>
      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
          <i class="bi bi-eye password-toggle text-dark" id="togglePassword"></i>
        </div>
        <div class="mb-4">
          <label class="form-label">Login as</label>
          <select name="login_type" class="form-select" required>
            <option value="" selected disabled>Select user type</option>
            <option value="buyer">Buyer</option>
            <option value="seller">Seller</option>
          </select>
        </div>
        <button type="submit" class="btn btn-login w-100">Login</button>
        <div class="login-footer">
          Don't have an account? <a href="signup.php">Sign up</a>
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