<?php
require_once('../db_config.php');
require_once('../Excel/vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
function update_sf8($pdo, $schoolYear, $gradeLevel, $studentSection, $userFullName)
{
    $filePath = $_SERVER['DOCUMENT_ROOT'] . '/sf_suite/school_forms_temp/SF8.xls';
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $schoolInfo = fetch_school_info($pdo);
    if ($schoolInfo) {
        $sheet->setCellValue('O5', $schoolInfo['region']);
        $sheet->setCellValue('I5', $schoolInfo['district']);
        $sheet->setCellValue('L5', $schoolInfo['division']);
        $sheet->setCellValue('C7', $schoolInfo['school_id']);
        $sheet->setCellValue('E5', $schoolInfo['school_name']);
    }
    // Insert the school year, grade level, and student section into the specified cells
    $sheet->setCellValue('O7', $schoolYear);
    $sheet->setCellValue('F7', $gradeLevel);
    $sheet->setCellValue('H7', $studentSection);
    // Fetch student data
    $students = get_hnr($pdo, $schoolYear, $gradeLevel, '', $studentSection);
    if (!$students) {
        die("Error: No students found.");
    }
    // Separate students by sex
    $maleStudents = array_filter($students, fn($student) => $student['sex'] === 'M');
    $femaleStudents = array_filter($students, fn($student) => $student['sex'] === 'F');
    // Reference row 12 for styles
    $rowTemplate = 12;
    $styleArray = $sheet->getStyle('A' . $rowTemplate . ':O' . $rowTemplate);
    $mergedCells = $sheet->getMergeCells();
    // Initialize row counters
    $row = 12;
    function insertStudents($students, &$row, $sheet, $styleArray, $mergedCells, $rowTemplate)
    {
        foreach ($students as $student) {
            if ($row > $rowTemplate) {
                $sheet->insertNewRowBefore($row, 1);
            }
            // Copy styles
            $sheet->duplicateStyle($styleArray, 'A' . $row . ':O' . $row);
            // Copy merged cells structure
            foreach ($mergedCells as $merge) {
                if (strpos($merge, (string) $rowTemplate) !== false) {
                    $newMerge = preg_replace('/(\d+)/', (string) $row, $merge);
                    $sheet->mergeCells($newMerge);
                }
            }
            // Set student data
            $sheet->setCellValueExplicit('A' . $row, $student['lrn'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)
                ->setCellValue('C' . $row, $student['name'])
                ->setCellValue('G' . $row, $student['birthdate'])
                ->setCellValue('H' . $row, $student['age'])
                ->setCellValue('I' . $row, $student['weight'])
                ->setCellValue('J' . $row, $student['height'])
                ->setCellValue('K' . $row, $student['height_squared'])
                ->setCellValue('L' . $row, $student['bmi'])
                ->setCellValue('M' . $row, $student['nutritional_status'])
                ->setCellValue('N' . $row, $student['height_for_age'])
                ->setCellValue('O' . $row, $student['remarks']);
            $row++;
        }
    }
    // Insert Male Students
    insertStudents($maleStudents, $row, $sheet, $styleArray, $mergedCells, $rowTemplate);
    // Save male summary row position
    $summaryStartRow = $row + 1;
    $row += 1;
    // Insert Female Students
    insertStudents($femaleStudents, $row, $sheet, $styleArray, $mergedCells, $rowTemplate);
    // Skip 8 rows for summary
    $row += 8;
    function insertSummary($students, &$row, $sheet, $title)
    {
        $summaryData = [
            'C' => 0, // Severely Wasted
            'E' => 0, // Wasted
            'F' => 0, // Normal
            'G' => 0, // Overweight
            'H' => 0, // Obese
            'J' => 0, // Severely Stunted
            'K' => 0, // Stunted
            'L' => 0, // Normal Height
            'M' => 0, // Tall
        ];
        foreach ($students as $student) {
            switch ($student['nutritional_status']) {
                case 'Severely Wasted':
                    $summaryData['C']++;
                    break;
                case 'Wasted':
                    $summaryData['E']++;
                    break;
                case 'Normal':
                    $summaryData['F']++;
                    break;
                case 'Overweight':
                    $summaryData['G']++;
                    break;
                case 'Obese':
                    $summaryData['H']++;
                    break;
            }
            switch ($student['height_for_age']) {
                case 'Severely Stunted':
                    $summaryData['J']++;
                    break;
                case 'Stunted':
                    $summaryData['K']++;
                    break;
                case 'Normal':
                    $summaryData['L']++;
                    break;
                case 'Tall':
                    $summaryData['M']++;
                    break;
            }
        }
        $summaryData['I'] = array_sum([$summaryData['C'], $summaryData['E'], $summaryData['F'], $summaryData['G'], $summaryData['H']]);
        $summaryData['N'] = array_sum([$summaryData['J'], $summaryData['K'], $summaryData['L'], $summaryData['M']]);
        // Insert summary row
        $sheet->setCellValue('A' . $row, $title);
        foreach ($summaryData as $col => $value) {
            $sheet->setCellValue($col . $row, $value);
        }
        $row++;
    }
    // Insert Male Summary
    insertSummary($maleStudents, $row, $sheet, 'Male');
    // Insert Female Summary
    insertSummary($femaleStudents, $row, $sheet, 'Female');
    // Insert Total Summary
    $totalRow = $row;
    $sheet->setCellValue('A' . $totalRow, 'Total');
    foreach (['C', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'] as $col) {
        $sheet->setCellValue($col . $totalRow, '=SUM(' . $col . ($totalRow - 2) . ':' . $col . ($totalRow - 1) . ')');
    }
    $currentDate = date('Y-m-d'); // Formats the date as YYYY-MM-DD
    $footerRow = $totalRow + 3;
    $sheet->setCellValue("A$footerRow", $currentDate);
    $sheet->setCellValue("E$footerRow", $userFullName);
    $newFileName = 'SF-SUITE_SF8_' . $studentSection . '-' . $schoolYear . date('Y-m-d_H-i-s') . '.xlsx';
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
    // Ensure that all parts of the name are trimmed and concatenated correctly
    $userFullName = trim($userInfo['UserFName'] . ' ' . $userInfo['UserMName'] . ' ' . $userInfo['UserLName'] . ' ' . $userInfo['UserEName']);
} else {
    // Debugging: Log or display an error message if the user is not found
    error_log("User  not found for identifier: $identifier");
    // Optionally, set a default value or handle the error as needed
    $userFullName = "Unknown User"; // or handle it in a way that fits your application
}
// Call the function to update the existing file and generate a new file for download
update_sf8($pdo, $schoolYear, $gradeLevel, $studentSection, $userFullName);
// Get student data
function get_hnr($pdo, $schoolYear, $gradeLevel, $studentSex, $studentSection)
{
    $query = "SELECT * FROM health_nutrition_tbl WHERE 1=1";
    if (!empty($schoolYear)) {
        $query .= " AND hnr_term = :schoolYear";
    }
    if (!empty($studentSection)) {
        $query .= " AND section = :studentSection";
    }
    $stmt = $pdo->prepare($query);
    if (!empty($schoolYear)) {
        $stmt->bindValue(':schoolYear', $schoolYear, PDO::PARAM_STR);
    }
    if (!empty($studentSection)) {
        $stmt->bindValue(':studentSection', $studentSection, PDO::PARAM_STR);
    }
    $stmt->execute();
    $hnrData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $finalData = [];
    foreach ($hnrData as $hnrDatas) {
        $lrn = $hnrDatas['lrn'];
        $studentQuery = "SELECT name, grade_level, sex FROM student_tbl WHERE lrn = :lrn";
        $studentStmt = $pdo->prepare($studentQuery);
        $studentStmt->bindValue(':lrn', $lrn, PDO::PARAM_STR);
        $studentStmt->execute();
        $student = $studentStmt->fetch(PDO::FETCH_ASSOC);
        if ($student) {
            if (
                (!empty($gradeLevel) && $student['grade_level'] !== $gradeLevel) ||
                (!empty($studentSex) && $student['sex'] !== $studentSex)
            ) {
                continue;
            }
            $hnrDatas['name'] = $student['name'];
            $hnrDatas['grade_level'] = $student['grade_level'];
            $hnrDatas['sex'] = $student['sex'];
            $finalData[] = $hnrDatas;
        }
    }
    return $finalData;
}
?>