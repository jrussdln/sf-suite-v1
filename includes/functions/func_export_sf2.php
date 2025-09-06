<?php
require_once('../db_config.php');
require_once('../Excel/vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// Get the filter parameters from the URL
$schoolYear = isset($_GET['schoolYear']) ? $_GET['schoolYear'] : '';
$gradeLevel = isset($_GET['gradeLevel']) ? $_GET['gradeLevel'] : '';
$studentMonth = isset($_GET['studentMonth']) ? $_GET['studentMonth'] : '';
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
// Call the function to update the existing file and generate a new file for download
update_sf2($pdo, $schoolYear, $gradeLevel, $studentSection, $studentMonth, $userFullName);
function update_sf2($pdo, $schoolYear, $gradeLevel, $studentSection, $studentMonth, $userFullName)
{
    $filePath = $_SERVER['DOCUMENT_ROOT'] . '/sf_suite/school_forms_temp/SF2.xls';
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $monthName = date("F", mktime(0, 0, 0, $studentMonth, 1));
    // Get current year and month
    $currentYear = date("Y");
    $currentMonth = date("m");
    // Fetch holiday dates for the current month and year
    $holidayData = get_holiday_dates($pdo, $currentYear, $currentMonth);
    $holidayDates = $holidayData['holidayDates']; // This is the array of holiday dates
    $schoolInfo = fetch_school_info($pdo);
    if ($schoolInfo) {
        $sheet->setCellValue('C6', $schoolInfo['school_id']);
        $sheet->setCellValue('C8', $schoolInfo['school_name']);
    }
    // Insert the school year, grade level, and student section into the specified cells
    $sheet->setCellValue('K6', $schoolYear);
    $sheet->setCellValue('X8', $gradeLevel);
    $sheet->setCellValue('AC8', $studentSection);
    // Insert the month name into cell X6
    $sheet->setCellValue('X6', $monthName);
    // Fetch male and female attendance data
    $studentsM = get_attendance_male($pdo, $schoolYear, $gradeLevel, $studentSection, $studentMonth);
    $studentsF = get_attendance_female($pdo, $schoolYear, $gradeLevel, $studentSection, $studentMonth);
    // Get current date details
    $createdAt = new DateTime($studentsM[0]['created_at']);
    $currentYear = $createdAt->format('Y');
    $currentMonth = $createdAt->format('m');
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);
    // Insert day numbers into row 11, starting from column D
    $columnIndex = 4;
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $dayOfWeek = date('N', strtotime("$currentYear-$currentMonth-$day"));
        if ($dayOfWeek >= 1 && $dayOfWeek <= 5) { // Monday-Friday
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex) . '11', $day);
            $columnIndex++;
        }
    }
    function insert_attendance($students, &$row, $sheet, $daysInMonth, $currentYear, $currentMonth, $holidayDates)
    {
        foreach ($students as $student) {
            if ($row > 13) {
                $sheet->insertNewRowBefore($row, 1);
                // Copy style (including font, alignment, borders, etc.)
                $styleSource = $sheet->getStyle('A13:AJ13');
                $sheet->duplicateStyle($styleSource, "A$row:AJ$row");
                // Copy merged cells from row 13
                foreach ($sheet->getMergeCells() as $mergeRange) {
                    if (strpos($mergeRange, '13') !== false) {
                        $newMergeRange = str_replace('13', $row, $mergeRange);
                        $sheet->mergeCells($newMergeRange);
                    }
                }
                // Explicitly set font size and alignment for the new row
                $sheet->getStyle("A$row:AJ$row")->applyFromArray([
                    'font' => [
                        'size' => 11, // Adjust font size if needed
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);
            }
            $sheet->setCellValue('B' . $row, "'" . $student['name'] . "'");
            $sheet->setCellValue('AE' . $row, $student['attendance_remarks']);
            $columnIndex = 4; // Starting from column D
            $xCount = 0;
            $lCount = 0;
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $dayOfWeek = date('N', strtotime("$currentYear-$currentMonth-$day"));
                if ($dayOfWeek >= 1 && $dayOfWeek <= 5) { // Only weekdays
                    $cellReference = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex) . $row;
                    if (in_array($day, $holidayDates)) {
                        $sheet->setCellValue($cellReference, 'HD'); // Insert 'HD' for holidays
                        // Change background color to yellow
                        $sheet->getStyle($cellReference)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                        $sheet->getStyle($cellReference)->getFill()->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_YELLOW);
                    } elseif (isset($student["Day$day"])) {
                        $attendanceValue = mb_convert_encoding($student["Day$day"], 'UTF-8', 'UTF-8');
                        $sheet->setCellValue($cellReference, $attendanceValue);
                        if ($attendanceValue === 'X') {
                            $xCount++;
                        }
                        if ($attendanceValue === 'L') {
                            $lCount++;
                        }
                    }
                    $columnIndex++;
                }
            }
            $sheet->setCellValue('AC' . $row, $xCount);
            $sheet->setCellValue('AD' . $row, $lCount);
            $row++;
        }
    }
    // Insert male students
    $row = 13;
    insert_attendance($studentsM, $row, $sheet, $daysInMonth, $currentYear, $currentMonth, $holidayDates);
    $lastMaleRow = $row - 1; // Last male student row
    $maleCountRow = $lastMaleRow + 2; // 2 rows below
    // Insert female students
    $row += 3;
    insert_attendance($studentsF, $row, $sheet, $daysInMonth, $currentYear, $currentMonth, $holidayDates);
    $lastFemaleRow = $row - 1; // Last female student row
    $femaleCountRow = $lastFemaleRow + 2; // 2 rows below
    $columnIndex = 4;
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $dayOfWeek = date('N', strtotime("$currentYear-$currentMonth-$day"));
        if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
            $formulaM = "=COUNTIFS($columnLetter" . "13:$columnLetter" . $lastMaleRow . ', "<>X", ' .
                $columnLetter . "13:$columnLetter" . $lastMaleRow . ', "<>L", ' .
                $columnLetter . "13:$columnLetter" . $lastMaleRow . ', "<>HD", ' . // Exclude 'HD'
                "AE13:AE" . $lastMaleRow . ', "<>T/O*", ' .
                "AE13:AE" . $lastMaleRow . ', "<>DRP*")';
            $formulaF = "=COUNTIFS($columnLetter" . ($lastMaleRow + 3) . ":$columnLetter" . $lastFemaleRow . ', "<>X", ' .
                $columnLetter . ($lastMaleRow + 3) . ":$columnLetter" . $lastFemaleRow . ', "<>L", ' .
                $columnLetter . ($lastMaleRow + 3) . ":$columnLetter" . $lastFemaleRow . ', "<>HD", ' . // Exclude 'HD'
                "AE" . ($lastMaleRow + 3) . ":AE" . $lastFemaleRow . ', "<>T/O*", ' .
                "AE" . ($lastMaleRow + 3) . ":AE" . $lastFemaleRow . ', "<>DRP*") - 1';
            $sheet->setCellValue($columnLetter . $maleCountRow, $formulaM);
            $sheet->setCellValue($columnLetter . $femaleCountRow, $formulaF);
            $columnIndex++;
        }
    }
    // Define the row where the total male + female count will be inserted
    $totalCountRow = $femaleCountRow + 1;
    // Insert sum formula for male and female counts (D to AB)
    $columnIndex = 4;
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $dayOfWeek = date('N', strtotime("$currentYear-$currentMonth-$day"));
        if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
            $formulaTotal = "=$columnLetter$maleCountRow + $columnLetter$femaleCountRow";
            $sheet->setCellValue($columnLetter . $totalCountRow, $formulaTotal);
            $columnIndex++;
        }
    }
    // ENROLLMENT AS OF FIRST FRIDAY OF JUNE
    $summaryRow = $totalCountRow + 4;
    $sheet->setCellValue("AH$summaryRow", "=COUNTA(B13:B$lastMaleRow)");
    $sheet->setCellValue("AI$summaryRow", "=COUNTA(B" . ($lastMaleRow + 3) . ":B$lastFemaleRow)");
    $sheet->setCellValue("AJ$summaryRow", "=AH$summaryRow + AI$summaryRow");
    // LATE ENROLLMENT (LE) COUNT, EXCLUDING DRP AND T/O
    $leCountRow = $summaryRow + 2;
    $firstFemaleRow = $lastMaleRow + 3;
    $sheet->setCellValue("AH$leCountRow", "=COUNTIFS(AE13:AE$lastMaleRow, \"LE*\", AE13:AE$lastMaleRow, \"<>DRP*\", AE13:AE$lastMaleRow, \"<>T/O*\")");
    $sheet->setCellValue("AI$leCountRow", "=COUNTIFS(AE$firstFemaleRow:AE$lastFemaleRow, \"LE*\", AE$firstFemaleRow:AE$lastFemaleRow, \"<>DRP*\", AE$firstFemaleRow:AE$lastFemaleRow, \"<>T/O*\")");
    $sheet->setCellValue("AJ$leCountRow", "=AH$leCountRow + AI$leCountRow");
    //REGISTERED LEARNERS AS OF END OF THE MONTH
    $adjustedFirstFemaleRow = $firstFemaleRow + 1;
    $filteredCountRow = $leCountRow + 2;
    $sheet->setCellValue("AH$filteredCountRow", "=COUNTIFS(AE13:AE$lastMaleRow, \"<>DRP*\", AE13:AE$lastMaleRow, \"<>T/O*\", B13:B$lastMaleRow, \"<>\")");
    $sheet->setCellValue("AI$filteredCountRow", "=COUNTIFS(AE$adjustedFirstFemaleRow:AE$lastFemaleRow, \"<>DRP*\", AE$adjustedFirstFemaleRow:AE$lastFemaleRow, \"<>T/O*\", B$adjustedFirstFemaleRow:B$lastFemaleRow, \"<>\")");
    $sheet->setCellValue("AJ$filteredCountRow", "=AH$filteredCountRow + AI$filteredCountRow");
    //PERCENTAGE OF ENROLLMENT			
    $percentageCountRow = $filteredCountRow + 2;
    $sheet->setCellValue("AH$percentageCountRow", "=IF(AH$summaryRow=0, 0, ROUND(AH$filteredCountRow/AH$summaryRow*100, 0))");
    $sheet->setCellValue("AI$percentageCountRow", "=IF(AI$summaryRow=0, 0, ROUND(AI$filteredCountRow/AI$summaryRow*100, 0))");
    $sheet->setCellValue("AJ$percentageCountRow", "=IF(AJ$summaryRow=0, 0, ROUND(AJ$filteredCountRow/AJ$summaryRow*100, 0))");
    // AVERAGE DAILY ATTENDANCE
    $validDaysCount = 0;
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $dayOfWeek = date('N', strtotime("$currentYear-$studentMonth-$day")); // Use $studentMonth here
        if ($dayOfWeek >= 1 && $dayOfWeek <= 5) { // Monday-Friday
            $validDaysCount++;
        }
    }
    // Ensure the adjusted valid days count is non-negative
    $validDaysCountAdjusted = max(0, $validDaysCount - count($holidayDates));
    // Set row for total attendance calculations
    $totalAttendanceRow = $percentageCountRow + 2;
    // Compute Average Daily Attendance for Males
    $sheet->setCellValue("AH$totalAttendanceRow", "=IFERROR(IF(AH$filteredCountRow=0, 0, SUM(D$maleCountRow:AB$maleCountRow) / $validDaysCountAdjusted), 0)");
    // Compute Average Daily Attendance for Females
    $sheet->setCellValue("AI$totalAttendanceRow", "=IFERROR(IF(AI$filteredCountRow=0, 0, SUM(D$femaleCountRow:AB$femaleCountRow) / $validDaysCountAdjusted), 0)");
    // Compute Total Average Daily Attendance
    $sheet->setCellValue("AJ$totalAttendanceRow", "=IFERROR(ROUND((AH$totalAttendanceRow + AI$totalAttendanceRow), 2), 0)");
    // PERCENTAGE OF ATTENDANCE
    $percentageRow = $totalAttendanceRow + 2;
    $sheet->setCellValue("AH$percentageRow", "=IFERROR(IF(AH$totalAttendanceRow=0, 0, (AH$totalAttendanceRow / AH$filteredCountRow) * 100), 0)");
    $sheet->setCellValue("AI$percentageRow", "=IFERROR(IF(AI$totalAttendanceRow=0, 0, (AI$totalAttendanceRow / AI$filteredCountRow) * 100), 0)");
    $sheet->setCellValue("AJ$percentageRow", "=IFERROR(ROUND((AH$percentageRow + AI$percentageRow) / 2, 2), 0)");
    $finalCountRow = $percentageRow + 2;
    $sheet->setCellValue("AH$finalCountRow", "=COUNTIFS(AC13:AC$lastMaleRow, \">=5\")");
    $sheet->setCellValue("AI$finalCountRow", "=COUNTIFS(AC" . ($lastMaleRow + 3) . ":AC$lastFemaleRow, \">=5\")");
    $sheet->setCellValue("AJ$finalCountRow", "=AH$finalCountRow + AI$finalCountRow");
    // DROPOUT COUNT
    $dropoutCountRow = $finalCountRow + 2; // Row for dropout counts
    $sheet->setCellValue("AH$dropoutCountRow", "=COUNTIF(AE13:AE$lastMaleRow, \"DRP*\")");
    $sheet->setCellValue("AI$dropoutCountRow", "=COUNTIF(AE$firstFemaleRow:AE$lastFemaleRow, \"DRP*\")");
    $sheet->setCellValue("AJ$dropoutCountRow", "=AH$dropoutCountRow + AI$dropoutCountRow");
    // TRANSFERRED OUT COUNT
    $tardyOutCountRow = $dropoutCountRow + 2;
    $sheet->setCellValue("AH$tardyOutCountRow", "=COUNTIF(AE13:AE$lastMaleRow, \"T/O*\")");
    $sheet->setCellValue("AI$tardyOutCountRow", "=COUNTIF(AE$firstFemaleRow:AE$lastFemaleRow, \"T/O*\")");
    $sheet->setCellValue("AJ$tardyOutCountRow", "=AH$tardyOutCountRow + AI$tardyOutCountRow");
    $tardyInCountRow = $tardyOutCountRow + 2;
    $sheet->setCellValue("AH$tardyInCountRow", "=COUNTIF(AE13:AE$lastMaleRow, \"T/I*\")");
    $sheet->setCellValue("AI$tardyInCountRow", "=COUNTIF(AE$firstFemaleRow:AE$lastFemaleRow, \"T/I*\")");
    $sheet->setCellValue("AJ$tardyInCountRow", "=AH$tardyInCountRow + AI$tardyInCountRow");
    $userNameRow = $tardyInCountRow + 5;
    $sheet->setCellValue("AD$userNameRow", $userFullName);
    $schoolHeadRow = $userNameRow + 4;
    $sheet->setCellValue("AD$schoolHeadRow", $schoolInfo['school_head']);
    $monthRow = $totalCountRow + 3;
    $sheet->setCellValue("AB$monthRow", $monthName);
    $dayInMonth = $totalCountRow + 2;
    $sheet->setCellValue("AG$dayInMonth", $validDaysCountAdjusted);
    $newFileName = 'SF-SUITE_SF2_' . $studentSection . '-' . $schoolYear . date('Y-m-d_H-i-s') . '.xlsx';
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
function get_attendance_by_gender($pdo, $schoolYear, $gradeLevel, $studentSection, $studentMonth, $gender)
{
    // First, fetch attendance data based on the provided filters
    $query = "SELECT * FROM attendance_tbl WHERE 1=1";
    if (!empty($schoolYear)) {
        $query .= " AND attendance_term = :schoolYear";
    }
    if (!empty($studentSection)) {
        $query .= " AND section = :studentSection";
    }
    if (!empty($studentMonth) && is_numeric($studentMonth) && $studentMonth >= 1 && $studentMonth <= 12) {
        $query .= " AND MONTH(created_at) = :studentMonth"; // Filter by month
    }
    $stmt = $pdo->prepare($query);
    if (!empty($schoolYear)) {
        $stmt->bindValue(':schoolYear', $schoolYear, PDO::PARAM_STR);
    }
    if (!empty($studentSection)) {
        $stmt->bindValue(':studentSection', $studentSection, PDO::PARAM_STR);
    }
    if (!empty($studentMonth) && is_numeric($studentMonth) && $studentMonth >= 1 && $studentMonth <= 12) {
        $stmt->bindValue(':studentMonth', $studentMonth, PDO::PARAM_INT);
    }
    $stmt->execute();
    $attendanceData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Prepare an array to hold the final data
    $finalData = [];
    // Now fetch names and additional details from student_tbl based on the LRN from attendance data
    foreach ($attendanceData as $attendance) {
        $lrn = $attendance['lrn'];
        // Fetch the student details for the current LRN
        $studentQuery = "SELECT * FROM student_tbl WHERE sex = :gender AND lrn = :lrn";
        $studentStmt = $pdo->prepare($studentQuery);
        $studentStmt->bindValue(':gender', $gender, PDO::PARAM_STR);
        $studentStmt->bindValue(':lrn', $lrn, PDO::PARAM_STR);
        $studentStmt->execute();
        $student = $studentStmt->fetch(PDO::FETCH_ASSOC);
        // Check if the student exists and matches the additional filters
        if ($student) {
            // Apply additional filters
            if (!empty($gradeLevel) && $student['grade_level'] !== $gradeLevel) {
                continue; // Skip this record if it doesn't match the grade level filter
            }
            // Combine attendance data with the student details
            $attendance['name'] = $student['name'];
            $attendance['grade_level'] = $student['grade_level'];
            $finalData[] = $attendance;
        }
    }
    return $finalData;
}
// Call functions for male and female students
function get_attendance_male($pdo, $schoolYear, $gradeLevel, $studentSection, $studentMonth)
{
    return get_attendance_by_gender($pdo, $schoolYear, $gradeLevel, $studentSection, $studentMonth, 'M');
}
function get_attendance_female($pdo, $schoolYear, $gradeLevel, $studentSection, $studentMonth)
{
    return get_attendance_by_gender($pdo, $schoolYear, $gradeLevel, $studentSection, $studentMonth, 'F');
}
?>