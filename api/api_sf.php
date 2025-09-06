<?php
require_once('../includes/sessions.php');
require_once('../includes/db_config.php');
require_once('../includes/functions/func_sf.php');
header('Content-type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        if (isset($_GET['student_list'])) {
            $schoolYear = $_GET['schoolYear'] ?? null;
            $gradeLevel = $_GET['gradeLevel'] ?? null;
            $studentSex = $_GET['studentSex'] ?? null;
            $studentSection = $_GET['studentSection'] ?? null;
            $studentListData = student_list($pdo, $schoolYear, $gradeLevel, $studentSex, $studentSection);
            echo json_encode($studentListData);
            exit;
        } elseif (isset($_GET['get_export_record'])) {
            // Fetch and return export records as JSON
            echo json_encode(get_export_record($pdo));
            exit;
        } elseif (isset($_GET['get_attendance'])) {
            $schoolYear = $_GET['schoolYear'] ?? null;
            $gradeLevel = $_GET['gradeLevel'] ?? null;
            $studentMonth = $_GET['studentMonth'] ?? null;
            $studentSection = $_GET['studentSection'] ?? null;
            $attendanceListData = get_attendance($pdo, $schoolYear, $gradeLevel, $studentSection, $studentMonth);
            echo json_encode($attendanceListData);
            exit;
        } elseif (isset($_GET['get_lm'])) {
            $schoolYear = $_GET['schoolYear'] ?? null;
            $gradeLevel = $_GET['gradeLevel'] ?? null;
            $studentSex = $_GET['studentSex'] ?? null;
            $studentSection = $_GET['studentSection'] ?? null;
            $lmListData = get_lm($pdo, $schoolYear, $gradeLevel, $studentSex, $studentSection);
            echo json_encode($lmListData);
            exit;
        } elseif (isset($_GET['hnr_list'])) {
            $schoolYear = $_GET['schoolYear'] ?? null;
            $gradeLevel = $_GET['gradeLevel'] ?? null;
            $studentSex = $_GET['studentSex'] ?? null;
            $studentSection = $_GET['studentSection'] ?? null;
            $hnrListData = get_hnr($pdo, $schoolYear, $gradeLevel, $studentSex, $studentSection);
            echo json_encode($hnrListData);
            exit;
        } elseif (isset($_GET['get_personnel'])) {
            $personnelListData = get_personnel($pdo, $_GET);
            echo json_encode($personnelListData);
            exit;
        } elseif (isset($_GET['promotion_list'])) {
            $schoolYear = $_GET['schoolYear'] ?? null;
            $gradeLevel = $_GET['gradeLevel'] ?? null;
            $studentSex = $_GET['studentSex'] ?? null;
            $studentSection = $_GET['studentSection'] ?? null;
            $promotionListData = promotion_list($pdo, $schoolYear, $gradeLevel, $studentSex, $studentSection);
            echo json_encode($promotionListData);
            exit;
        } elseif (isset($_GET['rc_grade'])) {
            $schoolYear = $_GET['schoolYear'] ?? null;
            $lrn = $_GET['lrn'] ?? null;
            $promotionListData = rc_grade($pdo, $schoolYear, $lrn);
            echo json_encode($promotionListData);
            exit;
        } elseif (isset($_GET['get_student_grade_level'])) {
            // Retrieve the grade level of a student from student_tbl based on LRN and schoolYear
            $lrn = $_GET['lrn'] ?? null;
            $schoolYear = $_GET['schoolYear'] ?? null;
            if ($lrn && $schoolYear) {
                $stmt = $pdo->prepare("SELECT grade_level FROM student_tbl WHERE lrn = :lrn AND school_year = :schoolYear LIMIT 1");
                $stmt->execute([
                    'lrn' => $lrn,
                    'schoolYear' => $schoolYear
                ]);
                $gradeLevel = $stmt->fetchColumn();
                if ($gradeLevel !== false) {
                    echo json_encode(['grade_level' => $gradeLevel]);
                } else {
                    echo json_encode(['error' => 'Student not found']);
                }
            } else {
                echo json_encode(['error' => 'Missing parameters']);
            }
            exit;
        }
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        break;
}
?>