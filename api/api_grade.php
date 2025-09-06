<?php
require_once('../includes/sessions.php');
require_once('../includes/db_config.php');
require_once('../includes/functions/func_grade.php');
header('Content-type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        if (isset($_GET['get_student_grade'])) {
            try {
                $sectionId = $_GET['sectionId'] ?? '';
                // Validate input
                if (empty($sectionId)) {
                    echo json_encode(['success' => false, 'message' => 'Missing required parameters.']);
                    exit;
                }
                // Sanitize SectionId
                $sectionId = (int) $sectionId;
                // Fetch student grades based on School Year
                $stmt = $pdo->prepare("
                    SELECT *
                    FROM student_grade_jhs_tbl
                    WHERE section_id = :sectionId
                ");
                $stmt->execute([
                    ':sectionId' => $sectionId
                ]);
                $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (empty($grades)) {
                    echo json_encode(['success' => false, 'message' => 'No grades found for the specified parameters.']);
                } else {
                    echo json_encode(['success' => true, 'data' => $grades]);
                }
            } catch (PDOException $e) {
                error_log("Error fetching student grades: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
            exit;
        } elseif (isset($_GET['get_section_data'])) {
            $sectionId = $_GET['sectionId'];
            // Call the function to get curriculum data
            $response = get_section_data($pdo, $sectionId); // Corrected variable name
            // Return the response as JSON
            echo json_encode($response);
            exit;
        } elseif (isset($_GET['get_transmutation_data'])) {
            try {
                // Query to fetch transmutation data from the database
                $query = "SELECT * FROM transmutation_tbl ORDER BY min_grade ASC";
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                // Fetch all the rows from the query
                $transmutationData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                // Prepare the response
                $response = [
                    'success' => true,
                    'data' => $transmutationData // Return the transmutation data
                ];
                // Return the response as JSON
                echo json_encode($response);
                exit;
            } catch (PDOException $e) {
                // If there is an error, log it and return an error message
                error_log('Error fetching transmutation data: ' . $e->getMessage());
                echo json_encode([
                    'success' => false,
                    'message' => 'Error fetching transmutation data.'
                ]);
                exit;
            }
        } elseif (isset($_GET['get_grade_details'])) {
            $gradeId = $_GET['id']; // Get the grade ID from the query parameter
            // Call the function to get grade details
            $response = get_grade_details($pdo, $gradeId);
            // Return the response as JSON
            echo json_encode($response);
            exit;
        } elseif (isset($_GET['get_grade_details_shs'])) {
            $gradeId = $_GET['id']; // Get the grade ID from the query parameter
            // Call the function to get grade details
            $response = get_grade_details_shs($pdo, $gradeId);
            // Return the response as JSON
            echo json_encode($response);
            exit;
        } elseif (isset($_GET['student_in_section'])) {
            if (isset($_GET['SectionId']) && !empty($_GET['SectionId'])) {
                $sectionId = $_GET['SectionId']; // Get the SectionId from the frontend
                // Fetch student data based on SectionId
                $studentInSectionData = student_in_section($pdo, $sectionId);
                // Return JSON response
                echo json_encode($studentInSectionData);
            } else {
                echo json_encode(['data' => []]);
            }
            exit;
        } elseif (isset($_GET['student_grade_data'])) {
            $lrn = trim($_GET['lrn'] ?? '');
            $school_year = trim($_GET['school_year'] ?? '');
            $grade_level = trim($_GET['grade_level'] ?? '');
            try {
                // Fetch grades from student_grade_jhs_tbl
                $query = "
                        SELECT * 
                        FROM student_grade_jhs_tbl 
                        WHERE lrn = :lrn 
                        AND grade_term = :school_year
                        ";
                $stmt = $pdo->prepare($query);
                $stmt->execute([
                    ':lrn' => $lrn,
                    ':school_year' => $school_year
                ]);
                $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
                // Fetch subject names separately (always runs)
                $subjectQuery = "
                                SELECT subject_id, subject_name 
                                FROM subjects_tbl 
                                WHERE grade_level = :grade_level
                                AND subject_term = :school_year
                                AND archive != 1
                            ";
                $stmt = $pdo->prepare($subjectQuery);
                $stmt->execute([':grade_level' => $grade_level, ':school_year' => $school_year]);
                $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
                // Convert subjects to associative array for easy lookup
                $subjectMap = [];
                foreach ($subjects as $subject) {
                    $subjectMap[$subject['subject_id']] = $subject['subject_name'];
                }
                // Filter out grades where the subject ID doesn't exist in subjects_tbl
                $filteredGrades = [];
                foreach ($grades as $grade) {
                    if (isset($subjectMap[$grade['subject_id']])) {
                        $grade['subject_name'] = $subjectMap[$grade['subject_id']];
                        $filteredGrades[] = $grade; // Add only valid subjects
                    }
                }
                echo json_encode(['success' => true, 'data' => $filteredGrades]);
            } catch (PDOException $e) {
                error_log("Error fetching JHS student grades: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Database error. Please try again later.', 'data' => []]);
            }
            exit;
        } elseif (isset($_GET['student_grade_data_teacher'])) {
            $lrn = trim($_GET['lrn'] ?? '');
            $school_year = trim($_GET['school_year'] ?? '');
            $grade_level = trim($_GET['grade_level'] ?? '');
            $identifier = trim($_GET['identifier'] ?? '');
            try {
                // Step 1: Get PersonnelId using identifier
                $personnelQuery = "
            SELECT PersonnelId 
            FROM school_per_tbl 
            WHERE EmpNo = :identifier
        ";
                $stmt = $pdo->prepare($personnelQuery);
                $stmt->execute([':identifier' => $identifier]);
                $personnel = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$personnel) {
                    throw new Exception("Personnel not found for the given identifier.");
                }
                $personnelId = $personnel['PersonnelId'];
                // Step 2: Get subject codes taught by this teacher
                $subjectTaughtQuery = "
            SELECT subject_code
            FROM subject_taught_tbl 
            WHERE PersonnelId = :personnelId 
            AND stac_term = :school_year
        ";
                $stmt = $pdo->prepare($subjectTaughtQuery);
                $stmt->execute([
                    ':personnelId' => $personnelId,
                    ':school_year' => $school_year
                ]);
                $taughtSubjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
                // Flatten the subject codes to an array
                $subjectCodes = array_column($taughtSubjects, 'subject_code');
                // Step 3: Fetch student grades
                $query = "
            SELECT * 
            FROM student_grade_jhs_tbl 
            WHERE lrn = :lrn 
            AND grade_term = :school_year
        ";
                $stmt = $pdo->prepare($query);
                $stmt->execute([
                    ':lrn' => $lrn,
                    ':school_year' => $school_year
                ]);
                $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
                // Step 4: Get all subjects info
                $subjectQuery = "
            SELECT subject_id, subject_name, subject_code 
            FROM subjects_tbl 
            WHERE grade_level = :grade_level
            AND subject_term = :school_year
            AND archive != 1
        ";
                $stmt = $pdo->prepare($subjectQuery);
                $stmt->execute([
                    ':grade_level' => $grade_level,
                    ':school_year' => $school_year
                ]);
                $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
                // Map subject_id to subject_name and subject_code
                $subjectMap = [];
                foreach ($subjects as $subject) {
                    $subjectMap[$subject['subject_id']] = [
                        'subject_name' => $subject['subject_name'],
                        'subject_code' => $subject['subject_code']
                    ];
                }
                // Step 5: Filter grades where subject_id exists in subjectMap AND subject_code is taught by teacher
                $filteredGrades = [];
                foreach ($grades as $grade) {
                    if (isset($subjectMap[$grade['subject_id']])) {
                        $subjectInfo = $subjectMap[$grade['subject_id']];
                        if (in_array($subjectInfo['subject_code'], $subjectCodes)) {
                            $grade['subject_name'] = $subjectInfo['subject_name'];
                            $grade['subject_code'] = $subjectInfo['subject_code'];
                            $filteredGrades[] = $grade;
                        }
                    }
                }
                echo json_encode(['success' => true, 'data' => $filteredGrades]);
            } catch (Exception $e) {
                error_log("Error fetching JHS student grades: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => $e->getMessage(), 'data' => []]);
            }
            exit;
        } elseif (isset($_GET['get_subject_taught1'])) {
            $lrn = $_GET['lrn'];
            $school_year = $_GET['school_year'];
            $grade_level = $_GET['grade_level'];
            $response = getSubjectTaught($pdo, $lrn, $school_year, $grade_level);
            echo json_encode($response);
            exit;
        } elseif (isset($_GET['get_subject_taught2'])) {
            $subject_code = $_GET['subject_code'];
            $subject_term = $_GET['subject_term'];
            $response = checkSubjectTaught($pdo, $subject_code, $subject_term);
            echo json_encode($response);
            exit;
        } elseif (isset($_GET['student_grade_data_shs'])) {
            $lrn = trim($_GET['lrn'] ?? '');
            $school_year = trim($_GET['school_year'] ?? '');
            $semester = trim($_GET['semester'] ?? ''); // Get semester value
            try {
                $query = "
                    SELECT 
                        sg.*, 
                        s.subject_name,
                        s.archive
                    FROM 
                        student_grade_shs_tbl sg
                    JOIN 
                        subjects_tbl s ON sg.subject_id = s.subject_id
                    WHERE 
                        sg.lrn = :lrn 
                        AND sg.grade_term = :school_year
                        AND sg.grade_semester = :semester 
                        AND s.archive != 1
                ";
                $stmt = $pdo->prepare($query);
                $stmt->execute([
                    ':lrn' => $lrn,
                    ':school_year' => $school_year,
                    ':semester' => $semester
                ]);
                $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $grades ?: []]); // Return empty array if no grades found
            } catch (PDOException $e) {
                error_log("Error fetching SHS student grades: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Database error. Please try again later.', 'data' => []]);
            }
            exit;
        } elseif (isset($_GET['student_grade_data_shs_teacher'])) {
            $lrn = trim($_GET['lrn'] ?? '');
            $school_year = trim($_GET['school_year'] ?? '');
            $semester = trim($_GET['semester'] ?? '');
            $identifier = trim($_GET['identifier'] ?? '');
            try {
                // Step 1: Get PersonnelId
                $personnelQuery = "
            SELECT PersonnelId 
            FROM school_per_tbl 
            WHERE EmpNo = :identifier
        ";
                $stmt = $pdo->prepare($personnelQuery);
                $stmt->execute([':identifier' => $identifier]);
                $personnel = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$personnel) {
                    throw new Exception("Personnel not found for the given identifier.");
                }
                $personnelId = $personnel['PersonnelId'];
                // Step 2: Get subject codes taught by this teacher
                $subjectTaughtQuery = "
            SELECT subject_code
            FROM subject_taught_tbl 
            WHERE PersonnelId = :personnelId 
            AND stac_term = :school_year
        ";
                $stmt = $pdo->prepare($subjectTaughtQuery);
                $stmt->execute([
                    ':personnelId' => $personnelId,
                    ':school_year' => $school_year
                ]);
                $taughtSubjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $subjectCodes = array_column($taughtSubjects, 'subject_code');
                // Step 3: Fetch SHS student grades including subject_code
                $query = "
            SELECT 
                sg.*, 
                s.subject_name,
                s.subject_code,
                s.archive
            FROM 
                student_grade_shs_tbl sg
            JOIN 
                subjects_tbl s ON sg.subject_id = s.subject_id
            WHERE 
                sg.lrn = :lrn 
                AND sg.grade_term = :school_year
                AND sg.grade_semester = :semester 
                AND s.archive != 1
        ";
                $stmt = $pdo->prepare($query);
                $stmt->execute([
                    ':lrn' => $lrn,
                    ':school_year' => $school_year,
                    ':semester' => $semester
                ]);
                $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
                // Step 4: Filter grades based on subject codes taught by this teacher
                $filteredGrades = [];
                foreach ($grades as $grade) {
                    if (in_array($grade['subject_code'], $subjectCodes)) {
                        $filteredGrades[] = $grade;
                    }
                }
                echo json_encode(['success' => true, 'data' => $filteredGrades]);
            } catch (Exception $e) {
                error_log("Error fetching SHS student grades for teacher: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => $e->getMessage(), 'data' => []]);
            }
            exit;
        } elseif (isset($_GET['update_nested_grade'])) {
            // Retrieve the gradeId from the GET request
            $gradeId = $_GET['gradeId'];
            $lrn = $_GET['lrn'];
            // Call your update function with only gradeId
            $response = update_nested_grade($pdo, $gradeId, $lrn);
            // Return the response as JSON
            echo json_encode($response);
            exit;
        } elseif (isset($_GET['get_grade_control_status'])) {
            // Query to fetch c_status for specific c_desc values (11, 12, 13, 14, 21, 22)
            $query = "SELECT c_desc, c_status FROM control_tbl WHERE c_desc IN (11, 12, 13, 14, 21, 22)";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            // Fetch all results as an associative array
            $statusData = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $statusData[$row['c_desc']] = $row['c_status'];
            }
            // Return the result as a JSON response
            echo json_encode([
                'success' => true,
                'data' => $statusData
            ]);
            exit;
        } elseif (isset($_GET['get_grade_control'])) {
            // Fetch the current grade control statuses from the database
            $query = "SELECT c_desc, c_status FROM control_tbl WHERE c_desc IN (11, 12, 13, 14, 21, 22)";
            try {
                // Prepare and execute the query
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                // Format the response data into an associative array using c_desc as keys
                $response = [];
                foreach ($data as $row) {
                    $response[$row['c_desc']] = $row['c_status'];
                }
                // Return the grade control data as a JSON response
                echo json_encode(['success' => true, 'data' => $response]);
            } catch (PDOException $e) {
                // Handle any database errors
                echo json_encode(['success' => false, 'message' => 'Error fetching grade control status: ' . $e->getMessage()]);
            }
            exit;
        } elseif (
            !isset($_GET['SectionName']) || empty($_GET['SectionName']) ||
            !isset($_GET['SectionId']) || !isset($_GET['GradeLevel'])
        ) {
            echo json_encode(['success' => false, 'message' => 'Section name, ID, and grade level are required.']);
            exit;
        }
        $sectionName = htmlspecialchars($_GET['SectionName']);
        $sectionId = (int) $_GET['SectionId'];
        $gradeLevel = (int) $_GET['GradeLevel']; // Ensure gradeLevel is assigned properly
        $schoolYear = htmlspecialchars($_GET['SchoolYear']); // Sanitize SchoolYear value
        $sectionStrand = htmlspecialchars($_GET['SectionStrand']);
        try {
            $students = getStudentsBySectionName($pdo, $sectionName, $schoolYear);
            if (!empty($students)) {
                $gradeData = [];
                foreach ($students as $student) {
                    if (insertGradeRecord($pdo, $student['lrn'], $student['school_year'], $sectionId, $gradeLevel, $sectionStrand)) {
                        $gradeData[] = [
                            'lrn' => $student['lrn'],
                            'school_year' => $student['school_year']
                        ];
                    }
                }
                echo json_encode(['success' => true, 'data' => $students, 'grades' => $gradeData]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No students available.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to sync grades.', 'error' => $e->getMessage()]);
        }
        break;
    case 'POST':
        $data = $_POST;
        // Check if the necessary fields are present for JHS update (u_sg_id)
        if (isset($data['u_sg_id'])) {
            // Call the function to update the JHS grade
            $response = update_grade_jhs($pdo, $data);
            echo json_encode($response);
            exit;
        } elseif (isset($_POST['update_transmutation'])) {
            // Validate received data
            if (!isset($_POST['transmutationId'], $_POST['minGrade'], $_POST['transmutedGrade'])) {
                echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
                exit;
            }
            $transmutationId = $_POST['transmutationId'];
            $minGrade = $_POST['minGrade'];
            $transmutedGrade = $_POST['transmutedGrade'];
            // Log received data for debugging
            error_log("Received Data: ID = $transmutationId, Min Grade = $minGrade, Transmuted Grade = $transmutedGrade");
            // Call function to update the database
            $response = update_transmutation_data($pdo, $transmutationId, $minGrade, $transmutedGrade);
            echo json_encode($response);
            exit;
        } elseif (isset($data['s_sg_id'])) {
            // Call the function to update the SHS grade
            $response = update_grade_shs($pdo, $data);
            echo json_encode($response);
            exit;
        } elseif (isset($_GET['update_grade_control'])) {
            // Read the incoming POST data
            $data = [
                'c_desc' => isset($_POST['c_desc']) ? $_POST['c_desc'] : null,
                'c_status' => isset($_POST['c_status']) ? $_POST['c_status'] : null
            ];
            // Check if necessary fields are present
            if (isset($data['c_desc']) && isset($data['c_status'])) {
                // Update grade control in the database
                $response = update_grade_control($pdo, $data);
                echo json_encode($response);
                exit;
            } else {
                // Missing required fields
                echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
                exit;
            }
        }
        break;
    default:
        // Handle invalid request method
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        break;
}
?>