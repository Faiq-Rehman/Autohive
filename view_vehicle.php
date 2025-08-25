<?php
session_start();
include 'dbconnect.php';
include 'navbar.php';

$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : 0;
$user_type = $is_logged_in ? $_SESSION['user_type'] : '';

$vehicle_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// ✅ Fetch vehicle details
$vehicle = null;

// Pehle user_vehicles check karo
$res = $conn->query("SELECT v.*, u.name AS seller_name, u.id AS seller_id, u.verified AS seller_verified, 'user' AS source 
                     FROM user_vehicles v 
                     JOIN users u ON v.user_id = u.id 
                     WHERE v.id = $vehicle_id");

if ($res->num_rows == 0) {
    // Agar user_vehicle nahi mila to admin_vehicles check karo
    $res = $conn->query("SELECT v.*, 'Admin' AS seller_name, 1 AS seller_id, 1 AS seller_verified, 'admin' AS source
                         FROM vehicles v 
                         WHERE v.id = $vehicle_id");
}

$vehicle = $res->fetch_assoc();

if (!$vehicle) {
    die("Vehicle not found!");
}

// Fetch seller info
$seller = [
    'name' => $vehicle['seller_name'],
    'verified' => $vehicle['seller_verified']
];

$seller_id = $vehicle['seller_id'];
if ($vehicle['source'] === 'user') {
    $seller_query = $conn->prepare("SELECT name, verified FROM users WHERE id = ?");
    $seller_query->bind_param("i", $seller_id);
    $seller_query->execute();
    $seller_result = $seller_query->get_result();
    if ($seller_result->num_rows > 0) {
        $seller = $seller_result->fetch_assoc();
    }
    $seller_query->close();
}

/* =====================================================
   ✅ CHAT LOGIC (AJAX send + fetch combined, no refresh)
   ===================================================== */
if ($is_logged_in) {
    // Send new chat message
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['chat_message'])) {
        $msg = trim($_POST['chat_message']);
        if (!empty($msg)) {
            $receiver_id = ($user_id == $seller_id) ? intval($_POST['buyer_id']) : $seller_id;
            $stmt = $conn->prepare("INSERT INTO chats (sender_id, receiver_id, message) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $user_id, $receiver_id, $msg);
            $stmt->execute();
        }

        // Stop refresh if AJAX request
        if (isset($_POST['ajax']) && $_POST['ajax'] == 1) {
            echo json_encode(["status" => "success"]);
            exit;
        }
    }

    // Fetch chat messages
    if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
        $sql_chat = "SELECT c.*, u.name AS sender_name, u.profile_image 
                     FROM chats c 
                     JOIN users u ON c.sender_id=u.id
                     WHERE (sender_id=$user_id AND receiver_id=$seller_id)
                        OR (sender_id=$seller_id AND receiver_id=$user_id)
                     ORDER BY c.sent_at ASC";
        $chat_messages = $conn->query($sql_chat);

        while ($m = $chat_messages->fetch_assoc()) {
    echo "<div class='chat-message'>
            <div>
              <strong>" . ($m['sender_id'] == $user_id ? 'You' : htmlspecialchars($m['sender_name'])) . ":</strong> 
              " . htmlspecialchars($m['message']) . "
              <br><small class='text-muted'>(".$m['sent_at'].")</small>
            </div>
          </div>";
}
        exit;
    }

    // For initial load (normal page render)
    $sql_chat = "SELECT c.*, u.name AS sender_name, u.profile_image 
                 FROM chats c 
                 JOIN users u ON c.sender_id=u.id
                 WHERE (sender_id=$user_id AND receiver_id=$seller_id)
                    OR (sender_id=$seller_id AND receiver_id=$user_id)
                 ORDER BY c.sent_at ASC";
    $chat_messages = $conn->query($sql_chat);
}

/* ================================
   ✅ Wishlist, Review, Purchase 
   ================================ */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_favorite']) && $is_logged_in) {
    $stmt = $conn->prepare("INSERT IGNORE INTO favorites (user_id, vehicle_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $vehicle_id);
    $stmt->execute();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_favorite']) && $is_logged_in) {
    $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND vehicle_id = ?");
    $stmt->bind_param("ii", $user_id, $vehicle_id);
    $stmt->execute();
}

// ✅ Check if already in favorites
$is_favorite = false;
if ($is_logged_in) {
    $check = $conn->prepare("SELECT 1 FROM favorites WHERE user_id = ? AND vehicle_id = ?");
    $check->bind_param("ii", $user_id, $vehicle_id);
    $check->execute();
    $check->store_result();
    $is_favorite = $check->num_rows > 0;
}

// ✅ Chat Logic (Buyer ↔ Seller)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['chat_message']) && !empty($_POST['chat_message']) && $is_logged_in) {
    $msg = trim($_POST['chat_message']);
    $receiver_id = ($user_id == $seller_id) ? intval($_POST['buyer_id']) : $seller_id; 
    $stmt = $conn->prepare("INSERT INTO chats (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $receiver_id, $msg);
    $stmt->execute();
}

// ✅ Fetch chat messages (only between buyer & seller)
$chat_messages = [];
if ($is_logged_in) {
    $sql_chat = "SELECT c.*, u.name AS sender_name, u.profile_image 
                 FROM chats c 
                 JOIN users u ON c.sender_id=u.id
                 WHERE (sender_id=$user_id AND receiver_id=$seller_id)
                 OR (sender_id=$seller_id AND receiver_id=$user_id)
                 ORDER BY c.sent_at ASC";
    $chat_messages = $conn->query($sql_chat);
}

// ✅ Save review
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rating'], $_POST['comment']) && isset($_SESSION['user_id'])) {
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);
    $user_id = $_SESSION['user_id'];

    $insert = $conn->prepare("INSERT INTO reviews (vehicle_id, user_id, rating, comment, source) VALUES (?, ?, ?, ?, ?)");
    $insert->bind_param("iiiss", $vehicle_id, $user_id, $rating, $comment, $vehicle['source']);
    $insert->execute();
    echo "<script>window.location.href='view_vehicle.php?id={$vehicle_id}';</script>";
    exit;
}

// ✅ Fetch reviews
$reviews_stmt = $conn->prepare("SELECT r.*, u.name FROM reviews r 
    JOIN users u ON r.user_id = u.id
    WHERE r.vehicle_id = ? ORDER BY r.created_at DESC");
$reviews_stmt->bind_param("i", $vehicle_id);
$reviews_stmt->execute();
$reviews = $reviews_stmt->get_result();

function formatPKR($amount) {
    if ($amount >= 10000000) {
        return round($amount / 10000000, 1) . ' Crore';
    } elseif ($amount >= 100000) {
        return round($amount / 100000, 1) . ' Lac';
    } else {
        return number_format($amount);
    }
}

// ✅ Purchase logic (handle user & admin vehicles separately)
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['buy_now']) && $is_logged_in && $user_type == 'buyer' && $vehicle['seller_verified']) {
    $price = $vehicle['price'];

    if ($vehicle['source'] === 'user') {
        // User vehicle
        $stmt = $conn->prepare("INSERT INTO transactions (buyer_id, seller_id, user_vehicle_id, price, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->bind_param("iiid", $user_id, $seller_id, $vehicle_id, $price);
    } else {
        // Admin vehicle
        $stmt = $conn->prepare("INSERT INTO transactions (buyer_id, seller_id, admin_vehicle_id, price, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->bind_param("iiid", $user_id, $seller_id, $vehicle_id, $price);
    }

    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // ✅ Vehicle ko sold mark karo
        if ($vehicle['source'] === 'user') {
            $conn->query("UPDATE user_vehicles SET status='sold' WHERE id=$vehicle_id");
        } else {
            $conn->query("UPDATE vehicles SET status='sold' WHERE id=$vehicle_id");
        }
        $message = "✅ Purchase request sent! This vehicle is now marked as SOLD.";
        $vehicle['status'] = 'sold'; // frontend update
    } else {
        $message = "❌ Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo $vehicle['name']; ?> - AutoHive</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/footer.css"/>
  <link rel="stylesheet" href="css/navbar.css"/>
  <style>
    .badge-verified { background-color: #28a745; color: white; padding: 5px 8px; border-radius: 5px; font-size: 12px; margin-left: 5px; }
    .badge-notverified { background-color: #dc3545; color: white; padding: 5px 8px; border-radius: 5px; font-size: 12px; margin-left: 5px; }
    .chat-box { border: 1px solid #ccc; height: 200px; overflow-y: auto; background: #fff; padding: 10px; }
    .chat-message { margin-bottom: 8px; display: flex; align-items: flex-start; gap: 10px; }
    .chat-message img { width: 40px; height: 40px; object-fit: cover; border-radius: 50%; }
    .chat-message div { max-width: 85%; }
    .chat-message strong { color: #007bff; }
    .vehicle-spec p { margin-bottom: 8px; }
    .containers { padding-top: 90px; }
    .carousel img { border-radius: 10px; object-fit: cover; }
    .card { border-radius: 10px; }
    .containers{ padding-top: 110px; padding-bottom: 20px; }
    .star { font-size: 1.5rem; cursor: pointer; color: lightgray; }
    .star.selected, .star.hover { color: gold; }
  </style>
</head>
<body class="bg-light">

<div class="container containers">
  <div class="row g-4">
    <!-- 🖼️ Vehicle Image Section (Updated) -->
    <div class="col-md-6">
      <?php $images = explode(",", $vehicle['images']); ?>
      
      <!-- Main Large Image -->
      <div class="shadow mb-3">
        <img id="mainImage" src="uploads/<?php echo $images[0]; ?>" class="d-block w-100" height="440" style="object-fit: cover; border-radius:10px;">
      </div>

      <!-- Thumbnails -->
      <div class="d-flex gap-2">
        <?php 
        $thumbs = array_slice($images, 0, 5); 
        foreach($thumbs as $index => $img): 
        ?>
          <img src="uploads/<?php echo $img; ?>" 
              class="thumb-img" 
              data-bs-toggle="<?php echo ($index == 4 && count($images) > 5) ? 'modal' : ''; ?>" 
              data-bs-target="<?php echo ($index == 4 && count($images) > 5) ? '#lightboxModal' : ''; ?>"
              onclick="<?php echo ($index < 4 || count($images) <= 5) ? "document.getElementById('mainImage').src=this.src" : ''; ?>"
              style="width: 120px; height: 120px; object-fit: cover; cursor:pointer; border-radius:5px;">
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Lightbox Modal -->
    <div class="modal fade" id="lightboxModal" tabindex="-1">
      <div class="modal-dialog modal-fullscreen">
        <div class="modal-content bg-dark">
          <div class="modal-header border-0">
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body d-flex justify-content-center align-items-center">
            <div id="lightboxCarousel" class="carousel slide w-100" data-bs-ride="carousel">
              <div class="carousel-inner text-center">
                <?php foreach($images as $i => $img): ?>
                  <div class="carousel-item <?php echo $i==0 ? 'active' : ''; ?>">
                    <img src="uploads/<?php echo $img; ?>" class="img-fluid" style="max-height: 90vh; margin:auto; border-radius:10px;">
                  </div>
                <?php endforeach; ?>
              </div>
              <button class="carousel-control-prev" type="button" data-bs-target="#lightboxCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#lightboxCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 🚘 Vehicle Info -->
    <div class="col-md-6 pb-5">
      <div class="card p-4 shadow">
        <!-- <h5 class="text-center fw-bold fs-1">Vehicle's Detail's</h5> -->
        <h3 class="mb-2"><?php echo $vehicle['name']; ?></h3>
        <div class="vehicle-spec">
          <p><strong>Brand:</strong> <?php echo $vehicle['brand']; ?></p>
          <p><strong>Model Year:</strong> <?php echo $vehicle['model_year']; ?></p>
          <p><strong>Mileage:</strong> <?php echo number_format($vehicle['mileage']); ?> km</p>
          <p><strong>Fuel Type:</strong> <?php echo $vehicle['fuel_type']; ?></p>
          <p><strong>Transmission:</strong> <?php echo $vehicle['transmission']; ?></p>
          <p><strong>Condition:</strong> <?php echo $vehicle['condition']; ?></p>
          <p><strong>Engine Capacity:</strong> <?php echo $vehicle['engine_capacity']; ?></p>
          <p><strong>Color:</strong> <?php echo $vehicle['color']; ?></p>
          <p><strong>Registration City:</strong> <?php echo $vehicle['registration_city']; ?></p>
          <p><strong>Category:</strong> <?php echo ucfirst($vehicle['category']); ?></p>
          <p><strong>Price:</strong> PKR <?php echo formatPKR($vehicle['price']); ?></p>
          <p><strong>Description:</strong> <?php echo $vehicle['description']; ?></p>
          <p><strong>Features:</strong> <?php echo nl2br($vehicle['features']); ?></p>
          <p>
            <strong>Seller:</strong> 
            <?php echo htmlspecialchars($seller['name']); ?>
            <?php if (!empty($seller['verified']) && $seller['verified'] == 1): ?>
              <span title="Verified Seller" style="color: green; font-size: 1.2em;">✔️</span>
            <?php endif; ?>
          </p>
        </div>

        <?php if(!empty($message)) echo "<div class='alert alert-info'>$message</div>"; ?>

        <!-- ❤️ Wishlist Button -->
          <?php if ($is_logged_in && $user_type == 'buyer'): ?>
              <?php
              $fav_check = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND vehicle_id = ?");
              $fav_check->bind_param("ii", $user_id, $vehicle_id);
              $fav_check->execute();
              $fav_res = $fav_check->get_result();
              $is_favorite = $fav_res->num_rows > 0;
              ?>
              <a href="toggle_favorite.php?id=<?php echo $vehicle_id; ?>" 
                class="btn <?php echo $is_favorite ? 'btn-warning' : 'btn-outline-danger'; ?> w-100 mb-2">
                  <?php echo $is_favorite ? '💔 Remove from Favorites' : '❤️ Save to Favorites'; ?>
              </a>
          <?php endif; ?>


        <!-- 💰 Buy Now / Sold Out Button -->
        <?php if($is_logged_in && $user_type=='buyer'): ?>
          <?php if($vehicle['status'] === 'sold'): ?>
            <button class="btn btn-danger w-100" disabled>🚫 SOLD OUT</button>
          <?php elseif($vehicle['seller_verified']): ?>
            <form method="POST"><button type="submit" name="buy_now" class="btn btn-success w-100">Buy Now</button></form>
          <?php else: ?>
            <button class="btn btn-secondary w-100" disabled>Seller Not Verified</button>
          <?php endif; ?>
        <?php elseif(!$is_logged_in): ?>
          <a href="login.php" class="btn btn-primary w-100">Login to Buy</a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- 💬 Chat Box -->
  <?php if($is_logged_in && $user_id != 0 && $user_type == 'buyer'): ?>
    <div class="row mt-5 pb-3">
      <div class="col-md-12">
        <h5>💬 Chat with <?php echo $vehicle['seller_name']; ?></h5>
        <div class="chat-box shadow-sm rounded" id="chat-box">
          <?php if($chat_messages->num_rows > 0): ?>
            <?php while($m = $chat_messages->fetch_assoc()): ?>
              <div class="chat-message">
                <div>
                  <strong><?php echo $m['sender_id'] == $user_id ? 'You' : htmlspecialchars($m['sender_name']); ?>:</strong>
                  <?php echo htmlspecialchars($m['message']); ?>
                  <br><small class="text-muted">(<?php echo $m['sent_at']; ?>)</small>
                </div>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <p class="text-muted">No messages yet. Start the conversation!</p>
          <?php endif; ?>
        </div>
        <form id="chat-form" class="mt-2">
          <div class="input-group">
            <input type="text" name="chat_message" id="chat-input" class="form-control" placeholder="Type your message..." autocomplete="off">
            <input type="hidden" name="buyer_id" value="<?php echo $user_id; ?>">
            <button type="submit" class="btn btn-primary">Send</button>
          </div>
        </form>
      </div>
    </div>
  <?php endif; ?>
  </div>

            <div class="container py-5">

    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="card my-4">
        <div class="card-header">Leave a Review</div>
        <div class="card-body">
            <form method="POST">
                <div id="star-rating">
                    <?php for ($i=1; $i<=5; $i++): ?>
                        <span class="star" data-value="<?= $i ?>">★</span>
                    <?php endfor; ?>
                </div>
                <input type="hidden" name="rating" id="rating" value="0">
                <div class="mt-3">
                    <textarea name="comment" class="form-control" placeholder="Write your review..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Submit Review</button>
            </form>
        </div>
    </div>
    <?php endif; ?>


       <h3>Review</h3>
    <?php if ($reviews->num_rows > 0): ?>
    <div class="row g-3">
        <?php while ($row = $reviews->fetch_assoc()): ?>
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title"><?= htmlspecialchars($row['name']) ?></h6>
                        <div class="mb-2">
                            <?php for ($i=1; $i<=5; $i++): ?>
                                <span style="color:<?= $i <= $row['rating'] ? 'gold' : 'lightgray' ?>">★</span>
                            <?php endfor; ?>
                        </div>
                        <p class="card-text"><?= htmlspecialchars($row['comment']) ?></p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <p>No reviews yet.</p>
<?php endif; ?>

</div>



<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  function fetchChat() {
  $.get(location.href, { ajax: 1 }, function(data) {
    $('#chat-box').html(data);
    $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight); // auto-scroll
  });
}

$('#chat-form').on('submit', function(e) {
  e.preventDefault();
  let message = $('#chat-input').val().trim();
  if (message === "") return;
  
  $.post(location.href, {
    chat_message: message,
    buyer_id: $('input[name="buyer_id"]').val(),
    ajax: 1
  }, function(res) {
    $('#chat-input').val('');   // ✅ input clear
    fetchChat();                // ✅ chat refresh
  });
});

$('#chat-input').on('keydown', function(e) {
  if (e.key === "Enter" && !e.shiftKey) {
    e.preventDefault();
    $('#chat-form').submit();   // ✅ Enter se bhi send
  }
});

setInterval(fetchChat, 1000); // auto-refresh every 2s
fetchChat();

let selectedRating = 0; // ✅ declare at top

document.querySelectorAll('.star').forEach((star, index) => {
    star.addEventListener('click', () => {
        const clickedRating = index + 1;

        if (selectedRating === clickedRating) {
            // Reset rating if same star clicked again
            selectedRating = 0;
        } else {
            // Set new rating
            selectedRating = clickedRating;
        }

        // Update star colors
        document.querySelectorAll('.star').forEach((s, i) => {
            s.style.color = (i < selectedRating) ? 'gold' : 'gray';
        });

        // Store in hidden input for form submission
        document.getElementById('rating').value = selectedRating;
    });

    // ✅ Optional: hover effect
    star.addEventListener('mouseover', () => {
        document.querySelectorAll('.star').forEach((s, i) => {
            s.style.color = (i <= index) ? 'gold' : (i < selectedRating ? 'gold' : 'gray');
        });
    });

    star.addEventListener('mouseleave', () => {
        document.querySelectorAll('.star').forEach((s, i) => {
            s.style.color = (i < selectedRating) ? 'gold' : 'gray';
        });
    });
});
</script>
</body>
</html>