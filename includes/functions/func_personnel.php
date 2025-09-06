<?php

function personnel_list($pdo, $personnelSex, $empStatus, $plaPos)
{
    // Base query with join
    $query = "
        SELECT s.*, p.pp_desc,
               (SELECT COUNT(*) FROM subject_taught_tbl st
                WHERE st.PersonnelId = s.PersonnelId
                AND st.stac_term = :activeSchoolYear) AS subject_count
        FROM school_per_tbl s
        LEFT JOIN plantilla_pos_tbl p ON s.PersonnelId = p.PersonnelId
        WHERE 1=1
    ";

    // Add condition for Sex if provided
    if (!empty($personnelSex)) {
        $query .= " AND s.Sex = :sex";
    }

    // Add condition for Employment Status if provided
    if (!empty($empStatus)) {
        $query .= " AND s.EmploymentStatus = :empStatus";
    }

    // Add condition for Position Description if provided
    if (!empty($plaPos)) {
        $query .= " AND p.pp_desc = :plaPos";
    }

    // Prepare the query
    $stmt = $pdo->prepare($query);

    // Bind the parameters
    if (!empty($personnelSex)) {
        $stmt->bindValue(':sex', $personnelSex, PDO::PARAM_STR);
    }
    if (!empty($empStatus)) {
        $stmt->bindValue(':empStatus', $empStatus, PDO::PARAM_STR);
    }
    if (!empty($plaPos)) {
        $stmt->bindValue(':plaPos', $plaPos, PDO::PARAM_STR);
    }

    // Bind the active school year (you may retrieve it from a config or variable)
    $activeSchoolYear = '2024-2025'; // Example active school year, modify as needed
    $stmt->bindValue(':activeSchoolYear', $activeSchoolYear, PDO::PARAM_STR);

    // Execute the query
    $stmt->execute();

    // Return the result set
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function add_personnel($pdo, $data) {
    try {
        // Extract the data array into variables
        extract($data);

        // Check if the EmpNo already exists in the database
        $checkQuery = "SELECT COUNT(*) FROM school_per_tbl WHERE EmpNo = ?";
        $stmt = $pdo->prepare($checkQuery);
        $stmt->execute([$a_empno]);
        $exists = $stmt->fetchColumn();

        if ($exists > 0) {
            return ['status' => 'error', 'message' => 'Duplicate entry: Employee Number already exists.'];
        }

        // SQL query to insert a new personnel record
        $query = "INSERT INTO school_per_tbl 
                  (EmpNo, email, EmpLName, EmpFName, EmpMName, EmpEName, Sex, FundSource, BirthDate, EmploymentStatus, EducDegree, EducMajor, EducMinor, PostGraduate, Specialization) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Begin the transaction
        $pdo->beginTransaction();

        // Prepare and execute the SQL statement
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            $a_empno,
            $a_email,
            $a_lname, 
            $a_fname, 
            $a_mname, 
            $a_ename, 
            $a_sex, 
            $a_fund_source, 
            $a_birthdate, 
            $a_employment_status, 
            $a_degree, 
            $a_major, 
            $a_minor,
            $a_post_graduate,
            $a_specialization
        ]);

        // Commit the transaction
        $pdo->commit();

        return ['status' => 'success', 'message' => 'Personnel added successfully.'];
    } catch (Exception $e) {
        // Rollback the transaction on error
        $pdo->rollBack();
        return ['status' => 'error', 'message' => 'Failed to add personnel: ' . $e->getMessage()];
    }
}
function edit_personnel($pdo, $data) {
    try {
        // Start transaction
        $pdo->beginTransaction();

        // Update school_per_tbl
        $sql1 = "UPDATE school_per_tbl SET
                    EmpNo = :EmpNo,
                    EmpLName = :EmpLName,
                    EmpFName = :EmpFName,
                    EmpMName = :EmpMName,
                    EmpEName = :EmpEName,
                    Sex = :Sex,
                    FundSource = :FundSource,
                    BirthDate = :BirthDate,
                    EmploymentStatus = :EmploymentStatus,
                    PostGraduate = :PostGraduate,
                    Specialization = :Specialization,
                    EducDegree = :EducDegree,
                    EducMajor = :EducMajor,
                    EducMinor = :EducMinor,
                    email = :email,
                    updated_at = NOW()
                WHERE PersonnelId = :PersonnelId";

        $stmt1 = $pdo->prepare($sql1);
        $stmt1->execute([
            ':EmpNo' => $data['e_EmpNo'],
            ':EmpLName' => $data['e_lname'],
            ':EmpFName' => $data['e_fname'],
            ':EmpMName' => $data['e_mname'],
            ':EmpEName' => $data['e_ename'],
            ':Sex' => $data['e_sex'],
            ':FundSource' => $data['e_fund_source'],
            ':BirthDate' => $data['e_birthdate'],
            ':EmploymentStatus' => $data['e_employment_status'],
            ':PostGraduate' => $data['e_post_graduate'],
            ':Specialization' => $data['e_specialization'],
            ':EducDegree' => $data['e_degree'],
            ':EducMajor' => $data['e_major'],
            ':EducMinor' => isset($data['e_minor']) ? $data['e_minor'] : null, // Fix applied
            ':email' => $data['e_email'],
            ':PersonnelId' => $data['PersonnelId']
        ]);

        // Update user_tbl
        $sql2 = "UPDATE user_tbl SET
                    UserFName = :UserFName,
                    UserLName = :UserLName,
                    UserMName = :UserMName,
                    UserEName = :UserEName,
                    Gender = :Gender,
                    BirthDate = :BirthDate,
                    Email = :Email,
                    updated_at = NOW()
                WHERE Identifier = :EmpNo";

        $stmt2 = $pdo->prepare($sql2);
        $stmt2->execute([
            ':UserFName' => $data['e_fname'],
            ':UserLName' => $data['e_lname'],
            ':UserMName' => $data['e_mname'],
            ':UserEName' => $data['e_ename'],
            ':Gender' => $data['e_sex'],
            ':BirthDate' => $data['e_birthdate'],
            ':Email' => $data['e_email'],
            ':EmpNo' => $data['e_EmpNo']
        ]);

        // Commit transaction if everything is successful
        $pdo->commit();

        return ['success' => true, 'message' => 'Personnel details updated successfully, including user account.'];
    } catch (PDOException $e) {
        // Rollback if an error occurs
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}


function get_personnel_data($pdo, $PersonnelId) {
    try {
        $sql = "SELECT * FROM school_per_tbl WHERE PersonnelId = :PersonnelId"; // Adjust the table name if necessary
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':PersonnelId', $PersonnelId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the user data as an associative array
    } catch (PDOException $e) {
        return false; // Handle error appropriately
    }
}function get_anc_assignment_data($pdo, $PersonnelId) {
    try {
        // Get the active school year term
        $sql = "SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active' LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $activeSchoolYear = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$activeSchoolYear) {
            return false; // No active school year found
        }

        $sy_term = $activeSchoolYear['sy_term'];

        // Get assignments where anc_ass_term matches active sy_term
        $sql = "SELECT * FROM anc_ass_tbl WHERE PersonnelId = :PersonnelId AND anc_ass_term = :sy_term";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':PersonnelId', $PersonnelId);
        $stmt->bindParam(':sy_term', $sy_term);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all records as an associative array
    } catch (PDOException $e) {
        return false; // Handle error appropriately
    }
}

function get_subject_taught_data($pdo, $PersonnelId) {
    try {
        // Step 1: Check for the active school year and term
        $sql = "SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        // Check if an active school year is found
        if ($stmt->rowCount() > 0) {
            $activeSchoolYear = $stmt->fetch(PDO::FETCH_ASSOC);
            $sy_term = $activeSchoolYear['sy_term'];

            // Step 2: Fetch subjects taught that match the active term
            $sql = "SELECT * FROM subject_taught_tbl WHERE PersonnelId = :PersonnelId AND stac_term = :sy_term";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':PersonnelId', $PersonnelId, PDO::PARAM_STR);
            $stmt->bindParam(':sy_term', $sy_term, PDO::PARAM_STR);
            $stmt->execute();

            // Check if data is found
            if ($stmt->rowCount() > 0) {
                // Fetch all data for the given PersonnelId and term
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Return success response with data
                return [
                    'success' => true,
                    'data' => $data
                ];
            } else {
                // Return an error if no data is found for the active term
                return [
                    'success' => false,
                    'message' => "No records found for the active term"
                ];
            }
        } else {
            // Return an error if no active school year is found
            return [
                'success' => false,
                'message' => "No active school year found"
            ];
        }
    } catch (PDOException $e) {
        // Handle any database-related errors
        return [
            'success' => false,
            'message' => "Database error: " . $e->getMessage()
        ];
    }
}

function get_subt_details($pdo, $stac_id) {
    // Prepare the SQL query to fetch the subject details
    $sql = "SELECT * FROM subject_taught_tbl WHERE stac_id = :stac_id";
    
    // Prepare the statement
    $stmt = $pdo->prepare($sql);
    
    // Bind the stac_id parameter to the query
    $stmt->bindParam(':stac_id', $stac_id, PDO::PARAM_INT);
    
    // Execute the query
    $stmt->execute();
    
    // Fetch the result
    $subject_details = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if subject details were found
    if ($subject_details) {
        // Return the subject details
        return ['data' => $subject_details];
    } else {
        // Return a message if no data was found
        return ['error' => 'No subject details found for this stac_id'];
    }
}
function add_subject_taughte($pdo, $data) {
    try {
        // Step 1: Get the active school year term
        $stmt = $pdo->prepare("SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active'");
        $stmt->execute();

        // Check if an active school year is found
        if ($stmt->rowCount() > 0) {
            $activeSchoolYear = $stmt->fetch(PDO::FETCH_ASSOC);
            $sy_term = $activeSchoolYear['sy_term'];

            // Step 2: Prepare the SQL statement to insert the subject taught
            $stmt = $pdo->prepare("INSERT INTO subject_taught_tbl (PersonnelId, subject_taught, subject_code, section, st_day, st_from, st_to, tat_min, stac_term) VALUES (:PersonnelId, :subject_taught, :subject_code, :section, :st_day, :st_from, :st_to, :tat_min, :stac_term)");

            // Bind parameters
            $stmt->bindParam(':PersonnelId', $data['a_PersonnelId']);
            $stmt->bindParam(':subject_taught', $data['a_subjectTaught']);
            $stmt->bindParam(':subject_code', $data['a_subject_code']);
            $stmt->bindParam(':section', $data['a_section']);
            $stmt->bindParam(':st_day', $data['a_stDay']);
            $stmt->bindParam(':st_from', $data['a_stFrom']);
            $stmt->bindParam(':st_to', $data['a_stTo']);
            $stmt->bindParam(':tat_min', $data['a_tatMin']);
            $stmt->bindParam(':stac_term', $sy_term); // Bind the sy_term to stac_term

            // Execute the statement
            if ($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Subject taught added successfully.'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to add subject taught. Please try again.'
                ];
            }
        } else {
            return [
                'status' => 'error',
                'message' => 'No active school year found. Cannot add subject taught.'
            ];
        }
    } catch (PDOException $e) {
        // Handle any database-related errors
        return [
            'status' => 'error',
            'message' => "Database error: " . $e->getMessage()
        ];
    }
}
function add_subject_taught($pdo, $data)
{
    try {
        // Step 1: Get the active school year term
        $stmt = $pdo->prepare("SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active'");
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $activeSchoolYear = $stmt->fetch(PDO::FETCH_ASSOC);
            $sy_term = $activeSchoolYear['sy_term'];

            // Step 2: Parse the input days
            $inputDays = array_map('trim', explode(',', $data['a_stDay']));
            $inputStart = strtotime($data['a_stFrom']);
            $inputEnd = strtotime($data['a_stTo']);

            // Step 3: Get all subjects of the same PersonnelId and term
            $stmt = $pdo->prepare("SELECT st_day, st_from, st_to FROM subject_taught_tbl 
                                   WHERE stac_term = :sy_term AND PersonnelId = :PersonnelId");
            $stmt->execute([
                ':sy_term' => $sy_term,
                ':PersonnelId' => $data['a_PersonnelId']
            ]);

            $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Step 4: Loop through each existing subject to check for conflicts
            foreach ($subjects as $subject) {
                $existingDays = array_map('trim', explode(',', $subject['st_day']));
                $existingStart = strtotime($subject['st_from']);
                $existingEnd = strtotime($subject['st_to']);

                // Check if any day overlaps
                $dayOverlap = array_intersect($inputDays, $existingDays);
                if (!empty($dayOverlap)) {
                    // If days overlap, check if time overlaps
                    if (
                        ($inputStart < $existingEnd && $inputEnd > $existingStart) // time overlaps
                    ) {
                        return [
                            'status' => 'error',
                            'message' => 'Schedule conflict detected with existing subject.'
                        ];
                    }
                }
            }

            // Step 5: Insert the subject if no conflicts
            $stmt = $pdo->prepare("INSERT INTO subject_taught_tbl 
                (PersonnelId, subject_taught, subject_code, section, st_day, st_from, st_to, tat_min, stac_term) 
                VALUES (:PersonnelId, :subject_taught, :subject_code, :section, :st_day, :st_from, :st_to, :tat_min, :stac_term)");

            $stmt->bindParam(':PersonnelId', $data['a_PersonnelId']);
            $stmt->bindParam(':subject_taught', $data['a_subjectTaught']);
            $stmt->bindParam(':subject_code', $data['a_subject_code']);
            $stmt->bindParam(':section', $data['a_section']);
            $stmt->bindParam(':st_day', $data['a_stDay']);
            $stmt->bindParam(':st_from', $data['a_stFrom']);
            $stmt->bindParam(':st_to', $data['a_stTo']);
            $stmt->bindParam(':tat_min', $data['a_tatMin']);
            $stmt->bindParam(':stac_term', $sy_term);

            if ($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Subject taught added successfully.'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to add subject taught. Please try again.'
                ];
            }

        } else {
            return [
                'status' => 'error',
                'message' => 'No active school year found. Cannot add subject taught.'
            ];
        }

    } catch (PDOException $e) {
        return [
            'status' => 'error',
            'message' => "Database error: " . $e->getMessage()
        ];
    }
}
function update_subject_taught($pdo, $data)
{
    try {
        // Step 1: Get the active school year term
        $stmt = $pdo->prepare("SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active'");
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $activeSchoolYear = $stmt->fetch(PDO::FETCH_ASSOC);
            $sy_term = $activeSchoolYear['sy_term'];

            // Step 2: Parse the input days and time
            $inputDays = array_map('trim', explode(',', $data['u_stDay']));
            $inputStart = strtotime($data['u_stFrom']);
            $inputEnd = strtotime($data['u_stTo']);

            // Step 3: Get all other subjects of the same PersonnelId and term (exclude current record)
            $stmt = $pdo->prepare("SELECT st_day, st_from, st_to FROM subject_taught_tbl 
                                   WHERE stac_term = :sy_term 
                                   AND PersonnelId = :PersonnelId 
                                   AND stac_id != :stac_id");
            $stmt->execute([
                ':sy_term' => $sy_term,
                ':PersonnelId' => $data['u_PersonnelId'],
                ':stac_id' => $data['u_stac_id']
            ]);

            $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Step 4: Loop through existing subjects to check for conflicts
            foreach ($subjects as $subject) {
                $existingDays = array_map('trim', explode(',', $subject['st_day']));
                $existingStart = strtotime($subject['st_from']);
                $existingEnd = strtotime($subject['st_to']);

                $dayOverlap = array_intersect($inputDays, $existingDays);
                if (!empty($dayOverlap)) {
                    if (
                        ($inputStart < $existingEnd && $inputEnd > $existingStart)
                    ) {
                        return [
                            'status' => 'error',
                            'message' => 'Schedule conflict detected with another subject.'
                        ];
                    }
                }
            }

            // Step 5: Proceed with the update
            $stmt = $pdo->prepare("UPDATE subject_taught_tbl SET 
                subject_taught = :subject_taught, 
                subject_code = :subject_code,
                section = :section,
                st_day = :st_day, 
                st_from = :st_from, 
                st_to = :st_to, 
                tat_min = :tat_min 
                WHERE stac_id = :stac_id AND PersonnelId = :PersonnelId");

            $stmt->bindParam(':subject_taught', $data['u_subjectTaught']);
            $stmt->bindParam(':subject_code', $data['u_subject_code']);
            $stmt->bindParam(':section', $data['u_section']);
            $stmt->bindParam(':st_day', $data['u_stDay']);
            $stmt->bindParam(':st_from', $data['u_stFrom']);
            $stmt->bindParam(':st_to', $data['u_stTo']);
            $stmt->bindParam(':tat_min', $data['u_tatMin']);
            $stmt->bindParam(':stac_id', $data['u_stac_id']);
            $stmt->bindParam(':PersonnelId', $data['u_PersonnelId']);

            if ($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Subject taught updated successfully.'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to update subject taught. Please try again.'
                ];
            }

        } else {
            return [
                'status' => 'error',
                'message' => 'No active school year found. Cannot update subject.'
            ];
        }

    } catch (PDOException $e) {
        return [
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}


// Function to update a subject taught
function update_subject_taugwht($pdo, $data) {
    // Prepare the SQL statement
    $stmt = $pdo->prepare("UPDATE subject_taught_tbl SET 
        subject_taught = :subject_taught, 
        subject_code = :subject_code,
        section = :section,
        st_day = :st_day, 
        st_from = :st_from, 
        st_to = :st_to, 
        tat_min = :tat_min 
        WHERE stac_id = :stac_id AND PersonnelId = :PersonnelId");

    // Bind parameters
    $stmt->bindParam(':subject_taught', $data['u_subjectTaught']);
    $stmt->bindParam(':subject_code', $data['u_subject_code']);
    $stmt->bindParam(':section', $data['u_section']);
    $stmt->bindParam(':st_day', $data['u_stDay']);
    $stmt->bindParam(':st_from', $data['u_stFrom']);
    $stmt->bindParam(':st_to', $data['u_stTo']);
    $stmt->bindParam(':tat_min', $data['u_tatMin']);
    $stmt->bindParam(':stac_id', $data['u_stac_id']); // Assuming this is the ID of the record to update
    $stmt->bindParam(':PersonnelId', $data['u_PersonnelId']); // Assuming this is the PersonnelId

    // Execute the statement
    if ($stmt->execute()) {
        return [
            'status' => 'success',
            'message' => 'Subject taught updated successfully.'
        ];
    } else {
        return [
            'status' => 'error',
            'message' => 'Failed to update subject taught. Please try again.'
        ];
    }
}

function add_anc_assignment($pdo, $data) {
    try {
        // Step 1: Get the active term from the school_year_tbl
        $stmt = $pdo->prepare("SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active'");
        $stmt->execute();

        // Check if an active school year is found
        if ($stmt->rowCount() > 0) {
            $activeSchoolYear = $stmt->fetch(PDO::FETCH_ASSOC);
            $sy_term = $activeSchoolYear['sy_term'];

            // Step 2: Prepare the SQL statement to insert the ancillary assignment
            $stmt = $pdo->prepare("INSERT INTO anc_ass_tbl (anc_ass_desc, anc_ass_term, PersonnelId, created_at, updated_at) VALUES (:anc_ass_desc, :anc_ass_term, :PersonnelId, NOW(), NOW())");

            // Bind parameters
            $stmt->bindParam(':anc_ass_desc', $data['a_ass_desc']);
            $stmt->bindParam(':anc_ass_term', $sy_term); // Use the active term
            $stmt->bindParam(':PersonnelId', $data['aa_PersonnelId']);

            // Execute the statement
            if ($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Ancillary assignment added successfully.'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to add ancillary assignment. Please try again.'
                ];
            }
        } else {
            return [
                'status' => 'error',
                'message' => 'No active school year found. Cannot add ancillary assignment.'
            ];
        }
    } catch (PDOException $e) {
        // Handle any database-related errors
        return [
            'status' => 'error',
            'message' => "Database error: " . $e->getMessage()
        ];
    }
}

function get_anc_ass_list_details($pdo) {
    try {
        // Prepare the SQL statement to fetch all records
        $stmt = $pdo->prepare("SELECT * FROM anc_ass_list_tbl");
        $stmt->execute();

        // Fetch all records
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'success' => true,
            'data' => $data
        ];
    } catch (PDOException $e) {
        // Handle any database-related errors
        return [
            'success' => false,
            'message' => "Database error: " . $e->getMessage()
        ];
    }
}

function add_anc_ass_list($pdo, $data) {
    try {
        // Prepare the SQL statement to insert a new ancillary assignment
        $stmt = $pdo->prepare("INSERT INTO anc_ass_list_tbl (anc_ass_list, created_at) VALUES (:anc_ass_list, NOW())");

        // Bind parameters
        $stmt->bindParam(':anc_ass_list', $data['anc_ass_list']);
        
        // Execute the statement
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Ancillary assignment added successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to add ancillary assignment. Please try again.'
            ];
        }
    } catch (PDOException $e) {
        // Handle any database-related errors
        return [
            'success' => false,
            'message' => "Database error: " . $e->getMessage()
        ];
    }
}

function delete_anc_ass_list($pdo, $id) {
    try {
        // Prepare the SQL statement to delete the record
        $stmt = $pdo->prepare("DELETE FROM anc_ass_list_tbl WHERE anc_ass_list_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Ancillary assignment deleted successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to delete ancillary assignment. Please try again.'
            ];
        }
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => "Database error: " . $e->getMessage()
        ];
    }
}
function delete_anc_ass($pdo, $id) {
    try {
        // Prepare the SQL statement to delete the record
        $stmt = $pdo->prepare("DELETE FROM anc_ass_tbl WHERE anc_ass_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Ancillary assignment deleted successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to delete ancillary assignment. Please try again.'
            ];
        }
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => "Database error: " . $e->getMessage()
        ];
    }
}
function get_assignment_list($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT anc_ass_list_id, anc_ass_list FROM anc_ass_list_tbl");
        $stmt->execute();
        $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $assignments; // Return the fetched assignments
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}
function get_subject_code_list($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT subject_code FROM subjects_tbl");
        $stmt->execute();
        $sc = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $sc; // Return the fetched subject codes
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}
function get_sy_list($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT sy_term FROM school_year_tbl");
        $stmt->execute();
        $sc = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $sc; // Return the fetched subject codes
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}
function get_subject2($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT DISTINCT subject_name FROM subjects_tbl");
        $stmt->execute();
        $sc = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $sc; // Return the fetched subject codes
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}
function get_ppl_list($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM plantilla_pos_list_tbl");
        $stmt->execute();
        $ppl = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $ppl; // Return the fetched assignments
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}
function get_section_list($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM section_tbl");
        $stmt->execute();
        $ppl = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $ppl; // Return the fetched assignments
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}
function get_sections_list_filtered($pdo, $PersonnelId) {
    try {
        // Get the active school year and term
        $stmt = $pdo->prepare("SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active'");
        $stmt->execute();
        $activeSchoolYear = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$activeSchoolYear) {
            return ['error' => 'No active school year found.'];
        }

        $sy_term = $activeSchoolYear['sy_term'];

        // Get the PersonnelId using the identifier
        $stmt = $pdo->prepare("SELECT PersonnelId FROM school_per_tbl WHERE EmpNo = :EmpNo");
        $stmt->bindParam(':EmpNo', $PersonnelId);
        $stmt->execute();
        $personnel = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$personnel) {
            return ['error' => 'Personnel not found.'];
        }

        $PersonnelId = $personnel['PersonnelId'];

        // Get the sections based on the PersonnelId and sy_term from anc_ass_tbl
        $stmt = $pdo->prepare("SELECT anc_ass_desc AS SectionName FROM anc_ass_tbl WHERE PersonnelId = :PersonnelId AND anc_ass_term = :sy_term");
        $stmt->bindParam(':PersonnelId', $PersonnelId);
        $stmt->bindParam(':sy_term', $sy_term);
        $stmt->execute();
        $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch sections from subject_taught_tbl
        $stmt = $pdo->prepare("SELECT section AS SectionName FROM subject_taught_tbl WHERE PersonnelId = :PersonnelId AND stac_term = :sy_term");
        $stmt->bindParam(':PersonnelId', $PersonnelId);
        $stmt->bindParam(':sy_term', $sy_term);
        $stmt->execute();
        $subjectSections = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Combine the results
        $allSections = array_merge($assignments, $subjectSections);

        // Remove duplicates if necessary
        $uniqueSections = [];
        foreach ($allSections as $section) {
            if (!in_array($section, $uniqueSections)) {
                $uniqueSections[] = $section;
            }
        }

        return $uniqueSections; // Return the fetched unique sections
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}
function add_plantilla($pdo, $data) {
    // Check if an entry with the same PersonnelId already exists
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM plantilla_pos_tbl WHERE PersonnelId = :PersonnelId");
    $checkStmt->bindParam(':PersonnelId', $data['pp_PersonnelId']);
    $checkStmt->execute();
    
    $count = $checkStmt->fetchColumn();
    
    if ($count > 0) {
        return [
            'status' => 'error',
            'message' => 'An entry for this PersonnelId already exists.'
        ];
    }

    // Prepare the insert statement
    $stmt = $pdo->prepare("INSERT INTO plantilla_pos_tbl (pp_id, pp_desc, PersonnelId) VALUES (:pp_id, :pp_desc, :PersonnelId)");

    // Bind parameters
    $stmt->bindParam(':pp_id', $data['pp_pp_id']);
    $stmt->bindParam(':pp_desc', $data['pp_pp_desc']);
    $stmt->bindParam(':PersonnelId', $data['pp_PersonnelId']);

    // Execute the statement
    if ($stmt->execute()) {
        return [
            'status' => 'success',
            'message' => 'Subject taught added successfully.'
        ];
    } else {
        return [
            'status' => 'error',
            'message' => 'Failed to add subject taught. Please try again.'
        ];
    }
}

function get_plantilla_data($pdo, $PersonnelId) {
    try {
        $sql = "SELECT * FROM plantilla_pos_tbl WHERE PersonnelId = :PersonnelId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':PersonnelId', $PersonnelId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return ['success' => false, 'message' => $e->getMessage()]; // Return error message
    }
}
function get_plantilla_details($pdo, $pp_id) {
    // Prepare the SQL query to fetch the subject details
    $sql = "SELECT * FROM plantilla_pos_tbl WHERE pp_id = :pp_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':pp_id', $pp_id, PDO::PARAM_INT);
    $stmt->execute();
    $pp_details = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if subject details were found
    if ($pp_details) {
        // Return the subject details
        return ['data' => $pp_details];
    } else {
        // Return a message if no data was found
        return ['error' => 'No subject details found.'];
    }
}
function update_plantilla($pdo, $data) {
    // Prepare the SQL statement
    $stmt = $pdo->prepare("UPDATE plantilla_pos_tbl SET 
        PersonnelId = :PersonnelId, 
        pp_desc = :pp_desc
        WHERE pp_id = :pp_id AND PersonnelId = :PersonnelId");

    // Bind parameters
    $stmt->bindParam(':PersonnelId', $data['u_pp_PersonnelId']);
    $stmt->bindParam(':pp_desc', $data['u_pp_pp_desc']);
    $stmt->bindParam(':pp_id', $data['u_pp_pp_id']); // Bind pp_id

    // Execute the statement
    if ($stmt->execute()) {
        return [
            'status' => 'success',
            'message' => 'Plantilla position updated successfully.'
        ];
    } else {
        return [
            'status' => 'error',
            'message' => 'Failed to update plantilla position. Please try again.'
        ];
    }
}
function get_plantilla_list_details($pdo) {
    try {
        // Prepare the SQL statement to fetch all records
        $stmt = $pdo->prepare("SELECT * FROM plantilla_pos_list_tbl");
        $stmt->execute();

        // Fetch all records
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'success' => true,
            'data' => $data
        ];
    } catch (PDOException $e) {
        // Handle any database-related errors
        return [
            'success' => false,
            'message' => "Database error: " . $e->getMessage()
        ];
    }
}
function add_plantilla_list($pdo, $data) {
    // Check if ppl_id already exists
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM plantilla_pos_list_tbl WHERE ppl_id = :ppl_id");
    $checkStmt->bindParam(':ppl_id', $data['a_ppl_id']);
    $checkStmt->execute();
    
    if ($checkStmt->fetchColumn() > 0) {
        return [
            'status' => 'error',
            'message' => 'The Plantilla ID already exists. Please use a different ID.'
        ];
    }

    // Prepare the insert statement
    $stmt = $pdo->prepare("INSERT INTO plantilla_pos_list_tbl (ppl_id, ppl_desc, ppl_code, ppl_rank, ppl_category) VALUES (:ppl_id, :ppl_desc, :ppl_code, :ppl_rank, :ppl_category)");

    // Bind parameters
    $stmt->bindParam(':ppl_id', $data['a_ppl_id']);
    $stmt->bindParam(':ppl_desc', $data['a_ppl_desc']);
    $stmt->bindParam(':ppl_code', $data['a_ppl_code']);
    $stmt->bindParam(':ppl_rank', $data['a_ppl_rank']);
    $stmt->bindParam(':ppl_category', $data['a_ppl_category']);

    // Execute the statement
    if ($stmt->execute()) {
        return [
            'status' => 'success',
            'message' => 'Plantilla position added successfully.'
        ];
    } else {
        return [
            'status' => 'error',
            'message' => 'Failed to add plantilla position. Please try again.'
        ];
    }
}
function get_plantilla_list_detailss($pdo, $ppl_id) {
    try {
        $sql = "SELECT * FROM plantilla_pos_list_tbl WHERE ppl_id = :ppl_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':ppl_id', $ppl_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return ['success' => false, 'message' => $e->getMessage()]; // Return error message
    }
}
function update_plantilla_list($pdo, $data)
{
    try {
        // Start a transaction to ensure all queries are executed successfully or none at all
        $pdo->beginTransaction();

        // First, get the current ppl_desc for the given u_ppl_id
        $stmt = $pdo->prepare("SELECT ppl_desc FROM plantilla_pos_list_tbl WHERE ppl_id = :ppl_id");
        $stmt->bindParam(':ppl_id', $data['u_ppl_id']);
        $stmt->execute();

        // Fetch the current ppl_desc
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $old_ppl_desc = $row['ppl_desc'];

            // Update the plantilla_pos_list_tbl with the new values
            $stmt = $pdo->prepare("UPDATE plantilla_pos_list_tbl SET 
                ppl_desc = :ppl_desc,
                ppl_code = :ppl_code, 
                ppl_category = :ppl_category, 
                ppl_rank = :ppl_rank
                WHERE ppl_id = :ppl_id");

            // Bind parameters for the update query
            $stmt->bindParam(':ppl_id', $data['u_ppl_id']);
            $stmt->bindParam(':ppl_desc', $data['u_ppl_desc']);
            $stmt->bindParam(':ppl_code', $data['u_ppl_code']);
            $stmt->bindParam(':ppl_category', $data['u_ppl_category']);
            $stmt->bindParam(':ppl_rank', $data['u_ppl_rank']);

            // Execute the update query
            if ($stmt->execute()) {
                // Perform the second update on plantilla_pos_tbl based on the old description
                $stmt = $pdo->prepare("UPDATE plantilla_pos_tbl SET pp_desc = :new_desc WHERE pp_desc = :old_desc");
                $stmt->bindParam(':new_desc', $data['u_ppl_desc']);
                $stmt->bindParam(':old_desc', $old_ppl_desc);

                // Execute the second update
                if ($stmt->execute()) {
                    // Commit the transaction if both queries are successful
                    $pdo->commit();
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Plantilla position updated successfully.'
                    ]);
                } else {
                    // Rollback if the second update fails
                    $pdo->rollBack();
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Failed to update plantilla description in plantilla_pos_tbl.'
                    ]);
                }
            } else {
                // Rollback if the first update fails
                $pdo->rollBack();
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to update plantilla position in plantilla_pos_list_tbl.'
                ]);
            }
        } else {
            // Rollback if the first query fails to find the record
            $pdo->rollBack();
            echo json_encode([
                'status' => 'error',
                'message' => 'Plantilla position not found.'
            ]);
        }

        exit; // Stop further execution after the response is sent
    } catch (Exception $e) {
        // Rollback transaction in case of an exception
        $pdo->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ]);
        exit; // Stop execution after response
    }
}

function get_personnel_name($pdo, $PersonnelId) {
    $query = "SELECT EmpLName, EmpFName, EmpMName, EmpEName FROM school_per_tbl WHERE EmpNo = :PersonnelId";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':PersonnelId', $PersonnelId);
    
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            // Concatenate the names
            $fullName = trim($row['EmpFName'] . ' ' . $row['EmpMName'] . ' ' . $row['EmpLName'] . ' ' . $row['EmpEName']);
            return ['success' => true, 'data' => ['fullName' => $fullName]];
        } else {
            return ['success' => false, 'message' => 'No personnel found.'];
        }
    } else {
        return ['success' => false, 'message' => 'Database query failed.'];
    }
}
function create_account($pdo, $data)
{
    if (!isset($data['PersonnelId'])) {
        return ['success' => false, 'message' => 'Invalid Personnel ID.'];
    }

    $personnelId = $data['PersonnelId'];

    // Get EmpNo and Email from school_per_tbl using PersonnelId
    $stmt = $pdo->prepare("SELECT EmpNo, Email FROM school_per_tbl WHERE PersonnelId = ?");
    $stmt->execute([$personnelId]);
    $empRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$empRow) {
        return ['success' => false, 'message' => 'EmpNo not found for this Personnel ID.'];
    }

    $empNo = $empRow['EmpNo'];

    // Fetch personnel details from school_per_tbl using EmpNo
    $stmt = $pdo->prepare("SELECT EmpNo, EmpFName, EmpLName, EmpMName, EmpEName, BirthDate, Sex, email FROM school_per_tbl WHERE EmpNo = ?");
    $stmt->execute([$empNo]);
    $person = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$person) {
        return ['success' => false, 'message' => 'Personnel details not found.'];
    }

    // Check if email is empty
    if (empty($person['BirthDate'])) {
        return ['success' => false, 'message' => 'No birth date provided. Account not created.'];
    }

    // Check if the EmpNo already exists in user_tbl
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM user_tbl WHERE Identifier = ?");
    $checkStmt->execute([$empNo]);
    $exists = $checkStmt->fetchColumn();

    if ($exists) {
        return ['success' => false, 'message' => 'Account already exists for this personnel.'];
    }

    // Extract personnel details
    $firstName = trim($person['EmpFName']);
    $lastName = trim($person['EmpLName']);
    $middleName = trim($person['EmpMName']);
    $extName = trim($person['EmpEName']);
    $birthDate = $person['BirthDate'];
    $gender = $person['Sex'];
    $email = $person['email'];

    // Use EmpNo as the username
    $username = $empNo;

    // Generate password (MMDDYYYY format from BirthDate)
    $password = date('mdY', strtotime($birthDate));

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Set role and access level
    $role = 'TEACHER';
    $accessLevel = $role;

    // Insert into user_tbl
    $insertStmt = $pdo->prepare("INSERT INTO user_tbl (Identifier, UserFName, UserLName, UserMName, UserEName, Gender, BirthDate, Role, username, password, access_level, email, user_status) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL)");

    $insertStmt->execute([
        $empNo,         // Identifier (EmpNo)
        $firstName,     // UserFName
        $lastName,      // UserLName
        $middleName,    // UserMName
        $extName,       // UserEName
        $gender,        // Gender
        $birthDate,     // BirthDate
        $role,          // Role
        $username,      // Username (EmpNo)
        $hashedPassword, // Hashed password
        $accessLevel,   // Access level
        $email          // Email from school_per_tbl
    ]);

    return ['success' => true, 'message' => 'Account successfully created!'];
}

function smartCopy_subject_taught($pdo, $PersonnelId)
{
    try {
        // Step 1: Get active school year term
        $stmt = $pdo->prepare("SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active'");
        $stmt->execute();
        if ($stmt->rowCount() === 0) {
            return ['status' => 'error', 'message' => 'No active school year found.'];
        }

        $active_sy = $stmt->fetch(PDO::FETCH_ASSOC)['sy_term'];

        // Step 2: Get previous school year term
        $sy_parts = explode('-', $active_sy);
        $prev_sy = ($sy_parts[0] - 1) . '-' . ($sy_parts[1] - 1);

        // Step 3: Get all records from the previous term
        $stmt = $pdo->prepare("SELECT * FROM subject_taught_tbl 
                               WHERE PersonnelId = :PersonnelId AND stac_term = :prev_sy");
        $stmt->execute([
            ':PersonnelId' => $PersonnelId,
            ':prev_sy' => $prev_sy
        ]);

        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($subjects)) {
            return ['status' => 'error', 'message' => "No records found for Personnel ID {$PersonnelId} in {$prev_sy}."];
        }

        // Step 4: Prepare insertion statement
        $insertStmt = $pdo->prepare("INSERT INTO subject_taught_tbl 
            (PersonnelId, subject_taught, subject_code, section, st_day, st_from, st_to, tat_min, stac_term)
            VALUES (:PersonnelId, :subject_taught, :subject_code, :section, :st_day, :st_from, :st_to, :tat_min, :stac_term)");

        // Step 5: Loop and insert only if no duplicate exists
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM subject_taught_tbl 
            WHERE PersonnelId = :PersonnelId AND stac_term = :active_sy AND st_day = :st_day 
            AND st_from = :st_from AND st_to = :st_to AND section = :section");

        $copiedCount = 0;
        foreach ($subjects as $subj) {
            // Check for conflict
            $checkStmt->execute([
                ':PersonnelId' => $subj['PersonnelId'],
                ':active_sy' => $active_sy,
                ':st_day' => $subj['st_day'],
                ':st_from' => $subj['st_from'],
                ':st_to' => $subj['st_to'],
                ':section' => $subj['section']
            ]);

            $exists = $checkStmt->fetchColumn();

            if ($exists == 0) {
                // Insert if not duplicate
                $insertStmt->execute([
                    ':PersonnelId' => $subj['PersonnelId'],
                    ':subject_taught' => $subj['subject_taught'],
                    ':subject_code' => $subj['subject_code'],
                    ':section' => $subj['section'],
                    ':st_day' => $subj['st_day'],
                    ':st_from' => $subj['st_from'],
                    ':st_to' => $subj['st_to'],
                    ':tat_min' => $subj['tat_min'],
                    ':stac_term' => $active_sy
                ]);
                $copiedCount++;
            }
        }

        return [
            'status' => 'success',
            'message' => "{$copiedCount} subject(s) copied to {$active_sy} successfully."
        ];

    } catch (PDOException $e) {
        return ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()];
    }
}
?>