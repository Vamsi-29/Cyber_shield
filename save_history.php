<?php
// save_history.php
header('Content-Type: application/json; charset=utf-8');

// Read JSON body
$body = file_get_contents('php://input');
$data = json_decode($body, true);
if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
    exit;
}

// sanitize incoming values
$user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
$scenario_id = substr($data['scenario_id'] ?? ($data['scenario'] ?? 'unknown'), 0, 100);
$simulation_type = substr($data['simulation_type'] ?? 'unknown', 0, 50);
$action_taken = substr($data['action'] ?? $data['action_taken'] ?? 'unknown', 0, 255);
$result = substr($data['result'] ?? 'unknown', 0, 50);
$damage_estimate = isset($data['damage_estimate']) ? substr((string)$data['damage_estimate'],0,255) : null;

// --- DB connection: replace with your config or existing $pdo ---
// Example using PDO (MySQL)
try {
    // Replace these values with your DB credentials OR use your app's existing PDO object
    $host = '127.0.0.1';
    $db   = 'cyber_shield';
    $user = 'root';
    $pass = '';
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    // If DB connection fails, still return success to avoid breaking the simulator UI (best-effort)
    error_log("DB connect failed: " . $e->getMessage());
    echo json_encode(['status' => 'warning', 'message' => 'DB connection failed, result kept locally']);
    exit;
}

// Insert into simulator_history
try {
    $stmt = $pdo->prepare("INSERT INTO simulator_history (user_id, scenario_id, simulation_type, action_taken, result, damage_estimate) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $scenario_id, $simulation_type, $action_taken, $result, $damage_estimate]);
    echo json_encode(['status' => 'ok']);
} catch (Exception $e) {
    error_log("DB insert failed: " . $e->getMessage());
    // return a soft error — UI already saved locally
    echo json_encode(['status' => 'error', 'message' => 'DB insert failed']);
}
