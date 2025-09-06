<?php
require_once('../includes/sessions.php');
require_once('../includes/db_config.php');
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        if (isset($_GET['get_announcement_history'])) {
            // Fetch all announcements except Alumni Notice (latest 5)
            try {
                $stmt = $pdo->query("SELECT id, title, subject, message, recipient, created_at FROM announcement_tbl WHERE title != 'Alumni Notice' ORDER BY created_at DESC LIMIT 5");
                $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $announcements]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error fetching announcements: ' . $e->getMessage()]);
            }
        } elseif (isset($_GET['get_questions'])) {
            try {
                // ✅ Step 1: Get the active school year (sy_term)
                $syStmt = $pdo->prepare("SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active' LIMIT 1");
                $syStmt->execute();
                $activeSchoolYear = $syStmt->fetchColumn();
                if (!$activeSchoolYear) {
                    echo json_encode(['success' => false, 'message' => 'No active school year found.']);
                    exit;
                }
                // ✅ Step 2: Fetch questions from quest_tracer_tbl for the active school year
                $stmt = $pdo->prepare("SELECT question_id, question_desc 
                                       FROM quest_tracer_tbl 
                                       WHERE school_year = :activeSchoolYear 
                                       ORDER BY question_id DESC");
                $stmt->bindParam(':activeSchoolYear', $activeSchoolYear);
                $stmt->execute();
                $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $questions]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error fetching questions: ' . $e->getMessage()]);
            }
            exit;
        } elseif (isset($_GET['get_choices'])) {
            // Fetch choices for a specific question
            $question_id = $_GET['question_id'] ?? null;
            if ($question_id) {
                try {
                    $stmt = $pdo->prepare("SELECT choices_id, choices_content FROM choices_tracer_tbl WHERE question_id = :question_id");
                    $stmt->bindParam(':question_id', $question_id, PDO::PARAM_INT);
                    $stmt->execute();
                    $choices = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode(['success' => true, 'data' => $choices]);
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => 'Error fetching choices: ' . $e->getMessage()]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Question ID is required.']);
            }
            exit;
        } elseif (isset($_GET['get_question_summary']) && isset($_GET['question_id'])) {
            $questionId = $_GET['question_id'];
            try {
                // Step 1: Get all choices for the given question
                $stmt = $pdo->prepare("SELECT choices_id, choices_content FROM choices_tracer_tbl WHERE question_id = ?");
                $stmt->execute([$questionId]);
                $choices = $stmt->fetchAll(PDO::FETCH_ASSOC);
                // Step 2: For each choice, count how many responses selected it
                $choiceResponseData = [];
                foreach ($choices as $choice) {
                    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM responses WHERE question_id = ? AND choice_id = ?");
                    $stmt->execute([$questionId, $choice['choices_id']]);
                    $count = $stmt->fetchColumn();
                    $choiceResponseData[] = [
                        'label' => $choice['choices_content'], // Correct key
                        'count' => (int) $count
                    ];
                }
                echo json_encode($choiceResponseData);
            } catch (PDOException $e) {
                echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
            }
            exit;
        } elseif (isset($_GET['get_responses'])) {
            try {
                // ✅ Step 1: Get active school year
                $syStmt = $pdo->prepare("SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active' LIMIT 1");
                $syStmt->execute();
                $activeSchoolYear = $syStmt->fetchColumn();
                if (!$activeSchoolYear) {
                    echo json_encode(['error' => 'No active school year found.']);
                    exit;
                }
                // ✅ Step 2: Fetch responses only for the active school year
                $stmt = $pdo->prepare("
                    SELECT 
                        CONCAT(u.UserFName, ' ', u.UserMName, ' ', u.UserLName, ' ', u.UserEName) AS full_name,
                        r.submitted_at
                    FROM 
                        response_log_tbl r
                    LEFT JOIN 
                        user_tbl u ON r.identifier = u.identifier
                    WHERE
                        r.school_year = :activeSchoolYear
                    ORDER BY 
                        r.submitted_at DESC
                ");
                $stmt->bindParam(':activeSchoolYear', $activeSchoolYear);
                $stmt->execute();
                $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($logs);
            } catch (PDOException $e) {
                echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
            }
            exit;
        } elseif (isset($_GET['get_tracer_history'])) {
            // Fetch Alumni Notice announcements (latest 5)
            try {
                $stmt = $pdo->query("SELECT id, title, subject, message, recipient, created_at FROM announcement_tbl WHERE title = 'Alumni Notice' ORDER BY created_at DESC LIMIT 5");
                $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $announcements]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error fetching announcements: ' . $e->getMessage()]);
            }
        } elseif (isset($_GET['announcement_id'])) {
            // Fetch single announcement details by id
            $announcement_id = $_GET['announcement_id'];
            $stmt = $pdo->prepare("SELECT * FROM announcement_tbl WHERE id = :id");
            $stmt->bindParam(':id', $announcement_id);
            $stmt->execute();
            $announcement = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($announcement) {
                echo json_encode(['success' => true, 'data' => $announcement]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Announcement not found.']);
            }
        } elseif (isset($_GET['all_tracer_announcements'])) {
            // Fetch Alumni Notice announcements with optional filters (recipient, date)
            $query = "SELECT * FROM announcement_tbl WHERE title = 'Alumni Notice'";
            if (!empty($_GET['recipient'])) {
                $query .= " AND recipient = :recipient";
            }
            if (!empty($_GET['date'])) {
                $query .= " AND DATE(created_at) = :date";
            }
            $stmt = $pdo->prepare($query);
            if (!empty($_GET['recipient'])) {
                $stmt->bindParam(':recipient', $_GET['recipient']);
            }
            if (!empty($_GET['date'])) {
                $stmt->bindParam(':date', $_GET['date']);
            }
            $stmt->execute();
            $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($announcements) {
                echo json_encode(['success' => true, 'data' => $announcements]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No announcements found.']);
            }
        } else {
            // Default: fetch announcements except Alumni Notice with optional filters
            $recipient = $_GET['recipient'] ?? "";
            $date = $_GET['date'] ?? "";
            $query = "SELECT id, title, subject, message, recipient, created_at FROM announcement_tbl WHERE title != 'Alumni Notice'";
            if (!empty($recipient)) {
                $query .= " AND recipient = :recipient";
            }
            if (!empty($date)) {
                $query .= " AND DATE(created_at) = :date";
            }
            $query .= " ORDER BY created_at DESC";
            $stmt = $pdo->prepare($query);
            if (!empty($recipient)) {
                $stmt->bindParam(':recipient', $recipient);
            }
            if (!empty($date)) {
                $stmt->bindParam(':date', $date);
            }
            try {
                $stmt->execute();
                $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $announcements]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error fetching announcements: ' . $e->getMessage()]);
            }
        }
        break;
    case 'POST':
        $data = $_POST;
        if (isset($data['action']) && $data['action'] === 'delete_announcement') {
            $id = $data['id'] ?? '';
            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'Announcement ID is required.']);
                exit;
            }
            try {
                $stmt = $pdo->prepare("DELETE FROM announcement_tbl WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(['success' => true, 'message' => 'Announcement deleted successfully.']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error deleting announcement: ' . $e->getMessage()]);
            }
            exit;
        } elseif (isset($data['action']) && $data['action'] === 'delete_choice') {
            // Delete choice by choice_id
            $choice_id = $data['choice_id'] ?? '';
            if (empty($choice_id)) {
                echo json_encode(['success' => false, 'message' => 'Choice ID is required.']);
                exit;
            }
            try {
                $stmt = $pdo->prepare("DELETE FROM choices_tracer_tbl WHERE choices_id = ?");
                $stmt->execute([$choice_id]);
                echo json_encode(['success' => true, 'message' => 'Choice deleted successfully.']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error deleting choice: ' . $e->getMessage()]);
            }
            exit;
        } elseif (isset($data['question_desc'])) {
            // Add new question
            $question_desc = trim($data['question_desc']);
            if (empty($question_desc)) {
                echo json_encode(['success' => false, 'message' => 'Question description is required.']);
                exit;
            }
            try {
                $stmt = $pdo->prepare("INSERT INTO quest_tracer_tbl (question_desc) VALUES (?)");
                $stmt->execute([$question_desc]);
                echo json_encode(['success' => true, 'message' => 'Question added successfully.']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error adding question: ' . $e->getMessage()]);
            }
            exit;
        } elseif (isset($data['action']) && $data['action'] === 'update_question') {
            // Update existing question
            $question_id = $data['edit_question_id'] ?? '';
            $question_desc = trim($data['edit_question_desc'] ?? '');
            if (empty($question_id) || empty($question_desc)) {
                echo json_encode(['success' => false, 'message' => 'Question ID and description are required.']);
                exit;
            }
            try {
                $stmt = $pdo->prepare("UPDATE quest_tracer_tbl SET question_desc = ? WHERE question_id = ?");
                $stmt->execute([$question_desc, $question_id]);
                echo json_encode(['success' => true, 'message' => 'Question updated successfully.']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error updating question: ' . $e->getMessage()]);
            }
            exit;
        } elseif (isset($data['action']) && $data['action'] === 'add_choice') {
            // New case for adding a choice
            $question_id = $data['question_id'] ?? '';
            $choice_content = trim($data['choices_content'] ?? '');
            if (empty($question_id) || empty($choice_content)) {
                echo json_encode(['success' => false, 'message' => 'Question ID and choice content are required.']);
                exit;
            }
            try {
                $stmt = $pdo->prepare("INSERT INTO choices_tracer_tbl (question_id, choices_content) VALUES (?, ?)");
                $stmt->execute([$question_id, $choice_content]); // Use $choice_content instead of $choices_content
                echo json_encode(['success' => true, 'message' => 'Choice added successfully.']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error adding choice: ' . $e->getMessage()]);
            }
            exit;
        } else {
            // Create new announcement
            $title = $data['title'] ?? '';
            $subject = $data['subject'] ?? '';
            $message = $data['message'] ?? '';
            $recipient = $data['recipient'] ?? '';
            if (empty($title) || empty($subject) || empty($message) || empty($recipient)) {
                echo json_encode(['success' => false, 'message' => 'All fields are required.']);
                exit;
            }
            try {
                $stmt = $pdo->prepare("INSERT INTO announcement_tbl (title, subject, message, recipient) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $subject, $message, $recipient]);
                echo json_encode(['success' => true, 'message' => 'Announcement created successfully.']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error creating announcement: ' . $e->getMessage()]);
            }
        }
        break;
    case 'PUT':
        // Update existing announcement
        parse_str(file_get_contents('php://input'), $data);
        $id = $data['id'] ?? '';
        $title = $data['title'] ?? '';
        $subject = $data['subject'] ?? '';
        $message = $data['message'] ?? '';
        $recipient = $data['recipient'] ?? '';
        if (empty($id) || empty($title) || empty($subject) || empty($message) || empty($recipient)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit;
        }
        try {
            $stmt = $pdo->prepare("UPDATE announcement_tbl SET title = ?, subject = ?, message = ?, recipient = ? WHERE id = ?");
            $stmt->execute([$title, $subject, $message, $recipient, $id]);
            echo json_encode(['success' => true, 'message' => 'Announcement updated successfully.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error updating announcement: ' . $e->getMessage()]);
        }
        break;
    case 'DELETE':
        // Delete announcement (alternative DELETE support)
        parse_str(file_get_contents("php://input"), $input);
        $id = $input['id'] ?? '';
        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'Announcement ID is required.']);
            exit;
        }
        try {
            $stmt = $pdo->prepare("DELETE FROM announcement_tbl WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Announcement deleted successfully.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error deleting announcement: ' . $e->getMessage()]);
        }
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Unsupported HTTP method']);
        break;
}
