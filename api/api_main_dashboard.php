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
        } elseif (isset($_GET['get_enrollment_by_year'])) {
            try {
                $stmt = $pdo->prepare("
                    SELECT 
                        school_year AS term,
                        COUNT(id) AS student_count
                    FROM 
                        student_tbl
                    WHERE 
                        school_year IS NOT NULL
                    GROUP BY 
                        school_year
                    ORDER BY 
                        school_year ASC
                ");
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($results);
            } catch (PDOException $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        } elseif (isset($_GET['get_yearlevel_achievement'])) {
            try {
                $stmt = $pdo->prepare("
                    SELECT 
                        grade_level,
                        action_taken,
                        COUNT(lrn) AS total_count
                    FROM 
                        prom_achievement_tbl
                    WHERE 
                        UPPER(action_taken) IN ('PROMOTED', 'CONDITIONAL', 'RETAINED')
                        AND grade_level BETWEEN 7 AND 12
                    GROUP BY 
                        grade_level, action_taken
                    ORDER BY 
                        grade_level ASC, action_taken ASC
                ");
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($results);
            } catch (PDOException $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        } elseif (isset($_GET['get_enrolled_counts'])) {
            echo json_encode(get_enrolled_counts($pdo));
            exit;
        } elseif (isset($_GET['get_top_achievers'])) {
            echo json_encode(get_top_achievers($pdo, $_GET['section']));
            exit;
        } elseif (isset($_GET['count_enrolled'])) {
            echo json_encode(fetchEnrollmentData($pdo));
            exit;
        } elseif (isset($_GET['count_user'])) {
            echo json_encode(count_user($pdo));
            exit;
        } elseif (isset($_GET['count_personnel'])) {
            echo json_encode(['total_personnel' => count_personnel($pdo)]);
            exit;
        } elseif (isset($_GET['count_subjects'])) {
            echo json_encode(['total_subjects' => count_subjects($pdo)]);
            exit;
        } elseif (isset($_GET['get_section_list'])) {
            echo json_encode(get_section_list($pdo));
            exit;
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        break;
}
?>
