<?php
function get_curriculum_name($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT curriculum_desc, curriculum_id FROM school_curriculum_tbl");
        $stmt->execute();
        $sc = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $sc; // Return the fetched subject codes
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}
function get_school_year_list($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT sy_id, sy_term FROM school_year_tbl WHERE sy_status = 'Active'");
        $stmt->execute();
        $sc = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $sc; // Return the fetched subject codes
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}
function get_school_year_list1($pdo)
{
    try {
        $stmt = $pdo->prepare("SELECT sy_id, sy_term FROM school_year_tbl");
        $stmt->execute();
        $sc = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $sc; // Return the fetched subject codes
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}
function get_section_strand($pdo)
{
    try {
        $stmt = $pdo->prepare("SELECT strand_track FROM strand_track_tbl WHERE strand_track_status = 'Active'");
        $stmt->execute();
        $sc = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $sc; // Return the fetched strand tracks
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}
function get_plapos_list($pdo)
{
    try {
        $stmt = $pdo->prepare("SELECT ppl_desc FROM plantilla_pos_list_tbl");
        $stmt->execute();
        $sc = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $sc; // Return the fetched subject codes
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}
function get_subject_list_details($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT stac_id, subject_taught FROM subject_taught_tbl");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}
function get_section_list($pdo) {
    try {
        // Get the active school year term
        $syStmt = $pdo->prepare("SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active' LIMIT 1");
        $syStmt->execute();
        $activeSchoolYear = $syStmt->fetch(PDO::FETCH_ASSOC);

        if (!$activeSchoolYear) {
            return ['error' => 'No active school year found.'];
        }

        $syTerm = $activeSchoolYear['sy_term'];

        // Fetch sections based on the active school year
        $stmt = $pdo->prepare("SELECT SectionName FROM section_tbl WHERE SchoolYear = :syTerm");
        $stmt->bindParam(':syTerm', $syTerm, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}
function get_question_list($pdo)
{
    try {
        // Step 1: Get the active school year
        $syStmt = $pdo->prepare("SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active' LIMIT 1");
        $syStmt->execute();
        $activeSchoolYear = $syStmt->fetchColumn();

        if (!$activeSchoolYear) {
            return ['error' => 'No active school year found.'];
        }

        // Step 2: Fetch questions based on the active school year
        $stmt = $pdo->prepare("SELECT question_id, question_desc FROM quest_tracer_tbl WHERE school_year = :activeSchoolYear");
        $stmt->bindParam(':activeSchoolYear', $activeSchoolYear);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}

function get_school_curriculum($pdo)
{
    try {
        $stmt = $pdo->prepare("SELECT curriculum_desc FROM school_curriculum_tbl WHERE curriculum_status = 'Active'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}
function get_section_list_teacher($pdo, $identifier)
{
    try {
        // 1. Get the active school year term
        $syStmt = $pdo->prepare("SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active' LIMIT 1");
        $syStmt->execute();
        $activeSchoolYear = $syStmt->fetch(PDO::FETCH_ASSOC);

        if (!$activeSchoolYear) {
            return ['error' => 'No active school year found.'];
        }

        $syTerm = $activeSchoolYear['sy_term'];

        // 2. Get the PersonnelId using the EmpNo (identifier)
        $personnelStmt = $pdo->prepare("SELECT PersonnelId FROM school_per_tbl WHERE EmpNo = :identifier LIMIT 1");
        $personnelStmt->bindParam(':identifier', $identifier, PDO::PARAM_STR);
        $personnelStmt->execute();
        $personnel = $personnelStmt->fetch(PDO::FETCH_ASSOC);

        if (!$personnel) {
            return ['error' => 'No personnel found with this identifier.'];
        }

        $personnelId = $personnel['PersonnelId'];

        // 3. Get section assignments for this PersonnelId and current term
        $assignStmt = $pdo->prepare("SELECT anc_ass_desc FROM anc_ass_tbl WHERE PersonnelId = :personnelId AND anc_ass_term = :syTerm");
        $assignStmt->bindParam(':personnelId', $personnelId, PDO::PARAM_STR);
        $assignStmt->bindParam(':syTerm', $syTerm, PDO::PARAM_STR);
        $assignStmt->execute();
        $assignments = $assignStmt->fetchAll(PDO::FETCH_COLUMN); // get only values of `anc_ass_desc`

        if (empty($assignments)) {
            return []; // no assignments, return empty
        }

        // 4. Get section names that match the assigned sections
        $inQuery = implode(',', array_fill(0, count($assignments), '?'));
        $stmt = $pdo->prepare("SELECT SectionName FROM section_tbl WHERE SchoolYear = ? AND SectionName IN ($inQuery)");

        $params = array_merge([$syTerm], $assignments);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}
function get_section_list_teach($pdo)
{
    $stmt = $pdo->prepare("SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active' LIMIT 1");
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC); // fetch() instead of fetchAll() since you're only getting 1 row
}

function get_section_list_subwt($pdo, $identifier)
{
    try {
        // 1. Get the active school year term
        $syStmt = $pdo->prepare("SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active' LIMIT 1");
        $syStmt->execute();
        $activeSchoolYear = $syStmt->fetch(PDO::FETCH_ASSOC);

        if (!$activeSchoolYear) {
            return ['error' => 'No active school year found.'];
        }

        $syTerm = $activeSchoolYear['sy_term'];

        // 2. Get the PersonnelId using the EmpNo (identifier)
        $personnelStmt = $pdo->prepare("SELECT PersonnelId FROM school_per_tbl WHERE EmpNo = :identifier LIMIT 1");
        $personnelStmt->bindParam(':identifier', $identifier, PDO::PARAM_STR);
        $personnelStmt->execute();
        $personnel = $personnelStmt->fetch(PDO::FETCH_ASSOC);

        if (!$personnel) {
            return ['error' => 'No personnel found with this identifier.'];
        }

        $personnelId = $personnel['PersonnelId'];

        // 3. Get section assignments for this PersonnelId and current term
        $assignStmt = $pdo->prepare("SELECT anc_ass_desc FROM anc_ass_tbl WHERE PersonnelId = :personnelId AND anc_ass_term = :syTerm");
        $assignStmt->bindParam(':personnelId', $personnelId, PDO::PARAM_STR);
        $assignStmt->bindParam(':syTerm', $syTerm, PDO::PARAM_STR);
        $assignStmt->execute();
        $assignments = $assignStmt->fetchAll(PDO::FETCH_COLUMN); // get only values of `anc_ass_desc`

        if (empty($assignments)) {
            return []; // no assignments, return empty
        }

        // 4. Get section names that match the assigned sections
        $inQuery = implode(',', array_fill(0, count($assignments), '?'));
        $stmt = $pdo->prepare("SELECT SectionName FROM section_tbl WHERE SchoolYear = ? AND SectionName IN ($inQuery)");

        $params = array_merge([$syTerm], $assignments);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}
function get_section_list_subt($pdo, $identifier)
{
    try {
        // 1. Get the active school year term
        $syStmt = $pdo->prepare("SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active' LIMIT 1");
        $syStmt->execute();
        $activeSchoolYear = $syStmt->fetch(PDO::FETCH_ASSOC);

        if (!$activeSchoolYear) {
            return ['error' => 'No active school year found.'];
        }

        $syTerm = $activeSchoolYear['sy_term'];

        // 2. Get the PersonnelId using the EmpNo (identifier)
        $personnelStmt = $pdo->prepare("SELECT PersonnelId FROM school_per_tbl WHERE EmpNo = :identifier LIMIT 1");
        $personnelStmt->bindParam(':identifier', $identifier, PDO::PARAM_STR);
        $personnelStmt->execute();
        $personnel = $personnelStmt->fetch(PDO::FETCH_ASSOC);

        if (!$personnel) {
            return ['error' => 'No personnel found with this identifier.'];
        }

        $personnelId = $personnel['PersonnelId'];

        // 3. Get section assignments from anc_ass_tbl
        $assignStmt = $pdo->prepare("SELECT anc_ass_desc FROM anc_ass_tbl WHERE PersonnelId = :personnelId AND anc_ass_term = :syTerm");
        $assignStmt->bindParam(':personnelId', $personnelId, PDO::PARAM_STR);
        $assignStmt->bindParam(':syTerm', $syTerm, PDO::PARAM_STR);
        $assignStmt->execute();
        $assignments = $assignStmt->fetchAll(PDO::FETCH_COLUMN);

        // 4. Get sections from subject_taught_tbl (Query 4)
        $subjectStmt = $pdo->prepare("SELECT section FROM subject_taught_tbl WHERE PersonnelId = :personnelId AND stac_term = :syTerm");
        $subjectStmt->bindParam(':personnelId', $personnelId, PDO::PARAM_STR);
        $subjectStmt->bindParam(':syTerm', $syTerm, PDO::PARAM_STR);
        $subjectStmt->execute();
        $subjectSections = $subjectStmt->fetchAll(PDO::FETCH_COLUMN);

        // Check if both results are empty, if so, return nothing
        if (empty($assignments) && empty($subjectSections)) {
            return []; // No sections to fetch
        }

        // Merge both lists and remove duplicates
        $allSections = array_unique(array_merge($assignments, $subjectSections));

        // 5. Get full section details from section_tbl
        $inQuery = implode(',', array_fill(0, count($allSections), '?'));
        $stmt = $pdo->prepare("SELECT SectionName FROM section_tbl WHERE SchoolYear = ? AND SectionName IN ($inQuery)");

        $params = array_merge([$syTerm], $allSections);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}
