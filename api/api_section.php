<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('../includes/sessions.php');
require_once('../includes/db_config.php');
require_once('../includes/functions/func_section.php');
header('Content-type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        if (isset($_GET['section_list'])) {
            $gradeLevel = isset($_GET['gradeLevel']) ? $_GET['gradeLevel'] : null;
            $studentSection = isset($_GET['studentSection']) ? $_GET['studentSection'] : null;
            // Get the active school year
            $schoolYear = getActiveSchoolYear($pdo);
            $classes = fetchClasses($pdo, $schoolYear, $gradeLevel, $studentSection);
            foreach ($classes as &$class) {
                $class['StudentCount'] = student_count($class['SectionName'], $class['SchoolYear'], $pdo);
                $class['ClassAdviser'] = getClassAdviser($class['SectionName'], $pdo);
            }
            echo json_encode(['data' => $classes]);
            exit;
        } elseif (isset($_GET['get_section_data'])) {
            $SectionId = $_GET['SectionId'];
            $response = get_section_data($pdo, $SectionId);
            echo json_encode($response);
            exit;
        } elseif (isset($_GET['student_in_section'])) {
            if (isset($_GET['SectionId']) && !empty($_GET['SectionId'])) {
                $sectionId = $_GET['SectionId'];
                $studentInSectionData = student_in_section($pdo, $sectionId);
                echo json_encode($studentInSectionData);
            } else {
                echo json_encode(['data' => []]);
            }
            exit;
        }
        break;
    case 'POST':
        $data = $_POST;
        if (isset($_GET['add_section'])) {
            echo json_encode(add_section($pdo, $data));
            exit;
        } elseif (
            isset($data['e_sectionname']) &&
            isset($data['e_gradelevel']) &&
            isset($data['e_schoolyear'])
        ) {
            if (
                empty($data['e_sectionname']) ||
                empty($data['e_gradelevel']) ||
                empty($data['e_schoolyear'])
            ) {
                echo json_encode(['success' => false, 'message' => 'Required fields are missing.']);
                exit;
            }
            $result = edit_section($pdo, $data);
            echo json_encode([
                'success' => $result['success'],
                'message' => $result['success'] ? 'Section updated successfully.' : 'Failed to update section.'
            ]);
            exit;
        } elseif (isset($_POST['fetch_section'])) {
            $sy_from = $_POST['sy_from'];
            $subjects = fetchSection($pdo, $sy_from);
            echo json_encode($subjects);
            exit;
        } elseif (isset($_POST['copy_sy_from'], $_POST['copy_sy_to'])) {
            $sy_from = $_POST['copy_sy_from'];
            $sy_to = $_POST['copy_sy_to'];
            $selected_section = json_decode($_POST['selected_section_json'] ?? '[]', true);
            if (empty($selected_section)) {
                echo json_encode(['success' => false, 'message' => 'No sections selected.']);
                exit;
            }
            try {
                $pdo->beginTransaction();
                // Fetch all sections from the source term
                $section = fetchSection($pdo, $sy_from);
                // Filter only the selected sections by SectionId
                $section = array_filter($section, function ($sec) use ($selected_section) {
                    return in_array($sec['SectionName'], $selected_section);
                });
                if (empty($section)) {
                    echo json_encode(['success' => false, 'message' => 'No matching section found to copy.']);
                    $pdo->rollBack();
                    exit;
                }
                // Copy the sections to the new term
                $result = insertCopiedSection($pdo, $section, $sy_to);
                $pdo->commit();
                echo json_encode([
                    'success' => true,
                    'message' => "Sections copied successfully! Inserted: {$result['inserted']}, Skipped: {$result['skipped']}"
                ]);
            } catch (Exception $e) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'message' => 'Failed to copy sections. ' . $e->getMessage()]);
            }
            exit;
        } elseif (isset($_GET['add_anc_ass_list'])) {
            echo json_encode(add_anc_ass_list($pdo, $data));
            exit;
        }
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        break;
}
?>