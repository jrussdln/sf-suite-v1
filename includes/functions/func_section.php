<?php

function add_section($pdo, $data) {
try {
    extract($data);
    if (empty($a_sectionname) || empty($a_gradelevel)) {
        throw new Exception("Missing required fields.");
    }
    $query = "INSERT INTO section_tbl 
                (SectionName, GradeLevel, SchoolYear, Facility, SectionStrand) 
                VALUES (?, ?, ?, ?, ?)";

    $pdo->beginTransaction();
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        $a_sectionname, $a_gradelevel, $a_curriculumterm, $a_facility, $a_sectionstrand
    ]);

    $pdo->commit();
    return ['success' => true, 'message' => 'Class added successfully.'];
} catch (Exception $e) {
    $pdo->rollBack();
    return ['success' => false, 'message' => 'Failed to add class: ' . $e->getMessage()];
}
}
function fetchClasses($pdo, $schoolYear, $gradeLevel = null, $studentSection = null)
{
    $query = "SELECT * FROM section_tbl WHERE 1=1";

    if ($schoolYear) {
        $query .= " AND SchoolYear = :schoolYear";
    }
    if ($gradeLevel) {
        $query .= " AND GradeLevel = :gradeLevel";
    }
    if ($studentSection) {
        $query .= " AND SectionName = :studentSection";
    }

    $stmt = $pdo->prepare($query);

    if ($schoolYear) {
        $stmt->bindValue(':schoolYear', $schoolYear, PDO::PARAM_STR);
    }
    if ($gradeLevel) {
        $stmt->bindValue(':gradeLevel', $gradeLevel, PDO::PARAM_STR);
    }
    if ($studentSection) {
        $stmt->bindValue(':studentSection', $studentSection, PDO::PARAM_STR);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get the active school year from school_year_tbl
function getActiveSchoolYear($pdo)
{
    $query = "SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active' LIMIT 1";
    $stmt = $pdo->query($query);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['sy_term'] : null;
}

function edit_section($pdo, $data) {
    try {
        $sql = "UPDATE section_tbl SET
            SectionName = :sectionname,
            GradeLevel = :gradelevel,
            SchoolYear = :schoolyear,
            Facility = :facility,
            SectionStrand = :sectionstrand
            WHERE SectionId = :SectionId";

        // Prepare the statement
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':SectionId', $data['e_SectionId']);
        $stmt->bindParam(':sectionname', $data['e_sectionname']);
        $stmt->bindParam(':gradelevel', $data['e_gradelevel']);
        $stmt->bindParam(':schoolyear', $data['e_schoolyear']);
        $stmt->bindParam(':facility', $data['e_facility']);
        $stmt->bindParam(':sectionstrand', $data['e_sectionstrand']);

        // Execute the query        
        $stmt->execute();

        // Check if any rows were affected
        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'Section updated successfully.'];
        } else {
            return ['success' => false, 'message' => 'No changes made or section not found.'];
        }
    } catch (PDOException $e) {
        // Handle any errors
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

function getClassAdviser($sectionName, $pdo) {
// Match ClassName with anc_ass_desc in anc_ass_tbl
$adviserQuery = "
    SELECT PersonnelId 
    FROM anc_ass_tbl 
    WHERE anc_ass_desc LIKE :sectionName
";
$adviserStmt = $pdo->prepare($adviserQuery);
$adviserStmt->execute(['sectionName' => $sectionName]);
$adviser = $adviserStmt->fetch(PDO::FETCH_ASSOC);

if ($adviser) {
    return fetchAdviserName($adviser['PersonnelId'], $pdo);
} else {
    // No match found in anc_ass_tbl
    return null;
}
}
function fetchAdviserName($personnelId, $pdo) {
// Retrieve the adviser's name from school_per_tbl using PersonnelId
$nameQuery = "
    SELECT EmpFName, EmpMName, EmpLName, EmpEName 
    FROM school_per_tbl 
    WHERE PersonnelId = :personnelId
";
$nameStmt = $pdo->prepare($nameQuery);
$nameStmt->execute(['personnelId' => $personnelId]);
$nameRow = $nameStmt->fetch(PDO::FETCH_ASSOC);

if ($nameRow) {
    // Concatenate the adviser's full name
    return trim(
        $nameRow['EmpFName'] . ' ' .
        ($nameRow['EmpMName'] ? $nameRow['EmpMName'] . ' ' : '') .
        $nameRow['EmpLName'] .
        ($nameRow['EmpEName'] ? ', ' . $nameRow['EmpEName'] : '')
    );
} else {
    // No adviser found
    return null;
}
}
function get_section_data($pdo, $SectionId) {
if (!$SectionId) {
    return [
        "success" => false,
        "message" => "Invalid Section ID."
    ];
}

try {
    $stmt = $pdo->prepare("SELECT * FROM section_tbl WHERE SectionId = :SectionId");
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
function student_count($SectionName, $schoolYear, $pdo) {
    $query = "
        SELECT COUNT(*) AS count 
        FROM student_tbl
        WHERE section = ? AND school_year = ?
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$SectionName, $schoolYear]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result['count'] ?? 0; // Ensure it returns 0 if no records are found
}

function student_in_section($pdo, $sectionId) {
    // Get the SchoolYear from section_tbl based on SectionId
    $schoolYearQuery = "
        SELECT SchoolYear
        FROM section_tbl 
        WHERE SectionId = :sectionId
    ";

    $stmt = $pdo->prepare($schoolYearQuery);
    $stmt->execute(['sectionId' => $sectionId]);
    $schoolYearResult = $stmt->fetch(PDO::FETCH_ASSOC);

    // If no result, return an empty dataset
    if (!$schoolYearResult) {
        return ['data' => []];
    }

    $schoolYear = $schoolYearResult['SchoolYear'];

    // Get students in the section for the determined school year
    $query = "
        SELECT s.* 
        FROM student_tbl s
        WHERE s.section = (SELECT SectionName FROM section_tbl WHERE SectionId = :sectionId)
        AND s.school_year = :schoolYear
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'sectionId' => $sectionId,
        'schoolYear' => $schoolYear
    ]);

    $studentInSectionData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return ['data' => $studentInSectionData];
}
function fetchSection($pdo, $sy_from)
{
    $query = "SELECT SectionName, GradeLevel, SchoolYear, Facility, SectionStrand
              FROM section_tbl 
              WHERE SchoolYear = :sy_from
              ORDER BY CAST(GradeLevel AS UNSIGNED) ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':sy_from' => $sy_from
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function insertCopiedSection($pdo, $section, $copy_sy_to)
{
    // Query to check if a section already exists in the target term
    $checkQuery = "SELECT COUNT(*) FROM section_tbl 
                   WHERE SectionName = :SectionName
                   AND SchoolYear = :SchoolYear";
    $checkStmt = $pdo->prepare($checkQuery);

    // Query to insert a new section
    $insertQuery = "INSERT INTO section_tbl 
                    (SectionName, GradeLevel, SchoolYear, Facility, SectionStrand) 
                    VALUES 
                    (:SectionName, :GradeLevel, :SchoolYear, :Facility, :SectionStrand)";
    $insertStmt = $pdo->prepare($insertQuery);

    $insertedCount = 0;
    $skippedCount = 0;

    foreach ($section as $sections) {
        // Check if section already exists in the target school year
        $checkStmt->execute([
            ':SectionName' => $sections['SectionName'],
            ':SchoolYear' => $copy_sy_to,
        ]);
        $exists = $checkStmt->fetchColumn();

        if ($exists > 0) {
            $skippedCount++;
            continue;
        }

        // Insert the section
        try {
            $insertStmt->execute([
                ':SectionName' => $sections['SectionName'],
                ':GradeLevel' => $sections['GradeLevel'],
                ':SchoolYear' => $copy_sy_to,
                ':Facility' => $sections['Facility'],
                ':SectionStrand' => $sections['SectionStrand']
            ]);
            $insertedCount++;
        } catch (PDOException $e) {
            error_log("Insert failed for SectionName " . $sections['SectionName'] . ": " . $e->getMessage());
            $skippedCount++;
        }
    }

    return [
        'inserted' => $insertedCount,
        'skipped' => $skippedCount
    ];
}

