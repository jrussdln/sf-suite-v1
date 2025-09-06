<?php
require_once('../includes/sessions.php');
require_once('../includes/db_config.php');
require_once('../includes/functions/func_school_forms.php');
header('Content-type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        if (isset($_GET['sync_hnr'])) {
            if (!isset($_GET['SectionName']) || empty($_GET['SectionName']) || !isset($_GET['SectionId'])) {
                echo json_encode(['success' => false, 'message' => 'Section name and ID are required.']);
                exit;
            }
            $sectionName = htmlspecialchars($_GET['SectionName']);
            $sectionId = (int) $_GET['SectionId'];
            try {
                $students = getStudentsBySectionName($pdo, $sectionName);
                if (!empty($students)) {
                    $hnrData = [];
                    foreach ($students as $student) {
                        if (insertHnrRecord($pdo, $student['lrn'], $student['section'], $student['school_year'], $sectionId)) {
                            $hnrData[] = [
                                'lrn' => $student['lrn'],
                                'section' => $student['section'],
                                'school_year' => $student['school_year']
                            ];
                        }
                    }
                    echo json_encode(['success' => true, 'data' => $students, 'learning_materials' => $hnrData]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No students available.']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Failed to sync learning materials.', 'error' => $e->getMessage()]);
            }
            exit;
        } elseif (isset($_GET['get_hnr_data'])) {
            $SectionId = $_GET['SectionId'];
            // Call the function to get curriculum data
            $response = get_hnr_data($pdo, $SectionId);
            // Return the response as JSON
            echo json_encode($response);
            exit;
        } elseif (isset($_GET['get_hnrr'])) {
            $health_nutrition_id = $_GET['health_nutrition_id']; // Get the ID from the request
            // Call the function to get health and nutrition records by ID
            $response = get_hnrr($pdo, $health_nutrition_id);
            // Return the response as JSON
            echo json_encode($response);
            exit;
        } elseif (isset($_GET['get_hnr_by_section'])) {
            $sectionId = isset($_GET['sectionId']) && trim($_GET['sectionId']) !== '' ? $_GET['sectionId'] : null;
            $hnrData = get_hnr_by_section($pdo, $sectionId);
            if (!empty($hnrData)) {
                foreach ($hnrData as &$record) {
                    $record['name'] = get_student_name_by_lrn($pdo, $record['lrn']);
                }
                echo json_encode(['success' => true, 'data' => $hnrData]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No health and nutrition records found for the selected class.']);
            }
            exit;
        } elseif (isset($_GET['get_section_data_hnr'])) {
            $SectionId = $_GET['SectionId'];
            // Call the function to get curriculum data
            $response = get_section_data_hnr($pdo, $SectionId);
            // Return the response as JSON
            echo json_encode($response);
            exit;
        }
        break;
    case 'POST':
        $data = $_POST; // Get the posted data
        error_log(print_r($data, true)); // Log the incoming data for debugging
        if (isset($_GET['update_hnr'])) {
            try {
                // Get the health nutrition data from the POST request
                $data = $_POST;
                // Retrieve the necessary fields from the POST data
                $healthNutritionId = $data['hnr_health_nutrition_id'] ?? null; // Use the correct ID for the WHERE clause
                $birthdate = $data['hnr_birthdate'] ?? null;
                $age = $data['hnr_age'] ?? null;
                $weight = $data['hnr_weight'] ?? null;
                $height = $data['hnr_height'] ?? null;
                $heightSquared = $data['hnr_heightSquared'] ?? null;
                $bmi = $data['hnr_bmi'] ?? null;
                $nutritionalStatus = $data['hnr_nutritionalStatus'] ?? null;
                $heightForAge = $data['hnr_heightForAge'] ?? null;
                $remarks = $data['hnr_remarks'] ?? null;
                // Validate the required fields
                if (empty($healthNutritionId)) {
                    echo json_encode(['success' => false, 'message' => 'Health Nutrition ID is required.']);
                    exit;
                }
                // Prepare SQL for health nutrition update
                $stmt = $pdo->prepare("
                    UPDATE health_nutrition_tbl
                    SET 
                        birthdate = :birthdate,
                        age = :age,
                        weight = :weight,
                        height = :height,
                        height_squared = :heightSquared,
                        bmi = :bmi,
                        nutritional_status = :nutritionalStatus,
                        height_for_age = :heightForAge,
                        remarks = :remarks
                    WHERE health_nutrition_id = :healthNutritionId
                ");
                // Bind parameters
                $stmt->bindParam(':healthNutritionId', $healthNutritionId, PDO::PARAM_INT);
                $stmt->bindParam(':birthdate', $birthdate, PDO::PARAM_STR);
                $stmt->bindParam(':age', $age, PDO::PARAM_INT);
                $stmt->bindParam(':weight', $weight, PDO::PARAM_STR);
                $stmt->bindParam(':height', $height, PDO::PARAM_STR);
                $stmt->bindParam(':heightSquared', $heightSquared, PDO::PARAM_STR);
                $stmt->bindParam(':bmi', $bmi, PDO::PARAM_STR);
                $stmt->bindParam(':nutritionalStatus', $nutritionalStatus, PDO::PARAM_STR);
                $stmt->bindParam(':heightForAge', $heightForAge, PDO::PARAM_STR);
                $stmt->bindParam(':remarks', $remarks, PDO::PARAM_STR);
                // Execute the statement
                $stmt->execute();
                // Check if any rows were updated
                if ($stmt->rowCount() > 0) {
                    echo json_encode(['success' => true, 'message' => 'Health nutrition data updated successfully.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No rows updated. Please check if the data is different from the existing data.']);
                }
            } catch (PDOException $e) {
                error_log("Database error: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'An error occurred while updating data.']);
            } catch (Exception $e) {
                error_log("General error: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'An unexpected error occurred: ' . $e->getMessage()]);
            }
            exit;
        }
    case 'DELETE':
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        break;
}
?>