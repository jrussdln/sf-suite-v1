<?php
require_once('../includes/sessions.php');
require_once('../includes/db_config.php');
require_once('../includes/functions/func_main_dashboard.php');
header('Content-type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        if (isset($_GET['get_section_attendance'])) {
            echo json_encode(get_section_attendance($pdo));
            exit;
        } elseif (isset($_GET['get_enrolled_counts'])) {
            echo json_encode(get_enrolled_counts($pdo));
            exit;
        } elseif (isset($_GET['get_accomplishment_data'])) {
            echo json_encode(get_accomplishment_data($pdo));
            exit;
        } elseif (isset($_GET['get_top_achievers'])) {
            echo json_encode(get_top_achievers($pdo, $_GET['section']));
            exit;
        } elseif (isset($_GET['get_performance_counts'])) {
            echo json_encode(get_performance_counts($pdo));
            exit;
        } elseif (isset($_GET['student_list'])) {
            // Call the function to get the masterlist data
            $masterlistData = student_list($pdo);
            // Return the result as a JSON response
            echo json_encode($masterlistData);
            exit;
        } elseif (isset($_GET['count_enrolled'])) {
            $data = fetchEnrollmentData($pdo);
            echo json_encode($data);
            exit;
        } elseif (isset($_GET['count_user'])) {
            echo json_encode(count_user($pdo));
            exit;
        } elseif (isset($_GET['count_courses'])) {
            echo json_encode(['total_courses' => count_courses($pdo)]);
            exit;
        } elseif (isset($_GET['count_personnel'])) {
            echo json_encode(['total_personnel' => count_personnel($pdo)]);
            exit;
        } elseif (isset($_GET['count_subjects'])) {
            echo json_encode(['total_subjects' => count_subjects($pdo)]);
            exit;
        } elseif (isset($_GET['students_per_yearlevel'])) {
            echo json_encode(['students_per_yearlevel' => students_per_yearlevel($pdo)]);
            exit;
        } elseif (isset($_GET['get_section_list'])) {
            $response = get_section_list($pdo);
            echo json_encode($response);
            exit; // End script execution after response
        }
        break;
    case 'PUT':
        parse_str(file_get_contents('php://input'), $data);
        break;
    case 'POST':
        $data = $_POST;
        // Handle POST request if needed
        break;
    case 'DELETE':
        parse_str(file_get_contents("php://input"), $deleteData);
        // Handle DELETE request if needed
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        break;
}
?>