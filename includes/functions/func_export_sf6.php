<?php

require_once('../db_config.php');
require_once('../Excel/vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory; 
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
function update_sf6($pdo, $schoolYear, $userFullName) {
    // Load the existing Excel file
$filePath = $_SERVER['DOCUMENT_ROOT'] . '/sf_suite/school_forms_temp/SF6.xls';
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();

    $schoolInfo = fetch_school_info($pdo);
    if ($schoolInfo) {
        $sheet->setCellValue('J5', $schoolInfo['region']);
        $sheet->setCellValue('O7', $schoolInfo['district']);
        $sheet->setCellValue('O5', $schoolInfo['division']);
        $sheet->setCellValue('E5', $schoolInfo['school_id']);
        $sheet->setCellValue('D7', $schoolInfo['school_name']);
    }
    // Insert the school year, grade level, and student section into the specified cells
    $sheet->setCellValue('V7', $schoolYear);


    // Fetch data from database
    $pa_list = prom_achievement_list($pdo, $schoolYear); // Promoted list
    $conditional_list = cond_achievement_list($pdo, $schoolYear); // Conditional list
    $retained_list = retained_achievement_list($pdo, $schoolYear); // Retained list
    $lower_list = low_average_achievement_list($pdo, $schoolYear);
    $fairly_list = fairly_achievement_list($pdo, $schoolYear);
    $satisfactory_list = satisfactory_achievement_list($pdo, $schoolYear); // New
    $very_satisfactory_list = very_satisfactory_achievement_list($pdo, $schoolYear); // New
    $outstanding_list = outstanding_achievement_list($pdo, $schoolYear); // New

    // Get row 12 style and merge info
    $rowTemplate = 12;
    $styleArray = $sheet->getStyle('C' . $rowTemplate . ':W' . $rowTemplate);
    $mergedCells = $sheet->getMergeCells();

    // Set starting row
    $row = 11;

    // Initialize total counts
    $totalMaleCount = 0;
    $totalFemaleCount = 0;
    $totalCount = 0;

    $ConditionalTotalMaleCount = 0;
    $ConditionalTotalFemaleCount = 0;
    $ConditionalTotalCount = 0;

    $RetainedTotalMaleCount = 0;
    $RetainedTotalFemaleCount = 0;
    $RetainedTotalCount = 0;

    $LowTotalMaleCount = 0;
    $LowTotalFemaleCount = 0;
    $LowTotalCount = 0;

    $FairlyTotalMaleCount = 0;
    $FairlyTotalFemaleCount = 0;
    $FairlyTotalCount = 0;

    // Initialize totals for new categories
    $SatisfactoryTotalMaleCount = 0;
    $SatisfactoryTotalFemaleCount = 0;
    $SatisfactoryTotalCount = 0;

    $VerySatisfactoryTotalMaleCount = 0;
    $VerySatisfactoryTotalFemaleCount = 0;
    $VerySatisfactoryTotalCount = 0;

    $OutstandingTotalMaleCount = 0;
    $OutstandingTotalFemaleCount = 0;
    $OutstandingTotalCount = 0;

    // Initialize arrays for each category
    $promotedMaleCounts = [];
    $promotedFemaleCounts = [];
    $promotedTotalCounts = [];

    $ConditionalMaleCounts = [];
    $ConditionalFemaleCounts = [];
    $ConditionalTotalCounts = [];

    $RetainedMaleCounts = [];
    $RetainedFemaleCounts = [];
    $RetainedTotalCounts = [];

    $LowMaleCounts = [];
    $LowFemaleCounts = [];
    $LowTotalCounts = [];

    $FairlyMaleCounts = [];
    $FairlyFemaleCounts = [];
    $FairlyTotalCounts = [];

    // Initialize arrays for new categories
    $SatisfactoryMaleCounts = [];
    $SatisfactoryFemaleCounts = [];
    $SatisfactoryTotalCounts = [];

    $VerySatisfactoryMaleCounts = [];
    $VerySatisfactoryFemaleCounts = [];
    $VerySatisfactoryTotalCounts = [];

    $OutstandingMaleCounts = [];
    $OutstandingFemaleCounts = [];
    $OutstandingTotalCounts = [];

    // Loop through each grade level from 7 to 12
    for ($grade = 7; $grade <= 12; $grade++) {
        // Promoted counts
        $promotedMaleCounts[$grade] = $pa_list[$grade]['M'] ?? 0;
        $promotedFemaleCounts[$grade] = $pa_list[$grade]['F'] ?? 0;
        $promotedTotalCounts[$grade] = $promotedMaleCounts[$grade] + $promotedFemaleCounts[$grade];

        $totalMaleCount += $promotedMaleCounts[$grade];
        $totalFemaleCount += $promotedFemaleCounts[$grade];

        // Conditional counts
        $ConditionalMaleCounts[$grade] = $conditional_list[$grade]['M'] ?? 0;
        $ConditionalFemaleCounts[$grade] = $conditional_list[$grade]['F'] ?? 0;
        $ConditionalTotalCounts[$grade] = $ConditionalMaleCounts[$grade] + $ConditionalFemaleCounts[$grade];

        $ConditionalTotalMaleCount += $ConditionalMaleCounts[$grade];
        $ConditionalTotalFemaleCount += $ConditionalFemaleCounts[$grade];

        // Retained counts
        $RetainedMaleCounts[$grade] = $retained_list[$grade]['M'] ?? 0;
        $RetainedFemaleCounts[$grade] = $retained_list[$grade]['F'] ?? 0;
        $RetainedTotalCounts[$grade] = $RetainedMaleCounts[$grade] + $RetainedFemaleCounts[$grade];

        $RetainedTotalMaleCount += $RetainedMaleCounts[$grade];
        $RetainedTotalFemaleCount += $RetainedFemaleCounts[$grade];

        // Low counts
        $LowMaleCounts[$grade] = $lower_list[$grade]['M'] ?? 0;
        $LowFemaleCounts[$grade] = $lower_list[$grade]['F'] ?? 0;
        $LowTotalCounts[$grade] = $LowMaleCounts[$grade] + $LowFemaleCounts[$grade];

        $LowTotalMaleCount += $LowMaleCounts[$grade];
        $LowTotalFemaleCount += $LowFemaleCounts[$grade];

        // Fairly counts
        $FairlyMaleCounts[$grade] = $fairly_list[$grade]['M'] ?? 0;
        $FairlyFemaleCounts[$grade] = $fairly_list[$grade]['F'] ?? 0;
        $FairlyTotalCounts[$grade] = $FairlyMaleCounts[$grade] + $FairlyFemaleCounts[$grade];

        // Update total counts for Fairly
        $FairlyTotalMaleCount += $FairlyMaleCounts[$grade];
        $FairlyTotalFemaleCount += $FairlyFemaleCounts[$grade];

        // Satisfactory counts
        $SatisfactoryMaleCounts[$grade] = $satisfactory_list[$grade]['M'] ?? 0;
        $SatisfactoryFemaleCounts[$grade] = $satisfactory_list[$grade]['F'] ?? 0;
        $SatisfactoryTotalCounts[$grade] = $SatisfactoryMaleCounts[$grade] + $SatisfactoryFemaleCounts[$grade];

        // Update total counts for Satisfactory
        $SatisfactoryTotalMaleCount += $SatisfactoryMaleCounts[$grade];
        $SatisfactoryTotalFemaleCount += $SatisfactoryFemaleCounts[$grade];

        // Very Satisfactory counts
        $VerySatisfactoryMaleCounts[$grade] = $very_satisfactory_list[$grade]['M'] ?? 0;
        $VerySatisfactoryFemaleCounts[$grade] = $very_satisfactory_list[$grade]['F'] ?? 0;
        $VerySatisfactoryTotalCounts[$grade] = $VerySatisfactoryMaleCounts[$grade] + $VerySatisfactoryFemaleCounts[$grade];

        // Update total counts for Very Satisfactory
        $VerySatisfactoryTotalMaleCount += $VerySatisfactoryMaleCounts[$grade];
        $VerySatisfactoryTotalFemaleCount += $VerySatisfactoryFemaleCounts[$grade];

        // Outstanding counts
        $OutstandingMaleCounts[$grade] = $outstanding_list[$grade]['M'] ?? 0;
        $OutstandingFemaleCounts[$grade] = $outstanding_list[$grade]['F'] ?? 0;
        $OutstandingTotalCounts[$grade] = $OutstandingMaleCounts[$grade] + $OutstandingFemaleCounts[$grade];

        // Update total counts for Outstanding
        $OutstandingTotalMaleCount += $OutstandingMaleCounts[$grade];
        $OutstandingTotalFemaleCount += $OutstandingFemaleCounts[$grade];
    }

    // Calculate total counts
    $totalCount = $totalMaleCount + $totalFemaleCount;
    $ConditionalTotalCount = $ConditionalTotalMaleCount + $ConditionalTotalFemaleCount;
    $RetainedTotalCount = $RetainedTotalMaleCount + $RetainedTotalFemaleCount;
    $LowTotalCount = $LowTotalMaleCount + $LowTotalFemaleCount;
    $FairlyTotalCount = $FairlyTotalMaleCount + $FairlyTotalFemaleCount;
    $SatisfactoryTotalCount = $SatisfactoryTotalMaleCount + $SatisfactoryTotalFemaleCount;
    $VerySatisfactoryTotalCount = $VerySatisfactoryTotalMaleCount + $VerySatisfactoryTotalFemaleCount;
    $OutstandingTotalCount = $OutstandingTotalMaleCount + $OutstandingTotalFemaleCount;

    $schoolHeadRow = $OutstandingTotalCount + 22;
    $sheet->setCellValue("C$schoolHeadRow", $schoolInfo['school_head']);

    function insertDataRow($sheet, &$row, $styleArray, $mergedCells, $dataMale, $dataFemale, $dataTotal, $totalMale, $totalFemale, $totalOverall) {
        // Copy styles
        $sheet->duplicateStyle($styleArray, 'C' . $row . ':W' . $row);

        // Reapply merged cells
        foreach ($mergedCells as $merge) {
            $range = explode(':', $merge);
            preg_match('/([A-Z]+)(\d+)/', $range[0], $start);
            preg_match('/([A-Z]+)(\d+)/', $range[1], $end);
            if ((int)$start[2] == 12) {
                $newMerge = $start[1] . $row . ':' . $end[1] . $row;
                $sheet->mergeCells($newMerge);
            }
        }

        // Insert data
        $sheet->setCellValue('C' . $row, $dataMale[7])
              ->setCellValue('D' . $row, $dataFemale[7])
              ->setCellValue('E' . $row, $dataTotal[7])
              ->setCellValue('F' . $row, $dataMale[8])
              ->setCellValue('G' . $row, $dataFemale[8])
              ->setCellValue('H' . $row, $dataTotal[8])
              ->setCellValue('I' . $row, $dataMale[9])
              ->setCellValue('J' . $row, $dataFemale[9])
              ->setCellValue('K' . $row, $dataTotal[9])
              ->setCellValue('L' . $row, $dataMale[10])
              ->setCellValue('M' . $row, $dataFemale[10])
              ->setCellValue('N' . $row, $dataTotal[10])
              ->setCellValue('O' . $row, $dataMale[11])
              ->setCellValue('P' . $row, $dataFemale[11])
              ->setCellValue('Q' . $row, $dataTotal[11])
              ->setCellValue('R' . $row, $dataMale[12])
              ->setCellValue('S' . $row, $dataFemale[12])
              ->setCellValue('T' . $row, $dataTotal[12])
              ->setCellValue('U' . $row, $totalMale)
              ->setCellValue('V' . $row, $totalFemale)
              ->setCellValue('W' . $row, $totalOverall);

        // Move to next row
        $row++;
    }

    // Insert Promoted Data
    insertDataRow($sheet, $row, $styleArray, $mergedCells, $promotedMaleCounts, $promotedFemaleCounts, $promotedTotalCounts, $totalMaleCount, $totalFemaleCount, $totalCount);
    insertDataRow($sheet, $row, $styleArray, $mergedCells, $ConditionalMaleCounts, $ConditionalFemaleCounts, $ConditionalTotalCounts, $ConditionalTotalMaleCount, $ConditionalTotalFemaleCount, $ConditionalTotalCount);
    insertDataRow($sheet, $row, $styleArray, $mergedCells, $RetainedMaleCounts, $RetainedFemaleCounts, $RetainedTotalCounts, $RetainedTotalMaleCount, $RetainedTotalFemaleCount, $RetainedTotalCount);
    $row++; // Increment row for Low Average Data
    insertDataRow($sheet, $row, $styleArray, $mergedCells, $LowMaleCounts, $LowFemaleCounts, $LowTotalCounts, $LowTotalMaleCount, $LowTotalFemaleCount, $LowTotalCount);
    insertDataRow($sheet, $row, $styleArray, $mergedCells, $FairlyMaleCounts, $FairlyFemaleCounts, $FairlyTotalCounts, $FairlyTotalMaleCount, $FairlyTotalFemaleCount, $FairlyTotalCount);
    
    insertDataRow($sheet, $row, $styleArray, $mergedCells, $SatisfactoryMaleCounts, $SatisfactoryFemaleCounts, $SatisfactoryTotalCounts, $SatisfactoryTotalMaleCount, $SatisfactoryTotalFemaleCount, $SatisfactoryTotalCount);
    
    insertDataRow($sheet, $row, $styleArray, $mergedCells, $VerySatisfactoryMaleCounts, $VerySatisfactoryFemaleCounts, $VerySatisfactoryTotalCounts, $VerySatisfactoryTotalMaleCount, $VerySatisfactoryTotalFemaleCount, $VerySatisfactoryTotalCount);
    
    insertDataRow($sheet, $row, $styleArray, $mergedCells, $OutstandingMaleCounts, $OutstandingFemaleCounts, $OutstandingTotalCounts, $OutstandingTotalMaleCount, $OutstandingTotalFemaleCount, $OutstandingTotalCount);

    $newFileName = 'SF-SUITE_SF6_' . $schoolYear . date('Y-m-d_H-i-s') . '.xlsx';
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
function retained_achievement_list($pdo, $schoolYear) {
    $query = "
        SELECT 
            grade_level,
            sex,
            COUNT(*) as count
        FROM 
            prom_achievement_tbl
        WHERE 
            action_taken = 'RETAINED' AND 
            school_year = :schoolYear
        GROUP BY 
            grade_level, sex
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute(['schoolYear' => $schoolYear]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Initialize an array to hold the counts
    $counts = [];
    foreach ($results as $row) {
        $grade = $row['grade_level'];
        $sex = $row['sex'];
        $counts[$grade][$sex] = $row['count'];
    }

    return $counts;
}
function cond_achievement_list($pdo, $schoolYear) {
    $query = "
        SELECT 
            grade_level,
            sex,
            COUNT(*) as count
        FROM 
            prom_achievement_tbl
        WHERE 
            action_taken = 'IRREGULAR' AND 
            school_year = :schoolYear
        GROUP BY 
            grade_level, sex
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute(['schoolYear' => $schoolYear]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Initialize an array to hold the counts
    $counts = [];
    foreach ($results as $row) {
        $grade = $row['grade_level'];
        $sex = $row['sex'];
        $counts[$grade][$sex] = $row['count'];
    }

    return $counts;
}
function low_average_achievement_list($pdo, $schoolYear) {
    $query = "
        SELECT 
            grade_level,
            sex,
            COUNT(*) as count
        FROM 
            prom_achievement_tbl
        WHERE 
            general_average <= 74
            AND school_year = :schoolYear
        GROUP BY 
            grade_level, sex
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute(['schoolYear' => $schoolYear]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Initialize an array to hold the counts
    $counts = [];
    foreach ($results as $row) {
        $grade = $row['grade_level'];
        $sex = $row['sex'];
        $counts[$grade][$sex] = $row['count'];
    }

    return $counts;
}
function fairly_achievement_list($pdo, $schoolYear) {
    $query = "
        SELECT 
            grade_level,
            sex,
            COUNT(*) as count
        FROM 
            prom_achievement_tbl
        WHERE 
            general_average >= 75 
            AND general_average < 80
            AND school_year = :schoolYear
        GROUP BY 
            grade_level, sex
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute(['schoolYear' => $schoolYear]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Initialize an array to hold the counts
    $counts = [];
    foreach ($results as $row) {
        $grade = $row['grade_level'];
        $sex = $row['sex'];
        $counts[$grade][$sex] = $row['count'];
    }

    return $counts;
}
function satisfactory_achievement_list($pdo, $schoolYear) {
    $query = "
        SELECT 
            grade_level,
            sex,
            COUNT(*) as count
        FROM 
            prom_achievement_tbl
        WHERE 
            general_average >= 80 
            AND general_average < 85
            AND school_year = :schoolYear
        GROUP BY 
            grade_level, sex
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute(['schoolYear' => $schoolYear]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Initialize an array to hold the counts
    $counts = [];
    foreach ($results as $row) {
        $grade = $row['grade_level'];
        $sex = $row['sex'];
        $counts[$grade][$sex] = $row['count'];
    }

    return $counts;
}
function very_satisfactory_achievement_list($pdo, $schoolYear) {
    $query = "
        SELECT 
            grade_level,
            sex,
            COUNT(*) as count
        FROM 
            prom_achievement_tbl
        WHERE 
            general_average >= 85 
            AND general_average < 90
            AND school_year = :schoolYear
        GROUP BY 
            grade_level, sex
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute(['schoolYear' => $schoolYear]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Initialize an array to hold the counts
    $counts = [];
    foreach ($results as $row) {
        $grade = $row['grade_level'];
        $sex = $row['sex'];
        $counts[$grade][$sex] = $row['count'];
    }

    return $counts;
}
function outstanding_achievement_list($pdo, $schoolYear) {
    $query = "
        SELECT 
            grade_level,
            sex,
            COUNT(*) as count
        FROM 
            prom_achievement_tbl
        WHERE 
            general_average >= 90 
            AND general_average < 100
            AND school_year = :schoolYear
        GROUP BY 
            grade_level, sex
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute(['schoolYear' => $schoolYear]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Initialize an array to hold the counts
    $counts = [];
    foreach ($results as $row) {
        $grade = $row['grade_level'];
        $sex = $row['sex'];
        $counts[$grade][$sex] = $row['count'];
    }

    return $counts;
}
function prom_achievement_list($pdo, $schoolYear) {
    $query = "
        SELECT 
            grade_level,
            sex,
            COUNT(*) as count
        FROM 
            prom_achievement_tbl
        WHERE 
            action_taken = 'PROMOTED' AND 
            school_year = :schoolYear
        GROUP BY 
            grade_level, sex
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute(['schoolYear' => $schoolYear]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Initialize an array to hold the counts
    $counts = [];
    foreach ($results as $row) {
        $grade = $row['grade_level'];
        $sex = $row['sex'];
        $counts[$grade][$sex] = $row['count'];
    }

    return $counts;
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
update_sf6($pdo, $schoolYear, $userFullName);

