<?php
require_once('../db_config.php');
require_once('../Excel/vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
function update_sf($pdo, $schoolYear, $gradeLevel, $studentSection, $userFullName)
{
    $filePath = $_SERVER['DOCUMENT_ROOT'] . '/sf_suite/school_forms_temp/SF1.xls';
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    // Fetch School Information
    $schoolInfo = fetch_school_info($pdo);
    if ($schoolInfo) {
        $sheet->setCellValue('F3', $schoolInfo['school_id']);
        $sheet->setCellValue('K3', $schoolInfo['region']);
        $sheet->setCellValue('T3', $schoolInfo['division']);
        $sheet->setCellValue('F4', $schoolInfo['school_name']);
    }
    // Insert the school year, grade level, and student section into the specified cells
    $sheet->setCellValue('T4', $schoolYear); // School Year in T4
    $sheet->setCellValue('AE4', $gradeLevel); // Grade Level in AE4
    $sheet->setCellValue('AM4', $studentSection); // Student Section in AM4
    // Fetch Student Data
    $students = student_list($pdo, $schoolYear, $gradeLevel, $studentSection);
    $rowTemplate = 7;
    $styleArray = $sheet->getStyle('A' . $rowTemplate . ':AS' . $rowTemplate);
    $mergedCells = $sheet->getMergeCells();
    $maleCount = 0;
    $femaleCount = 0;
    $adjustedMaleCount = 0;
    $adjustedFemaleCount = 0;
    $row = 7;
    foreach ($students as $student) {
        if ($row > 7) {
            $sheet->insertNewRowBefore($row, 1);
        }
        $sheet->duplicateStyle($styleArray, 'A' . $row . ':AS' . $row);
        foreach ($mergedCells as $merge) {
            $range = explode(':', $merge);
            preg_match('/([A-Z]+)(\d+)/', $range[0], $start);
            preg_match('/([A-Z]+)(\d+)/', $range[1], $end);
            if ((int) $start[2] == $rowTemplate) {
                $newMerge = $start[1] . $row . ':' . $end[1] . $row;
                $sheet->mergeCells($newMerge);
            }
        }
        $birthDate = new DateTime($student['birth_date']);
        $referenceDate = new DateTime(date('Y') . '-10-31');
        $age = $birthDate->diff($referenceDate)->y;
        $sheet->setCellValue('A' . $row, "'" . $student['lrn'] . "'")
            ->setCellValue('C' . $row, $student['name'])
            ->setCellValue('G' . $row, $student['sex'])
            ->setCellValue('H' . $row, $student['birth_date'])
            ->setCellValue('J' . $row, $age)
            ->setCellValue('L' . $row, $student['mother_tongue'])
            ->setCellValue('N' . $row, $student['ethnic_group'])
            ->setCellValue('O' . $row, $student['religion'])
            ->setCellValue('P' . $row, $student['hssp'])
            ->setCellValue('R' . $row, $student['barangay'])
            ->setCellValue('U' . $row, $student['municipality_city'])
            ->setCellValue('W' . $row, $student['province'])
            ->setCellValue('AB' . $row, $student['father_name'])
            ->setCellValue('AF' . $row, $student['mother_maiden_name'])
            ->setCellValue('AK' . $row, $student['guardian_name'])
            ->setCellValue('AO' . $row, $student['guardian_relationship'])
            ->setCellValue('AP' . $row, $student['contact_number'])
            ->setCellValue('AR' . $row, $student['learning_modality'])
            ->setCellValue('AS' . $row, $student['remarks']);
        $remarks = strtoupper($student['remarks']);
        $isExcluded = (strpos($remarks, 'T/O') !== false || strpos($remarks, 'DRP') !== false);
        if (strtoupper($student['sex']) === 'M') {
            $maleCount++;
            if (!$isExcluded) {
                $adjustedMaleCount++;
            }
        } elseif (strtoupper($student['sex']) === 'F') {
            $femaleCount++;
            if (!$isExcluded) {
                $adjustedFemaleCount++;
            }
        }
        $row++;
    }
    $maleRow = $row + 3;
    $femaleRow = $maleRow + 3;
    $sumRow = $femaleRow + 2;
    $sheet->setCellValue('X' . $maleRow, $maleCount);
    $sheet->setCellValue('AA' . $maleRow, $adjustedMaleCount);
    $sheet->setCellValue('X' . $femaleRow, $femaleCount);
    $sheet->setCellValue('AA' . $femaleRow, $adjustedFemaleCount);
    $sheet->setCellValue('X' . $sumRow, $maleCount + $femaleCount);
    $sheet->setCellValue('AA' . $sumRow, $adjustedMaleCount + $adjustedFemaleCount);
    // Insert school head
    $sheet->setCellValue('AN' . ($row + 4), $schoolInfo['school_head']); // School Head in AN
    $sheet->setCellValue('AE' . ($row + 8), $schoolInfo['bosy_date']);
    $sheet->setCellValue('AI' . ($row + 8), $schoolInfo['eosy_date']);
    $sheet->setCellValue('AN' . ($row + 7), $schoolInfo['bosy_date']);
    $sheet->setCellValue('AQ' . ($row + 7), $schoolInfo['eosy_date']);
    $sheet->setCellValue('AE' . ($row + 3), $userFullName); // School Head in AN
    $newFileName = 'SF-SUITE_SF1_' . $studentSection . '-' . $schoolYear . date('Y-m-d_H-i-s') . '.xlsx';
    $writer = new Xlsx($spreadsheet);
    ob_clean();
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $newFileName . '"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
    // After export, insert record into export_record_tbl
    $sql = "INSERT INTO export_record_tbl (er_desc, status, exported_at) 
            VALUES (:er_desc, :status, NOW())";
    $stmt = $pdo->prepare($sql);
    $status = 'Completed';  // Set status as completed
    $stmt->bindParam(':er_desc', $newFileName, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->execute();
    exit;
}
function fetch_school_info($pdo)
{
    $query = "SELECT * FROM school_info_tbl LIMIT 1";
    $stmt = $pdo->query($query);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function get_user_full_name($pdo, $identifier)
{
    $query = "SELECT UserFName, UserMName, UserLName, UserEName FROM user_tbl WHERE Identifier = :identifier";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':identifier', $identifier, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
// Get the filter parameters from the URL
$schoolYear = isset($_GET['schoolYear']) ? $_GET['schoolYear'] : '';
$gradeLevel = isset($_GET['gradeLevel']) ? $_GET['gradeLevel'] : '';
$studentSection = isset($_GET['studentSection']) ? $_GET['studentSection'] : '';
$identifier = isset($_GET['identifier']) ? $_GET['identifier'] : '';
// Fetch the user's full name using the identifier
$userInfo = get_user_full_name($pdo, $identifier);
$userFullName = '';
if ($userInfo) {
    $userFullName = trim($userInfo['UserFName'] . ' ' . $userInfo['UserMName'] . ' ' . $userInfo['UserLName'] . ' ' . $userInfo['UserEName']);
}
// Call the function to update the existing file and generate a new file for download
update_sf($pdo, $schoolYear, $gradeLevel, $studentSection, $userFullName);
// Ensure the student_list function is defined
function student_list($pdo, $schoolYear, $gradeLevel, $studentSection)
{
    $query = "SELECT * FROM student_tbl WHERE 1=1";
    if (!empty($schoolYear)) {
        $query .= " AND school_year = :schoolYear";
    }
    if (!empty($gradeLevel)) {
        $query .= " AND grade_level = :gradeLevel";
    }
    if (!empty($studentSection)) {
        $query .= " AND section = :studentSection";
    }
    // Order by sex (DESC) then by name (ASC)
    $query .= " ORDER BY sex DESC, name ASC";
    $stmt = $pdo->prepare($query);
    if (!empty($schoolYear)) {
        $stmt->bindValue(':schoolYear', $schoolYear, PDO::PARAM_STR);
    }
    if (!empty($gradeLevel)) {
        $stmt->bindValue(':gradeLevel', $gradeLevel, PDO::PARAM_STR);
    }
    if (!empty($studentSection)) {
        $stmt->bindValue(':studentSection', $studentSection, PDO::PARAM_STR);
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>