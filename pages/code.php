<?php
session_start();
require_once('../includes/sessions.php');
require_once('../includes/db_config.php');
require '../includes/Excel/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (isset($_POST['save_excel'])) {
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $filename = $_FILES['file']['name'];
        $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
        $allowed_ext = ['xls', 'csv', 'xlsx'];
        if (in_array($file_ext, $allowed_ext)) {
            $inputFileNamePath = $_FILES['file']['tmp_name'];
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileNamePath);
                $data = $spreadsheet->getActiveSheet()->toArray();
                $stmt = $pdo->prepare("INSERT INTO student_tbl (
                    lrn, name, grade_level, section, school_year, strand_track, sex, birth_date, age, mother_tongue,
                    ethnic_group, religion, hssp, barangay, municipality_city, province,
                    father_name, mother_maiden_name, guardian_name, guardian_relationship,
                    contact_number, learning_modality, remarks
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                foreach ($data as $index => $row) {
                    if ($index === 0)
                        continue; // Skip header
                    $lrn = isset($row[0]) ? trim($row[0]) : null;
                    $student_name = isset($row[1]) ? trim($row[1]) : null;
                    $grade_level = isset($row[2]) ? trim($row[2]) : null;
                    $section = isset($row[3]) ? trim(str_replace(' ', '', $row[3])) : null;
                    $school_year = isset($row[4]) ? trim(str_replace(' ', '', $row[4])) : null;
                    $sex = isset($row[5]) ? trim($row[5]) : null;
                    $birth_date = isset($row[6]) ? date('Y-m-d', strtotime(trim($row[6]))) : null;
                    $age = isset($row[7]) ? trim($row[7]) : null;
                    $mother_tongue = isset($row[8]) ? trim($row[8]) : null;
                    $ethnic_group = isset($row[9]) ? trim($row[9]) : null;
                    $religion = isset($row[10]) ? trim($row[10]) : null;
                    $hssp = isset($row[11]) ? trim($row[11]) : null;
                    $barangay = isset($row[12]) ? trim($row[12]) : null;
                    $municipality_city = isset($row[13]) ? trim($row[13]) : null;
                    $province = isset($row[14]) ? trim($row[14]) : null;
                    $father_name = isset($row[15]) ? trim($row[15]) : null;
                    $mother_maiden_name = isset($row[16]) ? trim($row[16]) : null;
                    $guardian_name = isset($row[17]) ? trim($row[17]) : null;
                    $guardian_relationship = isset($row[18]) ? trim($row[18]) : null;
                    $contact_number = isset($row[19]) ? trim($row[19]) : null;
                    $learning_modality = isset($row[20]) ? trim($row[20]) : null;
                    $remarks = isset($row[21]) ? trim($row[21]) : null;
                    $strand_track = isset($row[22]) ? trim($row[22]) : null; // New column
                    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM student_tbl WHERE lrn = ? AND school_year = ?");
                    $checkStmt->execute([$lrn, $school_year]);
                    $exists = $checkStmt->fetchColumn();
                    if ($lrn && $student_name && $grade_level && $section && $school_year && $sex && $birth_date && !$exists) {
                        $stmt->execute([
                            $lrn,
                            $student_name,
                            $grade_level,
                            $section,
                            $school_year,
                            $strand_track, // Inserted after School Year
                            $sex,
                            $birth_date,
                            $age,
                            $mother_tongue,
                            $ethnic_group,
                            $religion,
                            $hssp,
                            $barangay,
                            $municipality_city,
                            $province,
                            $father_name,
                            $mother_maiden_name,
                            $guardian_name,
                            $guardian_relationship,
                            $contact_number,
                            $learning_modality,
                            $remarks
                        ]);
                    }
                }
                insert_student_promotion($pdo, $data);
                create_user_account($pdo);
                $_SESSION['message'] = "Successfully Imported.";
            } catch (Exception $e) {
                $_SESSION['message'] = "Error: " . $e->getMessage();
            }
        } else {
            $_SESSION['message'] = "Invalid file format. Please upload a CSV or Excel file.";
        }
    } else {
        $_SESSION['message'] = "File upload failed. Please try again.";
    }
    header('Location: school_section.php');
    exit(0);
}
function insert_student_promotion($pdo, $data)
{
    $achievementStmt = $pdo->prepare("INSERT INTO prom_achievement_tbl (
        lrn, name, grade_level, section, school_year, sex, general_average, action_taken, cecs, ecs, remarks
    ) VALUES (?, ?, ?, ?, ?, ?, NULL, NULL, NULL, NULL, NULL)");
    try {
        foreach ($data as $index => $row) {
            if ($index === 0)
                continue;
            $lrn = isset($row[0]) ? trim($row[0]) : null;
            $student_name = isset($row[1]) ? trim($row[1]) : null;
            $grade_level = isset($row[2]) ? trim($row[2]) : null;
            $section = isset($row[3]) ? trim(str_replace(' ', '', $row[3])) : null;
            $school_year = isset($row[4]) ? trim(str_replace(' ', '', $row[4])) : null;
            $sex = isset($row[5]) ? trim($row[5]) : null;
            // Check if student exists in student_tbl
            $checkStudentStmt = $pdo->prepare("SELECT COUNT(*) FROM student_tbl WHERE lrn = ? AND school_year = ?");
            $checkStudentStmt->execute([$lrn, $school_year]);
            $studentExists = $checkStudentStmt->fetchColumn();
            if ($studentExists) {
                // Check if student already exists in prom_achievement_tbl
                $checkPromotionStmt = $pdo->prepare("SELECT COUNT(*) FROM prom_achievement_tbl WHERE lrn = ? AND school_year = ?");
                $checkPromotionStmt->execute([$lrn, $school_year]);
                $promotionExists = $checkPromotionStmt->fetchColumn();
                if (!$promotionExists) { // Insert only if record does not exist
                    $achievementStmt->execute([$lrn, $student_name, $grade_level, $section, $school_year, $sex]);
                }
            }
        }
    } catch (Exception $e) {
        error_log("Error inserting student promotion: " . $e->getMessage());
    }
}
function create_user_account($pdo)
{
    $stmt = $pdo->query("SELECT lrn, sex, birth_date FROM student_tbl");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $insertStmt = $pdo->prepare("INSERT INTO user_tbl (Identifier, Gender, BirthDate, Role, username, password, access_level, user_status) VALUES (?, ?, ?, ?, ?, ?, ?, NULL)");
    foreach ($students as $student) {
        $lrn = $student['lrn'];
        $sex = $student['sex'];
        $birth_date = $student['birth_date'];
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM user_tbl WHERE Identifier = ?");
        $checkStmt->execute([$lrn]);
        $exists = $checkStmt->fetchColumn();
        if (!$exists) {
            $username = $lrn;
            $password = date('mdY', strtotime($birth_date));
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $role = 'STUDENT';
            $access_level = $role;
            $insertStmt->execute([$lrn, $sex, $birth_date, $role, $username, $hashedPassword, $access_level]);
        }
    }
}
?>