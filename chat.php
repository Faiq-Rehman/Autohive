<?php
session_start();
include 'dbconnect.php';
include 'navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'seller') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// ✅ Get receiver ID
$receiver_id = isset($_GET['user']) ? intval($_GET['user']) : 0;

// ✅ Send message via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && $receiver_id > 0) {
    $msg = trim($_POST['message']);
    if (!empty($msg)) {
        $stmt = $conn->prepare("INSERT INTO chats (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $receiver_id, $msg);
        $stmt->execute();
        exit();
    }
}

// ✅ Fetch messages via AJAX
if (isset($_GET['fetch']) && $receiver_id > 0) {
    $sql = "SELECT c.*, s.name AS sender_name
            FROM chats c
            JOIN users s ON c.sender_id = s.id
            WHERE (sender_id=$user_id AND receiver_id=$receiver_id)
            OR (sender_id=$receiver_id AND receiver_id=$user_id)
            ORDER BY c.sent_at ASC";
    $messages = $conn->query($sql);
    while ($msg = $messages->fetch_assoc()) {
        $is_self = $msg['sender_id'] == $user_id;
        echo '<div class="d-flex mb-2 ' . ($is_self ? 'justify-content-end' : '') . '">';
        echo '<div><strong>' . ($is_self ? 'You' : htmlspecialchars($msg['sender_name'])) . ':</strong> ' 
             . htmlspecialchars($msg['message']) . '<br><small class="text-muted">' . $msg['sent_at'] . '</small></div>';
        echo '</div>';
    }
    exit();
}

// ✅ Fetch Buyers who messaged this seller (for sidebar)
$user_list = $conn->query("
    SELECT DISTINCT u.id, u.name
    FROM chats c
    JOIN users u ON c.sender_id = u.id
    WHERE c.receiver_id = $user_id AND u.user_type = 'buyer'
");

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Live Chat - AutoHive</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    .containers { padding-top: 80px; }
    .chat-box { height: 400px; overflow-y: auto; background: #fff; padding: 1rem; border: 1px solid #ddd; }
  </style>
</head>
<body class="bg-light">
<div class="container mt-4 containers">
  <h3 class="text-center mb-4">Live Chat (Seller ↔ Buyer)</h3>
  <div class="row">
    <div class="col-md-3">
      <h5>Buyers</h5>
      <ul class="list-group">
        <?php if($user_list->num_rows > 0) {
            while($user = $user_list->fetch_assoc()) { ?>
          <li class="list-group-item <?php echo ($receiver_id == $user['id']) ? 'active' : ''; ?>">
            <a href="?user=<?php echo $user['id']; ?>"
               class="text-decoration-none <?php echo ($receiver_id == $user['id']) ? 'text-white' : ''; ?>">
              <?php echo $user['name']; ?>
            </a>
          </li>
        <?php }} else { ?>
          <li class="list-group-item">No buyers found.</li>
        <?php } ?>
      </ul>
    </div>

    <div class="col-md-9">
      <?php if($receiver_id > 0) { ?>
        <div class="chat-box mb-2" id="chat-box"></div>
        <form id="chat-form" class="input-group">
          <input type="text" name="message" id="message" class="form-control" placeholder="Type your message..." autocomplete="off">
          <button type="submit" class="btn btn-primary">Send</button>
        </form>
      <?php } else { ?>
        <p>Select a buyer to start chatting.</p>
      <?php } ?>
    </div>
  </div>
</div>
<script>
const receiverId = <?php echo $receiver_id; ?>;
function fetchMessages() {
  if (receiverId > 0) {
    $.get('?user=' + receiverId + '&fetch=1', function(data) {
      $('#chat-box').html(data);
      $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
    });
  }
}

$('#chat-form').submit(function(e) {
  e.preventDefault();
  const msg = $('#message').val();
  if (msg.trim() !== '') {
    $.post('?user=' + receiverId, { message: msg }, function() {
      $('#message').val('');
      fetchMessages();
    });
  }
});

$('#message').keypress(function(e) {
  if (e.which === 13 && !e.shiftKey) {
    $('#chat-form').submit();
    return false;
  }
});

setInterval(fetchMessages, 2000);
fetchMessages();
</script>
</body>
</html>