<?php
require_once('../includes/sessions.php');
require_once('../includes/db_config.php');
require_once('../includes/functions/func_learning_material.php');
header('Content-type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        if (isset($_GET['sync_lm'])) {
            if (!isset($_GET['SectionName']) || empty($_GET['SectionName']) || !isset($_GET['SectionId'])) {
                echo json_encode(['success' => false, 'message' => 'Section name and ID are required.']);
                exit;
            }
            $sectionName = htmlspecialchars($_GET['SectionName']);
            $sectionId = (int) $_GET['SectionId'];
            try {
                $students = getStudentsBySectionName($pdo, $sectionName);
                if (!empty($students)) {
                    $learningMaterialData = [];
                    foreach ($students as $student) {
                        if (insertLmRecord($pdo, $student['lrn'], $student['section'], $student['school_year'], $sectionId)) {
                            $learningMaterialData[] = [
                                'lrn' => $student['lrn'],
                                'section' => $student['section'],
                                'school_year' => $student['school_year']
                            ];
                        }
                    }
                    echo json_encode(['success' => true, 'data' => $students, 'learning_materials' => $learningMaterialData]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No students available.']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Failed to sync learning materials.', 'error' => $e->getMessage()]);
            }
            exit;
        } elseif (isset($_GET['get_lm_by_section'])) {
            $sectionId = $_GET['sectionId'];
            // Fetch learning materials by class name
            $response = get_lm_by_section($pdo, $sectionId);
            if (!empty($response)) {
                echo json_encode(['success' => true, 'data' => $response]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No learning materials found for the selected class.']);
            }
            exit;
        } elseif (isset($_GET['action'])) {
            if ($_GET['action'] == 'get_learning_materials_by_ids') {
                $ids = $_GET['ids']; // This will be an array of IDs
                $placeholders = rtrim(str_repeat('?,', count($ids)), ','); // Create placeholders for the query
                // Fetch the learning materials from the database using the IDs
                $stmt = $pdo->prepare("SELECT * FROM learning_material_tbl WHERE learning_material_id IN ($placeholders)");
                $stmt->execute($ids);
                $learningMaterials = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($learningMaterials) {
                    echo json_encode(['success' => true, 'data' => $learningMaterials]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No learning materials found for the selected IDs.']);
                }
            }
        } elseif (isset($_GET['get_lm_details'])) {
            $lm_id = isset($_GET['id']) ? intval($_GET['id']) : 0; // Get the learning material ID from the request
            if ($lm_id > 0) {
                $response = get_lm_details($pdo, $lm_id);
                echo json_encode($response); // Return the response as JSON
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid learning material ID.']);
            }
        } elseif (isset($_GET['checkStatus'])) {
            $response = check_status($pdo);
            echo json_encode($response);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        }
        break;
    case 'POST':
        $data = $_POST; // Get the posted data
        error_log(print_r($data, true)); // Log the incoming data for debugging
        if (isset($_GET['update_return'])) {
            error_log("Received update_return request");
            $response = update_return($pdo, $data);
            echo json_encode($response);
            exit;
        } elseif (isset($_GET['enableDistribution'])) {
            $response = enable_distribution($pdo, $data);
            echo json_encode($response);
            exit;
        } elseif (isset($_GET['disableDistribution'])) {
            $response = disable_distribution($pdo, $data);
            echo json_encode($response);
            exit;
        } elseif (isset($_GET['update_learning_material'])) {
            // Capture POST data with validation
            $learning_material_ids = isset($_POST['learning_material_ids']) ? explode(',', $_POST['learning_material_ids']) : [];
            $descriptions = [];
            for ($i = 1; $i <= 9; $i++) {
                $descriptions[] = isset($_POST["Desc$i"]) ? $_POST["Desc$i"] : null;
            }

            // Check if learning_material_ids is valid
            if (empty($learning_material_ids)) {
                echo json_encode(["success" => false, "message" => "Learning material IDs are required."]);
                exit;
            }

            // Call the core function to update learning materials
            $result = updateLearningMaterials($pdo, $learning_material_ids, $descriptions);

            // Return the result as JSON
            echo json_encode($result);
            exit;
        }
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        break;
}
?>
