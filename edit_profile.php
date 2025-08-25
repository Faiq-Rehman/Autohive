<?php
session_start();
include 'dbconnect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// ✅ Fetch current user data before update
$stmt = $conn->prepare("SELECT name, email, phone, profile_image, user_type FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $phone, $current_image, $user_type);
$stmt->fetch();
$stmt->close();

// ✅ Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $new_password = trim($_POST['password']);
    $new_user_type = $_POST['user_type'];

    $image = $current_image;

    // ✅ Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $img_name = time() . '_' . basename($_FILES['image']['name']);
        $target_path = "admin/pic/" . $img_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            // ✅ Delete old image
            if (!empty($current_image) && file_exists("admin/pic/" . $current_image)) {
                unlink("admin/pic/" . $current_image);
            }
            $image = $img_name;
        }
    }

    // ✅ Prepare update query
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, password=?, profile_image=?, user_type=? WHERE id=?");
        $stmt->bind_param("ssssssi", $name, $email, $phone, $hashed_password, $image, $new_user_type, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, profile_image=?, user_type=? WHERE id=?");
        $stmt->bind_param("sssssi", $name, $email, $phone, $image, $new_user_type, $user_id);
    }

    if ($stmt->execute()) {
        $message = "<p class='text-success text-center'>Profile updated successfully.</p>";
        $_SESSION['user_name'] = $name;
        $_SESSION['user_type'] = $new_user_type;
        $current_image = $image;
        $user_type = $new_user_type;
    } else {
        $message = "<p class='text-danger text-center'>Error updating profile.</p>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Profile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- ✅ Mobile friendly -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body{
      background: black;
    }
    .center-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding-top: 60px;
        padding-bottom: 40px;
    }
    .profile-img-preview {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 50%;
        margin-bottom: 10px;
        border: 2px solid #ccc;
    }
  </style>
</head>
<body>
  <div class="container center-wrapper">
    <div class="col-lg-6 col-md-8 col-12 bg-white p-4 shadow rounded">
      <h4 class="mb-4 text-center">Edit Profile</h4>
      <?php echo $message; ?>
      <form method="POST" enctype="multipart/form-data">
        <div class="text-center">
          <img src="admin/pic/<?php echo htmlspecialchars($current_image ?: 'default.png'); ?>" class="profile-img-preview" alt="Profile Image">
        </div>
        <div class="mb-3">
          <label class="form-label">Full Name</label>
          <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($name); ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($email); ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Phone</label>
          <input type="text" name="phone" class="form-control" required value="<?php echo htmlspecialchars($phone); ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Account Type</label>
          <select name="user_type" class="form-select" required>
            <option value="buyer" <?php echo $user_type=='buyer' ? 'selected' : ''; ?>>Buyer</option>
            <option value="seller" <?php echo $user_type=='seller' ? 'selected' : ''; ?>>Seller</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">New Password (optional)</label>
          <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
        </div>
        <div class="mb-3">
          <label class="form-label">Change Profile Image (optional)</label>
          <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png">
        </div>
        <button type="submit" class="btn btn-primary w-100">Save Changes</button>
      </form>
      <br>
      <a href="index.php"><button class="btn btn-dark">Back</button></a>
    </div>
  </div>
</body>
</html>