<?php
session_start();
require 'db_connection.php'; // Make sure this connects to your DB

if (!isset($_SESSION['user_id'])) {
    echo "<h2 class='text-center mt-20'>Please log in to view your history.</h2>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Prepared statement to fetch user queries
$stmt = $conn->prepare("SELECT message, suggestions, timestamp FROM queries WHERE user_id = ? ORDER BY timestamp DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Query History - Cyber Shield</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-8">

<div class="max-w-4xl mx-auto bg-white shadow-md rounded-2xl p-6">
    <h2 class="text-2xl font-bold text-blue-600 mb-6 text-center">📜 Your Submitted Queries</h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <ul class="space-y-4">
            <?php while ($row = $result->fetch_assoc()): ?>
                <li class="border border-gray-300 rounded-xl p-4 hover:shadow-lg transition">
                    <p class="text-gray-800 font-medium mb-2">📝 <strong>Your Query:</strong> <?= htmlspecialchars($row['message']) ?></p>
                    
                    <p class="text-sm text-gray-700 font-semibold">💡 Suggestions:</p>
                    <ul class="list-disc list-inside ml-4 text-gray-600">
                        <?php
                            $suggestions = json_decode($row['suggestions'], true);
                            if (is_array($suggestions)) {
                                foreach ($suggestions as $sugg) {
                                    echo "<li>" . htmlspecialchars($sugg) . "</li>";
                                }
                            } else {
                                echo "<li>No suggestions recorded</li>";
                            }
                        ?>
                    </ul>

                    <p class="text-xs text-gray-500 mt-2">Submitted on: <?= $row['timestamp'] ?></p>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p class="text-center text-gray-600 mt-10">You have not submitted any queries yet.</p>
    <?php endif; ?>

    <div class="text-center mt-6">
        <a href="index.html" class="text-blue-600 hover:underline">← Back to Help Desk</a>
    </div>
</div>

</body>
</html>
