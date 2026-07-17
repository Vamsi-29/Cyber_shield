<?php
session_start();

// Check if user is logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['email'])) {
    // Return a JSON response instead of plain text for easier handling in JS
    echo json_encode([
        'status' => 'logged_in',
        'email' => $_SESSION['email']
    ]);
} else {
    echo json_encode([
        'status' => 'not_logged_in'
    ]);
}
?>
