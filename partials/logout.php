<?php
session_start();

require_once('../includes/session_config.php');
require_once('../includes/db_config.php');

if (!isset($_SESSION['Identifier'])) {
    die("Unauthorized access.");
}

$identifier = $_SESSION['Identifier'];

try {
    $stmt = $pdo->prepare("UPDATE user_tbl SET user_status = 'Inactive' WHERE Identifier = ?");
    $stmt->execute([$identifier]);
} catch (PDOException $e) {
    // Optional: Redirect to error page or log silently in production
    die("An error occurred. Please try again later.");
}

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

header('Location: ../index.php');
exit;
