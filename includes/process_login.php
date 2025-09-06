<?php
session_start();
require_once('../includes/db_config.php');
require_once('../includes/session_config.php');
require_once('../includes/functions/func_login.php');
header('Content-type: application/json');
// Initialize login attempt variables
$max_attempts = 5;
$wait_time = 60; // 1 minute cooldown time for failed login attempts
// Extract POST data
extract($_POST);
$username = isset($username) ? trim($username) : null;
$password = isset($password) ? trim($password) : null;
$access_level = isset($access_level) ? trim($access_level) : null;
// Check if the number of login attempts has been exceeded
if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= $max_attempts) {
    // Calculate the time difference since the last login attempt
    $time_diff = time() - $_SESSION['last_attempt_time'];
    if ($time_diff < $wait_time) {
        $remaining = $wait_time - $time_diff; // time remaining in seconds
        $resp = [
            'success' => false,
            'message' => 'Too many failed login attempts. Please wait ' . $remaining . ' seconds before trying again.'
        ];
        echo json_encode($resp);
        exit;
    } else {
        // Reset the attempts count after the cooldown period has passed
        $_SESSION['login_attempts'] = 0;
    }
}
// Verify login credentials
if (verify_login_admin($pdo, $username, $password)) {
    // Get the user data from the database
    $user_data = get_user_data_admin($pdo, $username);
    if ($user_data) {
        // Store user data in session
        $_SESSION[$session_id] = $user_data;
        $_SESSION['access_level'] = $user_data['access_level'];
        $_SESSION['LAST_ACTIVITY'] = time(); // ✅ Add this line

        // Set the Identifier in session, or 'No Account' if not available
        try {
            $_SESSION['Identifier'] = !empty($user_data['Identifier']) ? $user_data['Identifier'] : 'No Account';
        } catch (Exception $e) {
            $_SESSION['Identifier'] = 'No Account';
            error_log('Error setting Identifier: ' . $e->getMessage());
        }
        // Construct the full name (with optional fields like middle name and extension)
        $_SESSION['UserFullName'] = trim(
            (!empty($user_data['UserFName']) ? htmlspecialchars($user_data['UserFName']) : '') . ' ' .
            (!empty($user_data['UserMName']) ? htmlspecialchars($user_data['UserMName']) : '') . ' ' .
            (!empty($user_data['UserLName']) ? htmlspecialchars($user_data['UserLName']) : '') . ' ' .
            (!empty($user_data['UserEName']) ? htmlspecialchars($user_data['UserEName']) : '')
        );
        // If full name is empty, set a default value
        if (empty(trim($_SESSION['UserFullName']))) {
            $_SESSION['UserFullName'] = 'No Name Available';
        }
        // **Update user_status to "Active"**
        update_user_status($pdo, $username);
        // Prepare the response to return
        $resp = [
            'success' => true,
            'message' => 'Login successful.',
            'access' => $_SESSION['access_level'],
            'Identifier' => $_SESSION['Identifier'], // Return Identifier in the response if needed
            'UserFullName' => $_SESSION['UserFullName'] // Return full name in the response if needed
        ];
        echo json_encode($resp);
        exit;
    } else {
        // Handle case where no user data was found
        $resp = [
            'success' => false,
            'message' => 'User data not found.'
        ];
        echo json_encode($resp);
        exit;
    }
} else {
    // Increase login attempts count and record the time of the failed attempt
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
    }
    $_SESSION['login_attempts']++;
    $_SESSION['last_attempt_time'] = time(); // Record the time of the failed attempt
    $resp = [
        'success' => false,
        'message' => 'Invalid credentials or You were blocked!'
    ];
    echo json_encode($resp);
    exit;
}
?>