<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once('../includes/sessions.php');
require_once('../includes/db_config.php');  // This file defines $pdo
require_once('../vendor/autoload.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    $recipientType = $_POST['recipient'] ?? '';  // Could be students, teachers, all, or a section name
    if (empty($title) || empty($subject) || empty($message) || empty($recipientType)) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit;
    }
    // Database connection check using PDO
    if (!$pdo) {
        echo json_encode(["success" => false, "message" => "Database connection failed."]);
        exit;
    }
    $mail = new PHPMailer(true);
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'pvpmnhs1998@gmail.com';
        $mail->Password = 'eonhzamfjxjehpem'; // Use App Password for security
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('pvpmnhs1998@gmail.com', 'Pedro V. Panaligan Memorial NHS');
        // Define SQL query based on recipient type
        if ($recipientType === 'students') {
            $query = "SELECT email FROM user_tbl WHERE access_level = 'STUDENT' AND (user_account_access IS NULL OR user_account_access != 'BLOCKED')";
        } elseif ($recipientType === 'teachers') {
            $query = "SELECT email FROM user_tbl WHERE access_level = 'TEACHER' AND (user_account_access IS NULL OR user_account_access != 'BLOCKED')";
        } elseif ($recipientType === 'all') {
            $query = "SELECT email FROM user_tbl WHERE (access_level = 'STUDENT' OR access_level = 'TEACHER') AND (user_account_access IS NULL OR user_account_access != 'BLOCKED')";
        } else {
            // If a section is selected, follow this process:
            // 1. Get the active school year
            $schoolYearQuery = "SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active' LIMIT 1";
            $syStmt = $pdo->query($schoolYearQuery);
            $syRow = $syStmt->fetch();
            if (!$syRow) {
                echo json_encode(["success" => false, "message" => "No active school year found."]);
                exit;
            }
            $syTerm = $syRow['sy_term'];
            // 2. Get LRNs of students in the selected section and active school year
            $lrnQuery = "SELECT lrn FROM student_tbl WHERE school_year = ? AND section = ?";
            $lrnStmt = $pdo->prepare($lrnQuery);
            $lrnStmt->execute([$syTerm, $recipientType]);
            $lrns = [];
            while ($lrnRow = $lrnStmt->fetch()) {
                $lrns[] = $lrnRow['lrn'];
            }
            if (empty($lrns)) {
                echo json_encode(["success" => false, "message" => "No students found in the selected section."]);
                exit;
            }
            // 3. Get emails from user_tbl where Identifier matches LRNs
            $placeholders = implode(',', array_fill(0, count($lrns), '?'));  // Generate placeholders (?,?,?,...)
            $query = "SELECT email FROM user_tbl WHERE Identifier IN ($placeholders) AND (user_account_access IS NULL OR user_account_access != 'BLOCKED')";
        }
        // Execute query and fetch emails
        $stmt = $pdo->prepare($query);
        if (!empty($lrns)) {
            $stmt->execute($lrns);  // Use LRNs as parameters if fetching section-based emails
        } else {
            $stmt->execute();
        }
        $recipientCount = 0;
        while ($row = $stmt->fetch()) {
            if (!empty($row['email'])) {
                $mail->addAddress($row['email']);
                $recipientCount++;
            }
        }
        if ($recipientCount === 0) {
            echo json_encode(["success" => false, "message" => "No recipients found."]);
            exit;
        }
        // Email content
        $mail->isHTML(true);
        $mail->Subject = $subject;
      $mail->Body = '
<!DOCTYPE html>
<html>
<head>
  <style>
    body {
      font-family: Arial, sans-serif;
      line-height: 1.6;
      margin: 0;
      padding: 0;
      background-color: #f5f5f5;
    }
    .email-container {
      max-width: 600px;
      margin: auto;
      background: #ffffff;
      border: 1px solid #ddd;
    }
    .email-header img,
    .email-footer img {
      width: 100%;
      height: auto;
      display: block;
    }
    .email-body {
      padding: 20px;
      color: #333;
    }
    .email-footer-text {
      background-color: #f1f1f1;
      color: #666;
      padding: 15px;
      text-align: center;
      font-size: 12px;
    }
  </style>
</head>
<body>
  <div class="email-container">
    <div class="email-header">
      <img src="https://sfsuite.fwh.is/dist/img/school_header.png" alt="School Header">
    </div>
    <div class="email-body">
      <h3>' . htmlspecialchars($title) . '</h3>
      <p>' . nl2br(htmlspecialchars($message)) . '</p>
    </div>
    <div class="email-footer">
      <img src="https://sfsuite.fwh.is/dist/img/school_footer.png" alt="School Footer">
    </div>
    <div class="email-footer-text">
      This message was sent by the school admin. Please do not reply directly to this email.<br>
      &copy; ' . date('Y') . ' Pedro V. Panaligan M.N.H.S.
    </div>
  </div>
</body>
</html>';

        $mail->AltBody = "$title\n\n$message\n\nThis message was sent by Pedro V. Panaligan M.N.H.S.";
        // Send email
        if ($mail->send()) {
            // Insert into announcement_tbl
            $insertStmt = $pdo->prepare("INSERT INTO announcement_tbl (title, subject, message, recipient) VALUES (?, ?, ?, ?)");
            $insertStmt->execute([$title, $subject, $message, $recipientType]);
            echo json_encode(["success" => true, "message" => "Notification sent and saved successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to send notification."]);
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Mailer Error: " . $mail->ErrorInfo]);
    }
}
?>