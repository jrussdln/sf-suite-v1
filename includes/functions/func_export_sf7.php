<?php
require_once('../db_config.php');
require_once('../Excel/vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
function update_sf7($pdo)
{
    $filePath = $_SERVER['DOCUMENT_ROOT'] . '/sf_suite/school_forms_temp/SF7.xls';
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $schoolInfo = fetch_school_info($pdo);
    if ($schoolInfo) {
        $sheet->setCellValue('H5', $schoolInfo['region']);
        $sheet->setCellValue('K7', $schoolInfo['district']);
        $sheet->setCellValue('K5', $schoolInfo['division']);
        $sheet->setCellValue('D5', $schoolInfo['school_id']);
        $sheet->setCellValue('D7', $schoolInfo['school_name']);
    }
    $personnel = get_personnel($pdo);
    $plantillaPositions = get_plantilla_positions($pdo); // Retrieve plantilla positions
    $templateStartRow = 20;
    $templateEndRow = 30;
    $blockSize = $templateEndRow - $templateStartRow + 1; // 11 rows
    $currentRow = $templateStartRow;
    foreach ($personnel as $index => $person) {
        if ($index > 0) {
            $sheet->insertNewRowBefore($currentRow, $blockSize);
        }
        // Copy formatting and styles
        for ($row = 0; $row < $blockSize; $row++) {
            $originalRow = $templateStartRow + $row;
            $newRow = $currentRow + $row;
            $sheet->duplicateStyle($sheet->getStyle('A' . $originalRow . ':S' . $originalRow), 'A' . $newRow . ':S' . $newRow);
        }
        // Copy merged cells dynamically
        foreach ($sheet->getMergeCells() as $mergeRange) {
            preg_match('/([A-S])(\d+):([A-S])(\d+)/', $mergeRange, $matches);
            if ($matches) {
                [$full, $colStart, $startRow, $colEnd, $endRow] = $matches;
                if ($startRow >= $templateStartRow && $endRow <= $templateEndRow) {
                    $newStartRow = $currentRow + ($startRow - $templateStartRow);
                    $newEndRow = $newStartRow + ($endRow - $startRow);
                    $sheet->mergeCells("$colStart$newStartRow:$colEnd$newEndRow");
                }
            }
        }
        // Query to get pp_desc from plantilla_pos_tbl
        $stmt = $pdo->prepare("SELECT pp_desc FROM plantilla_pos_tbl WHERE PersonnelId = :PersonnelId");
        $stmt->bindParam(':PersonnelId', $person['PersonnelId']);
        $stmt->execute();
        $pp_desc = $stmt->fetchColumn(); // Fetch the pp_desc value
        // Insert Personnel Data
        $fullName = trim($person['EmpLName'] . ' ' . $person['EmpFName'] . ' ' . $person['EmpMName'] . ' ' . $person['EmpEName']);
        $sheet->setCellValue('A' . $currentRow, "'" . $person['EmpNo'])
            ->setCellValue('B' . $currentRow, $fullName)
            ->setCellValue('C' . $currentRow, $person['Sex'])
            ->setCellValue('D' . $currentRow, $person['FundSource'])
            ->setCellValue('F' . $currentRow, $pp_desc) // Use the retrieved pp_desc
            ->setCellValue('G' . $currentRow, $person['EmploymentStatus'])
            ->setCellValue('H' . $currentRow, $person['EducDegree'] . "\n\n" . $person['PostGraduate']) // Adds a blank line between
            ->setCellValue('I' . $currentRow, $person['EducMajor'] . "\n\n" . $person['Specialization'])
            ->setCellValue('K' . $currentRow, $person['EducMinor']);
        $sheet->getStyle('H' . $currentRow)->getAlignment()->setWrapText(true);
        $sheet->getStyle('I' . $currentRow)->getAlignment()->setWrapText(true);
        // Insert Subject Details
        $subjectDetails = get_subject_details($pdo, $person['PersonnelId']);
        $subjectRow = $currentRow;
        $tatMinSum = 0;
        $tatMinCount = 0;
        foreach ($subjectDetails as $subject) {
            $sheet->setCellValue('M' . $subjectRow, $subject['subject_taught'] . " " . $subject['section'])
                ->setCellValue('N' . $subjectRow, $subject['st_day'])
                ->setCellValue('O' . $subjectRow, $subject['st_from'])
                ->setCellValue('P' . $subjectRow, $subject['st_to'])
                ->setCellValue('Q' . $subjectRow, $subject['tat_min']);
            $tatMinSum += (float) $subject['tat_min'];
            $tatMinCount++;
            $subjectRow++;
        }
        // Insert Ancillary Assignments
        $ancAssDescs = get_anc_ass_desc($pdo, $person['PersonnelId']);
        if ($ancAssDescs) {
            $subjectRow++; // Skip one row before inserting assignments
            foreach ($ancAssDescs as $desc) {
                $sheet->setCellValue('M' . $subjectRow, $desc);
                $subjectRow++;
            }
        }
        // Calculate tat_min Average
        $tatMinAvg = ($tatMinCount > 0) ? ($tatMinSum / $tatMinCount) : 0;
        $sheet->setCellValue('M' . ($currentRow + 10), '                                          Avg. Minutes per Day');
        $sheet->setCellValue('Q' . ($currentRow + 10), round($tatMinAvg, 2));
        // Move to the next block
        $currentRow += $blockSize;
    }
    $schoolHeadRow = $currentRow + 3;
    $sheet->setCellValue("O$schoolHeadRow", $schoolInfo['school_head']);
    // **Insert Plantilla Positions**
    $rowA = 12; // Starting row for category A
    $rowB = 12; // Starting row for category B
    $rowC = 12; // Starting row for category C
// Function to count PersonnelId in plantilla_pos_tbl
    function countPersonnelIdInPlantilla($pdo)
    {
        $stmt = $pdo->query("SELECT COUNT(*) FROM plantilla_pos_tbl");
        return $stmt->fetchColumn(); // Return the count of PersonnelId
    }
    // Function to count PersonnelId in school_per_tbl
    function countPersonnelIdInSchool($pdo)
    {
        $stmt = $pdo->query("SELECT COUNT(*) FROM school_per_tbl");
        return $stmt->fetchColumn(); // Return the count of PersonnelId
    }
    // Get the total count of PersonnelId in plantilla_pos_tbl
    $totalPlantillaCount = countPersonnelIdInPlantilla($pdo);
    // Get the total count of PersonnelId in school_per_tbl
    $totalSchoolCount = countPersonnelIdInSchool($pdo);
    foreach ($plantillaPositions as $position) {
        $pplCode = $position['ppl_code'];
        $category = $position['ppl_category'];
        // Get the description from plantilla_pos_list_tbl based on ppl_code
        $stmt = $pdo->prepare("SELECT ppl_desc FROM plantilla_pos_list_tbl WHERE ppl_code = :ppl_code");
        $stmt->execute([':ppl_code' => $pplCode]);
        $pplDesc = $stmt->fetchColumn();
        if ($pplDesc) {
            // Now compare the description with plantilla_pos_tbl
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM plantilla_pos_tbl WHERE pp_desc = :ppl_desc");
            $stmt->execute([':ppl_desc' => $pplDesc]);
            $count = $stmt->fetchColumn();
        } else {
            $count = 0; // No description found
        }
        // Set values in the Excel sheet based on the category
        if ($category == 'A') {
            $sheet->setCellValue('A' . $rowA, $pplCode); // Set ppl_code in column A
            $sheet->setCellValue('C' . $rowA, $count);   // Display count in column C
            $rowA++; // Move to the next row for category A
        } elseif ($category == 'B') {
            $sheet->setCellValue('F' . $rowB, $pplCode); // Set ppl_code in column F
            $sheet->setCellValue('I' . $rowB, $count);   // Display count in column I
            $rowB++; // Move to the next row for category B
        } elseif ($category == 'C') {
            $sheet->setCellValue('K' . $rowC, $pplCode); // Set ppl_code in column K
            $sheet->setCellValue('N' . $rowC, $count);   // Display count in column N
            $rowC++; // Move to the next row for category C
        }
    }
    // Set the total count of PersonnelId in plantilla_pos_tbl in column R, row 12
    $sheet->setCellValue('R12', $totalPlantillaCount);
    // Calculate the difference and set it in column S, row 12
    $difference = $totalPlantillaCount - $totalSchoolCount;
    $sheet->setCellValue('S12', $difference);
    $newFileName = 'SF-SUITE_SF7_' . date('Y-m-d_H-i-s') . '.xlsx';
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
function get_plantilla_positions($pdo)
{
    $stmt = $pdo->prepare("SELECT ppl_code, ppl_category FROM plantilla_pos_list_tbl ORDER BY ppl_rank ASC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function get_anc_ass_desc($pdo, $personnelId)
{
    // Fetch the active school year term
    $schoolYearStmt = $pdo->prepare("SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active'");
    $schoolYearStmt->execute();
    $activeSchoolYear = $schoolYearStmt->fetchColumn();
    // Fetch all anc_ass_desc and anc_ass_term for the PersonnelId in the active school year
    $stmt = $pdo->prepare("
        SELECT anc_ass_desc, anc_ass_term 
        FROM anc_ass_tbl 
        WHERE PersonnelId = :personnelId AND anc_ass_term = :activeSchoolYear
    ");
    $stmt->execute([
        'personnelId' => $personnelId,
        'activeSchoolYear' => $activeSchoolYear
    ]);
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $results = [];
    // For each assignment, check if it exists in section_tbl
    foreach ($assignments as $assignment) {
        $desc = $assignment['anc_ass_desc'];
        $term = $assignment['anc_ass_term'];
        $checkStmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM section_tbl 
            WHERE SectionName = :desc AND SchoolYear = :term
        ");
        $checkStmt->execute(['desc' => $desc, 'term' => $term]);
        $isAdviser = $checkStmt->fetchColumn() > 0;
        $results[] = $isAdviser ? "$desc Adviser" : $desc;
    }
    return $results; // Array of strings (with or without " Adviser")
}
function get_subject_details($pdo, $personnelId)
{
    // Fetch the active school year term
    $schoolYearStmt = $pdo->prepare("SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active'");
    $schoolYearStmt->execute();
    $activeSchoolYear = $schoolYearStmt->fetchColumn(); // Get the active sy_term
    // Now fetch all subject details based on PersonnelId
    $stmt = $pdo->prepare("SELECT subject_taught, section, st_day, st_from, st_to, tat_min, stac_term 
                            FROM subject_taught_tbl 
                            WHERE PersonnelId = :personnelId");
    $stmt->execute(['personnelId' => $personnelId]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all results
    // Filter results to only include those with a matching stac_term
    $filteredResults = array_filter($results, function ($result) use ($activeSchoolYear) {
        return $result['stac_term'] === $activeSchoolYear;
    });
    return $filteredResults; // Return the filtered results
}
// Call the function to update the existing file and generate a new file for download
update_sf7($pdo);
function get_personnel($pdo)
{
    $query = "
        SELECT s.*, p.pp_desc, l.ppl_rank 
        FROM school_per_tbl s
        LEFT JOIN plantilla_pos_tbl p ON s.PersonnelId = p.PersonnelId
        LEFT JOIN plantilla_pos_list_tbl l ON p.pp_desc = l.ppl_desc
        ORDER BY 
            CASE 
                WHEN ppl_rank IS NOT NULL THEN 0
                ELSE 1
            END,
        ppl_rank
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>