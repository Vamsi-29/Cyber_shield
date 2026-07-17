<?php
// ==== CONFIG ====
$BREVO_API_KEY = "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"; // Replace with your Brevo API key

// ==== DATABASE CONNECTION ====
$local = true; // set false when deploying online

if ($local) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "cyber_shield"; // your local DB name
} else {
    $servername = "sql300.infinityfree.com"; // your InfinityFree host
    $username = "if0_39876964";      // your InfinityFree username
    $password = "Bruce1929";        // your InfinityFree DB password
    $database = "if0_39876964_users";   // your InfinityFree DB name
}

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ==== COLLECT FORM DATA ====
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = password_hash($_POST['password'] ?? '', PASSWORD_BCRYPT);
$otp = rand(100000, 999999);

// ==== BASIC VALIDATION ====
if (empty($name) || empty($email) || empty($_POST['password'])) {
    echo "All fields are required.";
    exit;
}

// ==== CHECK IF EMAIL EXISTS ====
$sql = "SELECT id FROM users WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo "Email already registered.";
    exit;
}
$stmt->close();

// ==== INSERT NEW USER ====
$sql = "INSERT INTO users (full_name, email, password, otp_code, is_verified) VALUES (?, ?, ?, ?, 0)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $name, $email, $password, $otp);

if (!$stmt->execute()) {
    echo "DB Insert Error: " . $stmt->error;
    exit;
}
$stmt->close();

// ==== SEND OTP VIA BREVO ====
$payload = [
    "sender" => ["name" => "Cyber Shield", "email" => "team.cybershield.otp@gmail.com"],
    "to" => [["email" => $email]],
    "subject" => "Your OTP Code",
    "htmlContent" => "
        Hello " . htmlspecialchars($name) . ",<br><br>
        Your OTP is <b>$otp</b>.<br><br>
        Regards,<br>Cyber Shield Team
    "
];

$ch = curl_init("https://api.brevo.com/v3/smtp/email");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "accept: application/json",
    "api-key: $BREVO_API_KEY",
    "content-type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

$response = curl_exec($ch);

if ($response === false) {
    echo "Error sending OTP: " . curl_error($ch);
} else {
    $resData = json_decode($response, true);
    if (isset($resData['messageId'])) {
        echo "Registration successful. OTP sent to your email.";
    } else {
        echo "Registration saved, but OTP email failed. Response: " . $response;
    }
}

curl_close($ch);
$conn->close();
header("Location: verify.php?email=$email");
?>
