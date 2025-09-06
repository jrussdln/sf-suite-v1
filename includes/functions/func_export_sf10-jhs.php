<?php
require_once('../db_config.php');
require_once('../Excel/vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
function update_sf10($pdo, $lrn)
{
    // Fetch school years from student_tbl (LIMIT 2 ensues we get max 2 records)
    $stmt = $pdo->prepare("SELECT school_year FROM student_tbl WHERE lrn = :lrn ORDER BY school_year ASC LIMIT 4");
    $stmt->execute(['lrn' => $lrn]);
    $arraySchoolYear = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (count($arraySchoolYear) < 1) {
        die("Error: Not enough school years found for LRN $lrn (needs at least 2).");
    }
    $gradeMapping = [];
    if (!empty($arraySchoolYear[0])) {
        $gradeMapping[7] = $arraySchoolYear[0];
    }
    if (!empty($arraySchoolYear[1])) {
        $gradeMapping[8] = $arraySchoolYear[1];
    }

    foreach ($gradeMapping as $gradeLevel => $schoolYear) {
        if (!$schoolYear) {
            die("Error: Missing school year for Grade $gradeLevel.");
        }
    }
    $filePath = $_SERVER['DOCUMENT_ROOT'] . '/sf_suite/school_forms_temp/SF10-JHS.xls';
    if (!file_exists($filePath)) {
        die("Error: Template file not found.");
    }
    $spreadsheet = IOFactory::load($filePath);
    $sheetFront = $spreadsheet->getSheetByName('FRONT');
    if (!$sheetFront) {
        die("Error: Sheet 'FRONT' not found.");
    }
    $schoolInfo = fetch_school_info($pdo);
    if ($schoolInfo) {
        $currentDate = date('F d, Y');
        $fields = [
            'C20' => $schoolInfo['school_name'],
            'C46' => $schoolInfo['school_name'],
            'D76' => $schoolInfo['school_name'],
            'G20' => $schoolInfo['school_id'],
            'G46' => $schoolInfo['school_id'],
            'I76' => $schoolInfo['school_id'],
            'I20' => $schoolInfo['district'],
            'I46' => $schoolInfo['district'],
            'K20' => $schoolInfo['division'],
            'K46' => $schoolInfo['division'],
            'M20' => $schoolInfo['region'],
            'M46' => $schoolInfo['region'],
            'E78' => $schoolInfo['school_head'],
            'H21' => $schoolInfo['school_year'],
            'B78' => $currentDate
        ];
        foreach ($fields as $cell => $value) {
            $sheetFront->setCellValue($cell, $value);
        }
    }
    $arraySection = getSections($pdo, $lrn);
    $grade7Section = $arraySection[7];
    $grade8Section = $arraySection[8];
    $grade9Section = $arraySection[9];
    $grade10Section = $arraySection[10];
    $sheetFront->setCellValue('F21', $grade7Section);
    $sheetFront->setCellValue('F47', $grade8Section);
    $students = grade($pdo, $lrn);
    if (empty($students)) {
        die("Error: No student found for LRN $lrn.");
    }
    $grade7SchoolYear = $arraySchoolYear[0];
    $grade8SchoolYear = $arraySchoolYear[1];
    $grade9SchoolYear = $arraySchoolYear[2];
    $grade10SchoolYear = $arraySchoolYear[3];
    $sheetFront->setCellValue('H21', $grade7SchoolYear);
    $sheetFront->setCellValue('H47', $grade8SchoolYear);
    $student = $students[0];
    $nameParts = array_map('trim', explode(',', $student['name'] ?? ''));
    $fullName = implode(' ', array_filter($nameParts));
    $studentFields = [
        'F75' => $fullName,
        'I75' => "'" . $student['lrn'] . "'",
        'D7' => $nameParts[0] ?? '',
        'G7' => $nameParts[1] ?? '',
        'K7' => $nameParts[3] ?? '',
        'M7' => $nameParts[2] ?? '',
        'E8' => "'" . $student['lrn'] . "'",
        'J8' => $student['birth_date'],
        'M8' => $student['sex']
    ];
    foreach ($studentFields as $cell => $value) {
        $sheetFront->setCellValue($cell, $value);
    }
    $startRow = 25;
    foreach ($gradeMapping as $gradeLevel => $schoolYear) {
        $startRow = fetchAndInsertGrades($pdo, $sheetFront, $schoolYear, $lrn, $gradeLevel, $startRow);
        $startRow += 19; // Move to the next section for the next grade level
    }
    $sheetBack = $spreadsheet->getSheetByName('BACK');
    if (!$sheetBack) {
        die("Error: Sheet 'BACK' not found.");
    }
    $sheetBack->setCellValue('F4', $grade9Section);
    $sheetBack->setCellValue('F29', $grade10Section);
    $sheetBack->setCellValue('H4', $grade9SchoolYear);
    $sheetBack->setCellValue('H29', $grade10SchoolYear);
    $gradeMapping9_10 = [
        9 => $arraySchoolYear[2] ?? null,
        10 => $arraySchoolYear[3] ?? null
    ];
    $startRowGrade9 = 8;
    $startRowGrade10 = 32;
    if (isset($gradeMapping9_10[9]) && $gradeMapping9_10[9]) {
        $schoolYear9 = $gradeMapping9_10[9];
        error_log("Processing Grade 9 for School Year $schoolYear9");
        $startRowGrade9 = fetchAndInsertGrades($pdo, $sheetBack, $schoolYear9, $lrn, 9, $startRowGrade9);
        $startRowGrade9 += 17;
    }
    if (isset($gradeMapping9_10[10]) && $gradeMapping9_10[10]) {
        $schoolYear10 = $gradeMapping9_10[10];
        error_log("Processing Grade 10 for School Year $schoolYear10");
        $startRowGrade10 = fetchAndInsertGrades($pdo, $sheetBack, $schoolYear10, $lrn, 10, $startRowGrade10);
        $startRowGrade10 += 17;
    }
    updateICARD($pdo, $spreadsheet, $lrn);
    $newFileName = 'SF-SUITE_SF10JHS_'. $fullName . '_' . date('Y-m-d_H-i-s') . '.xlsx';
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
function getSections($pdo, $lrn)
{
    $stmt = $pdo->prepare("SELECT school_year, section FROM student_tbl WHERE lrn = :lrn ORDER BY school_year ASC LIMIT 4");
    $stmt->execute(['lrn' => $lrn]);
    $arraySections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($arraySections) < 1) {
        die("Error: Not enough school years and sections found for LRN $lrn (needs at least 2).");
    }
    $sectionMapping = [
        7 => $arraySections[0]['section'] ?? null,
        8 => $arraySections[1]['section'] ?? null,
        9 => $arraySections[2]['section'] ?? null,
        10 => $arraySections[3]['section'] ?? null
    ];
    return $sectionMapping;
}
function fetchAndInsertGrades($pdo, $sheet, $schoolYear, $lrn, $gradeLevel, $startRow)
{
    $query = "SELECT subject_id, subject_name FROM subjects_tbl WHERE grade_level = :gradeLevel AND subject_term = :schoolYear AND archive = 0";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':gradeLevel' => $gradeLevel, ':schoolYear' => $schoolYear]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($subjects)) {
        error_log("No subjects found for Grade $gradeLevel (School Year: $schoolYear).");
        return $startRow;
    }
    $colMap = ['G', 'H', 'I', 'J'];
    foreach ($subjects as $subject) {
        $stmt = $pdo->prepare("SELECT fstq_grade_tr, scndq_grade_tr, trdq_grade_tr, fthq_grade_tr FROM student_grade_jhs_tbl WHERE lrn = :lrn AND subject_id = :subjectId AND grade_term = :schoolYear");
        $stmt->execute([':lrn' => $lrn, ':subjectId' => $subject['subject_id'], ':schoolYear' => $schoolYear]);
        $grades = $stmt->fetch(PDO::FETCH_ASSOC) ?? [];
        $sheet->setCellValue("B$startRow", $subject['subject_name']);
        $sum = 0;
        $count = 0;
        foreach ($colMap as $index => $col) {
            $gradeValue = $grades[["fstq_grade_tr", "scndq_grade_tr", "trdq_grade_tr", "fthq_grade_tr"][$index]] ?? 0;
            $sheet->setCellValue("$col$startRow", $gradeValue);
            if ($gradeValue > 0) {
                $sum += $gradeValue;
                $count++;
            }
        }
        $average = ($count > 0) ? round($sum / $count) : 0;
        $sheet->setCellValue("K$startRow", $average);
        $sheet->setCellValue("L$startRow", ($average >= 75) ? "Passed" : "Failed");
        $startRow++;
    }
    return $startRow;
}
function fetch_school_info($pdo)
{
    return $pdo->query("SELECT * FROM school_info_tbl LIMIT 1")->fetch(PDO::FETCH_ASSOC);
}
function updateICARD($pdo, $spreadsheet, $lrn)
{
    $sheetName = 'BACK';
    $sheet = $spreadsheet->getSheetByName($sheetName);
    if (!$sheet) {
        die("Error: Sheet '$sheetName' not found.");
    }
    $schoolInfo = fetch_school_info($pdo);
    if ($schoolInfo) {
        $currentDate = date('F d, Y');
        $fields = [
            'C3' => $schoolInfo['school_name'],
            'C28' => $schoolInfo['school_name'],
            'D82' => $schoolInfo['school_name'],
            'G3' => $schoolInfo['school_id'],
            'G28' => $schoolInfo['school_id'],
            'I82' => $schoolInfo['school_id'],
            'I3' => $schoolInfo['district'],
            'I28' => $schoolInfo['district'],
            'K3' => $schoolInfo['division'],
            'K28' => $schoolInfo['division'],
            'M3' => $schoolInfo['region'],
            'M28' => $schoolInfo['region'],
            'E84' => $schoolInfo['school_head'],
            'B84' => $currentDate
        ];
        foreach ($fields as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
    }
    $students = grade($pdo, $lrn);
    if (empty($students)) {
        die("Error: No student found for LRN $lrn.");
    }
    $student = $students[0];
    $nameParts = array_map('trim', explode(',', $student['name'] ?? ''));
    $fullName = implode(' ', array_filter($nameParts));
    $sheet->setCellValue('F81', $fullName)->setCellValue('I81', "'" . $student['lrn'] . "'");
}
function grade($pdo, $lrn)
{
    $query = "SELECT * FROM student_tbl WHERE 1=1" . (!empty($lrn) ? " AND lrn = :lrn" : "");
    $stmt = $pdo->prepare($query);
    if (!empty($lrn)) {
        $stmt->bindValue(':lrn', $lrn, PDO::PARAM_STR);
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
$lrn = $_GET['lrn'] ?? '';
update_sf10($pdo, $lrn);
?>