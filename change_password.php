<?php
session_start();
if (!isset($_SESSION['email'])) {
  header("Location: login.html");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change Password</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <form method="POST" action="process_change_password.php" class="bg-white p-8 rounded-xl shadow-md w-full max-w-sm">
    <h2 class="text-2xl font-bold text-blue-600 mb-4 text-center">Change Password</h2>

    <label class="block mb-2 font-medium">Current Password</label>
    <input type="password" name="current_password" required class="w-full p-2 border rounded mb-4">

    <label class="block mb-2 font-medium">New Password</label>
    <input type="password" name="new_password" required class="w-full p-2 border rounded mb-4">

    <label class="block mb-2 font-medium">Confirm New Password</label>
    <input type="password" name="confirm_password" required class="w-full p-2 border rounded mb-4">

    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-full hover:bg-blue-700">
      Update Password
    </button>

    <div class="text-center mt-4">
      <a href="account_details.php" class="text-sm text-blue-600 hover:underline">← Back to Account</a>
    </div>
  </form>
</body>
</html>
