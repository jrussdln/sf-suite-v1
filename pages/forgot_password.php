<?php
session_start();
require_once('../includes/session_config.php');
if (isset($_SESSION[$session_id])) {
  // Redirect to the main dashboard if the user is already logged in
  header("Location: ../pages/main_dashboard.php");
  exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SF-Suite: Account Recovery</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="../dist/img/school-logo.png">
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="../plugins/sweetalert2/sweetalert2.min.css">
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition login-page" style="
        background-image: url('../dist/img/school-bgr.png'); 
        background-repeat: no-repeat; 
        background-size: cover; 
        background-position: center bottom; 
        height: 100vh; 
        margin: 0; 
        display: flex; 
        justify-content: center; 
        align-items: center;">
  <div class="login-box" style="width: 400px;">
    <!-- Logo Section -->
    <div class="login-logo" style="
      text-align: center; 
      margin-bottom: 20px; 
      width: 100%; 
      display: flex; 
      flex-direction: column; 
      align-items: center;">
      <img src="../dist/img/3.png" alt="" height="92" style="margin-bottom: 10px;">
    </div>
    <!-- Forgot Password Form -->
    <div style="
     background: #fff; 
     border-radius: 7px; 
     box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); 
     padding: 30px; 
     ">
      <h2 style="text-align: center; font-size: 22px; color: #333; font-weight: bold; margin-bottom: 20px;">Account
        Recovery</h2>
      <div style="margin-bottom: 15px;">
        <input type="text" id="username" name="username" placeholder="Enter your username" required style="
             width: 100%; 
             padding: 10px; 
             border: 1px solid #ccc; 
             border-radius: 5px; 
             box-sizing: border-box;" />
      </div>
      <div>
        <button type="button" id="verifyButton" style="
             width: 100%; 
             background: #6a11cb; 
             color: white; 
             border: none; 
             font-size: 16px; 
             font-weight: bold; 
             padding: 10px; 
             border-radius: 5px; 
             transition: background 0.3s ease; 
             cursor: pointer;" onmouseover="this.style.background='#2575fc';"
          onmouseout="this.style.background='#6a11cb';">
          Verify
        </button>
      </div>
      <!-- Back Button -->
      <div style="margin-top: 10px;">
        <button type="button" onclick="window.location.href='Login.php'" style="
             width: 100%; 
             background: #ccc; 
             color: #333; 
             border: none; 
             font-size: 16px; 
             font-weight: bold; 
             padding: 10px; 
             border-radius: 5px; 
             transition: background 0.3s ease; 
             cursor: pointer;" onmouseover=" this.style.background='#aaa';" onmouseout="this.style.background='#ccc';">
          Back to Login
        </button>
      </div>
      <div style="text-align: center; margin-top: 20px;">
        <p style="font-size: 14px; color: #666;">&copy; <?php echo date("Y"); ?> CCC System Development Team</p>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    $(document).ready(function () {
      $('#verifyButton').on('click', function () {
        const username = $('#username').val().trim(); // Get the username input
        if (!username) {
          Swal.fire({
            title: "Error!",
            text: "Please enter a username.",
            icon: "error",
          });
          return; // Exit if the username is empty
        }
        // Send AJAX request to the server
        $.ajax({
          url: 'process_forgot_password.php', // URL to your PHP script
          type: 'POST',
          data: { username: username },
          dataType: 'json',
          success: function (response) {
            if (response.success) {
              Swal.fire({
                title: "Success!",
                text: response.message,
                icon: "success",
              }).then(() => {
              });
            } else {
              Swal.fire({
                title: "Error!",
                text: response.message,
                icon: "error",
              });
            }
          },
          error: function (xhr, status, error) {
            Swal.fire({
              title: "Error!",
              text: "An unexpected error occurred. Please try again later.",
              icon: "error",
            });
          }
        });
      });
    });
  </script>
</body>
</html>