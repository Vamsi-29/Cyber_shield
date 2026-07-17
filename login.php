<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "cyber_shield");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user info
    $stmt = $mysqli->prepare("SELECT id, password, is_verified FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($user_id, $hashed_password, $is_verified);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            if ($is_verified == 1) {
                $_SESSION['user_id'] = $user_id;
                $_SESSION['email'] = $email;
                echo "<script>
                  sessionStorage.setItem('user_email','$email');
                  location.href = 'index.html';
                </script>";
                exit;
            } else {
                $error = "Please verify your email using the OTP sent.";
            }
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "No account found with this email.";
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
  <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-sm">
    <h2 class="text-2xl font-bold text-blue-600 mb-4 text-center">Login to Cyber Shield</h2>
    
    <?php if (!empty($error)): ?>
      <p class="text-red-600 text-center font-semibold mb-4"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
      <label class="block text-gray-700 mb-1">Email</label>
      <input type="email" name="email" required
             class="w-full p-2 border border-gray-300 rounded mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
      
      <label class="block text-gray-700 mb-1">Password</label>
      <input type="password" name="password" required
             class="w-full p-2 border border-gray-300 rounded mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500">

      <button type="submit"
              class="bg-blue-600 text-white w-full py-2 rounded hover:bg-blue-700 transition">
        Login
      </button>
    </form>
  </div>
</body>
</html>

