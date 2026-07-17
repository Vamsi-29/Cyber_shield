<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit();
}

$email = $_SESSION['email'];
$message = "";

// ==== FETCH USER DETAILS ====
$stmt = $conn->prepare("SELECT full_name, email, is_verified, created_at FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $message = "<p class='text-red-600 font-semibold'>❌ No account found.</p>";
} else {
    $user = $result->fetch_assoc();
}
$stmt->close();

// Convert is_verified to human readable
$status = isset($user['is_verified']) && $user['is_verified'] == 1 ? "✅ Verified" : "❌ Not Verified";

// Format date
$reg_date = isset($user['created_at']) ? date("d M Y, H:i", strtotime($user['created_at'])) : "-";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Account Details - Cyber Shield</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">

<div class="bg-white shadow-lg rounded-2xl p-8 w-full max-w-md border border-gray-200">
    <h1 class="text-2xl font-bold text-blue-600 mb-6 text-center">👤 Account Details</h1>

    <?php if ($message): ?>
        <div class="mb-4 text-center"><?= $message ?></div>
    <?php else: ?>
        <div class="mb-6 text-left space-y-3 text-lg">
            <div>
                <span class="font-semibold text-gray-700">Full Name:</span>
                <p class="text-gray-900"><?= htmlspecialchars($user['full_name']) ?></p>
            </div>
            <div>
                <span class="font-semibold text-gray-700">Email:</span>
                <p class="text-gray-900"><?= htmlspecialchars($user['email']) ?></p>
            </div>
            <div>
                <span class="font-semibold text-gray-700">Registration Date:</span>
                <p class="text-gray-900"><?= $reg_date ?></p>
            </div>
            <div>
                <span class="font-semibold text-gray-700">Verification Status:</span>
                <p class="text-gray-900"><?= $status ?></p>
            </div>
        </div>
    <?php endif; ?>

    <div class="mt-6 flex justify-between items-center">
        <a href="index.html" class="text-blue-600 hover:underline text-sm">← Back to Help Desk</a>
        <a href="change_password.php" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 text-sm">Change Password</a>
    </div>
</div>

</body>
</html>
