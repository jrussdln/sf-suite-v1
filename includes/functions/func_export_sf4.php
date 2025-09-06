<?php
require_once('../db_config.php');
require_once('../Excel/vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$schoolYear = isset($_GET['schoolYear']) ? $_GET['schoolYear'] : '';
$studentMonth = isset($_GET['studentMonth']) ? $_GET['studentMonth'] : '';
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
update_sf4($pdo, $schoolYear, $studentMonth, $userFullName);
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
function update_sf4($pdo, $schoolYear, $studentMonth, $userFullName)
{
    // Load the existing Excel file
    $filePath = $_SERVER['DOCUMENT_ROOT'] . '/sf_suite/school_forms_temp/SF4.xls';
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $monthName = date("F", mktime(0, 0, 0, $studentMonth, 1));
    $schoolInfo = fetch_school_info($pdo);
    if ($schoolInfo) {
        $sheet->setCellValue('D5', $schoolInfo['school_id']);
        $sheet->setCellValue('C7', $schoolInfo['school_name']);
        $sheet->setCellValue('I5', $schoolInfo['region']);
        $sheet->setCellValue('N5', $schoolInfo['division']);
        $sheet->setCellValue('Y5', $schoolInfo['district']);
        $sheet->setCellValue('I5', $schoolInfo['region']);
        $sheet->setCellValue('N5', $schoolInfo['division']);
    }
    $sheet->setCellValue('Y7', $schoolYear);
    $sheet->setCellValue('AJ7', $monthName);
    $section = section_list($pdo, $schoolYear);
    $row = 12;  // Starting row for data insertion
    $style = $sheet->getStyle('A12:AN12');
    $mergedCells = $sheet->getMergeCells();
    $mergedInRow12 = [];
    foreach ($mergedCells as $mergedRange) {
        if (preg_match('/(\D+)12:(\D+)12/', $mergedRange, $matches)) {
            $mergedInRow12[] = $mergedRange;
        }
    }
    $lastInsertedRow = $row;  // Track the last inserted row
    foreach ($section as $student) {
        // Insert a new row before the current row
        $sheet->insertNewRowBefore($row, 1);
        // Fetch PersonnelId based on schoolYear and GradeLevel
        $personnelId = getPersonnelId($pdo, $schoolYear, $student['SectionName']);
        $fullName = getPersonnelName($pdo, $personnelId);
        //student count
        $maleCount = getStudentCount($pdo, $schoolYear, $student['SectionName'], 'M');
        $femaleCount = getStudentCount($pdo, $schoolYear, $student['SectionName'], 'F');
        $totalCount = $maleCount + $femaleCount;
        //Averge
        $countMaleAbsences = getAttendanceCount($pdo, $schoolYear, $student['SectionName'], 'M', $studentMonth);
        $countFemaleAbsences = getAttendanceCount($pdo, $schoolYear, $student['SectionName'], 'F', $studentMonth);
        $totalAbsences = $countMaleAbsences + $countFemaleAbsences;
        // Absence rate calculations
        $maleAbsenceRate = ($maleCount > 0) ? round(($countMaleAbsences / $maleCount) * 100) : 0;
        $femaleAbsenceRate = ($femaleCount > 0) ? round(($countFemaleAbsences / $femaleCount) * 100) : 0;
        $overallAbsenceRate = ($totalCount > 0) ? round(($totalAbsences / $totalCount) * 100) : 0;
        // Get count for columns N and O
        $countMaleDO = getAttendanceDOCount($pdo, $schoolYear, $student['SectionName'], 'M', $studentMonth);
        $countFemaleDO = getAttendanceDOCount($pdo, $schoolYear, $student['SectionName'], 'F', $studentMonth);
        $totalDO = $countMaleDO + $countFemaleDO; // Column P
        // Get counts for the previous month (N, O, P)
        $countMaleDOPrevious = getAttendanceDOCountForPreviousMonth($pdo, $schoolYear, $student['SectionName'], 'M', $studentMonth);
        $countFemaleDOPrevious = getAttendanceDOCountForPreviousMonth($pdo, $schoolYear, $student['SectionName'], 'F', $studentMonth);
        $totalDOPrevious = $countMaleDOPrevious + $countFemaleDOPrevious; // Column P
        // Absence rate calculations
        $sumMaleDOp = ($countMaleDO + $countMaleDOPrevious);
        $sumFemaleDOp = ($countFemaleDO + $countFemaleDOPrevious);
        $sumTotalDOp = ($totalDO + $totalDOPrevious);
        // Set data
        // Get count for columns W and X (Current Month T/O)
        $countMaleTO = getAttendanceTOCount($pdo, $schoolYear, $student['SectionName'], 'M', $studentMonth);
        $countFemaleTO = getAttendanceTOCount($pdo, $schoolYear, $student['SectionName'], 'F', $studentMonth);
        $totalTO = $countMaleTO + $countFemaleTO; // Column Y
        // Get counts for the previous month (W, X, Y)
        $countMaleTOPrevious = getAttendanceTOCountForPreviousMonth($pdo, $schoolYear, $student['SectionName'], 'M', $studentMonth);
        $countFemaleTOPrevious = getAttendanceTOCountForPreviousMonth($pdo, $schoolYear, $student['SectionName'], 'F', $studentMonth);
        $totalTOPrevious = $countMaleTOPrevious + $countFemaleTOPrevious; // Column Y
        // Absence rate calculations for T/O
        $sumMaleTOp = ($countMaleTO + $countMaleTOPrevious);
        $sumFemaleTOp = ($countFemaleTO + $countFemaleTOPrevious);
        $sumTotalTOp = ($totalTO + $totalTOPrevious);
        // Get count for columns AF and AG (Current Month T/I)
        $countMaleTI = getAttendanceTICount($pdo, $schoolYear, $student['SectionName'], 'M', $studentMonth);
        $countFemaleTI = getAttendanceTICount($pdo, $schoolYear, $student['SectionName'], 'F', $studentMonth);
        $totalTI = $countMaleTI + $countFemaleTI; // Column AH
        // Get counts for the previous month (AF, AG, AH)
        $countMaleTIPrevious = getAttendanceTICountForPreviousMonth($pdo, $schoolYear, $student['SectionName'], 'M', $studentMonth);
        $countFemaleTIPrevious = getAttendanceTICountForPreviousMonth($pdo, $schoolYear, $student['SectionName'], 'F', $studentMonth);
        $totalTIPrevious = $countMaleTIPrevious + $countFemaleTIPrevious; // Column AH
        // Absence rate calculations for T/I
        $sumMaleTIp = ($countMaleTI + $countMaleTIPrevious);
        $sumFemaleTIp = ($countFemaleTI + $countFemaleTIPrevious);
        $sumTotalTIp = ($totalTI + $totalTIPrevious);
        $sheet->setCellValue('A' . $row, $student['GradeLevel'])
            ->setCellValue('B' . $row, $student['SectionName'])
            ->setCellValue('C' . $row, $fullName)  // Display fetched name
            ->setCellValue('E' . $row, $maleCount)  // Count of male students
            ->setCellValue('F' . $row, $femaleCount)  // Count of female students
            ->setCellValue('G' . $row, $totalCount)  // Sum of male and female counts
            ->setCellValue('H' . $row, $countMaleAbsences) // Male absences
            ->setCellValue('I' . $row, $countFemaleAbsences) // Female absences
            ->setCellValue('J' . $row, $totalAbsences) // Total absences
            ->setCellValue('K' . $row, $maleAbsenceRate) // Male Absenteeism %
            ->setCellValue('L' . $row, $femaleAbsenceRate) // Female Absenteeism %
            ->setCellValue('M' . $row, $overallAbsenceRate) // Overall Absenteeism %
            ->setCellValue('N' . $row, $countMaleDOPrevious) // Previous Month Male 'DRP'
            ->setCellValue('O' . $row, $countFemaleDOPrevious) // Previous Month Female 'DRP'
            ->setCellValue('P' . $row, $totalDOPrevious) // Previous Month Total 'DRP'
            ->setCellValue('Q' . $row, $countMaleDO) // Count of Male 'DRP'
            ->setCellValue('R' . $row, $countFemaleDO) // Count of Female 'DRP'
            ->setCellValue('S' . $row, $totalDO) // Total (sum of N and O)
            ->setCellValue('T' . $row, $sumMaleDOp) // Count of Male 'DRP'
            ->setCellValue('U' . $row, $sumFemaleDOp) // Count of Female 'DRP'
            ->setCellValue('V' . $row, $sumTotalDOp) // Total (sum of N and O)
            ->setCellValue('W' . $row, $countMaleTOPrevious) // Previous Month Male 'T/O'
            ->setCellValue('X' . $row, $countFemaleTOPrevious) // Previous Month Female 'T/O'
            ->setCellValue('Y' . $row, $totalTOPrevious) // Previous Month Total 'T/O'
            ->setCellValue('Z' . $row, $countMaleTO) // Count of Male 'T/O'
            ->setCellValue('AA' . $row, $countFemaleTO) // Count of Female 'T/O'
            ->setCellValue('AB' . $row, $totalTO) // Total (sum of W and X)
            ->setCellValue('AC' . $row, $sumMaleTOp) // Sum of Male 'T/O' (previous + current)
            ->setCellValue('AD' . $row, $sumFemaleTOp) // Sum of Female 'T/O' (previous + current)
            ->setCellValue('AE' . $row, $sumTotalTOp) // Total sum of previous + current 'T/O'
            ->setCellValue('AF' . $row, $countMaleTIPrevious) // Previous Month Male 'T/I'
            ->setCellValue('AG' . $row, $countFemaleTIPrevious) // Previous Month Female 'T/I'
            ->setCellValue('AH' . $row, $totalTIPrevious) // Previous Month Total 'T/I'
            ->setCellValue('AI' . $row, $countMaleTI) // Count of Male 'T/I'
            ->setCellValue('AJ' . $row, $countFemaleTI) // Count of Female 'T/I'
            ->setCellValue('AK' . $row, $totalTI) // Total (sum of AF and AG)
            ->setCellValue('AL' . $row, $sumMaleTIp) // Sum of Male 'T/I' (previous + current)
            ->setCellValue('AM' . $row, $sumFemaleTIp) // Sum of Female 'T/I' (previous + current)
            ->setCellValue('AN' . $row, $sumTotalTIp); // Total sum of previous + current 'T/I'
        // Apply the style to the new row
        $sheet->duplicateStyle($style, 'A' . $row . ':AN' . $row);
        // Reapply merged cells from row 12 to the new row
        foreach ($mergedInRow12 as $mergedRange) {
            $newMergedRange = preg_replace('/12/', $row, $mergedRange);
            $sheet->mergeCells($newMergedRange);
        }
        $lastInsertedRow = $row;
        $row++;  // Move to the next row for the next student
    }
    // Starting row for totals based on grade levels
    $gradeLevels = range(7, 12);
    $currentRow = $lastInsertedRow + 4;
    foreach ($gradeLevels as $grade) {
        // Insert a row for each grade level total
        $sheet->insertNewRowBefore($currentRow, 1);
        // Set grade label in column A
        $sheet->setCellValue('A' . $currentRow, 'GRADE ' . $grade);
        for ($col = 5; $col <= 40; $col++) {  // Columns E (5) to AN (40)
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $columnRange = $columnLetter . '12:' . $columnLetter . $lastInsertedRow;
            // Check if the column is K, L, or M (11, 12, 13)
            if ($col >= 11 && $col <= 13) {
                // Average formula for columns K, L, M with default value of 0
                $totalFormula = "=IFERROR(AVERAGEIF(A12:A$lastInsertedRow, $grade, $columnLetter" . "12:$columnLetter$lastInsertedRow), 0)";
            } else {
                // Conditional sum formula for other columns with default value of 0
                $totalFormula = "=IFERROR(SUMIF(A12:A$lastInsertedRow, $grade, $columnLetter" . "12:$columnLetter$lastInsertedRow), 0)";
            }
            $sheet->setCellValue($columnLetter . $currentRow, $totalFormula);
        }
        $currentRow++;
    }
    // Insert row for overall total
    $sheet->insertNewRowBefore($currentRow, 1);
    $sheet->setCellValue('A' . $currentRow, 'TOTAL');
    for ($col = 5; $col <= 40; $col++) {
        $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
        $columnRange = $columnLetter . '12:' . $columnLetter . $lastInsertedRow;
        // Check if the column is K, L, or M (11, 12, 13)
        if ($col >= 11 && $col <= 13) {
            // Average formula for columns K, L, M with default value of 0
            $totalFormula = '=IFERROR(AVERAGE(' . $columnRange . '), 0)';
        } else {
            // Sum formula for other columns with default value of 0
            $totalFormula = '=IFERROR(SUM(' . $columnRange . '), 0)';
        }
        $sheet->setCellValue($columnLetter . $currentRow, $totalFormula);
    }
    $schoolHeadRow = $lastInsertedRow + 14;
    $sheet->setCellValue("AC$schoolHeadRow", $schoolInfo['school_head']);
    $newFileName = 'SF-SUITE_SF4_' . $schoolYear . date('Y-m-d_H-i-s') . '.xlsx';
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
function getAttendanceTICount($pdo, $schoolYear, $section, $gender, $studentMonth)
{
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT a.lrn) 
        FROM attendance_tbl a
        JOIN student_tbl s ON a.lrn = s.lrn
        WHERE a.attendance_term = :schoolYear
        AND a.section = :section
        AND s.sex = :gender
        AND SUBSTRING_INDEX(a.attendance_remarks, ' ', 1) = 'T/I'
        AND MONTH(a.created_at) = :studentMonth
    ");
    $stmt->execute([
        ':schoolYear' => $schoolYear,
        ':section' => $section,
        ':gender' => $gender,
        ':studentMonth' => $studentMonth
    ]);
    return $stmt->fetchColumn();
}
function getAttendanceTICountForPreviousMonth($pdo, $schoolYear, $section, $gender, $studentMonth)
{
    $previousMonth = ($studentMonth == 1) ? 12 : $studentMonth - 1; // Handle December to January transition
    $previousYear = ($studentMonth == 1) ? date('Y') - 1 : date('Y');
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT a.lrn) 
        FROM attendance_tbl a
        JOIN student_tbl s ON a.lrn = s.lrn
        WHERE a.attendance_term = :schoolYear
        AND a.section = :section
        AND s.sex = :gender
        AND SUBSTRING_INDEX(a.attendance_remarks, ' ', 1) = 'T/I'
        AND MONTH(a.created_at) = :previousMonth
        AND YEAR(a.created_at) = :previousYear
    ");
    $stmt->execute([
        ':schoolYear' => $schoolYear,
        ':section' => $section,
        ':gender' => $gender,
        ':previousMonth' => $previousMonth,
        ':previousYear' => $previousYear
    ]);
    return $stmt->fetchColumn();
}
function getAttendanceTOCount($pdo, $schoolYear, $section, $gender, $studentMonth)
{
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT a.lrn) 
        FROM attendance_tbl a
        JOIN student_tbl s ON a.lrn = s.lrn
        WHERE a.attendance_term = :schoolYear
        AND a.section = :section
        AND s.sex = :gender
        AND SUBSTRING_INDEX(a.attendance_remarks, ' ', 1) = 'T/O'
        AND MONTH(a.created_at) = :studentMonth
    ");
    $stmt->execute([
        ':schoolYear' => $schoolYear,
        ':section' => $section,
        ':gender' => $gender,
        ':studentMonth' => $studentMonth
    ]);
    return $stmt->fetchColumn();
}
function getAttendanceTOCountForPreviousMonth($pdo, $schoolYear, $section, $gender, $studentMonth)
{
    $previousMonth = ($studentMonth == 1) ? 12 : $studentMonth - 1; // Handle December to January transition
    $previousYear = ($studentMonth == 1) ? date('Y') - 1 : date('Y');
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT a.lrn) 
        FROM attendance_tbl a
        JOIN student_tbl s ON a.lrn = s.lrn
        WHERE a.attendance_term = :schoolYear
        AND a.section = :section
        AND s.sex = :gender
        AND SUBSTRING_INDEX(a.attendance_remarks, ' ', 1) = 'T/O'
        AND MONTH(a.created_at) = :previousMonth
        AND YEAR(a.created_at) = :previousYear
    ");
    $stmt->execute([
        ':schoolYear' => $schoolYear,
        ':section' => $section,
        ':gender' => $gender,
        ':previousMonth' => $previousMonth,
        ':previousYear' => $previousYear
    ]);
    return $stmt->fetchColumn();
}
function getAttendanceDOCountForPreviousMonth($pdo, $schoolYear, $section, $gender, $studentMonth)
{
    $previousMonth = ($studentMonth == 1) ? 12 : $studentMonth - 1; // Handle December to January transition
    $previousYear = ($studentMonth == 1) ? date('Y') - 1 : date('Y');
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT a.lrn) 
        FROM attendance_tbl a
        JOIN student_tbl s ON a.lrn = s.lrn
        WHERE a.attendance_term = :schoolYear
        AND a.section = :section
        AND s.sex = :gender
        AND SUBSTRING_INDEX(a.attendance_remarks, ' ', 1) = 'DRP'
        AND MONTH(a.created_at) = :previousMonth
        AND YEAR(a.created_at) = :previousYear
    ");
    $stmt->execute([
        ':schoolYear' => $schoolYear,
        ':section' => $section,
        ':gender' => $gender,
        ':previousMonth' => $previousMonth,
        ':previousYear' => $previousYear
    ]);
    return $stmt->fetchColumn();
}
function getAttendanceDOCount($pdo, $schoolYear, $section, $gender, $studentMonth)
{
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT a.lrn) 
        FROM attendance_tbl a
        JOIN student_tbl s ON a.lrn = s.lrn
        WHERE a.attendance_term = :schoolYear
        AND a.section = :section
        AND s.sex = :gender
        AND SUBSTRING_INDEX(a.attendance_remarks, ' ', 1) = 'DRP'
        AND MONTH(a.created_at) = :studentMonth
    ");
    $stmt->execute([
        ':schoolYear' => $schoolYear,
        ':section' => $section,
        ':gender' => $gender,
        ':studentMonth' => $studentMonth
    ]);
    return $stmt->fetchColumn();
}
function get_holiday_dates($pdo, $currentYear, $currentMonth)
{
    // Query to fetch holiday dates
    $query = "SELECT holiday_date FROM holidays_tbl WHERE YEAR(holiday_date) = :year AND MONTH(holiday_date) = :month";
    // Prepare and execute the query
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':year', $currentYear, PDO::PARAM_INT);
    $stmt->bindValue(':month', $currentMonth, PDO::PARAM_INT);
    $stmt->execute();
    // Fetch all holiday dates
    $holidayDates = $stmt->fetchAll(PDO::FETCH_COLUMN);
    // Debugging: Check what holiday dates are fetched
    if ($holidayDates === false) {
        echo "Error fetching holiday dates.";
        return ['holidayDates' => [], 'holidayCount' => 0];
    }
    // Format the holiday dates to get only the day
    $formattedHolidayDates = array_map(function ($date) {
        return (int) date('j', strtotime($date)); // Get the day of the month as an integer
    }, $holidayDates);
    // Debugging: Check the formatted holiday dates
    echo "Formatted Holiday Dates: " . implode(", ", $formattedHolidayDates) . "\n";
    // Return both the formatted holiday dates and the count of these dates
    return ['holidayDates' => $formattedHolidayDates, 'holidayCount' => count($formattedHolidayDates)];
}
function getAttendanceCount($pdo, $schoolYear, $section, $gender, $studentMonth)
{
    // Get current year and month
    $currentYear = date("Y");
    $currentMonth = date("m");
    // Fetch holiday dates for the current month and year
    $holidayData = get_holiday_dates($pdo, $currentYear, $currentMonth);
    $holidayDates = $holidayData['holidayDates']; // Expected format: [1, 2, 3]
    $year = date('Y'); // Get the current year
    // Get total days in the selected month
    $firstDay = strtotime("$year-$studentMonth-01");
    $totalDays = date('t', $firstDay);
    // Identify valid weekdays (Monday to Friday) excluding holidays
    $validDays = [];
    for ($day = 1; $day <= $totalDays; $day++) {
        $weekday = date('N', strtotime("$year-$studentMonth-$day")); // 1 = Monday, ..., 7 = Sunday
        if ($weekday < 6 && !in_array($day, $holidayDates)) {  // Compare only the day
            $validDays[] = "`Day$day`";  // Escape column names
        }
    }
    if (empty($validDays)) {
        return 0; // Avoid invalid SQL query
    }
    $weekdayCount = count($validDays); // Total weekdays in the selected month
    // Fetch LRNs from student_tbl
    $studentQuery = "
        SELECT lrn 
        FROM student_tbl 
        WHERE section = :section 
        AND sex = :gender
        AND school_year = :schoolYear
    ";
    $studentStmt = $pdo->prepare($studentQuery);
    $studentStmt->execute([
        ':section' => $section,
        ':gender' => $gender,
        ':schoolYear' => $schoolYear
    ]);
    $lrns = $studentStmt->fetchAll(PDO::FETCH_COLUMN);
    if (empty($lrns)) {
        return 0; // No students found
    }
    // Prepare placeholders for `IN` clause
    $placeholders = implode(',', array_fill(0, count($lrns), '?'));
    // Fetch attendance records
    $dayColumns = implode(", ", $validDays);
    $attendanceQuery = "
        SELECT $dayColumns
        FROM attendance_tbl
        WHERE attendance_term = ?
        AND section = ?
        AND lrn IN ($placeholders)
        AND (attendance_remarks IS NULL OR 
            (attendance_remarks NOT LIKE 'T/O%' 
             AND attendance_remarks NOT LIKE 'DRP%'))
        AND MONTH(created_at) = ?
    ";
    $params = array_merge([$schoolYear, $section], $lrns, [$studentMonth]);
    $attendanceStmt = $pdo->prepare($attendanceQuery);
    $attendanceStmt->execute($params);
    $totalAbsences = 0;
    // Count absences based on valid days
    while ($row = $attendanceStmt->fetch(PDO::FETCH_ASSOC)) {
        foreach ($validDays as $column) {
            $colName = trim($column, '`');  // Remove backticks
            if (!isset($row[$colName]) || $row[$colName] === 'L') {
                $totalAbsences++;
            }
        }
    }
    // Calculate absence rate
    $absenceRate = $totalAbsences / $weekdayCount;
    return round($absenceRate, 2); // Return rounded absence rate
}
// Function to count students based on conditions
function getStudentCount($pdo, $schoolYear, $section, $gender)
{
    $query = "SELECT COUNT(lrn) as count FROM student_tbl 
              WHERE school_year = :schoolYear 
              AND sex = :gender 
              AND section = :section 
              AND (remarks IS NULL OR 
                remarks NOT LIKE 'T/O%' 
            AND remarks NOT LIKE 'DRP%')";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':schoolYear', $schoolYear, PDO::PARAM_STR);
    $stmt->bindValue(':gender', $gender, PDO::PARAM_STR);
    $stmt->bindValue(':section', $section, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] ?? 0; // Return 0 if count is not set
}
function getPersonnelId($pdo, $schoolYear, $gradeLevel)
{
    $query = "SELECT PersonnelId FROM anc_ass_tbl 
              WHERE anc_ass_term = :schoolYear 
              AND anc_ass_desc = :gradeLevel 
              LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':schoolYear', $schoolYear, PDO::PARAM_STR);
    $stmt->bindValue(':gradeLevel', $gradeLevel, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['PersonnelId'] : null;
}
function getPersonnelName($pdo, $personnelId)
{
    if (!$personnelId) {
        return '';  // Return empty if no PersonnelId is found
    }
    $query = "SELECT EmpFName, EmpMName, EmpLName, EmpEName FROM school_per_tbl 
              WHERE PersonnelId = :personnelId LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':personnelId', $personnelId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        // Concatenate name parts while handling null values
        return trim("{$result['EmpFName']} {$result['EmpMName']} {$result['EmpLName']} {$result['EmpEName']}");
    }
    return '';
}
// Ensure the section_list function is defined
function section_list($pdo, $schoolYear)
{
    $query = "SELECT * FROM section_tbl WHERE 1=1";
    if (!empty($schoolYear)) {
        $query .= " AND SchoolYear = :schoolYear";
    }
    $query .= " ORDER BY GradeLevel ASC";
    $stmt = $pdo->prepare($query);
    if (!empty($schoolYear)) {
        $stmt->bindValue(':schoolYear', $schoolYear, PDO::PARAM_STR);
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>