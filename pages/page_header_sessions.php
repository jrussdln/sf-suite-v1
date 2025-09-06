<?php
session_start();
require_once('../includes/initialize.php');

date_default_timezone_set('Asia/Manila');

// Session timeout duration (in seconds)
$timeout_duration = 300; // 5 minutes (60 seconds × 5)

// Check if user is logged in
if (!isset($_SESSION['access_level'])) {
    header("location: ../index.php");
    exit();
}

// Check for session timeout
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    // Session expired - set user to inactive and destroy session
    require_once('../includes/db_config.php'); // Make sure you include DB config
    if (!empty($_SESSION['Identifier'])) {
        $identifier = $_SESSION['Identifier'];

        try {
            $stmt = $pdo->prepare("UPDATE user_tbl SET user_status = 'Inactive' WHERE Identifier = ?");
            $stmt->execute([$identifier]);
        } catch (PDOException $e) {
            error_log("🛑 Session timeout DB error: " . $e->getMessage());
        }
    }

    // Destroy session
    $_SESSION = [];
    session_destroy();

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    header("Location: ../index.php?timeout=true");
    exit();
}

// Update last activity timestamp
$_SESSION['LAST_ACTIVITY'] = time();

$access_level = $_SESSION['access_level'];
$identifier = !empty($_SESSION['Identifier']) ? htmlspecialchars($_SESSION['Identifier']) : '';
include_once(PARTIALS_PATH . 'header.php');
?>