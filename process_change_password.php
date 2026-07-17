<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['email'])) {
  header("Location: login.html");
  exit();
}

$email = $_SESSION['email'];
$current = $_POST['current_password'];
$new = $_POST['new_password'];
$confirm = $_POST['confirm_password'];

$status = '';
$message = '';

// Validation
if ($new !== $confirm) {
  $status = 'error';
  $message = "New passwords do not match.";
} elseif (strlen($new) < 6) {
  $status = 'error';
  $message = "New password must be at least 6 characters.";
} else {
  // Fetch old password hash
  $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 0) {
    $status = 'error';
    $message = "User not found.";
  } else {
    $user = $result->fetch_assoc();

    if (!password_verify($current, $user['password'])) {
      $status = 'error';
      $message = "Current password is incorrect.";
    } else {
      // Hash and update
      $newHash = password_hash($new, PASSWORD_BCRYPT);
      $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
      $update->bind_param("ss", $newHash, $email);

      if ($update->execute()) {
        $status = 'success';
        $message = "Password updated successfully.";
      } else {
        $status = 'error';
        $message = "Failed to update password.";
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change Password Status</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen px-4">

  <div class="bg-white p-8 rounded-2xl shadow-xl max-w-md w-full text-center animate-fade-in">
    <?php if ($status === 'success'): ?>
      <h2 class="text-2xl font-bold text-green-600 mb-4">✅ Success</h2>
      <p class="text-gray-800 mb-6"><?= htmlspecialchars($message) ?></p>
      <a href="account_details.php" class="text-blue-600 hover:underline text-sm">← Back to Account</a>
    <?php else: ?>
      <h2 class="text-2xl font-bold text-red-600 mb-4">❌ Error</h2>
      <p class="text-gray-800 mb-6"><?= htmlspecialchars($message) ?></p>
      <a href="change_password.php" class="text-blue-600 hover:underline text-sm">← Try Again</a>
    <?php endif; ?>
  </div>

  <style>
    @keyframes fade-in {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
      animation: fade-in 0.5s ease-out;
    }
  </style>

</body>
</html>
