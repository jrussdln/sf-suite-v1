<?php
require_once('../db_config.php');
require_once('../Excel/vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
function update_sf10($pdo, $schoolYear, $lrn)
{
    // Load the existing Excel file
    $filePath = 'C:/xampp/htdocs/edukeep-cap/school_forms_temp/SF10-JHS.xls';
    if (!file_exists($filePath)) {
        die("Error: Template file not found.");
    }
    $spreadsheet = IOFactory::load($filePath);
    updateICARD($pdo, $spreadsheet, $schoolYear, $lrn);
    // Update FRONT sheet with student info
    $sheet = $spreadsheet->getSheetByName('FRONT');
    if (!$sheet) {
        die("Error: Sheet 'FRONT' not found.");
    }
    $schoolInfo = fetch_school_info($pdo);
    if ($schoolInfo) {
        $currentDate = date('F d, Y');
        // Define cell locations for each field
        $school_name_fields = ['C20', 'C46', 'D76'];
        $school_id_fields = ['G20', 'G46', 'I76'];
        $district_fields = ['I20', 'I46'];
        $division_fields = ['K20', 'K46'];
        $region_fields = ['M20', 'M46'];
        $school_head_fields = ['E78'];
        // Loop through each field array and insert corresponding data
        foreach ($school_head_fields as $cell) {
            $sheet->setCellValue($cell, $schoolInfo['school_head']);
        }
        foreach ($school_name_fields as $cell) {
            $sheet->setCellValue($cell, $schoolInfo['school_name']);
        }
        foreach ($school_id_fields as $cell) {
            $sheet->setCellValue($cell, $schoolInfo['school_id']);
        }
        foreach ($district_fields as $cell) {
            $sheet->setCellValue($cell, $schoolInfo['district']);
        }
        foreach ($division_fields as $cell) {
            $sheet->setCellValue($cell, $schoolInfo['division']);
        }
        foreach ($region_fields as $cell) {
            $sheet->setCellValue($cell, $schoolInfo['region']);
        }
        // Insert School Year (Only one cell needed)
        $sheet->setCellValue('H21', $schoolInfo['school_year'])
            ->setCellValue('B78', $currentDate);
    }
    // Fetch student data
    $students = grade($pdo, $schoolYear, $lrn);
    if (empty($students)) {
        die("Error: No student found for LRN $lrn.");
    }
    $student = $students[0];
    // Process student name parts
    $nameParts = explode(',', $student['name'] ?? '');
    $lastName = $nameParts[0] ?? '';
    $firstName = $nameParts[1] ?? '';
    $middleName = $nameParts[2] ?? '';
    $extension = $nameParts[3] ?? '';
    $fullName = trim($lastName) . ', ' . trim($firstName) . ' ' . trim($middleName) . ' ' . trim($extension);
    $fullName = preg_replace('/\s+/', ' ', trim($fullName)); // Remove extra spaces
    // Insert student info
    $sheet->setCellValue('F75', $fullName)
        ->setCellValue('I75', "'" . $student['lrn'] . "'")
        ->setCellValue('D7', trim($lastName))
        ->setCellValue('G7', trim($firstName))
        ->setCellValue('K7', trim($extension))
        ->setCellValue('M7', trim($middleName))
        ->setCellValue('E8', "'" . $student['lrn'] . "'")
        ->setCellValue('J8', $student['birth_date'])
        ->setCellValue('M8', $student['sex'])
    ;
    $startRow = 25;
    fetchAndInsertGradess($pdo, $sheet, $schoolYear, $lrn, 7, $startRow);
    $startRow += 26;
    fetchAndInsertGradess($pdo, $sheet, $schoolYear, $lrn, 8, $startRow);
    // **Insert Grades in the BACK Sheet**
    $sheet = $spreadsheet->getSheetByName('BACK');
    if (!$sheet) {
        die("Error: Sheet 'BACK' not found.");
    }
    $newFileName = 'SF-SUITE_SF10JHS_' . date('Y-m-d_H-i-s') . '.xlsx';
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
// **Function to Fetch and Insert Grades**
function fetchAndInsertGradess($pdo, $sheet, $schoolYear, $lrn, $gradeLevel, $startRow)
{
    $subjectQuery = "SELECT subject_id, subject_name FROM subjects_tbl 
                     WHERE grade_level = :gradeLevel AND subject_term = :schoolYear";
    $stmt = $pdo->prepare($subjectQuery);
    $stmt->execute([':gradeLevel' => $gradeLevel, ':schoolYear' => $schoolYear]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($subjects)) {
        error_log("No subjects found for grade level: $gradeLevel, school year: $schoolYear");
        return $startRow;
    }
    $colMap = ['G', 'H', 'I', 'J']; // Q1-Q4 columns
    foreach ($subjects as $subject) {
        $subjectId = $subject['subject_id'];
        $subjectName = $subject['subject_name'];
        // Fetch grades
        $gradesQuery = "SELECT fstq_grade_tr, scndq_grade_tr, trdq_grade_tr, fthq_grade_tr 
                        FROM student_grade_jhs_tbl 
                        WHERE lrn = :lrn AND subject_id = :subjectId AND grade_term = :schoolYear";
        $stmt = $pdo->prepare($gradesQuery);
        $stmt->execute([':lrn' => $lrn, ':subjectId' => $subjectId, ':schoolYear' => $schoolYear]);
        $grades = $stmt->fetch(PDO::FETCH_ASSOC);
        // Insert subject name in column A
        $sheet->setCellValue("B$startRow", $subjectName);
        $sum = 0;
        $count = 0;
        foreach ($colMap as $index => $col) {
            $gradeKey = ["fstq_grade_tr", "scndq_grade_tr", "trdq_grade_tr", "fthq_grade_tr"][$index];
            $gradeValue = $grades[$gradeKey] ?? 0;
            $sheet->setCellValue("$col$startRow", $gradeValue);
            if ($gradeValue > 0) {
                $sum += $gradeValue;
                $count++;
            }
        }
        // Calculate and insert average
        $average = ($count > 0) ? round($sum / $count) : 0;
        $sheet->setCellValue("K$startRow", $average);
        // Pass/Fail status
        $status = ($average >= 75) ? "Passed" : "Failed";
        $sheet->setCellValue("L$startRow", $status);
        $startRow++; // Move to next row
    }
    return $startRow;
}
function fetch_school_info($pdo)
{
    $query = "SELECT * FROM school_info_tbl LIMIT 1";
    $stmt = $pdo->query($query);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function updateICARD($pdo, $spreadsheet, $schoolYear, $lrn)
{
    $sheetName = 'BACK';
    $sheet = $spreadsheet->getSheetByName($sheetName);
    if (!$sheet) {
        die("Error: Sheet '$sheetName' not found.");
    }
    $schoolInfo = fetch_school_info($pdo);
    if ($schoolInfo) {
        $currentDate = date('F d, Y');
        // Define cell locations for each field
        $school_name_fields = ['B3', 'B28', 'C82'];
        $school_id_fields = ['F3', 'F28', 'H82'];
        $district_fields = ['H3', 'H28'];
        $division_fields = ['J3', 'J28'];
        $region_fields = ['L3', 'L28'];
        // Loop through each field array and insert corresponding data
        foreach ($school_name_fields as $cell) {
            $sheet->setCellValue($cell, $schoolInfo['school_name']);
        }
        foreach ($school_id_fields as $cell) {
            $sheet->setCellValue($cell, $schoolInfo['school_id']);
        }
        foreach ($district_fields as $cell) {
            $sheet->setCellValue($cell, $schoolInfo['district']);
        }
        foreach ($division_fields as $cell) {
            $sheet->setCellValue($cell, $schoolInfo['division']);
        }
        foreach ($region_fields as $cell) {
            $sheet->setCellValue($cell, $schoolInfo['region']);
        }
        // Insert School Year (Only one cell needed)
        $sheet->setCellValue('D84', $schoolInfo['school_head'])
            ->setCellValue('A84', $currentDate);
    }
    // Fetch student details
    $students = grade($pdo, $schoolYear, $lrn);
    if (empty($students)) {
        die("Error: No student found for LRN $lrn.");
    }
    $student = $students[0];
    $nameParts = explode(',', $student['name'] ?? '');
    $lastName = $nameParts[0] ?? '';
    $firstName = $nameParts[1] ?? '';
    $middleName = $nameParts[2] ?? '';
    $extension = $nameParts[3] ?? '';
    $fullName = trim($lastName) . ', ' . trim($firstName) . ' ' . trim($middleName) . ' ' . trim($extension);
    $fullName = preg_replace('/\s+/', ' ', trim($fullName)); // Remove extra spaces
    // Insert student info
    $sheet->setCellValue('E81', $fullName)
        ->setCellValue('H81', "'" . $student['lrn'] . "'")
    ;
    // Function to fetch and insert grades
    function fetchAndInsertGrades($pdo, $sheet, $schoolYear, $lrn, $gradeLevel, $startRow)
    {
        // Fetch subjects for the given grade level and school year
        $subjectQuery = "SELECT subject_id, subject_name 
                         FROM subjects_tbl 
                         WHERE grade_level = :gradeLevel
                         AND subject_term = :schoolYear";
        $stmt = $pdo->prepare($subjectQuery);
        $stmt->execute([
            ':gradeLevel' => $gradeLevel,
            ':schoolYear' => $schoolYear
        ]);
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($subjects)) {
            error_log("No subjects found for grade level: $gradeLevel, school year: $schoolYear");
            return;
        }
        $colMap = ['F', 'G', 'H', 'I']; // Q1-Q4
        foreach ($subjects as $subject) {
            $subjectId = $subject['subject_id'];
            $subjectName = $subject['subject_name'];
            // Fetch grades for the subject
            $gradesQuery = "SELECT fstq_grade_tr, scndq_grade_tr, trdq_grade_tr, fthq_grade_tr 
                            FROM student_grade_jhs_tbl 
                            WHERE lrn = :lrn 
                            AND subject_id = :subjectId
                            AND grade_term = :schoolYear";
            $stmt = $pdo->prepare($gradesQuery);
            $stmt->execute([
                ':lrn' => $lrn,
                ':subjectId' => $subjectId,
                ':schoolYear' => $schoolYear
            ]);
            $grades = $stmt->fetch(PDO::FETCH_ASSOC);
            // Insert subject name in column A
            $sheet->setCellValue("A$startRow", $subjectName);
            $sum = 0;
            $count = 0;
            // Insert grades into columns F-I, use 0 if no data found
            foreach ($colMap as $index => $col) {
                $gradeKey = ["fstq_grade_tr", "scndq_grade_tr", "trdq_grade_tr", "fthq_grade_tr"][$index];
                $gradeValue = isset($grades[$gradeKey]) ? $grades[$gradeKey] : 0;
                $sheet->setCellValue("$col$startRow", $gradeValue);
                // Calculate sum and count for average
                if ($gradeValue > 0) {
                    $sum += $gradeValue;
                    $count++;
                }
            }
            // Calculate rounded average in column J
            $average = ($count > 0) ? round($sum / $count) : 0;
            $sheet->setCellValue("J$startRow", $average);
            // Determine pass/fail status in column K
            $status = ($average >= 75) ? "Passed" : "Failed";
            $sheet->setCellValue("K$startRow", $status);
            $startRow++; // Move to next row for the next subject
        }
        return $startRow; // Return the last row used
    }
    // **Step 1: Fetch and insert Grade 9 subjects and grades**
    $startRow = 8;
    $startRow = fetchAndInsertGrades($pdo, $sheet, $schoolYear, $lrn, 9, $startRow);
    // **Step 2: Jump 25 rows below**
    $startRow += 17;
    // **Step 3: Fetch and insert Grade 10 subjects and grades**
    fetchAndInsertGrades($pdo, $sheet, $schoolYear, $lrn, 10, $startRow);
}
// Get the filter parameters from the URL
$schoolYear = isset($_GET['schoolYear']) ? $_GET['schoolYear'] : '';
$lrn = isset($_GET['lrn']) ? $_GET['lrn'] : '';
// Call the function to update the existing file and generate a new file for download
update_sf10($pdo, $schoolYear, $lrn);
// Ensure the student_list function is defined
function grade($pdo, $schoolYear, $lrn)
{
    $query = "SELECT * FROM student_tbl WHERE 1=1";
    if (!empty($schoolYear)) {
        $query .= " AND school_year = :schoolYear";
    }
    if (!empty($lrn)) {
        $query .= " AND lrn = :lrn";
    }
    $stmt = $pdo->prepare($query);
    if (!empty($schoolYear)) {
        $stmt->bindValue(':schoolYear', $schoolYear, PDO::PARAM_STR);
    }
    if (!empty($lrn)) {
        $stmt->bindValue(':lrn', $lrn, PDO::PARAM_STR);
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>