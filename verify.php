<?php
session_start();

// ==== DATABASE CONNECTION ====
$mysqli = new mysqli("localhost", "root", "", "cyber_shield");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$message = "";

// Get email from session or fallback to GET
$email = $_SESSION['email_for_verification'] ?? ($_GET['email'] ?? '');

// Stop if no email
if (!$email) {
    die("<p class='text-red-600 font-semibold'>No email found. Please register first.</p>");
}

// Handle OTP submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_otp = trim($_POST['otp'] ?? '');

    if (!empty($entered_otp)) {
        // Fetch OTP and verification status from DB
        $stmt = $mysqli->prepare("SELECT otp_code, is_verified FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result(); // Prevent "commands out of sync"
        $stmt->bind_result($otp, $is_verified);

        if ($stmt->fetch()) {
            // Close SELECT before running UPDATE
            $stmt->close();

            if ($is_verified == 1) {
                $message = "<p class='text-green-600 font-semibold'>✅ Email already verified. <a href='login.html' class='underline text-blue-600'>Login</a></p>";
            } elseif ($entered_otp == $otp) {
                // Correct OTP → update verification
                $update = $mysqli->prepare("UPDATE users SET is_verified = 1 WHERE email = ?");
                $update->bind_param("s", $email);
                $update->execute();
                $update->close();

                $message = "<p class='text-green-600 font-semibold'>✅ Email verified! Redirecting to login...</p>";

                // Clear session
                unset($_SESSION['email_for_verification']);

                // Redirect to login after 3 seconds
                header("refresh:3;url=login.html");
            } else {
                $message = "<p class='text-red-600 font-semibold'>❌ Incorrect OTP. Please try again.</p>";
            }
        } else {
            $stmt->close();
            $message = "<p class='text-red-600 font-semibold'>❌ Email not found. Please register again.</p>";
        }
    } else {
        $message = "<p class='text-red-600 font-semibold'>❌ OTP is required.</p>";
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Verify OTP - Cyber Shield</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white min-h-screen flex items-center justify-center px-4">
  <div class="bg-white shadow-lg rounded-2xl p-8 w-full max-w-md border border-gray-200">
    <h1 class="text-2xl font-bold text-blue-600 mb-6 text-center">Cyber Shield: Verify Your Email</h1>

    <?php if ($message): ?>
      <div class="mb-4 text-center"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
      <label for="otp" class="block mb-2 text-gray-700 font-medium">Enter the OTP sent to your email:</label>
      <input type="text" id="otp" name="otp"
             class="w-full px-4 py-3 border border-gray-300 rounded-xl mb-6 focus:outline-none focus:ring-2 focus:ring-blue-500"
             placeholder="6-digit OTP" required>

      <button type="submit"
              class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-xl font-semibold transition">
        Verify OTP
      </button>
    </form>

    <p class="text-sm text-center text-gray-500 mt-6">
      Didn't get the OTP? Please <a href="register.html" class="text-blue-600 underline">register again</a>.
    </p>
  </div>
</body>
</html>
