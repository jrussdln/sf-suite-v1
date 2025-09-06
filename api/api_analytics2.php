<?php
require_once('../includes/sessions.php');
require_once('../includes/db_config.php');
header('Content-type: application/json');
// We expect a POST request with the query parameter "get_user_info" set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['get_user_info'])) {
    $lrn = isset($_POST['lrn']) ? trim($_POST['lrn']) : '';
    if (empty($lrn)) {
        echo json_encode(null);
        exit;
    }
    // Query joining user_tbl and student_tbl so that we get the student's section and location info
    // Location info is retrieved from student_tbl columns: hssp, barangay, municipality_city, province
    $query = "
    SELECT 
        u.*, 
        stu.section, 
        stu.lrn,
        CONCAT_WS(', ', stu.hssp, stu.barangay, stu.municipality_city, stu.province) AS location
    FROM user_tbl u
    LEFT JOIN student_tbl stu ON u.identifier = stu.lrn
    WHERE u.identifier = :lrn
    LIMIT 1
";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['lrn' => $lrn]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($userData);
    exit;
} else {
    echo json_encode(['error' => 'Invalid Request']);
    exit;
}
?>