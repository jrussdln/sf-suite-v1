<?php
function getAttendanceBySection(PDO $pdo, string $sectionId, int $days = 31): array
{
    try {
        // Get the current month
        $currentMonth = date('m');

        // Get the SchoolYear from section_tbl using sectionId
        $stmt = $pdo->prepare("SELECT SchoolYear FROM section_tbl WHERE SectionId = :section_id");
        $stmt->bindParam(':section_id', $sectionId, PDO::PARAM_STR);
        $stmt->execute();
        $section = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if section is found and has SchoolYear
        if (!$section || !isset($section['SchoolYear'])) {
            return ['success' => false, 'message' => 'Invalid Section ID or School Year not found.'];
        }

        $schoolYear = $section['SchoolYear'];

        // Dynamically construct the column names for the number of days
        $dayColumns = [];
        for ($i = 1; $i <= $days; $i++) {
            $dayColumns[] = "COALESCE(attendance_tbl.Day$i, 'Absent') AS Day$i";
        }
        $dayColumnsStr = implode(", ", $dayColumns);

        // SQL query to fetch attendance
        $stmt = $pdo->prepare("
            SELECT 
                attendance_tbl.lrn, 
                attendance_tbl.section,
                attendance_tbl.section_id,  
                attendance_tbl.created_at,
                attendance_tbl.attendance_remarks, 
                $dayColumnsStr
            FROM 
                attendance_tbl
            WHERE 
                attendance_tbl.section_id = :section_id
                AND attendance_tbl.attendance_term = :schoolYear
                AND MONTH(attendance_tbl.created_at) = :currentMonth
        ");
        $stmt->bindParam(':section_id', $sectionId, PDO::PARAM_STR);
        $stmt->bindParam(':schoolYear', $schoolYear, PDO::PARAM_STR);
        $stmt->bindParam(':currentMonth', $currentMonth, PDO::PARAM_INT);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$results) {
            return ['success' => false, 'message' => 'No attendance data found for the given section and school year.'];
        }

        // Extract LRN values to get student names
        $lrnList = array_column($results, 'lrn');
        $studentNames = getStudentNamesByLrn($pdo, $lrnList);

        // Map LRN to names
        $nameMapping = [];
        foreach ($studentNames as $student) {
            $nameMapping[$student['lrn']] = $student['name'];
        }

        // Add student names to attendance results
        foreach ($results as &$result) {
            $result['name'] = $nameMapping[$result['lrn']] ?? 'Unknown';
        }

        return ['success' => true, 'data' => $results];

    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}
function getStudentNamesByLrn(PDO $pdo, array $lrnList): array
{
    if (empty($lrnList)) {
        return [];
    }
    // Prepare a placeholder string for the IN clause
    $placeholders = implode(',', array_fill(0, count($lrnList), '?'));
    // Prepare the SQL query
    $stmt = $pdo->prepare("
        SELECT lrn, name 
        FROM student_tbl 
        WHERE lrn IN ($placeholders)
    ");
    // Execute the query with the lrn values
    $stmt->execute($lrnList);
    // Fetch all results
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getStudentsBySectionName(PDO $pdo, string $sectionName): array
{
    try {
        $stmt = $pdo->prepare("
            SELECT 
                lrn, 
                section, 
                school_year,
                remarks  
            FROM 
                student_tbl 
            WHERE 
                section = :sectionName
        ");
        $stmt->bindParam(':sectionName', $sectionName, PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $results ?: []; // Return empty array if no results
    } catch (PDOException $e) {
        error_log('Error in getStudentsBySectionName: ' . $e->getMessage());
        return [];
    }
}
function insertAttendanceRecord(PDO $pdo, string $lrn, string $section, string $school_year, int $sectionId, ?string $remarks): bool
{
    try {
        // Get the current month and year
        $currentMonth = date('m');
        $currentYear = date('Y');
        // Check if a record with the same LRN and school year exists in the same month and year
        $checkStmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM attendance_tbl 
            WHERE lrn = :lrn 
            AND attendance_term = :attendance_term
            AND MONTH(created_at) = :currentMonth 
            AND YEAR(created_at) = :currentYear
        ");
        $checkStmt->execute([
            'lrn' => $lrn,
            'attendance_term' => $school_year,
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear
        ]);
        $existingRecord = $checkStmt->fetchColumn();
        if ($existingRecord > 0) {
            error_log("Duplicate entry for LRN: $lrn in school year: $school_year for the current month.");
            return false;
        }
        // Insert the new attendance record with remarks
        $stmt = $pdo->prepare("
            INSERT INTO attendance_tbl 
            (lrn, section, section_id, attendance_term, attendance_remarks) 
            VALUES 
            (:lrn, :section, :section_id, :attendance_term, :attendance_remarks)
        ");
        $stmt->execute([
            'lrn' => $lrn,
            'section' => $section,
            'section_id' => $sectionId,
            'attendance_term' => $school_year,
            'attendance_remarks' => $remarks  // Insert the remarks
        ]);
        return true;
    } catch (PDOException $e) {
        error_log("Error inserting attendance record: " . $e->getMessage());
        return false;
    }
}
