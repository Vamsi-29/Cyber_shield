<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "cyber_shield");

if (!isset($_SESSION['user_id'])) {
    echo "Access denied.";
    exit;
}

$user_id = $_SESSION['user_id'];

// Confirm deletion via POST only
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    session_unset();
    session_destroy();

    echo "<script>alert('Your account has been permanently deleted.');
    window.location.href = 'index.html';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Delete Account – Cyber Shield</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen flex items-center justify-center">
  <div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-md">
    <h2 class="text-2xl font-bold text-red-600 mb-4 text-center">Delete Your Account</h2>

    <p class="text-gray-700 text-sm mb-6 text-center">
      This action is <span class="font-semibold">permanent</span> and will remove all your data from the Cyber Shield system. Are you sure you want to continue?
    </p>

    <form method="POST" class="flex flex-col gap-4">
      <button type="submit" class="bg-red-600 text-white py-2 rounded-xl hover:bg-red-700 transition duration-200">
        Yes, Delete My Account
      </button>
      <a href="index.html" class="text-center text-blue-600 hover:underline text-sm">Cancel and go back</a>
    </form>
  </div>
</body>
</html>
