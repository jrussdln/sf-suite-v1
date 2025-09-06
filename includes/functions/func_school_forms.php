<?php
function get_hnr_by_section($pdo, $sectionId) {
    try {
        // First, fetch the School Year from the section_tbl using the sectionId
        $stmt = $pdo->prepare("
            SELECT SchoolYear 
            FROM section_tbl 
            WHERE SectionId = :sectionId
        ");
        $stmt->bindParam(':sectionId', $sectionId, PDO::PARAM_INT);
        $stmt->execute();
        $section = $stmt->fetch(PDO::FETCH_ASSOC);
        // If the section is found, proceed to fetch health and nutrition records
        if ($section) {
            $schoolYear = $section['SchoolYear'];
            // Now fetch the health and nutrition records using the school year
            $stmt = $pdo->prepare("
                SELECT * 
                FROM health_nutrition_tbl 
                WHERE hnr_term = :schoolYear AND section_id = :sectionId
            ");
            $stmt->bindParam(':schoolYear', $schoolYear, PDO::PARAM_STR);
            $stmt->bindParam(':sectionId', $sectionId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: []; // Return records or an empty array
        }
        return []; // Return an empty array if the section is not found
    } catch (Exception $e) {
        return ["success" => false, "message" => "Error fetching records: " . $e->getMessage()];
    }
}
function get_hnr_data($pdo, $SectionId) {
    if (!$SectionId) {
        return [
            "success" => false,
            "message" => "Invalid Class ID."
        ];
    }
    try {
        // Query to fetch curriculum data
        $stmt = $pdo->prepare("SELECT SectionId, SectionName, GradeLevel, Facility, SchoolYear
                               FROM section_tbl 
                               WHERE SectionId = :SectionId");
        $stmt->bindParam(':SectionId', $SectionId, PDO::PARAM_INT);
        $stmt->execute();   
        $sectionData = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($sectionData) {
            return [
                "success" => true,
                "data" => $sectionData
            ];
        } else {    
            return [
                "success" => false,
                "message" => "Class data not found."
            ];
        }
    } catch (Exception $e) {
        return [
            "success" => false,
            "message" => "Error fetching class data: " . $e->getMessage()
        ];
    }
}
function get_hnrr($pdo, $health_nutrition_id) {
    try {
        // Specify the columns you want to select
        $stmt = $pdo->prepare("
            SELECT 
                health_nutrition_id, 
                birthdate, 
                age, 
                weight, 
                height,
                bmi, 
                height_squared, 
                nutritional_status, 
                height_for_age, 
                remarks 
            FROM health_nutrition_tbl 
            WHERE health_nutrition_id = :health_nutrition_id
        ");
        $stmt->execute(['health_nutrition_id' => $health_nutrition_id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            return ['success' => true, 'data' => $data];
        } else {
            return ['success' => false, 'message' => 'No records found for this ID.'];
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return ['success' => false, 'message' => 'An error occurred while fetching data.'];
    }
}
function get_section_data_hnr($pdo, $SectionId) {
    if (!$SectionId) {
        return [
            "success" => false,
            "message" => "Invalid Section ID."
        ];
    }
    try {
        $stmt = $pdo->prepare("SELECT SectionId, SectionName, GradeLevel, Facility, SchoolYear
                               FROM section_tbl 
                               WHERE SectionId = :SectionId");
        $stmt->bindParam(':SectionId', $SectionId, PDO::PARAM_INT);
        $stmt->execute();   
        $sectionData = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($sectionData) {
            return [
                "success" => true,
                "data" => $sectionData
            ];
        } else {    
            return [
                "success" => false,
                "message" => "Section data not found."
            ];
        }
    } catch (Exception $e) {
        return [
            "success" => false,
            "message" => "Error fetching class data: " . $e->getMessage()
        ];
    }
}
function getStudentsBySectionName(PDO $pdo, string $sectionName): array {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                lrn, 
                section, 
                school_year 
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
function get_student_name_by_lrn($pdo, $lrn) {
    try {
        $stmt = $pdo->prepare("
            SELECT name
            FROM student_tbl
            WHERE lrn = :lrn
        ");
        $stmt->bindParam(':lrn', $lrn, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['name'] : null;
    } catch (Exception $e) {
        return null; // Return null if there's an error or no result found
    }
}
function insertHnrRecord(PDO $pdo, string $lrn, string $section, string $schoolYear, int $sectionId): bool {
    try {
        // Check if the LRN already has an entry for the same school year (hnr_term)
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM health_nutrition_tbl WHERE lrn = :lrn AND hnr_term = :school_year");
        $checkStmt->execute(['lrn' => $lrn, 'school_year' => $schoolYear]);
        $existingRecord = $checkStmt->fetchColumn();
        if ($existingRecord > 0) {
            error_log("Duplicate entry: LRN $lrn already exists for school year $schoolYear.");
            return false;
        }
        // Insert new record (allowing duplicate LRN but ensuring hnr_term is unique per LRN)
        $stmt = $pdo->prepare("INSERT INTO health_nutrition_tbl (lrn, section, section_id, hnr_term, birthdate, age, weight, height, height_squared,bmi, nutritional_status, height_for_age, remarks) 
                               VALUES (:lrn, :section, :sectionId, :hnr_term, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL)");
        $stmt->execute([
            'lrn' => $lrn,
            'section' => $section,
            'sectionId' => $sectionId,
            'hnr_term' => $schoolYear
        ]);
        return true;
    } catch (PDOException $e) {
        error_log('Database error in insertHnrRecord: ' . $e->getMessage());
        return false;
    }
}
?>