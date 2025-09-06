<?php
require_once('../db_config.php');
require_once('../Excel/vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory; 
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
function update_sf5($pdo, $schoolYear, $gradeLevel, $studentSection, $userFullName) {
    // Load the existing Excel file
$filePath = $_SERVER['DOCUMENT_ROOT'] . '/sf_suite/school_forms_temp/SF5.xls';
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $schoolInfo = fetch_school_info($pdo);
    if ($schoolInfo) {
        $sheet->setCellValue('C3', $schoolInfo['region']);
        $sheet->setCellValue('E3', $schoolInfo['district']);
        $sheet->setCellValue('I3', $schoolInfo['division']);
        $sheet->setCellValue('C5', $schoolInfo['school_id']);
        $sheet->setCellValue('C7', $schoolInfo['school_name']);
        $sheet->setCellValue('I5', $schoolInfo['school_curriculum']);
    }
    // Insert the school year, grade level, and student section into the specified cells
    $sheet->setCellValue('G5', $schoolYear);
    $sheet->setCellValue('I7', $gradeLevel);
    $sheet->setCellValue('K7', $studentSection);
    // Fetch student data
    $students = promotion_list($pdo, $schoolYear, $gradeLevel, $studentSection);
    // Separate students by sex
    $maleStudents = [];
    $femaleStudents = [];
    foreach ($students as $student) {
        if ($student['sex'] === 'M') {
            $maleStudents[] = $student;
        } elseif ($student['sex'] === 'F') {
            $femaleStudents[] = $student;
        }
    }
    // Get row 7 style and merge info
    $rowTemplate = 13;
    $styleArray = $sheet->getStyle('A' . $rowTemplate . ':AS' . $rowTemplate);
    $mergedCells = $sheet->getMergeCells();
    // Start inserting data from row 13
    $row = 13;
    // Insert male students
    foreach ($maleStudents as $student) {
        if ($row > 13) {
            $sheet->insertNewRowBefore($row, 1);
        }
        // Copy styles from row 7
        $sheet->duplicateStyle($styleArray, 'A' . $row . ':K' . $row);
        // Copy merged cells structure
        foreach ($mergedCells as $merge) {
            $range = explode(':', $merge);
            preg_match('/([A-Z]+)(\d+)/', $range[0], $start);
            preg_match('/([A-Z]+)(\d+)/', $range[1], $end);
            if ((int)$start[2] == $rowTemplate) {
                $newMerge = $start[1] . $row . ':' . $end[1] . $row;
                $sheet->mergeCells($newMerge);
            }
        }
        // Set student data
        $sheet->setCellValue('A' . $row, "'" . $student['lrn'] . "'")
              ->setCellValue('B' . $row, $student['name'])
              ->setCellValue('F' . $row, $student['general_average'])
              ->setCellValue('G' . $row, $student['action_taken'])
              ->setCellValue('H' . $row, $student['cecs'])
              ->setCellValue('J' . $row, $student['ecs']);
        $row++;
    }
    // Insert total count of male students
    $maleCount = count($maleStudents);
    $sheet->setCellValue('F' . $row, $maleCount);
    $row++;
    // Insert female students
    foreach ($femaleStudents as $student) {
        if ($row > 13) {
            $sheet->insertNewRowBefore($row, 1);
        }
        // Copy styles from row 7
        $sheet->duplicateStyle($styleArray, 'A' . $row . ':K' . $row);
        // Copy merged cells structure
        foreach ($mergedCells as $merge) {
            $range = explode(':', $merge);
            preg_match('/([A-Z]+)(\d+)/', $range[0], $start);
            preg_match('/([A-Z]+)(\d+)/', $range[1], $end);
            if ((int)$start[2] == $rowTemplate) {
                $newMerge = $start[1] . $row . ':' . $end[1] . $row;
                $sheet->mergeCells($newMerge);
            }
        }
        // Set student data
        $sheet->setCellValue('A' . $row, "'" . $student['lrn'] . "'")
              ->setCellValue('B' . $row, $student['name'])
              ->setCellValue('F' . $row, $student['general_average'])
              ->setCellValue('G' . $row, $student['action_taken'])
              ->setCellValue('H' . $row, $student['cecs'])
              ->setCellValue('J' . $row, $student['ecs']);
        $row++;
    }
    // Insert total count of female students
    $femaleCount = count($femaleStudents);
    $sheet->setCellValue('F' . $row, $femaleCount);
    $row++;
    // After inserting total count of male and female students
    $totalCount = $maleCount + $femaleCount;
    $sheet->setCellValue('F' . $row, $totalCount);
    $row++;
    $userNameRow = $row + 14;
    $sheet->setCellValue("F$userNameRow", $userFullName);
    $schoolHeadRow = $userNameRow + 5;
    $sheet->setCellValue("F$schoolHeadRow", $schoolInfo['school_head']);
    // Jump 3 rows below the current row (row is now where total count is)
    $row += 2;
    // Count the number of male students who are promoted
    $promotedMaleCount = 0;
    foreach ($maleStudents as $student) {
        if (strtoupper($student['action_taken']) === 'PROMOTED') {
            $promotedMaleCount++;
        }
    }
    // Count the number of female students who are promoted
    $promotedFemaleCount = 0;
    foreach ($femaleStudents as $student) {
        if (strtoupper($student['action_taken']) === 'PROMOTED') {
            $promotedFemaleCount++;
        }
    }
    // Insert the count of promoted male students into column B of the new row
    $sheet->setCellValue('B' . $row, $promotedMaleCount);
    // Insert the count of promoted female students into column C of the same row
    $sheet->setCellValue('C' . $row,  $promotedFemaleCount);
    // Insert the sum of promoted male and female students into column D
    $sheet->setCellValue('D' . $row, $promotedMaleCount + $promotedFemaleCount);
    // Increment row counter if necessary
    $row++;
    // Jump 2 rows below the current row
    $row += 1;
    // Count the number of male students who are irregular
    $irregularMaleCount = 0;
    foreach ($maleStudents as $student) {
        if (strtoupper($student['action_taken']) === 'IRREGULAR') {
            $irregularMaleCount++;
        }
    }
    // Count the number of female students who are irregular
    $irregularFemaleCount = 0;
    foreach ($femaleStudents as $student) {
        if (strtoupper($student['action_taken']) === 'IRREGULAR') {
            $irregularFemaleCount++;
        }
    }
    // Insert the count of irregular male students into column B of the new row
    $sheet->setCellValue('B' . $row, $irregularMaleCount);
    // Insert the count of irregular female students into column C of the same row
    $sheet->setCellValue('C' . $row,  $irregularFemaleCount);
    // Insert the sum of irregular male and female students into column D
    $sheet->setCellValue('D' . $row, $irregularMaleCount + $irregularFemaleCount);
    // Increment row counter if necessary
    $row++;
    // Jump 2 rows below the current row for RETAINED counts
    $row += 1;
    // Count the number of male students who are retained
    $retainedMaleCount = 0;
    foreach ($maleStudents as $student) {
        if (strtoupper($student['action_taken']) === 'RETAINED') {
            $retainedMaleCount++;
        }
    }
    // Count the number of female students who are retained
    $retainedFemaleCount = 0;
    foreach ($femaleStudents as $student) {
        if (strtoupper($student['action_taken']) === 'RETAINED') {
            $retainedFemaleCount++;
        }
    }
    // Insert the count of retained male students into column B of the new row
    $sheet->setCellValue('B' . $row, $retainedMaleCount);
    // Insert the count of retained female students into column C of the same row
    $sheet->setCellValue('C' . $row, $retainedFemaleCount);
    // Insert the sum of retained male and female students into column D
    $sheet->setCellValue('D' . $row, $retainedMaleCount + $retainedFemaleCount);
    // Increment row counter if necessary
    $row++;
    // Jump 4 rows below the current row for general average counts
    $row += 4;
    // Count the number of male students with general_average <= 74
    $lowAverageMaleCount = 0;
    foreach ($maleStudents as $student) {
        if ($student['general_average'] <= 74) {
            $lowAverageMaleCount++;
        }
    }
    // Count the number of female students with general_average <= 74
    $lowAverageFemaleCount = 0;
    foreach ($femaleStudents as $student) {
        if ($student['general_average'] <= 74) {
            $lowAverageFemaleCount++;
        }
    }
    // Insert the count of male students with low average into column B
    $sheet->setCellValue('B' . $row, $lowAverageMaleCount);
    // Insert the count of female students with low average into column C
    $sheet->setCellValue('C' . $row, $lowAverageFemaleCount);
    // Insert the sum of male and female students with low average into column D
    $sheet->setCellValue('D' . $row, $lowAverageMaleCount + $lowAverageFemaleCount);
    // Jump 1 row for 75-79
    $row+=2;
    $count75to79Male = 0;
    $count75to79Female = 0;
    foreach ($maleStudents as $student) {
        if ($student['general_average'] >= 75 && $student['general_average'] < 80) {
            $count75to79Male++;
        }
    }
    foreach ($femaleStudents as $student) {
        if ($student['general_average'] >= 75 && $student['general_average'] < 80) {
            $count75to79Female++;
        }
    }
    $sheet->setCellValue('B' . $row, $count75to79Male);
    $sheet->setCellValue('C' . $row, $count75to79Female);
    $sheet->setCellValue('D' . $row, $count75to79Male + $count75to79Female);
    // Jump 1 row for 80-84
    $row+=2;
    $count80to84Male = 0;
    $count80to84Female = 0;
    foreach ($maleStudents as $student) {
        if ($student['general_average'] >= 80 && $student['general_average'] < 85) {
            $count80to84Male++;
        }
    }
    foreach ($femaleStudents as $student) {
        if ($student['general_average'] >= 80 && $student['general_average'] < 85) {
            $count80to84Female++;
        }
    }
    $sheet->setCellValue('B' . $row, $count80to84Male);
    $sheet->setCellValue('C' . $row, $count80to84Female);
    $sheet->setCellValue('D' . $row, $count80to84Male + $count80to84Female);
    // Jump 1 row for 85-89
    $row+=2;
    $count85to89Male = 0;
    $count85to89Female = 0;
    foreach ($maleStudents as $student) {
        if ($student['general_average'] >= 85 && $student['general_average'] < 90) {
            $count85to89Male++;
        }
    }
    foreach ($femaleStudents as $student) {
        if ($student['general_average'] >= 85 && $student['general_average'] < 90) {
            $count85to89Female++;
        }
    }
    $sheet->setCellValue('B' . $row, $count85to89Male);
    $sheet->setCellValue('C' . $row, $count85to89Female);
    $sheet->setCellValue('D' . $row, $count85to89Male + $count85to89Female);
    // Jump 1 row for 90-100
    $row+=2;
    $count90to100Male = 0;
    $count90to100Female = 0;
    foreach ($maleStudents as $student) {
        if ($student['general_average'] >= 90 && $student['general_average'] <= 100) {
            $count90to100Male++;
        }
    }
    foreach ($femaleStudents as $student) {
        if ($student['general_average'] >= 90 && $student['general_average'] <= 100) {
            $count90to100Female++;
        }
    }
    $sheet->setCellValue('B' . $row, $count90to100Male);
    $sheet->setCellValue('C' . $row, $count90to100Female);
    $sheet->setCellValue('D' . $row, $count90to100Male + $count90to100Female);
    $newFileName = 'SF-SUITE_SF5_' . $studentSection . '-' . $schoolYear . date('Y-m-d_H-i-s') . '.xlsx';
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
update_sf5($pdo, $schoolYear, $gradeLevel, $studentSection, $userFullName);
// Ensure the student_list function is defined
function promotion_list($pdo, $schoolYear, $gradeLevel, $studentSection) {
    $query = "SELECT * FROM prom_achievement_tbl WHERE 1=1";
    if (!empty($schoolYear)) {
        $query .= " AND school_year = :schoolYear";
    }
    if (!empty($gradeLevel)) {
        $query .= " AND grade_level = :gradeLevel";
    }
    if (!empty($studentSection)) {
        $query .= " AND section = :studentSection";
    }
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