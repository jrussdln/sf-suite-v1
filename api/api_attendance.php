<?php
require_once('../includes/sessions.php');
require_once('../includes/db_config.php');
require_once('../includes/functions/func_attendance.php');
header('Content-type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        if (isset($_GET['get_attendance_by_section'])) {
            $sectionId = isset($_GET['selectedId']) && trim($_GET['selectedId']) !== '' ? htmlspecialchars($_GET['selectedId']) : null;
            $days = isset($_GET['days']) ? min((int) $_GET['days'], 365) : 31;
            if (!$sectionId) {
                echo json_encode(['success' => false, 'message' => 'No section ID provided.']);
                exit;
            }
            try {
                $response = getAttendanceBySection($pdo, $sectionId, $days);
                echo json_encode($response);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to fetch attendance.',
                    'error' => $e->getMessage()
                ]);
            }
            exit;
        } elseif (isset($_GET['get_holidays'])) {
            $stmt = $pdo->prepare("SELECT holiday_date FROM holidays_tbl WHERE holiday_date is not null");
            $stmt->execute();
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($events);
            exit;
        } elseif (isset($_GET['sync_attendance'])) {
            if (!isset($_GET['SectionName']) || empty($_GET['SectionName']) || !isset($_GET['SectionId'])) {
                echo json_encode(['success' => false, 'message' => 'Section name and ID are required.']);
                exit;
            }
            $sectionName = htmlspecialchars($_GET['SectionName']);
            $sectionId = (int) $_GET['SectionId'];
            try {
                $students = getStudentsBySectionName($pdo, $sectionName);
                if (!empty($students)) {
                    $attendanceData = [];
                    foreach ($students as $student) {
                        $remarks = $student['remarks'] ?? "";
                        if (
                            insertAttendanceRecord(
                                $pdo,
                                $student['lrn'],
                                $student['section'],
                                $student['school_year'],
                                $sectionId,
                                $remarks
                            )
                        ) {
                            $attendanceData[] = [
                                'lrn' => $student['lrn'],
                                'section' => $student['section'],
                                'school_year' => $student['school_year'],
                                'remarks' => $remarks
                            ];
                        }
                    }
                    echo json_encode(['success' => true, 'data' => $students, 'attendance' => $attendanceData]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No students available.']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Failed to sync attendance.', 'error' => $e->getMessage()]);
            }
            exit;
        }
        break;
    case 'POST':
        $data = $_POST;
        if (isset($_GET['edit_attendance'])) {
            $lrn = $_POST['lrn'] ?? null;
            $sectionId = $_POST['sectionId'] ?? null;
            $day = $_POST['day'] ?? null;
            $value = $_POST['value'] ?? '';
            $schoolYear = $_POST['schoolYear'] ?? null;
            if (!$lrn || !$sectionId || !$day || !$schoolYear) {
                echo json_encode(['success' => false, 'message' => 'Missing required data']);
                exit;
            }
            error_log("Received Data - LRN: $lrn, SectionID: $sectionId, Day: $day, Value: $value, SchoolYear: $schoolYear");
            try {
                $sql = "UPDATE attendance_tbl 
                        SET $day = :value 
                        WHERE lrn = :lrn 
                          AND section_id = :sectionId 
                          AND attendance_term = :schoolYear";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':value', $value, PDO::PARAM_STR);
                $stmt->bindParam(':lrn', $lrn, PDO::PARAM_STR);
                $stmt->bindParam(':sectionId', $sectionId, PDO::PARAM_STR);
                $stmt->bindParam(':schoolYear', $schoolYear, PDO::PARAM_STR);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Database update failed']);
                }
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            exit;
        }
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        break;
}
?>