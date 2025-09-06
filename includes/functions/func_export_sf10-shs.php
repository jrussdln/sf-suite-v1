<?php
require_once('../db_config.php');
require_once('../Excel/vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
function update_sf10($pdo, $lrn)
{
    $stmt = $pdo->prepare("SELECT school_year FROM student_tbl WHERE lrn = :lrn ORDER BY school_year ASC LIMIT 2");
    $stmt->execute(['lrn' => $lrn]);
    $arraySchoolYear = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (count($arraySchoolYear) < 1) {
        die("Error: No school year found for LRN $lrn.");
    }
    $gradeMapping = [];
    if (!empty($arraySchoolYear[0])) {
        $gradeMapping[11] = $arraySchoolYear[0];
    }
    if (!empty($arraySchoolYear[1])) {
        $gradeMapping[12] = $arraySchoolYear[1];
    }
    $filePath = $_SERVER['DOCUMENT_ROOT'] . '/sf_suite/school_forms_temp/SF10-SHS.xls';
    if (!file_exists($filePath)) {
        die("Error: Template file not found.");
    }
    $spreadsheet = IOFactory::load($filePath);
    $sheetFront = $spreadsheet->getSheetByName('FRONT');
    $sheetBack = $spreadsheet->getSheetByName('BACK');
    if (!$sheetFront || !$sheetBack) {
        die("Error: Missing sheet(s) in template.");
    }
    $arraySection = getSections($pdo, $lrn);
    $sheetFront->setCellValue('AS25', $arraySection[11]);
    $sheetFront->setCellValue('AS68', $arraySection[11]);
    $sheetBack->setCellValue('AS5', $arraySection[12]);
    $sheetBack->setCellValue('AS48', $arraySection[12]);
    $schoolInfo = fetch_school_info($pdo);
    if ($schoolInfo) {
        $fields = [
            'E23' => $schoolInfo['school_name'],
            'E66' => $schoolInfo['school_name'],
            'Z14' => $schoolInfo['school_name'],
            'AF23' => $schoolInfo['school_id'],
            'AF66' => $schoolInfo['school_id'],
        ];
        foreach ($fields as $cell => $value) {
            $sheetFront->setCellValue($cell, $value);
        }
    }
    $students = grade($pdo, $lrn);
    if (empty($students)) {
        die("Error: No student found for LRN $lrn.");
    }
    $student = $students[0];
    $nameParts = array_map('trim', explode(',', $student['name'] ?? ''));
    $fullName = implode(' ', array_filter($nameParts));
    $birthDateFormatted = '';
    if (!empty($student['birth_date'])) {
        $dateObj = DateTime::createFromFormat('Y-m-d', $student['birth_date']);
        $birthDateFormatted = $dateObj ? $dateObj->format('m/d/Y') : '';
    }
    $strandTrackDesc = '';
    $stmt = $pdo->prepare("SELECT description FROM strand_track_tbl WHERE strand_track_status = 'Active' AND strand_track = :strand_track LIMIT 1");
    $stmt->execute(['strand_track' => $student['strand_track']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $strandTrackDesc = $result['description'];
    }
    $currentDate = date('m/d/Y');
    $grade11SY = $gradeMapping[11];
    $grade12SY = $gradeMapping[12];
    // Fill student and school year info
    if (isset($gradeMapping[11])) {
        $grade11SY = $gradeMapping[11];
        $sheetFront->setCellValue('BA23', $grade11SY);
        $sheetFront->setCellValue('BA66', $grade11SY);
    }
    if (isset($gradeMapping[12])) {
        $grade12SY = $gradeMapping[12];
        $sheetBack->setCellValue('BA4', $grade12SY);
        $sheetBack->setCellValue('BA46', $grade12SY);
        $sheetBack->setCellValue('G5', $strandTrackDesc);
    }
    $studentFields = [
        'C9' => "'" . $student['lrn'] . "'",
        'F8' => $nameParts[0] ?? '',
        'Y8' => $nameParts[1] ?? '',
        'AZ8' => $nameParts[2] ?? '',
        'AA9' => $birthDateFormatted,
        'AN9' => $student['sex'],
        'BH9' => $currentDate,
        'G68' => $strandTrackDesc,
        'G25' => $strandTrackDesc
    ];
    foreach ($studentFields as $cell => $value) {
        $sheetFront->setCellValue($cell, $value);
    }
    $colMap = ['AT', 'AY'];
    function fetchSubjects($pdo, $gradeLevel, $schoolYear, $subjectType, $subjectSemester)
    {
        $stmt = $pdo->prepare("
            SELECT subject_id, subject_name, subjectType
            FROM subjects_tbl 
            WHERE grade_level = :gradeLevel 
              AND subjectType = :subjectType
              AND subject_term = :schoolYear
              AND subject_semester = :subjectSemester
            ORDER BY subject_order ASC
        ");
        $stmt->execute([
            ':gradeLevel' => $gradeLevel,
            ':schoolYear' => $schoolYear,
            ':subjectType' => $subjectType,
            ':subjectSemester' => $subjectSemester
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    function getSubjectsByTypeAndSemester($pdo, $gradeLevel, $schoolYear, $types, $semester)
    {
        $allSubjects = [];
        foreach ($types as $type) {
            $allSubjects = array_merge($allSubjects, fetchSubjects($pdo, $gradeLevel, $schoolYear, $type, $semester));
        }
        return $allSubjects;
    }
    function insertGrades($subjects, $pdo, $sheetFront, $lrn, $schoolYear, $startRow, $colMap, $gradeSemester)
    {
        foreach ($subjects as $subject) {
            $stmt = $pdo->prepare("
                SELECT fsts_grade_tr, scnds_grade_tr
                FROM student_grade_shs_tbl 
                WHERE lrn = :lrn 
                  AND subject_id = :subjectId
                  AND grade_term = :schoolYear
                  AND grade_semester = :gradeSemester
            ");
            $stmt->execute([
                ':lrn' => $lrn,
                ':subjectId' => $subject['subject_id'],
                ':schoolYear' => $schoolYear,
                ':gradeSemester' => $gradeSemester
            ]);
            $grades = $stmt->fetch(PDO::FETCH_ASSOC);
            $sheetFront->setCellValue("I$startRow", $subject['subject_name']);
            $sheetFront->setCellValue("A$startRow", $subject['subjectType']);
            $sum = 0;
            $count = 0;
            foreach ($colMap as $index => $col) {
                $gradeKey = ["fsts_grade_tr", "scnds_grade_tr"][$index];
                $gradeValue = isset($grades[$gradeKey]) && is_numeric($grades[$gradeKey]) ? $grades[$gradeKey] : "-";
                $sheetFront->setCellValue("$col$startRow", $gradeValue);
                if (is_numeric($gradeValue) && $gradeValue > 0) {
                    $sum += $gradeValue;
                    $count++;
                }
            }
            $average = ($count > 0) ? round($sum / $count) : "-";
            $sheetFront->setCellValue("BD$startRow", $average);
            $startRow++;
        }
    }
    if (isset($gradeMapping[11])) {
        $coreSem1 = getSubjectsByTypeAndSemester($pdo, 11, $gradeMapping[11], ['Core'], 1);
        $appliedSpecSem1 = getSubjectsByTypeAndSemester($pdo, 11, $gradeMapping[11], ['Applied', 'Specialized'], 1);
        $coreSem2 = getSubjectsByTypeAndSemester($pdo, 11, $gradeMapping[11], ['Core'], 2);
        $appliedSpecSem2 = getSubjectsByTypeAndSemester($pdo, 11, $gradeMapping[11], ['Applied', 'Specialized'], 2);

        insertGrades($coreSem1, $pdo, $sheetFront, $lrn, $gradeMapping[11], 31, $colMap, 1);
        insertGrades($appliedSpecSem1, $pdo, $sheetFront, $lrn, $gradeMapping[11], 31 + count($coreSem1), $colMap, 1);
        insertGrades($coreSem2, $pdo, $sheetFront, $lrn, $gradeMapping[11], 74, $colMap, 2);
        insertGrades($appliedSpecSem2, $pdo, $sheetFront, $lrn, $gradeMapping[11], 74 + count($coreSem2), $colMap, 2);
    }

    updateICARD($pdo, $spreadsheet, $lrn);
    $newFileName = 'SF-SUITE_SF10SHS_'. $fullName . '_' . date('Y-m-d_H-i-s') . '.xlsx';
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
function updateICARD($pdo, $spreadsheet, $lrn)
{
    // Access the BACK (ICARD) sheet
    $sheetBack = $spreadsheet->getSheetByName('BACK');
    if (!$sheetBack) {
        die("Error: Sheet 'BACK' (ICARD) not found.");
    }
    // Fetch school info to populate school name and ID in ICARD
    $schoolInfo = fetch_school_info($pdo);
    if ($schoolInfo) {
        $currentDate = date('m/d/y');
        $fields = [
            'E4' => $schoolInfo['school_name'],
            'E46' => $schoolInfo['school_name'],
            'AF4' => $schoolInfo['school_id'],
            'AF46' => $schoolInfo['school_id'],
            'A94' => $schoolInfo['school_head'],
            'T94' => $currentDate,
            'J114' => $currentDate
        ];
        foreach ($fields as $cell => $value) {
            $sheetBack->setCellValue($cell, $value);
        }
    }
    // Fetch the student information for the provided LRN
    $students = grade($pdo, $lrn);
    if (empty($students)) {
        die("Error: No student found for LRN $lrn.");
    }
    $student = $students[0];
    $grade12SchoolYear = $student['school_year'];  // Grade 12 School Year (from student info)
    $coreSubjectsSem1 = fetchSubjects($pdo, 12, $grade12SchoolYear, 'Core', 1);
    $specializedSubjectsSem1 = array_merge(
        fetchSubjects($pdo, 12, $grade12SchoolYear, 'Applied', 1),
        fetchSubjects($pdo, 12, $grade12SchoolYear, 'Specialized', 1)
    );
    // For Semester 2
    $coreSubjectsSem2 = fetchSubjects($pdo, 12, $grade12SchoolYear, 'Core', 2);
    $specializedSubjectsSem2 = array_merge(
        fetchSubjects($pdo, 12, $grade12SchoolYear, 'Applied', 2),
        fetchSubjects($pdo, 12, $grade12SchoolYear, 'Specialized', 2)
    );
    $colMap = ['AT', 'AY'];
    $startRowSem1Core = 11;
    // Insert Core Subjects (Semester 1)
    insertGradesToICARD($coreSubjectsSem1, $pdo, $sheetBack, $lrn, $grade12SchoolYear, $startRowSem1Core, $colMap, 1);
    // Get the last row used after inserting Core subjects
    $lastRowSem1Core = $startRowSem1Core + count($coreSubjectsSem1);
    // Insert Specialized Subjects (Semester 1) immediately after Core subjects
    $startRowSem1Specialized = $lastRowSem1Core;  // Start specialized subjects right after core subjects
    insertGradesToICARD($specializedSubjectsSem1, $pdo, $sheetBack, $lrn, $grade12SchoolYear, $startRowSem1Specialized, $colMap, 1);
    $startRowSem2Core = 54;
    insertGradesToICARD($coreSubjectsSem2, $pdo, $sheetBack, $lrn, $grade12SchoolYear, $startRowSem2Core, $colMap, 2);
    $lastRowSem2Core = $startRowSem2Core + count($coreSubjectsSem2);
    $startRowSem2Specialized = $lastRowSem2Core;  // Start specialized subjects right after core subjects
    insertGradesToICARD($specializedSubjectsSem2, $pdo, $sheetBack, $lrn, $grade12SchoolYear, $startRowSem2Specialized, $colMap, 2);
    // Save changes to the ICARD file
    $newFileName = 'Updated_ICARD_' . date('Y-m-d_H-i-s') . '.xlsx';
    $writer = new Xlsx($spreadsheet);
    ob_end_clean();
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$newFileName\"");
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    $writer->save('php://output');
    exit;
}
// Function to insert grades into the ICARD sheet
function insertGradesToICARD($subjects, $pdo, $sheetBack, $lrn, $schoolYear, $startRow, $colMap, $semester)
{
    foreach ($subjects as $subject) {
        $subjectId = $subject['subject_id'];
        $subjectName = $subject['subject_name'];
        $subjectType = $subject['subjectType'];
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
            ':gradeSemester' => $semester
        ]);
        $grades = $stmt->fetch(PDO::FETCH_ASSOC);
        // Set subject name in column I of the current row
        $sheetBack->setCellValue("I$startRow", $subjectName);
        $sheetBack->setCellValue("A$startRow", $subjectType);
        $sum = 0;
        $count = 0;
        // Insert each grade into its mapped column (AT, AY)
        foreach ($colMap as $index => $col) {
            $gradeKey = ["fsts_grade_tr", "scnds_grade_tr"][$index];
            $gradeValue = isset($grades[$gradeKey]) ? $grades[$gradeKey] : "-";
            $sheetBack->setCellValue("$col$startRow", $gradeValue);
            if ($gradeValue > 0) { // Only count valid grades
                $sum += $gradeValue;
                $count++;
            }
        }
        // Calculate the rounded average and place it in column BD
        $average = ($count > 0) ? round($sum / $count) : "-";
        $sheetBack->setCellValue("BD$startRow", $average);
        $startRow++; // Move to the next row
    }
}
function getSections($pdo, $lrn)
{
    $stmt = $pdo->prepare("SELECT school_year, section FROM student_tbl WHERE lrn = :lrn ORDER BY school_year ASC LIMIT 2");
    $stmt->execute(['lrn' => $lrn]);
    $arraySections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($arraySections) < 2) {
        die("Error: Not enough school years and sections found for LRN $lrn (needs at least 2).");
    }
    $sectionMapping = [
        11 => $arraySections[0]['section'] ?? null,
        12 => $arraySections[1]['section'] ?? null
    ];
    return $sectionMapping;
}
function fetchAndInsertGrades($pdo, $sheetFront, $schoolYear, $lrn, $gradeLevel, $semester, $startRow)
{
    $query = "SELECT subject_id, subject_name FROM subjects_tbl WHERE grade_level = :gradeLevel AND subject_term = :schoolYear AND semester = :semester";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':gradeLevel' => $gradeLevel, ':schoolYear' => $schoolYear, ':semester' => $semester]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($subjects)) {
        error_log("No subjects found for Grade $gradeLevel (School Year: $schoolYear).");
        return $startRow;
    }
    $colMap = ['AT', 'AY'];
    foreach ($subjects as $subject) {
        $stmt = $pdo->prepare("SELECT fsts_grade_tr, scnds_grade_tr FROM student_grade_shs_tbl WHERE lrn = :lrn AND subject_id = :subjectId AND grade_term = :schoolYear AND grade_semester = :semester");
        $stmt->execute([':lrn' => $lrn, ':subjectId' => $subject['subject_id'], ':schoolYear' => $schoolYear, ':semester' => $semester]);
        $grades = $stmt->fetch(PDO::FETCH_ASSOC) ?? [];
        $sheetFront->setCellValue("I$startRow", $subject['subject_name']);
        $sum = 0;
        $count = 0;
        foreach ($colMap as $index => $col) {
            $gradeValue = $grades[["fsts_grade_tr", "scnds_grade_tr"][$index]] ?? 0;
            $sheetFront->setCellValue("$col$startRow", $gradeValue);
            if ($gradeValue > 0) {
                $sum += $gradeValue;
                $count++;
            }
        }
        $average = ($count > 0) ? round($sum / $count) : 0;
        $sheetFront->setCellValue("BD$startRow", $average);
        $sheetFront->setCellValue("BI$startRow", ($average >= 75) ? "Passed" : "Failed");
        $startRow++;
    }
    return $startRow;
}
function fetch_school_info($pdo)
{
    return $pdo->query("SELECT * FROM school_info_tbl LIMIT 1")->fetch(PDO::FETCH_ASSOC);
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