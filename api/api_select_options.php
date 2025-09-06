<?php
require_once('../includes/sessions.php');
require_once('../includes/db_config.php');
require_once('../includes/functions/func_select_options.php');

header('Content-type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
    if (isset($_GET['get_curriculum_name'])) {
        $response = get_curriculum_name ($pdo);
        echo json_encode($response);
        exit; // End script execution after response
    } elseif (isset($_GET['get_school_year_list'])) {
        $response = get_school_year_list ($pdo);
        echo json_encode($response);
        exit; // End script execution after response
    } elseif (isset($_GET['get_school_year_list1'])) {
        $response = get_school_year_list1 ($pdo);
        echo json_encode($response);
        exit; // End script execution after response
    } elseif (isset($_GET['get_section_strand'])) {
        $response = get_section_strand($pdo);
        echo json_encode($response);
        exit;
    } elseif (isset($_GET['get_plapos_list'])) {
        $response = get_plapos_list($pdo);
        echo json_encode($response);
        exit; // End script execution after response
    } elseif (isset($_GET['get_school_curriculum'])) {
        $response = get_school_curriculum($pdo);
        echo json_encode($response);
        exit; // End script execution after response
    } elseif (isset($_GET['get_section_list'])) {
        $response = get_section_list ($pdo);
        echo json_encode($response);
        exit; // End script execution after response
    } elseif (isset($_GET['get_question_list'])) {
    $response = get_question_list ($pdo);
    echo json_encode($response);
    exit; // End script execution after response
    } elseif (isset($_GET['get_section_list_teach'])) {
        $response = get_section_list_teach($pdo);
        echo json_encode([$response]); // wrap in array to make it consistent for `$.each`
        exit;

    } elseif (isset($_GET['get_section_list_teacher'])) {
        $identifier = $_GET['identifier'] ?? null; // Get the identifier from URL
        $response = get_section_list_teacher($pdo, $identifier);
        echo json_encode($response);
        exit;
    } elseif (isset($_GET['get_section_list_subt'])) {
        $identifier = $_GET['identifier'] ?? null; // Get the identifier from URL
        $response = get_section_list_subt($pdo, $identifier);
        echo json_encode($response);
        exit;
    }
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        break;
}
?>