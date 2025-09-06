<?php
function student_list($pdo, $schoolYear, $gradeLevel, $studentSex, $studentSection) {
    $query = "SELECT * FROM student_tbl WHERE 1=1";
    
    if (!empty($schoolYear)) {
        $query .= " AND school_year = :schoolYear";
    }
    if (!empty($gradeLevel)) {
        $query .= " AND grade_level = :gradeLevel";
    }
    if (!empty($studentSex)) {
        $query .= " AND sex = :studentSex";
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
    if (!empty($studentSex)) {
        $stmt->bindValue(':studentSex', $studentSex, PDO::PARAM_STR);
    }
    if (!empty($studentSection)) {
        $stmt->bindValue(':studentSection', $studentSection, PDO::PARAM_STR);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
} 
function get_lm($pdo, $schoolYear, $gradeLevel, $studentSex, $studentSection) {
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
        $studentQuery = "SELECT name, grade_level, sex FROM student_tbl WHERE lrn = :lrn";
        $studentStmt = $pdo->prepare($studentQuery);
        $studentStmt->bindValue(':lrn', $lrn, PDO::PARAM_STR);
        $studentStmt->execute();
        $student = $studentStmt->fetch(PDO::FETCH_ASSOC);

        // Check if the student exists and matches the additional filters
        if ($student) {
            // Apply additional filters
            if ((!empty($gradeLevel) && $student['grade_level'] !== $gradeLevel) ||
                (!empty($studentSex) && $student['sex'] !== $studentSex)) {
                continue; // Skip this record if it doesn't match the filters
            }

            // Combine attendance data with the student details
            $lm['name'] = $student['name'];
            $lm['grade_level'] = $student['grade_level'];
            $lm['sex'] = $student['sex'];
            $finalData[] = $lm;
        }
    }

    return $finalData;
}
function get_attendance($pdo, $schoolYear, $gradeLevel, $studentSection, $studentMonth) {
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
        $studentQuery = "SELECT name, grade_level FROM student_tbl WHERE lrn = :lrn";
        $studentStmt = $pdo->prepare($studentQuery);
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

function get_hnr($pdo, $schoolYear, $gradeLevel, $studentSex, $studentSection) {

    $query = "SELECT * FROM health_nutrition_tbl WHERE 1=1";
    
    if (!empty($schoolYear)) {
        $query .= " AND hnr_term = :schoolYear";
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
    $hnrData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $finalData = [];

    foreach ($hnrData as $hnrDatas) {
        $lrn = $hnrDatas['lrn'];

        $studentQuery = "SELECT name, grade_level, sex FROM student_tbl WHERE lrn = :lrn";
        $studentStmt = $pdo->prepare($studentQuery);
        $studentStmt->bindValue(':lrn', $lrn, PDO::PARAM_STR);
        $studentStmt->execute();
        $student = $studentStmt->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            if ((!empty($gradeLevel) && $student['grade_level'] !== $gradeLevel) ||
                (!empty($studentSex) && $student['sex'] !== $studentSex)) {
                continue; 
            }
            $hnrDatas['name'] = $student['name'];
            $hnrDatas['grade_level'] = $student['grade_level'];
            $hnrDatas['sex'] = $student['sex'];
            $finalData[] = $hnrDatas;
        }
    }

    return $finalData;
}
function get_personnel($pdo, $data) {
    $query = "SELECT * FROM school_per_tbl";
    
    // Prepare and execute the query
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    // Fetch all results
    $personnelListData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $personnelListData;
}
function promotion_list($pdo, $schoolYear, $gradeLevel, $studentSex, $studentSection) {
    $query = "SELECT * FROM prom_achievement_tbl WHERE 1=1";
    
    if (!empty($schoolYear)) {
        $query .= " AND school_year = :schoolYear";
    }
    if (!empty($gradeLevel)) {
        $query .= " AND grade_level = :gradeLevel";
    }
    if (!empty($studentSex)) {
        $query .= " AND sex = :studentSex";
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
    if (!empty($studentSex)) {
        $stmt->bindValue(':studentSex', $studentSex, PDO::PARAM_STR);
    }
    if (!empty($studentSection)) {
        $stmt->bindValue(':studentSection', $studentSection, PDO::PARAM_STR);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
} 
function rc_grade($pdo, $schoolYear, $lrn) {
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
// Function to fetch export records from the database
// Function to fetch export records from the database
function get_export_record($pdo)
{
    // SQL query to fetch the export records
    $sql = "SELECT * FROM export_record_tbl ORDER BY exported_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Fetch all records as an associative array
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the records in JSON format, wrapped in a 'data' key
    return ['data' => $records];
}



?>