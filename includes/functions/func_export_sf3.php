<?php
require_once('../db_config.php');
require_once('../Excel/vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
function update_sf3($pdo, $schoolYear, $gradeLevel, $studentSection)
{
    // Load the existing Excel file
    $filePath = $_SERVER['DOCUMENT_ROOT'] . '/sf_suite/school_forms_temp/SF3.xls';
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $schoolInfo = fetch_school_info($pdo);
    if ($schoolInfo) {
        $sheet->setCellValue('C5', $schoolInfo['school_id']);
        $sheet->setCellValue('C7', $schoolInfo['school_name']);
    }
    // Insert the school year, grade level, and student section into the specified cells
    $sheet->setCellValue('K5', $schoolYear);
    $sheet->setCellValue('K7', $gradeLevel);
    $sheet->setCellValue('N7', $studentSection);
    // Fetch students
    $studentsMale = get_lm_male($pdo, $schoolYear, $gradeLevel, $studentSection);
    $studentsFemale = get_lm_female($pdo, $schoolYear, $gradeLevel, $studentSection);
    // Define row templates
    $rowTemplate = 13;
    $styleArray = $sheet->getStyle('B' . $rowTemplate . ':T' . $rowTemplate);
    $mergedCells = $sheet->getMergeCells(); // Get all merged cells
    $rowStartMale = 13;
    $lastRowM = insert_students($sheet, $studentsMale, $rowStartMale, $styleArray, $mergedCells);
    // Skip 2 rows after inserting males
    $rowStartFemale = $lastRowM + 2; // +2 to move 2 rows forward before inserting females
    // Insert Female students
    $lastRowF = insert_students($sheet, $studentsFemale, $rowStartFemale, $styleArray, $mergedCells);
    // Count non-empty cells for each column from D to S for male students
    for ($col = 'D'; $col <= 'S'; $col++) {
        $sheet->setCellValue($col . ($lastRowM + 1), '=COUNTA(' . $col . '13:' . $col . $lastRowM . ')');
    }
    // Count non-empty cells for each column from D to S for female students
    for ($col = 'D'; $col <= 'S'; $col++) {
        $sheet->setCellValue($col . ($lastRowF + 1), '=COUNTA(' . $col . ($rowStartFemale) . ':' . $col . $lastRowF . ')');
    }
    // Insert a formula to sum both male and female counts for each column
    for ($col = 'D'; $col <= 'S'; $col++) {
        $sheet->setCellValue($col . ($lastRowF + 2), '=' . $col . ($lastRowM + 1) . ' + ' . $col . ($lastRowF + 1));
    }
    $newFileName = 'SF-SUITE_SF3_' . $studentSection . '-' . $schoolYear . date('Y-m-d_H-i-s') . '.xlsx';
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
function insert_students($sheet, $students, $startRow, $styleArray, $mergedCells)
{
    if (empty($students))
        return $startRow;
    // Extract description headers from first student
    $desc = [
        'Desc1' => $students[0]['Desc1'] ?? '',
        'Desc2' => $students[0]['Desc2'] ?? '',
        'Desc3' => $students[0]['Desc3'] ?? '',
        'Desc4' => $students[0]['Desc4'] ?? '',
        'Desc5' => $students[0]['Desc5'] ?? '',
        'Desc6' => $students[0]['Desc6'] ?? '',
        'Desc7' => $students[0]['Desc7'] ?? '',
        'Desc8' => $students[0]['Desc8'] ?? '',
    ];
    // Write descriptors to row 10
    $sheet->setCellValue('D10', $desc['Desc1']);
    $sheet->setCellValue('F10', $desc['Desc2']);
    $sheet->setCellValue('H10', $desc['Desc3']);
    $sheet->setCellValue('J10', $desc['Desc4']);
    $sheet->setCellValue('L10', $desc['Desc5']);
    $sheet->setCellValue('N10', $desc['Desc6']);
    $sheet->setCellValue('P10', $desc['Desc7']);
    $sheet->setCellValue('R10', $desc['Desc8']);
    foreach ($students as $student) {
        if ($startRow > 13) {
            $sheet->insertNewRowBefore($startRow, 1);
        }
        // Apply style
        $sheet->duplicateStyle($styleArray, 'B' . $startRow . ':T' . $startRow);
        // Reapply merged cells for this row
        foreach ($mergedCells as $mergeRange) {
            if (preg_match('/^([A-Z]+)13:([A-Z]+)13$/', $mergeRange, $matches)) {
                $startColumn = $matches[1];
                $endColumn = $matches[2];
                $newMergeRange = "{$startColumn}{$startRow}:{$endColumn}{$startRow}";
                $sheet->mergeCells($newMergeRange);
            }
        }
        // Populate student data
        $sheet->setCellValue('B' . $startRow, $student['name'])
            ->setCellValue('D' . $startRow, $student['Status1'])
            ->setCellValue('E' . $startRow, $student['Returned1'])
            ->setCellValue('F' . $startRow, $student['Status2'])
            ->setCellValue('G' . $startRow, $student['Returned2'])
            ->setCellValue('H' . $startRow, $student['Status3'])
            ->setCellValue('I' . $startRow, $student['Returned3'])
            ->setCellValue('J' . $startRow, $student['Status4'])
            ->setCellValue('K' . $startRow, $student['Returned4'])
            ->setCellValue('L' . $startRow, $student['Status5'])
            ->setCellValue('M' . $startRow, $student['Returned5'])
            ->setCellValue('N' . $startRow, $student['Status6'])
            ->setCellValue('O' . $startRow, $student['Returned6'])
            ->setCellValue('P' . $startRow, $student['Status7'])
            ->setCellValue('Q' . $startRow, $student['Returned7'])
            ->setCellValue('R' . $startRow, $student['Status8'])
            ->setCellValue('S' . $startRow, $student['Returned8'])
            ->setCellValue('T' . $startRow, $student['Returned8']); // Double check if T should be something else
        $startRow++;
    }
    return $startRow - 1;
}
// Get the filter parameters from the URL
$schoolYear = isset($_GET['schoolYear']) ? $_GET['schoolYear'] : '';
$gradeLevel = isset($_GET['gradeLevel']) ? $_GET['gradeLevel'] : '';
$studentSection = isset($_GET['studentSection']) ? $_GET['studentSection'] : '';
// Call the function to update the existing file and generate a new file for download
update_sf3($pdo, $schoolYear, $gradeLevel, $studentSection);
function get_lm_female($pdo, $schoolYear, $gradeLevel, $studentSection)
{
    $query = "SELECT * FROM learning_material_tbl WHERE 1=1";
    if (!empty($schoolYear)) {
        $query .= " AND lm_term = :schoolYear";
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
    $lmData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Prepare an array to hold the final data
    $finalData = [];
    // Now fetch names and additional details from student_tbl based on the LRN from attendance data
    foreach ($lmData as $lm) {
        $lrn = $lm['lrn'];
        // Fetch the student details for the current LRN
        $studentQuery = "SELECT name, grade_level FROM student_tbl WHERE lrn = :lrn AND sex = 'F'";
        $studentStmt = $pdo->prepare($studentQuery);
        $studentStmt->bindValue(':lrn', $lrn, PDO::PARAM_STR);
        $studentStmt->execute();
        $student = $studentStmt->fetch(PDO::FETCH_ASSOC);
        // Check if the student exists and matches the additional filters
        if ($student) {
            // Apply additional filters
            if ((!empty($gradeLevel) && $student['grade_level'] !== $gradeLevel)) {
                continue; // Skip this record if it doesn't match the filters
            }
            $lm['name'] = $student['name'];
            $lm['grade_level'] = $student['grade_level'];
            $finalData[] = $lm;
        }
    }
    return $finalData;
}
function get_lm_male($pdo, $schoolYear, $gradeLevel, $studentSection)
{
    $query = "SELECT * FROM learning_material_tbl WHERE 1=1";
    if (!empty($schoolYear)) {
        $query .= " AND lm_term = :schoolYear";
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
    $lmData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Prepare an array to hold the final data
    $finalData = [];
    // Now fetch names and additional details from student_tbl based on the LRN from attendance data
    foreach ($lmData as $lm) {
        $lrn = $lm['lrn'];
        // Fetch the student details for the current LRN
        $studentQuery = "SELECT name, grade_level FROM student_tbl WHERE lrn = :lrn AND sex = 'M'";
        $studentStmt = $pdo->prepare($studentQuery);
        $studentStmt->bindValue(':lrn', $lrn, PDO::PARAM_STR);
        $studentStmt->execute();
        $student = $studentStmt->fetch(PDO::FETCH_ASSOC);
        // Check if the student exists and matches the additional filters
        if ($student) {
            // Apply additional filters
            if ((!empty($gradeLevel) && $student['grade_level'] !== $gradeLevel)) {
                continue; // Skip this record if it doesn't match the filters
            }
            $lm['name'] = $student['name'];
            $lm['grade_level'] = $student['grade_level'];
            $finalData[] = $lm;
        }
    }
    return $finalData;
}
?>