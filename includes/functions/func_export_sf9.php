<?php
require_once('../db_config.php');
require_once('../Excel/vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
function update_sf9($pdo, $schoolYear, $lrn, $userFullName)
{
    // Load the existing Excel file
    $filePath = $_SERVER['DOCUMENT_ROOT'] . '/sf_suite/school_forms_temp/SF9.xls';
    $spreadsheet = IOFactory::load($filePath);
    // Update ICARD sheet
    updateICARD($pdo, $spreadsheet, $schoolYear, $lrn);
    $sheetName = 'FBCARD';
    $sheet = $spreadsheet->getSheetByName($sheetName);
    if (!$sheet) {
        die("Error: Sheet '$sheetName' not found.");
    }
    $schoolInfo = fetch_school_info($pdo);
    if ($schoolInfo) {
        $sheet->setCellValue('Q8', $schoolInfo['region']);
        $sheet->setCellValue('Q10', $schoolInfo['district']);
        $sheet->setCellValue('S9', $schoolInfo['division']);
        $sheet->setCellValue('P13', $schoolInfo['school_name']);
    }
    // Fetch student data
    $students = grade($pdo, $schoolYear, $lrn);
    if (!empty($students)) {
        $student = $students[0];
        // Calculate age as of October 31
        $birthDate = new DateTime($student['birth_date']);
        $referenceDate = new DateTime(date('Y') . '-10-31');
        $age = $birthDate->diff($referenceDate)->y;
        // Set student data in specified cells
        $sheet->setCellValue('Q24', $student['name'])
            ->setCellValue('S26', "'" . $student['lrn'] . "'")
            ->setCellValue('Q28', $age)
            ->setCellValue('T28', $student['sex'])
            ->setCellValue('Q30', $student['grade_level'])
            ->setCellValue('T30', $student['section'])
            ->setCellValue('Q32', $student['school_year']);
    } else {
        die("Error: No student found for LRN $lrn.");
    }
    // Define months and corresponding columns
    $months = [
        'B' => 6,
        'C' => 7,
        'D' => 8,
        'E' => 9,
        'F' => 10,
        'G' => 11,
        'H' => 12,
        'I' => 1,
        'J' => 2,
        'K' => 3,
        'L' => 4
    ];
    $totalWeekdays = 0;
    $totalPresent = 0;
    $totalLate = 0;
    foreach ($months as $col => $month) {
        $year = ($month >= 6) ? date('Y') : date('Y') + 1;
        // Get total school days in the month
        $weekdays = countWeekdaysInMonth($year, $month);
        $sheet->setCellValue("{$col}9", $weekdays);
        $totalWeekdays += $weekdays;
        // Get present days
        $presentDays = getPresentDays($pdo, $schoolYear, $lrn, $year, $month);
        $sheet->setCellValue("{$col}11", $presentDays);
        $totalPresent += $presentDays;
        // Get late days
        $lateDays = getLateDays($pdo, $schoolYear, $lrn, $year, $month);
        $sheet->setCellValue("{$col}14", $lateDays); // INSERT in Row 14
        $totalLate += $lateDays;
    }
    // Insert totals in column M
    $sheet->setCellValue('M9', $totalWeekdays);
    $sheet->setCellValue('M11', $totalPresent);
    $sheet->setCellValue('M14', $totalLate); // Total late days
    $sheet->setCellValue('A34', $schoolInfo['school_head']);
    $sheet->setCellValue('H34', $userFullName);
    $sheet->setCellValue('P42', $schoolInfo['school_head']);
    $sheet->setCellValue('S42', $userFullName);
    $newFileName = 'SF-SUITE_SF9JHS_' . $student['name'] . '_' . date('Y-m-d_H-i-s') . '.xlsx';
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
function updateICARD($pdo, $spreadsheet, $schoolYear, $lrn)
{
    $sheetName = 'ICARD';
    $sheet = $spreadsheet->getSheetByName($sheetName);
    if (!$sheet) {
        die("Error: Sheet '$sheetName' not found.");
    }
    // Fetch student details to get grade level
    $students = grade($pdo, $schoolYear, $lrn);
    if (empty($students)) {
        die("Error: No student found for LRN $lrn.");
    }
    $student = $students[0];
    $gradeLevel = $student['grade_level'];
    // AND nested_id is null Fetch subjects for the grade level and school year
    $subjectQuery = "SELECT subject_id, subject_name 
                    FROM subjects_tbl 
                    WHERE grade_level = :gradeLevel 
                    AND subject_term = :schoolYear
                    ORDER BY subject_order ASC";
    $stmt = $pdo->prepare($subjectQuery);
    $stmt->execute([
        ':gradeLevel' => $gradeLevel,
        ':schoolYear' => $schoolYear
    ]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($subjects)) {
        error_log("No subjects found for grade level: $gradeLevel and school year: $schoolYear");
        return; // Exit if no subjects found
    }
    // Start inserting at row 9
    $startRow = 9;
    $colMap = ['D', 'E', 'F', 'G']; // Q1-Q4
    foreach ($subjects as $subject) {
        $subjectId = $subject['subject_id'];
        $subjectName = $subject['subject_name'];
        // Fetch grades for the subject AND nested_id is null
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
        // Insert grades into columns D-G, use 0 if no data found
        foreach ($colMap as $index => $col) {
            $gradeKey = ["fstq_grade_tr", "scndq_grade_tr", "trdq_grade_tr", "fthq_grade_tr"][$index];
            $gradeValue = isset($grades[$gradeKey]) ? $grades[$gradeKey] : "-";
            $sheet->setCellValue("$col$startRow", $gradeValue);
            // Calculate sum and count for average
            if ($gradeValue > 0) { // Only count valid grades
                $sum += $gradeValue;
                $count++;
            }
        }
        // Calculate rounded average in column H
        $average = ($count > 0) ? round($sum / $count) : "-";
        $sheet->setCellValue("H$startRow", $average);
        // Determine pass/fail status in column I
        if ($count != 4) {
            $status = " - ";  // If there aren't exactly 4 grades, show "-"
        } else {
            $status = ($average >= 75) ? "Passed" : "Failed"; // Normal pass/fail logic
        }
        $sheet->setCellValue("I$startRow", $status);
        $startRow++; // Move to next row for the next subject
    }
}
function getLateDays($pdo, $schoolYear, $lrn, $year, $month)
{
    // Query attendance for the specific student, school year, and month
    $query = "SELECT * FROM attendance_tbl 
              WHERE lrn = :lrn 
              AND attendance_term = :schoolYear  
              AND MONTH(created_at) = :month";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':lrn' => $lrn,
        ':schoolYear' => $schoolYear,
        ':month' => $month
    ]);
    $lateDays = 0;
    // Loop through each row returned from the query
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Loop through each day of the month
        for ($day = 1; $day <= 31; $day++) {
            $column = "Day" . $day;
            if (!isset($row[$column])) {
                continue; // Skip if column does not exist
            }
            $value = trim($row[$column] ?? '');
            // Validate date and skip weekends
            $dateStr = "$year-$month-$day";
            $date = DateTime::createFromFormat('Y-m-d', $dateStr);
            if (!$date || $date->format('N') >= 6) {
                continue; // Skip weekends
            }
            // Count 'L' as late (assuming 'L' indicates late)
            if ($value === 'X') {
                $lateDays++;
            }
        }
    }
    return $lateDays; // Return the count of late days
}
function getPresentDays($pdo, $schoolYear, $lrn, $year, $month)
{
    // Adjust the query to only filter by LRN, school year, and month
    $query = "SELECT * FROM attendance_tbl 
              WHERE lrn = :lrn 
              AND attendance_term = :schoolYear  
              AND MONTH(created_at) = :month";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':lrn' => $lrn,
        ':schoolYear' => $schoolYear,
        ':month' => $month
    ]);
    $presentDays = 0;
    // Loop through each row returned from the query
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Loop through each day of the month
        for ($day = 1; $day <= 31; $day++) {
            $column = "Day" . $day;
            // Check if the column exists in the row
            if (!isset($row[$column])) {
                continue; // Skip if the column does not exist
            }
            // Convert NULL to empty string
            $value = trim($row[$column] ?? '');
            // Check for weekdays only
            $dateStr = "$year-$month-$day";
            $date = DateTime::createFromFormat('Y-m-d', $dateStr);
            // Skip weekends
            if (!$date || $date->format('N') >= 6) {
                continue; // Skip weekends
            }
            // Count 'X' as present
            if ($value === 'X') {
                $presentDays++;
            }
        }
    }
    // Calculate total weekdays in the month
    $totalWeekdays = countWeekdaysInMonth($year, $month);
    // Subtract present days from total weekdays
    $absentDays = $totalWeekdays - $presentDays;
    // Debugging output
    error_log("LRN: $lrn, Year: $year, Month: $month, Present Days: $presentDays, Total Weekdays: $totalWeekdays, Absent Days: $absentDays");
    return $absentDays; // Return the number of absent days
}
// Function to count weekdays (Monday to Friday) in a given month
function countWeekdaysInMonth($year, $month)
{
    $startDate = new DateTime("$year-$month-01");
    $endDate = new DateTime("$year-$month-" . $startDate->format('t')); // Last day of the month
    $count = 0;
    while ($startDate <= $endDate) {
        if ($startDate->format('N') < 6) { // Monday to Friday (1-5)
            $count++;
        }
        $startDate->modify('+1 day');
    }
    return $count;
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
$lrn = isset($_GET['lrn']) ? $_GET['lrn'] : '';
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
update_sf9($pdo, $schoolYear, $lrn, $userFullName);
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