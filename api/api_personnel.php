<?php
require_once('../includes/sessions.php');
require_once('../includes/db_config.php');
require_once('../includes/functions/func_personnel.php');
header('Content-type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        if (isset($_GET['personnel_list'])) {
            $personnelSex = $_GET['personnelSex'] ?? null;
            $empStatus = $_GET['empStatus'] ?? null;
            $plaPos = $_GET['plaPos'] ?? null;
            $personnelListData = personnel_list($pdo, $personnelSex, $empStatus, $plaPos);
            echo json_encode($personnelListData);
            exit;
        } elseif (isset($_GET['get_personnel_data'])) {
            $PersonnelId = $_GET['PersonnelId'];
            $personnelData = get_personnel_data($pdo, $PersonnelId);
            if ($personnelData) {
                echo json_encode(['success' => true, 'data' => $personnelData]);
            } else {
                echo json_encode(['success' => false, 'message' => 'User not found.']);
            }
            exit;
        } elseif (isset($_GET['get_assignment_list'])) {
            echo json_encode(get_assignment_list($pdo));
            exit;
        } elseif (isset($_GET['get_sy_list'])) {
            echo json_encode(get_sy_list($pdo));
            exit;
        } elseif (isset($_GET['get_plantilla_list_details'])) {
            echo json_encode(get_plantilla_list_details($pdo));
            exit;
        } elseif (isset($_GET['get_subject_taught_data'])) {
            $PersonnelId = $_GET['PersonnelId'];
            $response = get_subject_taught_data($pdo, $PersonnelId);
            echo json_encode($response);
            exit;
        } elseif (isset($_GET['get_personnel_name'])) {
            // Check if PersonnelId is provided
            if (isset($_GET['PersonnelId'])) {
                $PersonnelId = $_GET['PersonnelId'];
                // Fetch the personnel data
                $response = get_personnel_name($pdo, $PersonnelId);
                echo json_encode($response);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'PersonnelId is required.']);
                exit;
            }
        } elseif (isset($_GET['get_anc_assignment_data'])) {
            $PersonnelId = $_GET['PersonnelId'];
            $response = get_anc_assignment_data($pdo, $PersonnelId);
            if ($response === false) {
                echo json_encode(['success' => false, 'message' => 'Error fetching data']);
            } else {
                echo json_encode(['success' => true, 'data' => $response]);
            }
            exit;
        } elseif (isset($_GET['get_subt_details'])) {
            $stac_id = $_GET['stac_id'];
            $response = get_subt_details($pdo, $stac_id);
            echo json_encode($response);
            exit;
        } elseif (isset($_GET['get_anc_ass_list_details'])) {
            $response = get_anc_ass_list_details($pdo);
            echo json_encode($response);
            exit;
        } elseif (isset($_GET['get_sections_list_filtered'])) {
            $PersonnelId = $_GET['PersonnelId'];
            echo json_encode(get_sections_list_filtered($pdo, $PersonnelId));
            exit;
        } elseif (isset($_GET['get_section_list'])) {
            echo json_encode(get_section_list($pdo));
            exit;
        } elseif (isset($_GET['get_sections_list'])) {
            echo json_encode(get_section_list($pdo));
            exit;
        } elseif (isset($_GET['get_ppl_list'])) {
            echo json_encode(get_ppl_list($pdo));
            exit;
        } elseif (isset($_GET['get_plantilla_data'])) {
            $PersonnelId = $_GET['PersonnelId'];
            if (empty($PersonnelId)) {
                echo json_encode(['success' => false, 'message' => 'PersonnelId is required']);
                exit;
            }
            $response = get_plantilla_data($pdo, $PersonnelId);
            echo json_encode(['success' => true, 'data' => $response]);
            exit;
        } elseif (isset($_GET['get_subject_code_list'])) {
            $response = get_subject_code_list($pdo);
            echo json_encode($response);
            exit; // End script execution after response
        } elseif (isset($_GET['get_subject2'])) {
            $response = get_subject2($pdo);
            echo json_encode($response);
            exit; // End script execution after response
        } elseif (isset($_GET['get_plantilla_details'])) {
            $pp_id = $_GET['pp_id'];
            $response = get_plantilla_details($pdo, $pp_id);
            echo json_encode($response);
            exit;
        } elseif (isset($_GET['get_plantilla_list_detailss'])) {
            $ppl_id = $_GET['ppl_id'];
            if (empty($ppl_id)) {
                echo json_encode(['success' => false, 'message' => 'PersonnelId is required']);
                exit;
            }
            $response = get_plantilla_list_detailss($pdo, $ppl_id);
            echo json_encode(['success' => true, 'data' => $response]);
            exit;
        }
        break;
    case 'POST':
        $data = $_POST;
        if (isset($_GET['add_personnel'])) {
            echo json_encode(add_personnel($pdo, $data));
            exit;
        } elseif (isset($_GET['smartCopy_subject_taught'])) {
            // Check if PersonnelId is set and not empty
            if (!isset($_POST['PersonnelId']) || empty($_POST['PersonnelId'])) {
                echo json_encode(['status' => 'error', 'message' => 'Personnel ID is required.']);
                exit;
            }
            $PersonnelId = $_POST['PersonnelId']; // Use POST instead of GET
            // Call the function and return the response in JSON format
            $response = smartCopy_subject_taught($pdo, $PersonnelId);
            // Ensure the response is in JSON format
            echo json_encode($response);
            exit;
        } elseif (isset($_GET['add_plantilla'])) {
            echo json_encode(add_plantilla($pdo, $data));
            exit;
        } elseif (isset($_GET['add_subject_taught'])) {
            echo json_encode(add_subject_taught($pdo, $data));
            exit;
        } elseif (isset($_GET['add_anc_ass_list'])) {
            echo json_encode(add_anc_ass_list($pdo, $data));
            exit;
        } elseif (isset($_GET['delete_anc_ass_list'])) {
            $id = $_POST['id'];
            echo json_encode(delete_anc_ass_list($pdo, $id));
            exit;
        } elseif (isset($_GET['delete_anc_ass'])) {
            $id = $_POST['id'];
            echo json_encode(delete_anc_ass($pdo, $id));
            exit;
        } elseif (isset($_GET['update_subject_taught'])) {
            $data = $_POST;
            echo json_encode(update_subject_taught($pdo, $data));
            exit;
        } elseif (isset($_GET['update_plantilla'])) {
            $data = $_POST;
            echo json_encode(update_plantilla($pdo, $data));
            exit;
        } elseif (isset($_GET['update_plantilla_list'])) {
            $data = $_POST;
            echo json_encode(update_plantilla_list($pdo, $data));
            exit;
        } elseif (isset($_GET['create_account'])) {
            $data = $_POST;
            echo json_encode(create_account($pdo, $data));
            exit;
        } elseif (isset($data['e_EmpNo']) && isset($data['e_lname']) && isset($data['e_fname'])) {
            $result = edit_personnel($pdo, $data);
            echo json_encode([
                'success' => $result['success'],
                'message' => $result['success'] ? 'Personnel updated successfully.' : 'Failed to update personnel.'
            ]);
            exit;
        } elseif (isset($_GET['add_anc_assignment'])) {
            $response = add_anc_assignment($pdo, $data);
            echo json_encode($response);
            exit;
        } elseif (isset($_GET['add_plantilla_list'])) {
            $response = add_plantilla_list($pdo, $data);
            echo json_encode($response);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
            exit;
        }
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        break;
}
?>