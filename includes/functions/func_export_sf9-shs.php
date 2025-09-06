<?php

require_once('../db_config.php');
require_once('../Excel/vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function update_sf9($pdo, $schoolYear, $lrn, $userFullName)
{
    // Load the existing Excel file
$filePath = $_SERVER['DOCUMENT_ROOT'] . '/sf_suite/school_forms_temp/SF9-SHS.xls';
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
        $sheet->setCellValue('Q7', $schoolInfo['region']);
        $sheet->setCellValue('Q10', $schoolInfo['division']);
        $sheet->setCellValue('P12', $schoolInfo['school_name']);
        $sheet->setCellValue('R27', $schoolInfo['school_curriculum']);
    }
    $sheet->setCellValue('R28', $schoolYear);
    // Fetch student data
    $students = grade($pdo, $schoolYear, $lrn);

    if (!empty($students)) {
        $student = $students[0];

        // Calculate age as of October 31
        $birthDate = new DateTime($student['birth_date']);
        $referenceDate = new DateTime(date('Y') . '-10-31');
        $age = $birthDate->diff($referenceDate)->y;

        // Set student data in specified cells
        $sheet->setCellValue('Q22', $student['name'])
            ->setCellValue('Q24', $age)
            ->setCellValue('T24', $student['sex'])
            ->setCellValue('T3', "'" . $student['lrn'] . "'")
            ->setCellValue('Q26', $student['grade_level'])
            ->setCellValue('T26', $student['section'])
            ->setCellValue('R28', $student['school_year'])
            ->setCellValue('R29', $student['strand_track']);
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
        $sheet->setCellValue("{$col}7", $weekdays);
        $totalWeekdays += $weekdays;

        // Get present days
        $presentDays = getPresentDays($pdo, $schoolYear, $lrn, $year, $month);
        $sheet->setCellValue("{$col}9", $presentDays);
        $totalPresent += $presentDays;

        // Get late days
        $lateDays = getLateDays($pdo, $schoolYear, $lrn, $year, $month);
        $sheet->setCellValue("{$col}12", $lateDays); // INSERT in Row 14
        $totalLate += $lateDays;
    }

    // Insert totals in column M
    $sheet->setCellValue('M7', $totalWeekdays);
    $sheet->setCellValue('M9', $totalPresent);
    $sheet->setCellValue('M12', $totalLate); // Total late days
    $sheet->setCellValue('A32', $schoolInfo['school_head']);
    $sheet->setCellValue('H32', $userFullName);
    $sheet->setCellValue('P40', $schoolInfo['school_head']);
    $sheet->setCellValue('S40', $userFullName);
    // Save and force download
    $newFileName = 'SF-SUITE_SF9SHS_' . $student['name'] . '_' . date('Y-m-d_H-i-s') . '.xlsx';
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

    // Function to fetch subjects by type and subject_semester
    function fetchSubjects($pdo, $gradeLevel, $schoolYear, $subjectType, $subjectSemester)
    {
        $subjectQuery = "SELECT subject_id, subject_name
                         FROM subjects_tbl 
                         WHERE grade_level = :gradeLevel 
                           AND subjectType = :subjectType
                           AND subject_term = :schoolYear
                           AND subject_semester = :subjectSemester
                           ORDER BY subject_order ASC";
        $stmt = $pdo->prepare($subjectQuery);
        $stmt->execute([
            ':gradeLevel' => $gradeLevel,
            ':schoolYear' => $schoolYear,
            ':subjectType' => $subjectType,
            ':subjectSemester' => $subjectSemester
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // For Semester 1
    $semester1 = 1;
    // Fetch Core subjects for Semester 1
    $coreSubjectsSem1 = fetchSubjects($pdo, $gradeLevel, $schoolYear, 'Core', $semester1);
    // Fetch Specialized and Applied subjects for Semester 1
    $specializedSubjectsSem1 = array_merge(
        fetchSubjects($pdo, $gradeLevel, $schoolYear, 'Applied', $semester1),
        fetchSubjects($pdo, $gradeLevel, $schoolYear, 'Specialized', $semester1)
    );

    // For Semester 2
    $semester2 = 2;
    // Fetch Core subjects for Semester 2
    $coreSubjectsSem2 = fetchSubjects($pdo, $gradeLevel, $schoolYear, 'Core', $semester2);
    // Fetch Specialized and Applied subjects for Semester 2
    $specializedSubjectsSem2 = array_merge(
        fetchSubjects($pdo, $gradeLevel, $schoolYear, 'Applied', $semester2),
        fetchSubjects($pdo, $gradeLevel, $schoolYear, 'Specialized', $semester2)
    );

    // Check if any subjects were found (for at least one semester)
    if (
        (empty($coreSubjectsSem1) && empty($specializedSubjectsSem1)) &&
        (empty($coreSubjectsSem2) && empty($specializedSubjectsSem2))
    ) {
        error_log("No subjects found for grade level: $gradeLevel and school year: $schoolYear");
        return;
    }

    $colMap = ['C', 'D'];
    function insertGrades($subjects, $pdo, $sheet, $lrn, $schoolYear, $startRow, $colMap, $gradeSemester)
    {
        foreach ($subjects as $subject) {
            $subjectId = $subject['subject_id'];
            $subjectName = $subject['subject_name'];

            // Fetch grades for the subject using the provided grade_semester
            $gradesQuery = "SELECT fsts_grade_tr, scnds_grade_tr
                            FROM student_grade_shs_tbl 
                            WHERE lrn = :lrn 
                              AND subject_id = :subjectId
                              AND grade_term = :schoolYear
                              AND grade_semester = :gradeSemester";
            $stmt = $pdo->prepare($gradesQuery);
            $stmt->execute([
                ':lrn' => $lrn,
                ':subjectId' => $subjectId,
                ':schoolYear' => $schoolYear,
                ':gradeSemester' => $gradeSemester
            ]);
            $grades = $stmt->fetch(PDO::FETCH_ASSOC);

            // Set subject name in column B of the current row
            $sheet->setCellValue("B$startRow", $subjectName);

            $sum = 0;
            $count = 0;

            // Insert each grade into its mapped column (C & D)
            foreach ($colMap as $index => $col) {
                $gradeKey = ["fsts_grade_tr", "scnds_grade_tr"][$index];
                $gradeValue = isset($grades[$gradeKey]) ? $grades[$gradeKey] : "-";
                $sheet->setCellValue("$col$startRow", $gradeValue);

                if ($gradeValue > 0) { // Only count valid grades
                    $sum += $gradeValue;
                    $count++;
                }
            }

            // Calculate the rounded average and place it in column E
            $average = ($count > 0) ? round($sum / $count) : "-";
            $sheet->setCellValue("E$startRow", $average);

            $startRow++; // Move to the next row
        }
    }

    // --- Inserting for Semester 1 (grade_semester = 1) ---
    // Core subjects starting from row 7
    $coreStartRowSem1 = 7;
    insertGrades($coreSubjectsSem1, $pdo, $sheet, $lrn, $schoolYear, $coreStartRowSem1, $colMap, $semester1);

    // Specialized and Applied subjects starting from row 15
    $specializedStartRowSem1 = 14;
    insertGrades($specializedSubjectsSem1, $pdo, $sheet, $lrn, $schoolYear, $specializedStartRowSem1, $colMap, $semester1);

    // --- Inserting for Semester 2 (grade_semester = 2) ---
    // Core subjects starting from row 23
    $coreStartRowSem2 = 23;
    insertGrades($coreSubjectsSem2, $pdo, $sheet, $lrn, $schoolYear, $coreStartRowSem2, $colMap, $semester2);

    // Specialized and Applied subjects starting from row 30
    $specializedStartRowSem2 = 30;
    insertGrades($specializedSubjectsSem2, $pdo, $sheet, $lrn, $schoolYear, $specializedStartRowSem2, $colMap, $semester2);
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