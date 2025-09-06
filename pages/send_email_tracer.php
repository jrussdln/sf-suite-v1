<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once('../includes/sessions.php');
require_once('../includes/db_config.php');  // This file defines $pdo
require_once('../vendor/autoload.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Retrieve form fields
  $title = $_POST['title'] ?? '';
  $subject = $_POST['subject'] ?? '';
  $message = $_POST['message'] ?? '';
  $schoolYear = $_POST['school_year'] ?? '';
  // Validate input
  if (empty($title) || empty($subject) || empty($schoolYear)) {
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit;
  }
  // Check the database connection using PDO
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
    // Fetch students with emails, names, and LRN for the given school year and grade 12
    $query = "
            SELECT u.email, s.name, s.lrn
            FROM user_tbl u
            JOIN student_tbl s ON u.identifier = s.lrn
            WHERE u.access_level = 'STUDENT'
              AND s.school_year = :school_year
              AND s.grade_level = 12
        ";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':school_year', $schoolYear, PDO::PARAM_STR);
    $stmt->execute();
    if (!$stmt) {
      echo json_encode(["success" => false, "message" => "Database error while fetching recipients."]);
      exit;
    }
    function makeLinksClickable($text)
    {
      // Convert URLs into clickable links
      return preg_replace(
        '~(https?://[^\s]+)~',
        '<a href="$1" target="_blank">$1</a>',
        nl2br(htmlspecialchars($text))
      );
    }
    $formattedMessage = makeLinksClickable($message);
    $recipientCount = 0;
    // Loop through each recipient and send personalized email
    while ($row = $stmt->fetch()) {
      if (!empty($row['email'])) {
        $mail->clearAddresses();  // Clear previous recipient
        $mail->addAddress($row['email']);
        $studentName = htmlspecialchars($row['name']);
        $studentLrn = htmlspecialchars($row['lrn']);
        $personalizedGreeting = "<p>Hi {$studentName}!</p><p>LRN: {$studentLrn}</p>";
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = '
<!DOCTYPE html>
<html>
<head>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0; padding: 0;
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
  </style>
</head>
<body>
  <div class="email-container">
    <div class="email-header">
      <img src="https://sfsuite.fwh.is/dist/img/school_header.png" alt="School Header">
    </div>
    <div class="email-body">
      ' . $personalizedGreeting . '
      <h3>' . htmlspecialchars($title) . '</h3>
      <p>' . $formattedMessage . '</p>
      <p>To proceed, please click the link below to fill out the required form:</p>
      <p><a href="https://sfsuite.fwh.is/pages/form.php" style="color: #2E86C1; text-decoration: underline;" target="_blank">Click here to open the form</a></p>
    </div>
    <div class="email-footer">
      <img src="https://sfsuite.fwh.is/dist/img/school_footer.png" alt="School Footer">
    </div>
  </div>
</body>
</html>';
        $mail->AltBody = "Hi {$studentName}!\nLRN: {$studentLrn}\n\n{$title}\n\n{$message}\n\nSent to alumni of school year {$schoolYear}.";
        if ($mail->send()) {
          $recipientCount++;
        } else {
          // Optional: log failure for this recipient
        }
      }
    }
    if ($recipientCount > 0) {
      // Save announcement after successful sends
      $insertStmt = $pdo->prepare("INSERT INTO announcement_tbl (title, subject, message, recipient) VALUES (?, ?, ?, ?)");
      $insertStmt->execute([$title, $subject, $message, $schoolYear]);
      echo json_encode(["success" => true, "message" => "Notification sent to {$recipientCount} recipients and saved successfully."]);
    } else {
      echo json_encode(["success" => false, "message" => "No recipients found or failed to send emails."]);
    }
  } catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Mailer Error: " . $mail->ErrorInfo]);
  }
}
?>