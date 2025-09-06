<?php
require_once('../includes/db_config.php');
header('Content-Type: application/json');
// Read JSON input
$input = json_decode(file_get_contents('php://input'), true);
$identifier = $input['identifier'] ?? '';
if (!$identifier) {
    echo json_encode(['success' => false, 'message' => 'No identifier provided']);
    exit;
}
try {
    $stmt = $pdo->prepare("SELECT Identifier, UserLName, UserFName, UserMName, UserEName, Gender, BirthDate, email FROM user_tbl WHERE Identifier = :identifier LIMIT 1");
    $stmt->execute(['identifier' => $identifier]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
