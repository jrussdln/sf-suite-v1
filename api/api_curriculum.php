<?php
require_once('../includes/sessions.php');
require_once('../includes/db_config.php');
require_once('../includes/functions/func_curriculum.php');
header('Content-type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        //CURRICULUM MANAGEMENT PAGE
        if (isset($_GET['curriculum_list'])) {
            // Call the function to get the curriculum list
            $curriculumData = curriculum_list($pdo);
            // Return the result as a JSON response
            echo json_encode($curriculumData);
            exit;
        } elseif (isset($_GET['active_school_year'])) {
            // Fetch the active school year
            $activeSchoolYear = get_active_school_year($pdo);
            echo json_encode(['active_school_year' => $activeSchoolYear]);
            exit;
        } elseif (isset($_GET['school_year_list'])) {
            // Call the function to get the curriculum list
            $schoolYearData = school_year_list($pdo);
            // Return the result as a JSON response
            echo json_encode($schoolYearData);
            exit;
        } elseif (isset($_GET['get_curriculum_data'])) {
            $curriculum_id = $_GET['curriculum_id'];
            // Call the function to get curriculum data
            $response = get_curriculum_data($pdo, $curriculum_id);
            // Return the response as JSON
            echo json_encode($response);
            exit;
        } elseif (isset($_GET['get_strand_track_data'])) {
            $id = $_GET['id'];
            // Call the function to get strand track data
            $response = get_strand_track_data($pdo, $id);
            // Return the response as JSON
            echo json_encode($response);
            exit; // Ensure no extra output is sent
        } elseif (isset($_GET['strand_track_list'])) {
            // Call the function to get the curriculum list
            $strandTrackData = strand_track_list($pdo);
            // Return the result as a JSON response
            echo json_encode($strandTrackData);
            exit;
        }
        //SUBJECTS PAGE
        elseif (isset($_GET['subjects_list'])) {
            $schoolYear = $_GET['schoolYear'] ?? null;
            $gradeLevel = $_GET['gradeLevel'] ?? null;
            $semester = $_GET['semester'] ?? null;
            $subjectType = $_GET['subjectType'] ?? null;
            $subjectListData = subjects_list($pdo, $schoolYear, $gradeLevel, $semester, $subjectType);
            echo json_encode($subjectListData);
            exit;
        } elseif (isset($_GET['archive_subjects_list'])) {
            $schoolYear = $_GET['schoolYear'] ?? null;
            $gradeLevel = $_GET['gradeLevel'] ?? null;
            $semester = $_GET['semester'] ?? null;
            $subjectType = $_GET['subjectType'] ?? null;
            $subjectListData = archive_subjects_list($pdo, $schoolYear, $gradeLevel, $semester, $subjectType);
            echo json_encode($subjectListData);
            exit;
        } elseif (isset($_GET['get_subject_list'])) {
            // Call the function to get the subject details
            $response = get_subject_list_details($pdo);
            // Return the response as JSON
            echo json_encode($response);
            exit;
        } elseif (isset($_GET['get_subject_data'])) {
            $subject_id = $_GET['subject_id'];  // Get subject_id from request
            $subject_data = get_subject_data($pdo, $subject_id);
            echo json_encode($subject_data);
            exit;
        } elseif (isset($_GET['get_school_info'])) {
            $school_info = get_school_info($pdo);
            echo json_encode($school_info);
            exit;
        }
        break;
    case 'PUT':
        // Handle PUT requests here
        break;
    case 'POST':
        $data = $_POST; // Get the posted data
        error_log(print_r($data, true)); // Log the incoming data for debugging
        //CURRICULUM MANAGEMENT PAGE
        if (isset($_GET['add_school_year'])) {
            echo json_encode(add_school_year($pdo, $data));
            exit;
        } elseif (isset($_GET['add_strand_track'])) {
            echo json_encode(add_strand_track($pdo, $data));
            exit;
        } elseif (isset($data['ust_strand_track'])) {
            if (empty($data['ust_strand_track'])) {
                echo json_encode(['success' => false, 'message' => 'Required fields are missing.']);
                exit;
            }
            $result = edit_strand_track($pdo, $data);
            echo json_encode([
                'success' => $result['success'],
                'message' => $result['success'] ? 'Strand/Track updated successfully.' : 'Failed to update strand/track.'
            ]);
            exit;
        } elseif (isset($_GET['add_curriculum'])) {
            echo json_encode(add_curriculum($pdo, $data));
            exit;
        } elseif (isset($data['u_curriculum_desc'])) {
            if (empty($data['u_curriculum_desc'])) {
                echo json_encode(['success' => false, 'message' => 'Required fields are missing.']);
                exit;
            }
            $result = edit_curriculum($pdo, $data);
            echo json_encode([
                'success' => $result['success'],
                'message' => $result['success'] ? 'Curriculum updated successfully.' : 'Failed to update curriculum.'
            ]);
            exit;
        } elseif (isset($_POST['update_sy_status']) && isset($_POST['sy_id'])) {
            $sy_id = $_POST['sy_id']; // Get the school year ID from the request
            $result = update_sy_status($pdo, $sy_id); // Call the function to update the status
            echo json_encode([
                'success' => $result,
                'message' => $result ? 'School year status updated successfully.' : 'Failed to update school year status.'
            ]);
            exit;
        } elseif (isset($_POST['curriculum_id']) && isset($_POST['status'])) {
            $curriculum_id = $_POST['curriculum_id'];
            $status = $_POST['status']; // Get status from request
            $result = update_curriculum_status($pdo, $curriculum_id, $status);
            echo json_encode([
                'success' => $result['success'],
                'message' => $result['message']
            ]);
            exit;
        } elseif (isset($_POST['id']) && isset($_POST['status'])) {
            $id = $_POST['id'];
            $status = $_POST['status']; // Get status from request
            $result = update_strand_track($pdo, $id, $status);
            echo json_encode([
                'success' => $result['success'],
                'message' => $result['message']
            ]);
            exit;
        } elseif(isset($_POST['fetch_subjects'])) {
            $curriculum_id = $_POST['curriculum_id'];
            $sy_from = $_POST['sy_from'];
        
            $subjects = fetchSubjects($pdo, $curriculum_id, $sy_from);
            echo json_encode($subjects);
            exit;
        
        } elseif (isset($_POST['copy_curriculum'], $_POST['copy_sy_from'], $_POST['copy_sy_to'])) {
            $curriculum_id = $_POST['copy_curriculum'];
            $sy_from = $_POST['copy_sy_from'];
            $sy_to = $_POST['copy_sy_to'];
            $selected_subjects = json_decode($_POST['selected_subjects_json'] ?? '[]', true);
        
            if (empty($selected_subjects)) {
                echo json_encode(['success' => false, 'message' => 'No subjects selected.']);
                exit;
            }
        
            try {
                $pdo->beginTransaction();
                // Fetch subject
                $subjects = fetchSubjects($pdo, $curriculum_id, $sy_from);
        
                // Filter only selected subjects
                $subjects = array_filter($subjects, function ($subject) use ($selected_subjects) {
                    return in_array($subject['subject_code'], $selected_subjects);
                });
        
                if (empty($subjects)) {
                    echo json_encode(['success' => false, 'message' => 'No matching subjects found to copy.']);
                    $pdo->rollBack();
                    exit;
                }
        
                // Insert copied subjects
                $result = insertCopiedSubjects($pdo, $subjects, $curriculum_id, $sy_to);
                $pdo->commit();
                echo json_encode([
                    'success' => true,
                    'message' => "Subjects copied successfully! Inserted: {$result['inserted']}, Skipped: {$result['skipped']}"
                ]);
            } catch (Exception $e) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'message' => 'Failed to copy subjects. ' . $e->getMessage()]);
            }
            exit;
                
        }
        //SUBJECT PAGE
        elseif (isset($_GET['add_subject'])) {
            echo json_encode(add_subject($pdo, $data));
            exit;
        } elseif (isset($_GET['archive_subject'])) {
            // Get the subject_id from the POST data
            $subject_id = isset($_POST['subject_id']) ? $_POST['subject_id'] : null;
            if ($subject_id) {
                // Call the function with the subject_id
                echo json_encode(archive_subject($pdo, $subject_id));
            } else {
                echo json_encode(['success' => false, 'message' => 'Subject ID is missing.']);
            }
            exit;
        } elseif (isset($_GET['unarchive_subject'])) {
            // Get the subject_id from the POST data
            $subject_id = isset($_POST['subject_id']) ? $_POST['subject_id'] : null;
            if ($subject_id) {
                // Call the function with the subject_id
                echo json_encode(unarchive_subject($pdo, $subject_id));
            } else {
                echo json_encode(['success' => false, 'message' => 'Subject ID is missing.']);
            }
            exit;
        } elseif (isset($_GET['update_subject'])) {
            // Ensure $data is populated correctly
            if (empty($data['u_subject_code']) || empty($data['u_subject_name'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Required fields are missing.'
                ]);
                exit;
            }
            // Call the update_subject function to perform the update
            $result = update_subject($pdo, $data);
            echo json_encode([
                'success' => $result['success'],
                'message' => $result['success'] ? 'Subject updated successfully.' : $result['message']
            ]);
            exit;
        } elseif (isset($_POST['id'])) {
            // Assuming you have a function to update school info
            $result = update_school_info($pdo, $_POST); // Pass the $_POST data to your update function
            echo json_encode([
                'success' => $result['success'],
                'message' => $result['success'] ? 'School information updated successfully.' : 'Failed to update school information.'
            ]);
        }
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        break;
}
?>