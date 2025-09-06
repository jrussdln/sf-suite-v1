<?php

function student_list($pdo, $schoolYear, $gradeLevel, $studentSex, $studentSection, $studentRemarks)
{
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
    if (!empty($studentRemarks)) {
        $query .= " AND SUBSTRING_INDEX(remarks, ' DATE:', 1) = :studentRemarks";
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
    if (!empty($studentRemarks)) {
        $stmt->bindValue(':studentRemarks', $studentRemarks, PDO::PARAM_STR);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function promotion_list($pdo, $schoolYear, $gradeLevel, $studentSex, $studentSection, $studentRemarks)
{
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
    if (!empty($studentRemarks)) {
        $query .= " AND SUBSTRING_INDEX(remarks, ' DATE:', 1) = :studentRemarks";
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
    if (!empty($studentRemarks)) {
        $stmt->bindValue(':studentRemarks', $studentRemarks, PDO::PARAM_STR);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to retrieve student data
function student_data($pdo, $id) {
    // Prepare the SQL statement to prevent SQL injection
    $query = "SELECT * FROM student_tbl WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id]);

    // Fetch the student data
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function get_grade($pdo, $id) {
    // SQL query to fetch grade data for the student by ID
    $sql = "SELECT *
            FROM prom_achievement_tbl
            WHERE pa_id = :id";
    
    // Prepare the SQL statement
    $stmt = $pdo->prepare($sql);

    // Bind the student ID parameter to the prepared statement
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Execute the query
    if ($stmt->execute()) {
        // Fetch the grade data as an associative array
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if any grade data was found
        if ($result) {
            return $result; // Return the grade data
        } else {
            return ['success' => false, 'message' => 'No grade data found for the given student ID.'];
        }
    } else {
        // If query fails, return an error message
        return ['success' => false, 'message' => 'Error executing query to fetch grade data.'];
    }
}
function get_general_average($pdo, $lrn, $school_year)
{
    // Step 1: Retrieve the student's grade level
    $stmtGrade = $pdo->prepare("
        SELECT grade_level FROM student_tbl 
        WHERE lrn = :lrn AND school_year = :school_year
    ");
    $stmtGrade->execute(['lrn' => $lrn, 'school_year' => $school_year]);
    $gradeData = $stmtGrade->fetch(PDO::FETCH_ASSOC);
    $gradeLevel = $gradeData['grade_level'] ?? null;

    // If grade level is not found, return an error
    if (!$gradeLevel) {
        return ['error' => 'Grade level not found.'];
    }

    // Step 2: Determine the SQL query based on the grade level (JHS or SHS)
    if ($gradeLevel >= 7 && $gradeLevel <= 10) {
        // JHS table query (grades from 4 quarters)
        $sql = "SELECT subject_code, subject_name, fstq_grade_tr, scndq_grade_tr, trdq_grade_tr, fthq_grade_tr
                FROM student_grade_jhs_tbl
                JOIN subjects_tbl s ON s.subject_id = student_grade_jhs_tbl.subject_id
                WHERE lrn = :lrn AND grade_term = :school_year AND student_grade_jhs_tbl.nested_id is null";
    } else {
        // SHS table query (grades from 2 semesters)
        $sql = "SELECT subject_code, subject_name, fsts_grade_tr, scnds_grade_tr
                FROM student_grade_shs_tbl
                JOIN subjects_tbl s ON s.subject_id = student_grade_shs_tbl.subject_id
                WHERE lrn = :lrn AND grade_term = :school_year";
    }

    // Step 3: Execute the query
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':lrn' => $lrn, ':school_year' => $school_year]);
    $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$grades) {
        return ['error' => 'No grades found for this student.'];
    }

    // Step 4: Initialize variables
    $totalSum = 0;
    $subjectCount = 0;
    $subjects = [];

    // Step 5: Process the grades
    foreach ($grades as $grade) {
        if ($gradeLevel >= 7 && $gradeLevel <= 10) {
            // JHS - calculate average for each subject over 4 quarters
            $gradeValues = [
                $grade['fstq_grade_tr'],
                $grade['scndq_grade_tr'],
                $grade['trdq_grade_tr'],
                $grade['fthq_grade_tr']
            ];
        } else {
            // SHS - calculate average for each subject over 2 semesters
            $gradeValues = [$grade['fsts_grade_tr'], $grade['scnds_grade_tr']];
        }

        // Check if any grade is NULL
        if (in_array(null, $gradeValues, true)) {
            continue; // Skip subjects with incomplete grades
        }

        // Calculate subject average
        $sum = array_sum(array_map('floatval', $gradeValues));
        $avg = $sum / count($gradeValues);

        // Round the average to the nearest integer
        $avg = round($avg);

        // Add to the total sum and increment the subject count
        $totalSum += $avg;
        $subjectCount++;

        // Store subject details
        $subjects[] = [
            'subject_code' => $grade['subject_code'],
            'subject_name' => $grade['subject_name'],
            'average' => $avg // No need to use number_format here since it's already rounded
        ];

    }
    $generalAverage = ($subjectCount > 0) ? round($totalSum / $subjectCount) : 0;

    // Step 7: Return structured data
    return [
        'general_average' => $generalAverage,
        'subject_details' => $subjects
    ];
}


function add_student($pdo, $data) {
    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Insert into student_tbl
        $query = "INSERT INTO student_tbl (
            lrn, name, section, grade_level, school_year, birth_date, sex, 
            mother_tongue, ethnic_group, religion, hssp, barangay, municipality_city, 
            province, father_name, mother_maiden_name, guardian_name, guardian_relationship, 
            contact_number, learning_modality, remarks
        ) VALUES (
            :lrn, :name, :section, :grade_level, :school_year, :birth_date, :sex, 
            :mother_tongue, :ethnic_group, :religion, :hssp, :barangay, :municipality_city, 
            :province, :father_name, :mother_maiden_name, :guardian_name, :guardian_relationship, 
            :contact_number, :learning_modality, :remarks
        )";

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':lrn' => $data['as_lrn'],
            ':name' => $data['as_name'],
            ':section' => $data['as_section'],
            ':grade_level' => $data['as_grade_level'],
            ':school_year' => $data['as_school_year'],
            ':birth_date' => $data['as_birth_date'],
            ':sex' => $data['as_sex'],
            ':mother_tongue' => $data['as_mother_tongue'],
            ':ethnic_group' => $data['as_ethnic_group'],
            ':religion' => $data['as_religion'],
            ':hssp' => $data['as_hssp'],
            ':barangay' => $data['as_barangay'],
            ':municipality_city' => $data['as_municipality_city'],
            ':province' => $data['as_province'],
            ':father_name' => $data['as_father_name'],
            ':mother_maiden_name' => $data['as_mother_maiden_name'],
            ':guardian_name' => $data['as_guardian_name'],
            ':guardian_relationship' => $data['as_guardian_relationship'],
            ':contact_number' => $data['as_contact_number'],
            ':learning_modality' => $data['as_learning_modality'],
            ':remarks' => $data['as_remarks']
        ]);

        // Insert into prom_achievement_tbl
        $achievementQuery = "INSERT INTO prom_achievement_tbl (
            lrn, name, grade_level, section, school_year, sex, general_average, action_taken, cecs, ecs
        ) VALUES (
            :lrn, :name, :grade_level, :section, :school_year, :sex, NULL, NULL, NULL, NULL
        )";

        $achievementStmt = $pdo->prepare($achievementQuery);
        $achievementStmt->execute([
            ':lrn' => $data['as_lrn'],
            ':name' => $data['as_name'],
            ':grade_level' => $data['as_grade_level'],
            ':section' => $data['as_section'],
            ':school_year' => $data['as_school_year'],
            ':sex' => $data['as_sex']
        ]);

        // Commit the transaction
        $pdo->commit();

        return ['status' => 'success', 'message' => 'Student added successfully and promoted.'];

    } catch (PDOException $e) {
        // Rollback in case of an error
        $pdo->rollBack();
        return ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()];
    }
}
function smart_promote($pdo)
{
    try {
        // Step 1: Get the active school year
        $syStmt = $pdo->prepare("SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active' LIMIT 1");
        $syStmt->execute();
        $activeSY = $syStmt->fetchColumn();

        if (!$activeSY) {
            return ['success' => false, 'message' => "No active school year found."];
        }

        // Step 2: Get students from prom_achievement_tbl
        $stmt = $pdo->prepare("SELECT pa_id, lrn, school_year FROM prom_achievement_tbl WHERE school_year = :school_year");
        $stmt->execute([':school_year' => $activeSY]);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $updated = 0;

        foreach ($students as $student) {
            $pa_id = $student['pa_id'];
            $lrn = $student['lrn'];
            $sy = $student['school_year'];

            $avgResult = get_general_average($pdo, $lrn, $sy);

            // Skip if average is not available or is 0
            if (!isset($avgResult['general_average']) || $avgResult['general_average'] == 0) {
                continue;
            }

            $genAvg = $avgResult['general_average'];
            $action = ($genAvg >= 75) ? 'Promoted' : 'Retained';

            // Step 3: Update only for matching pa_id and school_year
            $update = $pdo->prepare("
                UPDATE prom_achievement_tbl 
                SET general_average = :avg, action_taken = :action 
                WHERE pa_id = :pa_id AND school_year = :school_year
            ");
            $update->execute([
                ':avg' => $genAvg,
                ':action' => $action,
                ':pa_id' => $pa_id,
                ':school_year' => $activeSY
            ]);
            $updated++;
        }

        return ['success' => true, 'message' => "Smart Promote completed. Updated $updated students."];
    } catch (Exception $e) {
        return ['success' => false, 'message' => "Error: " . $e->getMessage()];
    }
}
