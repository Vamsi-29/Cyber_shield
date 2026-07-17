<?php
header('Content-Type: application/json; charset=utf-8');

$password = $_POST['password'] ?? $_GET['password'] ?? '';
$password = (string)$password;

if ($password === '') {
    echo json_encode(['error' => 'No password provided']);
    exit;
}

// checks
$len = strlen($password);
$hasLower = preg_match('/[a-z]/', $password);
$hasUpper = preg_match('/[A-Z]/', $password);
$hasNum = preg_match('/[0-9]/', $password);
$hasSpec = preg_match('/[\W_]/', $password);

// scoring
$score = 0;
if ($len >= 8) $score += 1;
if ($len >= 12) $score += 1;
if ($hasLower) $score += 1;
if ($hasUpper) $score += 1;
if ($hasNum) $score += 1;
if ($hasSpec) $score += 1;

// classify
if ($score <= 2) $strength = 'Very Weak';
elseif ($score <= 3) $strength = 'Weak';
elseif ($score <= 4) $strength = 'Moderate';
elseif ($score <= 5) $strength = 'Strong';
else $strength = 'Very Strong';

// heuristic crack time estimate (very rough)
$entropy = 0;
$pool = 0;
if ($hasLower) $pool += 26;
if ($hasUpper) $pool += 26;
if ($hasNum) $pool += 10;
if ($hasSpec) $pool += 32;

if ($pool > 0) {
    $entropy = log(pow($pool, max($len,1)), 2); // bits
}

// map entropy to human crack estimate
if ($entropy < 28) $crack_time = 'Instantly (seconds)';
elseif ($entropy < 40) $crack_time = 'Minutes to hours';
elseif ($entropy < 60) $crack_time = 'Days to years';
elseif ($entropy < 80) $crack_time = 'Decades';
else $crack_time = 'Centuries (very strong)';

// details
$details = [];
$details[] = "Length: {$len} characters";
$details[] = $hasUpper ? "Contains uppercase letters" : "Missing uppercase letters";
$details[] = $hasLower ? "Contains lowercase letters" : "Missing lowercase letters";
$details[] = $hasNum ? "Contains numeric characters" : "Missing numbers";
$details[] = $hasSpec ? "Contains special characters" : "Missing special characters";
$details[] = "Entropy (approx): " . round($entropy, 1) . " bits";

$result = [
    'strength' => $strength,
    'score' => $score,
    'details' => $details,
    'crack_time' => $crack_time
];

echo json_encode($result);
