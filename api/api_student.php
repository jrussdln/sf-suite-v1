<?php
require_once('../includes/sessions.php');
require_once('../includes/db_config.php');
require_once('../includes/functions/func_student.php');
header('Content-type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        if (isset($_GET['student_list'])) {
            $schoolYear = $_GET['schoolYear'] ?? null;
            $gradeLevel = $_GET['gradeLevel'] ?? null;
            $studentSex = $_GET['studentSex'] ?? null;
            $studentSection = $_GET['studentSection'] ?? null;
            $studentRemarks = $_GET['studentRemarks'] ?? null;
            $studentListData = student_list($pdo, $schoolYear, $gradeLevel, $studentSex, $studentSection, $studentRemarks);
            echo json_encode($studentListData);
            exit;
        } elseif (isset($_GET['promotion_list'])) {
            $schoolYear = $_GET['schoolYear'] ?? null;
            $gradeLevel = $_GET['gradeLevel'] ?? null;
            $studentSex = $_GET['studentSex'] ?? null;
            $studentSection = $_GET['studentSection'] ?? null;
            $studentRemarks = $_GET['studentRemarks'] ?? null;
            $promotionListData = promotion_list($pdo, $schoolYear, $gradeLevel, $studentSex, $studentSection, $studentRemarks);
            echo json_encode($promotionListData);
            exit;
        } elseif (isset($_GET['get_student_data'])) {
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                // Fetch student data using the function
                $studentData = student_data($pdo, $id);
                // Check if student data was found
                if ($studentData) {
                    echo json_encode($studentData); // Return the student data as JSON
                } else {
                    echo json_encode(['success' => false, 'message' => 'No student found with the given ID.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Student ID is required.']);
            }
            exit; // Exit after handling the request
        } elseif (isset($_GET['get_grade'])) {
            // Handle the get grade request
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                // Fetch grade data using a function
                $gradeData = get_grade($pdo, $id);
                // Check if grade data was found
                if ($gradeData) {
                    echo json_encode($gradeData); // Return the grade data as JSON
                } else {
                    echo json_encode(['success' => false, 'message' => 'No grade data found for the given student ID.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Student ID is required to fetch grade data.']);
            }
            exit; // Exit after handling the request
        } elseif (isset($_GET['get_general_average'])) {
            if (isset($_GET['lrn']) && isset($_GET['school_year'])) {
                $lrn = $_GET['lrn'];
                $school_year = $_GET['school_year'];
                $response = get_general_average($pdo, $_GET['lrn'], $_GET['school_year']);
                if (isset($response['error'])) {
                    echo json_encode(['success' => false, 'message' => $response['error']]);
                } else {
                    echo json_encode(['success' => true, 'general_average' => $response['general_average']]);
                }
                exit;
            }
        }
        break;
    case 'POST':
        // Get the posted data
        $data = $_POST;
        if (isset($_GET['update_student_data'])) {
            $id = $_POST['es_studentId'] ?? null;
            $lrn = $_POST['es_lrn'] ?? '';
            $name = $_POST['es_name'] ?? '';
            $section = $_POST['es_section'] ?? '';
            $grade_level = $_POST['es_grade_level'] ?? '';
            $school_year = $_POST['es_school_year'] ?? '';
            $birth_date = $_POST['es_birth_date'] ?? '';
            $sex = $_POST['es_gender'] ?? '';
            $contact_number = $_POST['es_contact_number'] ?? '';
            $hssp = $_POST['es_hstp'] ?? '';
            $barangay = $_POST['es_barangay'] ?? '';
            $municipality_city = $_POST['es_city'] ?? '';
            $province = $_POST['es_province'] ?? '';
            $father_name = $_POST['es_father_name'] ?? '';
            $mother_maiden_name = $_POST['es_mother_maiden_name'] ?? '';
            $guardian_name = $_POST['es_guardian_name'] ?? '';
            $guardian_relationship = $_POST['es_guardian_relationship'] ?? '';
            $learning_modality = $_POST['es_learning_modality'] ?? '';
            $remarks = $_POST['es_remarks'] ?? '';
            $mother_tongue = $_POST['es_mother_tongue'] ?? '';
            $ethnic_group = $_POST['es_ethnic_group'] ?? '';
            $religion = $_POST['es_religion'] ?? '';
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Student ID is required.']);
                exit;
            }
            try {
                $pdo->beginTransaction();
                // Fetch existing remarks
                $stmt = $pdo->prepare("SELECT remarks FROM student_tbl WHERE id = ?");
                $stmt->execute([$id]);
                $existingData = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$existingData) {
                    throw new Exception('Student record not found.');
                }
                $existingRemarks = $existingData['remarks'];
                $remarksToUpdate = ($remarks !== "" && $remarks !== $existingRemarks) ? $remarks : $existingRemarks;
                // Update student_tbl
                $stmt = $pdo->prepare("UPDATE student_tbl SET 
            lrn = ?, name = ?, section = ?, grade_level = ?, school_year = ?, birth_date = ?, sex = ?, 
            contact_number = ?, hssp = ?, barangay = ?, municipality_city = ?, province = ?, 
            father_name = ?, mother_maiden_name = ?, guardian_name = ?, guardian_relationship = ?, 
            learning_modality = ?, remarks = ?, mother_tongue = ?, ethnic_group = ?, religion = ? 
            WHERE id = ?");
                $result = $stmt->execute([
                    $lrn,
                    $name,
                    $section,
                    $grade_level,
                    $school_year,
                    $birth_date,
                    $sex,
                    $contact_number,
                    $hssp,
                    $barangay,
                    $municipality_city,
                    $province,
                    $father_name,
                    $mother_maiden_name,
                    $guardian_name,
                    $guardian_relationship,
                    $learning_modality,
                    $remarksToUpdate,
                    $mother_tongue,
                    $ethnic_group,
                    $religion,
                    $id
                ]);
                if (!$result) {
                    throw new Exception('Failed to update student data.');
                }
                // Update prom_achievement_tbl only if remarks have changed
                if ($remarks !== "" && $remarks !== $existingRemarks) {
                    $stmt = $pdo->prepare("UPDATE prom_achievement_tbl SET 
                name = ?, grade_level = ?, section = ?, school_year = ?, sex = ?, remarks = ?
                WHERE lrn = ? AND school_year = ?");
                    $stmt->execute([$name, $grade_level, $section, $school_year, $sex, $remarks, $lrn, $school_year]);
                }
                // Update attendance_tbl only if remarks have changed
                if ($remarks !== "" && $remarks !== $existingRemarks) {
                    $stmt = $pdo->prepare("SELECT attendance_remarks FROM attendance_tbl WHERE lrn = ? AND attendance_term = ?");
                    $stmt->execute([$lrn, $school_year]);
                    $attendanceData = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($attendanceData) {
                        $existingAttendanceRemarks = $attendanceData['attendance_remarks'];
                        if ($existingAttendanceRemarks !== $remarks) {
                            $newRemarks = "$remarks DATE:" . date("Y-m-d");
                            $stmt = $pdo->prepare("UPDATE attendance_tbl SET 
                        attendance_remarks = ? WHERE lrn = ? AND attendance_term = ?");
                            $stmt->execute([$newRemarks, $lrn, $school_year]);
                        }
                    }
                }
                $pdo->commit();
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
            exit;
        } elseif (isset($_GET['add_student'])) {
            echo json_encode(add_student($pdo, $data));
            exit;
        } elseif (isset($_GET['smart_promote'])) {
            echo json_encode(smart_promote($pdo));
            exit;
        } elseif (isset($_GET['update_promotion'])) {
            try {
                // Get the promotion data from the POST request
                $data = $_POST;
                // Retrieve the necessary fields from the POST data
                $pa_id = $data['ep_pa_id'] ?? null; // Use the correct ID for the WHERE clause
                $name = $data['ep_name'] ?? null;
                $section = $data['ep_section'] ?? null;
                $sex = $data['ep_sex'] ?? null;
                $general_average = $data['ep_general_average'] ?? null;
                $action_taken = $data['ep_action_taken'] ?? null;
                $ecs = $data['ep_ecs'] ?? null;
                $cecs = $data['ep_cecs'] ?? null;
                // Validate the required fields
                if (empty($pa_id)) {
                    echo json_encode(['success' => false, 'message' => 'Invalid Promotion ID.']);
                    exit;
                }
                // Prepare SQL for promotion update
                $stmt = $pdo->prepare("
                        UPDATE prom_achievement_tbl
                        SET 
                            name = :name,
                            section = :section,
                            sex = :sex,
                            general_average = :general_average,
                            action_taken = :action_taken,
                            ecs = :ecs,
                            cecs = :cecs
                        WHERE pa_id = :pa_id
                    ");
                // Bind parameters
                $stmt->bindParam(':pa_id', $pa_id, PDO::PARAM_INT);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':section', $section, PDO::PARAM_STR);
                $stmt->bindParam(':sex', $sex, PDO::PARAM_STR);
                $stmt->bindParam(':general_average', $general_average, PDO::PARAM_STR);
                $stmt->bindParam(':action_taken', $action_taken, PDO::PARAM_STR);
                $stmt->bindParam(':ecs', $ecs, PDO::PARAM_STR);
                $stmt->bindParam(':cecs', $cecs, PDO::PARAM_STR);
                // Execute the statement
                $stmt->execute();
                // Check if any rows were updated
                if ($stmt->rowCount() > 0) {
                    echo json_encode(['success' => true, 'message' => 'Promotion information updated successfully.']);
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
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        break;
}
?>