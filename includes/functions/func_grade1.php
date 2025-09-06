<?php
function get_section_data($pdo, $sectionId) {
    if (!$sectionId) {
        return [
            "success" => false,
            "message" => "Invalid Section ID."
        ];
    }

    try {

        $stmt = $pdo->prepare("SELECT * FROM section_tbl WHERE SectionId = :sectionId");
        $stmt->bindParam(':sectionId', $sectionId, PDO::PARAM_INT);
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
            FROM student_grade_jhs_tbl s
            WHERE s.section = :sectionId
            AND s.grade_term = :schoolYear
        ";
    
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'sectionId' => $sectionId,
            'schoolYear' => $schoolYear
        ]);
    
        $studentInSectionData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        return ['data' => $studentInSectionData];
    }

function insertGradeRecordf($pdo, string $lrn, string $schoolYear, int $sectionId, int $gradeLevel, string $sectionStrand): bool { 
    try {
        // Determine the target table and columns based on grade level
        if ($gradeLevel >= 7 && $gradeLevel <= 10) {
            $tableName = 'student_grade_jhs_tbl';
            $columns = "(lrn, subject_id, fstq_grade, scndq_grade, trdq_grade, fthq_grade, 
                         fstq_grade_tr, scndq_grade_tr, trdq_grade_tr, fthq_grade_tr, grade_term, section_id";
            $values = "(:lrn, :subject_id, NULL, NULL, NULL, NULL, 
                         NULL, NULL, NULL, NULL, :grade_term, :section_id";
        } else {
            $tableName = 'student_grade_shs_tbl';
            $columns = "(lrn, subject_id, fsts_grade, scnds_grade, 
                         fsts_grade_tr, scnds_grade_tr, grade_term, section_id, grade_semester";
            $values = "(:lrn, :subject_id, NULL, NULL, NULL, NULL, :grade_term, :section_id, :grade_semester";
        }

        // Fetch subjects and check nested_id for each subject_id
        $subjectStmt = $pdo->prepare("
            SELECT subject_id, subject_semester, nested_id 
            FROM subjects_tbl 
            WHERE grade_level = :gradeLevel
            AND subject_term = :schoolYear
        ");
        $subjectStmt->execute(['gradeLevel' => $gradeLevel, 'schoolYear' => $schoolYear]);
        $subjects = $subjectStmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($subjects)) {
            error_log("No subjects found for Grade Level: $gradeLevel.");
            return false; 
        }

        foreach ($subjects as $subject) {
            $subjectId = $subject['subject_id'];
            $subjectSemester = $subject['subject_semester'];
            $existingNestedId = $subject['nested_id'];

            // Only include nested_id if it is NOT null
            $includeNestedId = !empty($existingNestedId);
            $nestedId = $includeNestedId ? $existingNestedId : null;

            // Check if record exists
            $checkStmt = $pdo->prepare("
                SELECT COUNT(*) FROM $tableName 
                WHERE lrn = :lrn AND grade_term = :grade_term AND subject_id = :subject_id
            ");
            $checkStmt->execute([
                'lrn' => $lrn,
                'grade_term' => $schoolYear,
                'subject_id' => $subjectId
            ]);
            $existingRecord = $checkStmt->fetchColumn();

            if ($existingRecord > 0) {
                error_log("Duplicate entry: LRN $lrn already exists for school year $schoolYear and subject_id $subjectId in $tableName.");
                continue;
            }

            // If nested_id is present, include it in the insertion
            $queryColumns = $columns;
            $queryValues = $values;
            if ($includeNestedId) {
                $queryColumns .= ", nested_id";
                $queryValues .= ", :nested_id";
            }
            $queryColumns .= ")";
            $queryValues .= ")";

            // Prepare insert statement
            $stmt = $pdo->prepare("
                INSERT INTO $tableName $queryColumns 
                VALUES $queryValues
            ");
            
            // Bind parameters
            $params = [
                'lrn' => $lrn,
                'subject_id' => $subjectId,
                'grade_term' => $schoolYear,
                'section_id' => $sectionId
            ];

            if ($gradeLevel > 10) {
                $params['grade_semester'] = $subjectSemester;
            } else {
                // Junior High School (JHS) will not have grade_semester
                unset($params['grade_semester']);
            }

            // Only bind nested_id if it is not null
            if ($includeNestedId) {
                $params['nested_id'] = $nestedId;
            }

            $stmt->execute($params);

            error_log("Successfully inserted record for LRN '$lrn' with subject_id '$subjectId', grade term '$schoolYear', and nested_id '".($nestedId ?? 'NULL')."' into $tableName.");
        }

        return true;
    } catch (PDOException $e) {
        error_log('Database error in insertGradeRecord: ' . $e->getMessage());
        return false;
    }
}

function getStudentsBySectionName($pdo, string $sectionName, string $schoolYear): array
{
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
            AND 
                school_year = :schoolYear
        ");
        $stmt->bindParam(':sectionName', $sectionName, PDO::PARAM_STR);
        $stmt->bindParam(':schoolYear', $schoolYear, PDO::PARAM_STR);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $results ?: []; // Return empty array if no results
    } catch (PDOException $e) {
        error_log('Error in getStudentsBySectionName: ' . $e->getMessage());
        return ['error' => 'Database error. Please try again later.'];
    }
}
function insertGradeRecord($pdo, string $lrn, string $schoolYear, int $sectionId, int $gradeLevel, string $sectionStrand): bool
{
    try {
        // Determine the target table and columns based on grade level
        if ($gradeLevel >= 7 && $gradeLevel <= 10) {
            $tableName = 'student_grade_jhs_tbl';
            $columns = "(lrn, subject_id, fstq_grade, scndq_grade, trdq_grade, fthq_grade, 
                         fstq_grade_tr, scndq_grade_tr, trdq_grade_tr, fthq_grade_tr, grade_term, section_id";
            $values = "(:lrn, :subject_id, NULL, NULL, NULL, NULL, 
                         NULL, NULL, NULL, NULL, :grade_term, :section_id";
            // Fetch subjects and check nested_id for each subject_id
            $subjectStmt = $pdo->prepare("
                    SELECT subject_id, subject_semester, nested_id 
                    FROM subjects_tbl 
                    WHERE grade_level = :gradeLevel
                    AND subject_term = :schoolYear
                ");

            $subjectStmt->execute(['gradeLevel' => $gradeLevel, 'schoolYear' => $schoolYear]);
            $subjects = $subjectStmt->fetchAll(PDO::FETCH_ASSOC);

        } else {
            $tableName = 'student_grade_shs_tbl';
            $columns = "(lrn, subject_id, fsts_grade, scnds_grade, 
                         fsts_grade_tr, scnds_grade_tr, grade_term, section_id, grade_semester";
            $values = "(:lrn, :subject_id, NULL, NULL, NULL, NULL, :grade_term, :section_id, :grade_semester";

            // Fetch subjects and check nested_id for each subject_id
            $subjectStmt = $pdo->prepare("
                    SELECT subject_id, subject_semester, nested_id 
                    FROM subjects_tbl 
                    WHERE grade_level = :gradeLevel
                    AND subject_term = :schoolYear
                    AND strand = :sectionStrand
                ");

            $subjectStmt->execute(['gradeLevel' => $gradeLevel, 'schoolYear' => $schoolYear, 'sectionStrand' => $sectionStrand]);
            $subjects = $subjectStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        if (empty($subjects)) {
            error_log("No subjects found for Grade Level: $gradeLevel.");
            return false;
        }

        foreach ($subjects as $subject) {
            $subjectId = $subject['subject_id'];
            $subjectSemester = $subject['subject_semester'];
            $existingNestedId = $subject['nested_id'];

            // Only include nested_id if it is NOT null
            $includeNestedId = !empty($existingNestedId);
            $nestedId = $includeNestedId ? $existingNestedId : null;

            // Check if record exists
            $checkStmt = $pdo->prepare("
                SELECT COUNT(*) FROM $tableName 
                WHERE lrn = :lrn AND grade_term = :grade_term AND subject_id = :subject_id
            ");
            $checkStmt->execute([
                'lrn' => $lrn,
                'grade_term' => $schoolYear,
                'subject_id' => $subjectId
            ]);
            $existingRecord = $checkStmt->fetchColumn();

            if ($existingRecord > 0) {
                error_log("Duplicate entry: LRN $lrn already exists for school year $schoolYear and subject_id $subjectId in $tableName.");
                continue;
            }

            // If nested_id is present, include it in the insertion
            $queryColumns = $columns;
            $queryValues = $values;
            if ($includeNestedId) {
                $queryColumns .= ", nested_id";
                $queryValues .= ", :nested_id";
            }
            $queryColumns .= ")";
            $queryValues .= ")";

            // Prepare insert statement
            $stmt = $pdo->prepare("
                INSERT INTO $tableName $queryColumns 
                VALUES $queryValues
            ");

            // Bind parameters
            $params = [
                'lrn' => $lrn,
                'subject_id' => $subjectId,
                'grade_term' => $schoolYear,
                'section_id' => $sectionId
            ];

            if ($gradeLevel > 10) {
                $params['grade_semester'] = $subjectSemester;
            } else {
                // Junior High School (JHS) will not have grade_semester
                unset($params['grade_semester']);
            }

            // Only bind nested_id if it is not null
            if ($includeNestedId) {
                $params['nested_id'] = $nestedId;
            }

            $stmt->execute($params);

            error_log("Successfully inserted record for LRN '$lrn' with subject_id '$subjectId', grade term '$schoolYear', and nested_id '" . ($nestedId ?? 'NULL') . "' into $tableName.");
        }

        return true;
    } catch (PDOException $e) {
        error_log('Database error in insertGradeRecord: ' . $e->getMessage());
        return false;
    }
}
// Function to get grade details
function get_grade_details($pdo, $gradeId) {
    // Prepare the SQL statement
    $stmt = $pdo->prepare("SELECT * FROM student_grade_jhs_tbl WHERE sgj_id = :gradeId");
    $stmt->bindParam(':gradeId', $gradeId, PDO::PARAM_INT);
    
    // Execute the statement
    $stmt->execute();

    // Fetch the result
    $gradeDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if data was found
    if ($gradeDetails) {
        return [
            'success' => true,
            'data' => $gradeDetails
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Grade details not found.'
        ];
    }
}
function get_grade_details_shs($pdo, $gradeId) {
    // Prepare the SQL statement
    $stmt = $pdo->prepare("SELECT * FROM student_grade_shs_tbl WHERE sg_id = :gradeId");
    $stmt->bindParam(':gradeId', $gradeId, PDO::PARAM_INT);
    
    // Execute the statement
    $stmt->execute();

    // Fetch the result
    $gradeDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if data was found
    if ($gradeDetails) {
        return [
            'success' => true,
            'data' => $gradeDetails
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Grade details not found.'
        ];
    }
}
function update_grade_jhs($pdo, $data)
{
    try {
        // Validate grades before proceeding
        if (
            $data['u_sg_fstq_grade'] > 100 || $data['u_sg_scndq_grade'] > 100 ||
            $data['u_sg_trdq_grade'] > 100 || $data['u_sg_fthq_grade'] > 100
        ) {
            return ['success' => false, 'message' => 'Grades must be in the range (0 - 100).'];
        }

        // Calculate transmuted grades
        $fstq_grade_tr = getTransmutedGrade($data['u_sg_fstq_grade'], $pdo);
        $scndq_grade_tr = getTransmutedGrade($data['u_sg_scndq_grade'], $pdo);
        $trdq_grade_tr = getTransmutedGrade($data['u_sg_trdq_grade'], $pdo);
        $fthq_grade_tr = getTransmutedGrade($data['u_sg_fthq_grade'], $pdo);

        // Prepare the SQL update statement
        $stmt = $pdo->prepare("UPDATE student_grade_jhs_tbl SET 
            fstq_grade = :fstq_grade,
            scndq_grade = :scndq_grade,
            trdq_grade = :trdq_grade,
            fthq_grade = :fthq_grade,
            fstq_grade_tr = :fstq_grade_tr,
            scndq_grade_tr = :scndq_grade_tr,
            trdq_grade_tr = :trdq_grade_tr,
            fthq_grade_tr = :fthq_grade_tr
            WHERE sgj_id = :id");

        // Bind parameters
        $stmt->bindParam(':id', $data['u_sg_id'], PDO::PARAM_INT);
        $stmt->bindParam(':fstq_grade', $data['u_sg_fstq_grade'], PDO::PARAM_STR);
        $stmt->bindParam(':scndq_grade', $data['u_sg_scndq_grade'], PDO::PARAM_STR);
        $stmt->bindParam(':trdq_grade', $data['u_sg_trdq_grade'], PDO::PARAM_STR);
        $stmt->bindParam(':fthq_grade', $data['u_sg_fthq_grade'], PDO::PARAM_STR);
        $stmt->bindParam(':fstq_grade_tr', $fstq_grade_tr, PDO::PARAM_INT);
        $stmt->bindParam(':scndq_grade_tr', $scndq_grade_tr, PDO::PARAM_INT);
        $stmt->bindParam(':trdq_grade_tr', $trdq_grade_tr, PDO::PARAM_INT);
        $stmt->bindParam(':fthq_grade_tr', $fthq_grade_tr, PDO::PARAM_INT);

        // Execute the statement
        $stmt->execute();

        // Check if any rows were updated
        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'Grade updated successfully.'];
        } else {
            return ['success' => false, 'message' => 'No changes made. The record may not exist.'];
        }
    } catch (PDOException $e) {
        error_log('Error updating grade: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Error updating grade. Please try again later.'];
    }
}
// Function to update grades in the SHS table
function update_grade_shs($pdo, $data)
{
    try {
        // Calculate transmuted grades
        $fsts_grade_tr = getTransmutedGradeShs($data['s_sg_fsts_grade'], $pdo);
        $scnds_grade_tr = getTransmutedGradeShs($data['s_sg_scnds_grade'], $pdo);

        // Prepare the SQL update statement
        $stmt = $pdo->prepare("UPDATE student_grade_shs_tbl SET 
            fsts_grade = :fsts_grade,
            scnds_grade = :scnds_grade,
            fsts_grade_tr = :fsts_grade_tr,
            scnds_grade_tr = :scnds_grade_tr
            WHERE sg_id = :id");

        // Bind parameters
        $stmt->bindParam(':id', $data['s_sg_id'], PDO::PARAM_INT);
        $stmt->bindParam(':fsts_grade', $data['s_sg_fsts_grade'], PDO::PARAM_STR);
        $stmt->bindParam(':scnds_grade', $data['s_sg_scnds_grade'], PDO::PARAM_STR);
        $stmt->bindParam(':fsts_grade_tr', $fsts_grade_tr, PDO::PARAM_INT);
        $stmt->bindParam(':scnds_grade_tr', $scnds_grade_tr, PDO::PARAM_INT);

        // Execute the statement
        $stmt->execute();

        // Check if any rows were updated
        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'Grade updated successfully.'];
        } else {
            return ['success' => false, 'message' => 'No changes made. The record may not exist.'];
        }
    } catch (PDOException $e) {
        error_log('Error updating grade: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Error updating grade. Please try again later.'];
    }
}
function getTransmutedGrade($initialGrade, $pdo)
{
    // Check for null or empty input first
    if ($initialGrade === null || $initialGrade === '') {
        return '-'; // Return '-' for invalid input
    }

    // Ensure the input is a number
    if (!is_numeric($initialGrade)) {
        return '-'; // Return '-' for non-numeric input
    }

    // Convert to float for comparison
    $initialGrade = floatval($initialGrade);

    try {
        // Query to get the transmuted grade from the transmutation_tbl
        $stmt = $pdo->prepare("SELECT transmuted_grade 
                               FROM transmutation_tbl 
                               WHERE :initialGrade >= min_grade 
                               ORDER BY min_grade DESC 
                               LIMIT 1");
        $stmt->bindParam(':initialGrade', $initialGrade, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the result and return the transmuted grade
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // If a result is found, return the transmuted grade, else return '-'
        return $result ? $result['transmuted_grade'] : '-';
    } catch (PDOException $e) {
        error_log('Error retrieving transmuted grade: ' . $e->getMessage()); // Log the error
        return '-'; // Return '-' in case of error
    }
}

function getTransmutedGradeShs($initialGradeShs, $pdo)
{
    // Check for null or empty input
    if ($initialGradeShs === null || $initialGradeShs === '') {
        return '-'; // Return '-' for invalid input
    }

    // Ensure the input is numeric
    if (!is_numeric($initialGradeShs)) {
        return '-'; // Return '-' for non-numeric input
    }

    // Convert the input to float for accurate comparisons
    $initialGradeShs = floatval($initialGradeShs);

    try {
        // Query to get the transmuted grade from the transmutation_tbl_shs
        $stmt = $pdo->prepare("SELECT transmuted_grade 
                               FROM transmutation_tbl 
                               WHERE :initialGradeShs >= min_grade 
                               ORDER BY min_grade DESC 
                               LIMIT 1");
        $stmt->bindParam(':initialGradeShs', $initialGradeShs, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the result and return the transmuted grade
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // If a result is found, return the transmuted grade, else return '-'
        return $result ? $result['transmuted_grade'] : '-';
    } catch (PDOException $e) {
        error_log('Error retrieving transmuted grade: ' . $e->getMessage()); // Log the error
        return '-'; // Return '-' in case of error
    }
}

function checkSubjectTaught($pdo, $subject_code, $subject_term) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM subject_taught_tbl WHERE subject_code = :subject_code AND stac_term = :subject_term");
        $stmt->bindParam(':subject_code', $subject_code);
        $stmt->bindParam(':subject_term', $subject_term);
        $stmt->execute();

        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($subjects) {
            return ['success' => true, 'data' => $subjects];
        } else {
            return ['success' => false, 'message' => 'No matching subjects found.'];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}
function getSubjectTaught($pdo, $lrn, $school_year, $grade_level) {
    try {
        $stmt = $pdo->prepare("SELECT subject_code, subject_term FROM subjects_tbl WHERE subject_term = :school_year AND grade_level = :grade_level");
        $stmt->bindParam(':school_year', $school_year);
        $stmt->bindParam(':grade_level', $grade_level);
        $stmt->execute();

        $subject = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($subject) {
            return ['success' => true, 'data' => $subject];
        } else {
            return ['success' => false, 'message' => 'No subject found for the given parameters.'];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}
function update_nested_grade($pdo, $gradeId, $lrn) {
    // Step 1: Fetch the subject_id based on gradeId (which is sgj_id) from student_grade_jhs_tbl
    $query = "SELECT subject_id FROM student_grade_jhs_tbl WHERE sgj_id = :gradeId";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':gradeId', $gradeId);
    $stmt->execute();

    // Check if we found the grade entry
    if ($stmt->rowCount() == 0) {
        return ['success' => false, 'message' => 'Grade ID not found'];
    }
    
    // Get the subject_id from the first result
    $gradeData = $stmt->fetch(PDO::FETCH_ASSOC);
    $subjectId = $gradeData['subject_id'];
    
    // Step 2: Fetch the subject_code from subjects_tbl using subject_id
    $query = "SELECT subject_code FROM subjects_tbl WHERE subject_id = :subjectId";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':subjectId', $subjectId);
    $stmt->execute();

    // Check if the subject was found
    if ($stmt->rowCount() == 0) {
        return ['success' => false, 'message' => 'Subject not found'];
    }
    
    // Get the subject_code
    $subjectData = $stmt->fetch(PDO::FETCH_ASSOC);
    $subjectCode = $subjectData['subject_code'];
    
    // Step 3: Fetch all grades where nested_id = subject_code
    $query = "SELECT fstq_grade_tr, scndq_grade_tr, trdq_grade_tr, fthq_grade_tr 
              FROM student_grade_jhs_tbl 
              WHERE nested_id = :subjectCode AND lrn = :lrn";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':subjectCode', $subjectCode);
    $stmt->bindParam(':lrn', $lrn);
    $stmt->execute();

    // Check if any nested data was found
    if ($stmt->rowCount() == 0) {
        return ['success' => false, 'message' => 'No nested grades found for this subject'];
    }

    // Step 4: Calculate the averages for each quarter grade
    $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $totalFstq = 0;
    $totalScndq = 0;
    $totalTrdq = 0;
    $totalFthq = 0;
    $count = count($grades);

    // Sum up the values for each column
    foreach ($grades as $grade) {
        $totalFstq += $grade['fstq_grade_tr'];
        $totalScndq += $grade['scndq_grade_tr'];
        $totalTrdq += $grade['trdq_grade_tr'];
        $totalFthq += $grade['fthq_grade_tr'];
    }

    // Calculate the average for each quarter and round it to the nearest whole number
    $avgFstq = round($totalFstq / $count);
    $avgScndq = round($totalScndq / $count);
    $avgTrdq = round($totalTrdq / $count);
    $avgFthq = round($totalFthq / $count);

    // Step 5: Update the student's grade data with the calculated averages
    $updateQuery = "UPDATE student_grade_jhs_tbl 
                    SET fstq_grade = :fstqGrade, scndq_grade = :scndqGrade, 
                        trdq_grade = :trdqGrade, fthq_grade = :fthqGrade
                    WHERE sgj_id = :gradeId
                    ";

    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->bindParam(':fstqGrade', $avgFstq);
    $updateStmt->bindParam(':scndqGrade', $avgScndq);
    $updateStmt->bindParam(':trdqGrade', $avgTrdq);
    $updateStmt->bindParam(':fthqGrade', $avgFthq);
    $updateStmt->bindParam(':gradeId', $gradeId);

    if ($updateStmt->execute()) {
        // Return a success message along with averages
        return [
            'success' => true, 
            'averages' => [
                'fstq' => $avgFstq, 
                'scndq' => $avgScndq, 
                'trdq' => $avgTrdq, 
                'fthq' => $avgFthq
            ]
        ];
    } else {
        return ['success' => false, 'message' => 'Failed to update grades in the database'];
    }
}


function update_grade_control($pdo, $data)
{
    // Ensure c_desc and c_status are set
    if (isset($data['c_desc']) && isset($data['c_status'])) {
        $query = "UPDATE control_tbl SET c_status = :c_status WHERE c_desc = :c_desc";

        try {
            // Prepare the SQL query
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':c_status', $data['c_status'], PDO::PARAM_INT);
            $stmt->bindParam(':c_desc', $data['c_desc'], PDO::PARAM_INT); // c_desc should be an integer

            // Execute the query
            $stmt->execute();

            // Check if any rows were updated
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Grade control updated successfully.'];
            } else {
                return ['success' => false, 'message' => 'No matching record found to update.'];
            }
        } catch (PDOException $e) {
            // Handle any database errors
            return ['success' => false, 'message' => 'Error updating grade control: ' . $e->getMessage()];
        }
    } else {
        return ['success' => false, 'message' => 'Missing required fields (c_desc, c_status).'];
    }
}

function update_transmutation_data($pdo, $transmutationId, $minGrade, $transmutedGrade)
{
    try {
        // Validate input
        if ($minGrade > 100 || $transmutedGrade > 100) {
            return ['success' => false, 'message' => 'Grades must not be greater than 100.'];
        }

        $stmt = $pdo->prepare("UPDATE transmutation_tbl 
                               SET min_grade = :minGrade, transmuted_grade = :transmutedGrade 
                               WHERE transmutation_id = :transmutationId");

        // Bind parameters
        $stmt->bindParam(':transmutationId', $transmutationId, PDO::PARAM_INT);
        $stmt->bindParam(':minGrade', $minGrade, PDO::PARAM_INT);
        $stmt->bindParam(':transmutedGrade', $transmutedGrade, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'Transmutation data updated successfully.'];
        } else {
            return ['success' => true, 'message' => 'Transmutation data updated successfully.(No changes made)'];
        }
    } catch (PDOException $e) {
        error_log('Database Error: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Error updating transmutation data.'];
    }
}
