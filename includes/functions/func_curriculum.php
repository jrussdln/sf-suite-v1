<?php
//CURRICULUM MANAGEMENT
function edit_curriculum($pdo, $data) {
    try {
        $sql = "UPDATE school_curriculum_tbl SET
            curriculum_desc = :curriculum_desc
            WHERE curriculum_id = :curriculum_id";
        // Prepare the statement
        $stmt = $pdo->prepare($sql);
        // Bind parameters
        $stmt->bindParam(':curriculum_id', $data['curriculum_id']);
        $stmt->bindParam(':curriculum_desc', $data['u_curriculum_desc']);
        // Execute the query        
        $stmt->execute();
        // Check if any rows were affected
        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'Curriculum updated successfully.'];
        } else {
            return ['success' => false, 'message' => 'No changes made or curriculum not found.'];
        }
    } catch (PDOException $e) {
        // Handle any errors
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}
function get_curriculum_data($pdo, $curriculum_id) {
    if (!$curriculum_id) {
        return [
            "success" => false,
            "message" => "Invalid curriculum ID."
        ];
    }
    try {
        // Fetch the active school year
        $stmt = $pdo->prepare("SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active' LIMIT 1");
        $stmt->execute();
        $activeSchoolYear = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$activeSchoolYear) {
            return [
                "success" => false,
                "message" => "No active school year found."
            ];
        }
        $sy_term = $activeSchoolYear['sy_term'];
        // Fetch curriculum details
        $stmt = $pdo->prepare("SELECT curriculum_id, curriculum_desc FROM school_curriculum_tbl WHERE curriculum_id = :curriculum_id");
        $stmt->bindParam(':curriculum_id', $curriculum_id, PDO::PARAM_INT);
        $stmt->execute();
        $curriculumData = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$curriculumData) {
            return [
                "success" => false,
                "message" => "Curriculum data not found."
            ];
        }
        // Fetch subjects by grade level
        $subjectsByGrade = [];
        $grades = [7, 8, 9, 10, 11, 12];
        foreach ($grades as $grade) {
            $stmt = $pdo->prepare("SELECT GROUP_CONCAT(subject_code ORDER BY subject_code SEPARATOR ', ') AS subjects 
                                   FROM subjects_tbl 
                                   WHERE curriculum_id = :curriculum_id AND subject_term = :sy_term AND grade_level = :grade_level AND archive = 0");
            $stmt->bindParam(':curriculum_id', $curriculum_id, PDO::PARAM_INT);
            $stmt->bindParam(':sy_term', $sy_term, PDO::PARAM_STR);
            $stmt->bindParam(':grade_level', $grade, PDO::PARAM_INT);
            $stmt->execute();
            $subjectData = $stmt->fetch(PDO::FETCH_ASSOC);
            $subjectsByGrade[$grade] = $subjectData['subjects'] ?? 'No subjects available';
        }
        return [
            "success" => true,
            "data" => [
                "curriculum" => $curriculumData,
                "subjects" => $subjectsByGrade
            ]
        ];
    } catch (Exception $e) {
        return [
            "success" => false,
            "message" => "Error fetching curriculum data: " . $e->getMessage()
        ];
    }
}
function add_curriculum($pdo, $data) {
    try {
        // Insert query with correct number of columns
        $query = "INSERT INTO school_curriculum_tbl (curriculum_desc) VALUES (?)";
        $pdo->beginTransaction(); // Start transaction
        $stmt = $pdo->prepare($query);
        $stmt->execute([$data['a_curriculum_desc']]); // Use correct variable from $data array
        $pdo->commit(); // Commit transaction
        return ['success' => true, 'message' => 'Curriculum added successfully.'];
    } catch (Exception $e) {
        $pdo->rollBack(); // Rollback transaction if error occurs
        return ['success' => false, 'message' => 'Error adding curriculum: ' . $e->getMessage()];
    }
}
function school_year_list($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM school_year_tbl");
    $stmt->execute();
    // Fetch all results
    $schoolYears = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Return the data
    return [
        'data' => $schoolYears // Wrap the data in a 'data' key for DataTables
    ];
}
function update_sy_status($pdo, $sy_id) {
    // First, set all other statuses to 'Inactive'
    $updateInactive = $pdo->prepare("UPDATE school_year_tbl SET sy_status = 'Inactive' WHERE sy_status = 'Active'");
    $updateInactive->execute();
    // Then, set the selected status to 'Active'
    $updateActive = $pdo->prepare("UPDATE school_year_tbl SET sy_status = 'Active' WHERE sy_id = :sy_id");
    $updateActive->bindParam(':sy_id', $sy_id);
    return $updateActive->execute();
}
function get_active_school_year($pdo) {
    $query = "SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active'";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchColumn(); // Fetch the single value
}
function add_school_year($pdo, $data) {
    $sy_term = $data['a_sy_term'] ?? null; // Correct name to match form field
    // Check for duplicate School Year (sy_term)
    $checkQuery = "SELECT COUNT(*) FROM school_year_tbl WHERE sy_term = ?";
    $stmt = $pdo->prepare($checkQuery);
    $stmt->execute([$sy_term]);
    $count = $stmt->fetchColumn();
    if ($count > 0) {
        http_response_code(409); // Conflict
        return [
            'success' => false,
            'message' => 'Registration failed: School Year already exists.'
        ];
    }
    // Insert the school year without sy_id, assuming sy_id is auto-incremented
    $query = "INSERT INTO school_year_tbl (sy_term) VALUES (?)";
    // Start a transaction
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$sy_term]);
        $insert_id = $pdo->lastInsertId();
        $pdo->commit(); // Commit the transaction
        http_response_code(201); // Created
        return [
            'success' => true,
            'message' => 'School year successfully added!',
            'insert_id' => $insert_id
        ];
    } catch (Exception $e) {
        $pdo->rollBack(); // Rollback the transaction on error
        http_response_code(500); // Internal Server Error
        return [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}
function curriculum_list($pdo) {
    $query = "SELECT * FROM school_curriculum_tbl";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $curriculumData = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all rows
    return [
        'data' => $curriculumData // Return the array properly
    ];
}
function update_curriculum_status($pdo, $curriculum_id, $status) {
    try {
        $query = "UPDATE school_curriculum_tbl SET curriculum_status = :status WHERE curriculum_id = :curriculum_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':curriculum_id', $curriculum_id);
        $stmt->execute();
        return [
            'success' => true,
            'message' => 'Curriculum status updated successfully.'
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error updating status: ' . $e->getMessage()
        ];
    }
}
function strand_track_list($pdo)
{
    $stmt = $pdo->prepare("SELECT * FROM strand_track_tbl");
    $stmt->execute();
    // Fetch all results
    $strandTrack = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Return the data
    return [
        'data' => $strandTrack 
    ];
}
function add_strand_track($pdo, $data)
{
    try {
        // Insert query with correct number of columns
        $query = "INSERT INTO strand_track_tbl (strand_track, description) VALUES (?, ?)";
        $pdo->beginTransaction(); // Start transaction
        $stmt = $pdo->prepare($query);
        $stmt->execute([$data['ast_strand_track'], $data['ast_description']]); // Include both values
        $pdo->commit(); // Commit transaction
        return ['success' => true, 'message' => 'Strand/Track added successfully.'];
    } catch (Exception $e) {
        $pdo->rollBack(); // Rollback transaction if error occurs
        return ['success' => false, 'message' => 'Error adding strand/track: ' . $e->getMessage()];
    }
}
function get_strand_track_data($pdo, $id)
{
    try {
        // Prepare the SQL statement
        $stmt = $pdo->prepare("SELECT * FROM strand_track_tbl WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        // Fetch data
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            return [
                'success' => true,
                'data' => $data
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No record found.'
            ];
        }
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}
function edit_strand_track($pdo, $data)
{
    try {
        $sql = "UPDATE strand_track_tbl SET
            strand_track = :strand_track,
            description = :description
            WHERE id = :id";
        // Prepare the statement
        $stmt = $pdo->prepare($sql);
        // Bind parameters
        $stmt->bindParam(':id', $data['ust_id']);
        $stmt->bindParam(':strand_track', $data['ust_strand_track']);
        $stmt->bindParam(':description', $data['ust_description']);
        // Execute the query        
        $stmt->execute();
        // Check if any rows were affected
        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'Strand/Track updated successfully.'];
        } else {
            return ['success' => false, 'message' => 'No changes made or strand/track not found.'];
        }
    } catch (PDOException $e) {
        // Handle any errors
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}
function update_strand_track($pdo, $id, $status)
{
    try {
        $query = "UPDATE strand_track_tbl SET strand_track_status = :status WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return [
            'success' => true,
            'message' => 'Strand/Track status updated successfully.'
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error updating status: ' . $e->getMessage()
        ];
    }
}
//SUBJECTS
function subjects_list($pdo, $schoolYear, $gradeLevel, $semester, $subjectType) {
    // Get the active school year's sy_term
    $syQuery = "SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active' LIMIT 1";
    $syStmt = $pdo->query($syQuery);
    $activeSchoolYear = $syStmt->fetch(PDO::FETCH_ASSOC);
    $activeSyTerm = $activeSchoolYear ? $activeSchoolYear['sy_term'] : null;
    // Base query with JOIN to filter active curriculums
    $query = "SELECT s.* 
              FROM subjects_tbl s
              JOIN school_curriculum_tbl c ON s.curriculum_id = c.curriculum_id
              WHERE s.archive = 0 AND c.curriculum_status = 'Active'";
    // If schoolYear is empty, use activeSyTerm
    if (empty($schoolYear) && $activeSyTerm !== null) {
        $query .= " AND s.subject_term = :activeSyTerm";
    } elseif (!empty($schoolYear)) {
        $query .= " AND s.subject_term = :schoolYear";
    }
    if (!empty($gradeLevel)) {
        $query .= " AND s.grade_level = :gradeLevel";
    }
    if (!empty($semester)) {
        $query .= " AND s.subject_semester = :semester";
    }
    if (!empty($subjectType)) {
        $query .= " AND s.subjectType = :subjectType";
    }
    $stmt = $pdo->prepare($query);
    // Bind values
    if (empty($schoolYear) && $activeSyTerm !== null) {
        $stmt->bindValue(':activeSyTerm', $activeSyTerm, PDO::PARAM_STR);
    } elseif (!empty($schoolYear)) {
        $stmt->bindValue(':schoolYear', $schoolYear, PDO::PARAM_STR);
    }
    if (!empty($gradeLevel)) {
        $stmt->bindValue(':gradeLevel', $gradeLevel, PDO::PARAM_STR);
    }
    if (!empty($semester)) {
        $stmt->bindValue(':semester', $semester, PDO::PARAM_STR);
    }
    if (!empty($subjectType)) {
        $stmt->bindValue(':subjectType', $subjectType, PDO::PARAM_STR);
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function archive_subjects_list($pdo, $schoolYear, $gradeLevel, $semester, $subjectType) {
    // Get the active school year's sy_term
    $syQuery = "SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active' LIMIT 1";
    $syStmt = $pdo->query($syQuery);
    $activeSchoolYear = $syStmt->fetch(PDO::FETCH_ASSOC);
    $activeSyTerm = $activeSchoolYear ? $activeSchoolYear['sy_term'] : null;
    // Start the query
    $query = "SELECT * FROM subjects_tbl WHERE archive = 1";
    // If schoolYear is empty, use activeSyTerm
    if (empty($schoolYear) && $activeSyTerm !== null) {
        $query .= " AND subject_term = :activeSyTerm";
    } elseif (!empty($schoolYear)) {
        $query .= " AND subject_term = :schoolYear";
    }
    if (!empty($gradeLevel)) {
        $query .= " AND grade_level = :gradeLevel";
    }
    if (!empty($semester)) {
        $query .= " AND subject_semester = :semester";
    }
    if (!empty($subjectType)) {
        $query .= " AND subjectType = :subjectType";
    }
    $stmt = $pdo->prepare($query);
    // Bind values
    if (empty($schoolYear) && $activeSyTerm !== null) {
        $stmt->bindValue(':activeSyTerm', $activeSyTerm, PDO::PARAM_STR);
    } elseif (!empty($schoolYear)) {
        $stmt->bindValue(':schoolYear', $schoolYear, PDO::PARAM_STR);
    }
    if (!empty($gradeLevel)) {
        $stmt->bindValue(':gradeLevel', $gradeLevel, PDO::PARAM_STR);
    }
    if (!empty($semester)) {
        $stmt->bindValue(':semester', $semester, PDO::PARAM_STR);
    }
    if (!empty($subjectType)) {
        $stmt->bindValue(':subjectType', $subjectType, PDO::PARAM_STR);
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function archive_subject($pdo, $subject_id) {
    // Update the subject's archive status
    $updateQuery = "UPDATE subjects_tbl 
                    SET archive = 1 
                    WHERE subject_id = :subject_id";
    try {
        // Begin a transaction
        $pdo->beginTransaction();
        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute([
            ':subject_id' => $subject_id
        ]);
        // Commit the transaction
        $pdo->commit();
        return [
            'success' => true,
            'message' => 'Subject successfully archived!'
        ];
    } catch (Exception $e) {
        // Rollback the transaction on error
        $pdo->rollBack();
        return [
            'success' => false,
            'message' => 'Failed to archive subject. Error: ' . $e->getMessage()
        ];
    }
}
function unarchive_subject($pdo, $subject_id) {
    // Update the subject's archive status
    $updateQuery = "UPDATE subjects_tbl 
                    SET archive = 0 
                    WHERE subject_id = :subject_id";
    try {
        // Begin a transaction
        $pdo->beginTransaction();
        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute([
            ':subject_id' => $subject_id
        ]);
        // Commit the transaction
        $pdo->commit();
        return [
            'success' => true,
            'message' => 'Subject successfully restored!'
        ];
    } catch (Exception $e) {
        // Rollback the transaction on error
        $pdo->rollBack();
        return [
            'success' => false,
            'message' => 'Failed to restore subject. Error: ' . $e->getMessage()
        ];
    }
}
function add_subject($pdo, $data)
{
    // Access variables directly from the data array
    $subject_code = $data['a_subject_code'] ?? null;
    $curriculum = $data['a_curriculum'] ?? null;
    $grade_level = $data['a_grade_level'] ?? null;
    $strand = $data['a_strand'] ?? null;
    $subject_name = $data['a_subject_name'] ?? null;
    $subject_desc = $data['a_subject_desc'] ?? null;
    $weekly_hours = $data['a_weekly_hours'] ?? null;
    $subjectType = $data['a_subject_type'] ?? null;
    $subjectSemester = $data['a_subject_quarter'] ?? null;
    $subjectOrder = $data['a_subject_order'] ?? null;
    $nested_id = isset($data['a_checkbox']) ? "MAPEH-$grade_level" : null;
    $archive = 0;
    // Check for duplicate Subject Code
    $checkQuery = "SELECT COUNT(*) FROM subjects_tbl WHERE subject_code = ?";
    $stmt = $pdo->prepare($checkQuery);
    $stmt->execute([$subject_code]);
    $count = $stmt->fetchColumn();
    if ($count > 0) {
        return [
            'success' => false,
            'message' => 'Subject Code already exists. Please use a different code.'
        ];
    }
    // Fetch the active school year term
    $termQuery = "SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active'";
    $stmt = $pdo->prepare($termQuery);
    $stmt->execute();
    $activeTerm = $stmt->fetchColumn();
    if (!$activeTerm) {
        return [
            'success' => false,
            'message' => 'No active school year found. Please activate a school year before adding subjects.'
        ];
    }
    // Insert the subject, including nested_id
    $insertQuery = "INSERT INTO subjects_tbl (subject_code, curriculum_id, grade_level, strand, subject_name, subject_desc, weekly_hours, subjectType, subject_term, archive, subject_semester, subject_order, nested_id) 
                    VALUES (:subject_code, :curriculum_id, :grade_level, :strand, :subject_name, :subject_desc, :weekly_hours, :subjectType, :subject_term, :archive, :subject_semester, :subject_order, :nested_id)";
    try {
        // Begin a transaction
        $pdo->beginTransaction();
        $stmt = $pdo->prepare($insertQuery);
        $stmt->execute([
            ':subject_code' => $subject_code,
            ':curriculum_id' => $curriculum,
            ':grade_level' => $grade_level,
            ':strand' => $strand,
            ':subject_name' => $subject_name,
            ':subject_desc' => $subject_desc,
            ':weekly_hours' => $weekly_hours,
            ':subjectType' => $subjectType,
            ':subject_term' => $activeTerm,
            ':archive' => $archive,
            ':subject_semester' => $subjectSemester,
            ':subject_order' => $subjectOrder,
            ':nested_id' => $nested_id
        ]);
        // Commit the transaction
        $pdo->commit();
        return [
            'success' => true,
            'message' => 'Subject successfully added!',
            'insert_id' => $pdo->lastInsertId()
        ];
    } catch (Exception $e) {
        // Rollback the transaction on error
        $pdo->rollBack();
        return [
            'success' => false,
            'message' => 'Failed to add subject. Error: ' . $e->getMessage()
        ];
    }
}
function update_subject($pdo, $data)
{
    try {
        // Determine the value for nested_id based on checkbox state
        $nested_id = (!empty($data['u_checkbox']) && $data['u_checkbox'] == 1) ? "MAPEH-{$data['u_grade_level']}" : null;
        // Debugging: Log received data
        error_log("Received Data: " . print_r($data, true));
        // Prepare the SQL statement
        $stmt = $pdo->prepare("
            UPDATE subjects_tbl 
            SET 
                subject_code = :subject_code, 
                curriculum_id = :curriculum_id,
                subject_name = :subject_name, 
                subject_desc = :subject_desc, 
                grade_level = :grade_level, 
                strand = :strand, 
                weekly_hours = :weekly_hours, 
                subjectType = :subject_type,
                subject_term = :subject_term,
                subject_semester = :subject_semester,
                subject_order = :subject_order,
                nested_id = :nested_id
            WHERE subject_id = :subject_id
        ");
        // Execute the statement
        $stmt->execute([
            ':subject_code' => $data['u_subject_code'],
            ':curriculum_id' => $data['u_curriculum'],
            ':subject_name' => $data['u_subject_name'],
            ':subject_desc' => $data['u_subject_desc'],
            ':grade_level' => $data['u_grade_level'],
            ':strand' => $data['u_strand'],
            ':weekly_hours' => $data['u_weekly_hours'],
            ':subject_type' => $data['u_subject_type'],
            ':subject_term' => $data['u_subject_term'],
            ':subject_id' => $data['u_subject_id'],
            ':subject_semester' => $data['u_subject_quarter'],
            ':subject_order' => $data['u_subject_order'],
            ':nested_id' => $nested_id // Ensure null values are handled correctly
        ]);
        // Debugging: Check for SQL errors
        if ($stmt->errorCode() != '00000') {
            error_log("SQL Error: " . print_r($stmt->errorInfo(), true));
            return ['success' => false, 'message' => 'SQL error occurred.'];
        }
        // Check if rows were affected
        if ($stmt->rowCount() >= 0) {
            return ['success' => true, 'message' => 'Subject updated successfully.'];
        } else {
            return ['success' => false, 'message' => 'No changes made or subject does not exist.'];
        }
    } catch (PDOException $e) {
        error_log("Error updating subject: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}
function fetchSubjects($pdo, $curriculum_id, $sy_from) {
    $query = "SELECT subject_code, grade_level, strand, subject_name, subject_desc, weekly_hours, 
                     subjectType, subject_semester, subject_order, subject_term, subject_id, nested_id
              FROM subjects_tbl 
              WHERE curriculum_id = :curriculum_id AND subject_term = :sy_from AND archive = 0
              ORDER BY CAST(grade_level AS UNSIGNED) ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':curriculum_id' => $curriculum_id,
        ':sy_from' => $sy_from
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function insertCopiedSubjects($pdo, $subjects, $copy_curriculum, $copy_sy_to)
{
    // Query to check if a subject already exists in the target term
    $checkQuery = "SELECT COUNT(*) FROM subjects_tbl 
                   WHERE subject_code = :subject_code 
                   AND subject_term = :subject_term
                   AND curriculum_id = :curriculum_id
                   AND archive = 0";
    $checkStmt = $pdo->prepare($checkQuery);

    // Query to insert a new subject
    $insertQuery = "INSERT INTO subjects_tbl 
                    (subject_code, grade_level, strand, subject_name, subject_desc, weekly_hours, 
                     subjectType, subject_term, subject_semester, subject_order, archive, curriculum_id, nested_id) 
                    VALUES 
                    (:subject_code, :grade_level, :strand, :subject_name, :subject_desc, :weekly_hours, 
                     :subjectType, :subject_term, :subject_semester, :subject_order, 0, :curriculum_id, :nested_id)";
    $insertStmt = $pdo->prepare($insertQuery);

    $insertedCount = 0;
    $skippedCount = 0;

    foreach ($subjects as $subject) {
        // Check if subject already exists in the target term
        $checkStmt->execute([
            ':subject_code' => $subject['subject_code'],
            ':subject_term' => $copy_sy_to,
            ':curriculum_id' => $copy_curriculum
        ]);
        $exists = $checkStmt->fetchColumn();

        if ($exists > 0) {
            $skippedCount++;
            continue;
        }

        // Insert subject directly with whatever nested_id value it already has
        try {
            $insertStmt->execute([
                ':subject_code' => $subject['subject_code'],
                ':grade_level' => $subject['grade_level'],
                ':strand' => $subject['strand'],
                ':subject_name' => $subject['subject_name'],
                ':subject_desc' => $subject['subject_desc'],
                ':weekly_hours' => $subject['weekly_hours'],
                ':subjectType' => $subject['subjectType'],
                ':subject_term' => $copy_sy_to,
                ':subject_semester' => $subject['subject_semester'],
                ':subject_order' => $subject['subject_order'],
                ':curriculum_id' => $copy_curriculum,
                ':nested_id' => !empty($subject['nested_id']) ? $subject['nested_id'] : null
            ]);
            $insertedCount++;
        } catch (PDOException $e) {
            error_log("Insert failed for subject_code " . $subject['subject_code'] . ": " . $e->getMessage());
            $skippedCount++;
        }
    }

    return [
        'inserted' => $insertedCount,
        'skipped' => $skippedCount
    ];
}

function get_subject_data($pdo, $subject_id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM subjects_tbl WHERE subject_id = :subject_id");
        $stmt->bindParam(':subject_id', $subject_id);
        $stmt->execute();
        $subject = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($subject) {
            return ['success' => true, 'data' => $subject];
        } else {
            return ['success' => false, 'message' => 'Subject not found.'];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error fetching subject data.'];
    }
}
function get_school_info($pdo) {
    $query = "SELECT * FROM school_info_tbl LIMIT 1"; // Adjust the query as needed
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function update_school_info($pdo, $data) {
    $query = "UPDATE school_info_tbl SET 
                school_id = :school_id,
                school_name = :school_name,
                region = :region,
                division = :division,
                district = :district,
                bosy_date = :bosy_date,
                eosy_date = :eosy_date,
                school_head = :school_head,
                school_curriculum = :school_curriculum
              WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':school_id', $data['school_id']);
    $stmt->bindParam(':school_name', $data['school_name']);
    $stmt->bindParam(':region', $data['region']);
    $stmt->bindParam(':division', $data['division']);
    $stmt->bindParam(':district', $data['district']);
    $stmt->bindParam(':bosy_date', $data['bosy_date']);
    $stmt->bindParam(':eosy_date', $data['eosy_date']);
    $stmt->bindParam(':school_head', $data['school_head']);
    $stmt->bindParam(':school_curriculum', $data['school_curriculum']);
    $stmt->bindParam(':id', $data['id']); // Assuming 'id' is the primary key
    if ($stmt->execute()) {
        return ['success' => true];
    } else {
        return ['success' => false, 'message' => 'Database update failed.'];
    }
}
?>