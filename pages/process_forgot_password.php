<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// process_forgot_password.php
// Include the Composer autoloader
require_once('../vendor/autoload.php');
// Include your database connection file
require_once('../includes/db_config.php');  // Make sure you have this file for DB connection
require_once('../includes/session_config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Get the username from POST data
  $username = $_POST['username'];
  // Validate the username
  if (empty($username)) {
    echo json_encode(['success' => false, 'message' => 'Username is required.']);
    exit();
  }
  // Check if the username exists in the user_tbl
  $stmt = $pdo->prepare("SELECT * FROM user_tbl WHERE username = :username");
  $stmt->execute(['username' => $username]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Username not found.']);
    exit();
  }
  // Check if the email exists in the user data
  if (!isset($user['email'])) {
    echo json_encode(['success' => false, 'message' => 'Email address not found for this user.']);
    exit();
  }
  // Generate a random password (e.g., 12 characters long, including letters and numbers)
  $newPassword = generateRandomPassword(12);
  // Hash the new password
  $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
  // Update the password in the database
  $updateStmt = $pdo->prepare("UPDATE user_tbl SET password = :password WHERE username = :username");
  $updateStmt->execute(['password' => $hashedPassword, 'username' => $username]);
  // Send an email to the user with the new password
  $mail = new PHPMailer(true); // Enable exceptions
  try {
    // SMTP Configuration
    $mail->isSMTP(); // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
    $mail->SMTPAuth = true; // Enable SMTP authentication
    $mail->Username = 'pvpmnhs1998@gmail.com'; // Your Gmail address
    $mail->Password = 'eonhzamfjxjehpem'; // Your Gmail password or App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
    $mail->Port = 587;
    // Sender and recipient settings
    $mail->setFrom('pvpmnhs1998@gmail.com', 'Pedro V. Panaligan Memorial National High School');
    $mail->addAddress($user['email'], $user['username']); // Send to the user's email
    // Email content
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = 'Your New Password';
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
      padding: 10px;
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
      <p>Hello <strong>' . htmlspecialchars($user['username']) . '</strong>,</p>
      <p>Your password has been successfully reset. Your new password is:</p>
      <p style="font-size:18px;"><strong>' . htmlspecialchars($newPassword) . '</strong></p>
      <p>Please log in and change your password immediately for security reasons.</p>
    </div>
    <div class="email-footer">
      <img src="https://sfsuite.fwh.is/dist/img/school_footer.png" alt="School Footer">
    </div>
    <div class="email-footer-text">
      &copy; ' . date('Y') . ' Pedro V. Panaligan M.N.H.S. All rights reserved.
    </div>
  </div>
</body>
</html>';
    $mail->AltBody = "Hello " . $user['username'] . ",\n\nYour password has been reset. Your new password is: $newPassword\n\nPlease change it after logging in.\n\nPedro V. Panaligan Memorial National High School";
    // Send the email
    if (!$mail->send()) {
      echo json_encode(['success' => false, 'message' => 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo]);
    } else {
      echo json_encode(['success' => true, 'message' => 'Your password has been reset. Please check your email for the new password.']);
    }
  } catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
  }
}
// Function to generate a random password
function generateRandomPassword($length = 12)
{
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
  $charactersLength = strlen($characters);
  $randomPassword = '';
  for ($i = 0; $i < $length; $i++) {
    $randomPassword .= $characters[rand(0, $charactersLength - 1)];
  }
  return $randomPassword;
}
?>