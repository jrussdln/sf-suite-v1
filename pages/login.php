<?php
require_once('../includes/session_config.php');
// Do NOT start a session immediately
if (!empty($_POST['username']) && !empty($_POST['password'])) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>SF-Suite: PVPMNHS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="../dist/img/school-logo.png">
    <!-- Combined CSS Dependencies -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="../plugins/sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../dist/css/modal.css">
    <link rel="stylesheet" href="../dist/css/login.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body>
    <div class="container" id="container">
        <!-- Recover Account Section -->
        <div class="form-container sign-up-container">
            <form action="#" method="POST" id="recoverForm">
                <h1>FORGOT PASSWORD</h1>
                <span>Username is required for account recovery</span>
                <div id="recoverStatus"></div>
                <input type="text" id="fp_username" placeholder="Username" required />
                <button type="button" id="verifyButton">Submit</button>
            </form>
        </div>
        <!-- Desktop Login Section -->
        <div class="form-container sign-in-container">
            <form id="formLoginDesktop" action="includes/process_login.php" method="POST">
                <div class="social-container">
                    <a href="#" class="social">
                        <img src="../dist/img/3.png" alt="Facebook Logo" style="height: 55px;" />
                    </a>
                </div>
                <h1>LOG IN</h1>
                <span>PVPMNHS | SF-SUITE</span>
                <div id="loginStatus"></div>
                <div class="input-container">
                    <span class="input-icon">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" name="username" placeholder="Username" required />
                </div>
                <div class="input-container">
                    <span class="input-icon">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" placeholder="Password" required id="passwordInputDesktop" />
                    <span class="toggle-password" id="togglePasswordIconDesktop">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <div class="checkbox-container">
                    <input type="checkbox" id="agreeCheckboxDesktop" required>
                    <p class="priv-label">Do you agree to the Privacy Consent Notice?</p>
                </div>
                <a href="#" id="signUp" class="forgot-password">Forgot your password?</a>
                <button type="submit">Log in</button>
            </form>
        </div>
        <!-- Overlay Section -->
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1 style="color: white;">Welcome Back!</h1>
                    <p>Stay connected with us! Log in to access your account and continue your journey.</p>
                    <button class="ghost" id="signIn">Log In</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <div class="exclamation-container">
                        <a href="#" class="exclamation">
                            <i class="fas fa-exclamation"></i>
                        </a>
                    </div>
                    <p>PRIVACY CONSENT NOTICE</p>
                    <p class="privacy-consent">
                        By using this System, you agree that the data/information submitted shall be used solely for
                        PVPMNHS report monitoring purposes.
                        We may likewise disclose establishment's or your personal information to the extent that we are
                        required to do so by the Data Privacy Act of 2012.
                    </p>
                    <p class="privacy-consent">
                        As a general rule, we may only keep your information until such time that we have attained the
                        purpose by which we collect them.
                    </p>
                    <p class="privacy-consent">
                        Under the foregoing circumstances and to the extent permissible by applicable law, you agree not
                        to take any action against the PVPMNHS for the disclosure and retention of your information.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="container1" id="container1">
        <!-- Mobile Login Section -->
        <div class="form-container sign-in-container">
            <form id="formLoginMobile" action="includes/process_login.php" method="POST">
                <div class="social-container">
                    <a href="#" class="social">
                        <img src="../dist/img/3.png" alt="Facebook Logo" style="height: 55px;" />
                    </a>
                </div>
                <h1>LOG IN</h1>
                <span>PVPMNHS | SF-SUITE</span>
                <div id="loginStatusMobile"></div>
                <div class="input-container">
                    <span class="input-icon">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" name="username" placeholder="Username" required />
                </div>
                <div class="input-container">
                    <span class="input-icon">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" placeholder="Password" required id="passwordInputMobile" />
                    <span class="toggle-password" id="togglePasswordIconMobile">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <div class="button-row">
                    <button type="button" class="btn btn-link forgot-password" data-toggle="modal"
                        data-target="#forgotPasswordModal">
                        Forgot
                    </button>
                    <button type="submit">Log in</button>
                </div>
            </form>
        </div>
    </div>
    <!-- jQuery and JS Dependencies -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="../dist/js/adminlte.min.js"></script>
    <script>
        $(document).ready(function () {
            // Bind login handler for both desktop and mobile
            function bindLogin(formId, checkboxId, statusId, passwordInputId, toggleIconId) {
                $(formId).on('submit', function (e) {
                    e.preventDefault();
                    // On desktop, require checkbox checked before submitting
                    if (window.innerWidth > 768) {
                        if (!$(checkboxId).prop('checked')) {
                            $(statusId).html("<span style='color: red;'>You must agree to the Privacy Consent Notice to log in.</span>");
                            return;
                        }
                    }
                    $(statusId).html("");
                    $.ajax({
                        url: '../includes/process_login.php',
                        type: 'POST',
                        data: $(this).serialize(),
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                $(statusId).html("<span style='color: green;'><i class='fas fa-spinner fa-spin'></i> Loading, please wait...</span>");
                                if (window.innerWidth <= 768) {
                                    // Show Privacy Consent Modal on mobile
                                    $('#privacyConsentModal').modal({
                                        backdrop: 'static',
                                        keyboard: false
                                    });
                                } else {
                                    // Redirect after short delay on desktop
                                    setTimeout(function () {
                                        window.location.href = '../pages/main_dashboard.php';
                                    }, 3000);
                                }
                            } else {
                                $(statusId).html("<span style='color: red;'>" + response.message + "</span>");
                            }
                        },
                        error: function () {
                            $(statusId).html("<span style='color: red;'>An unexpected error occurred. Please try again later.</span>");
                        }
                    });
                });
                // Toggle password visibility
                $(toggleIconId).on('click', function () {
                    const passwordInput = $(passwordInputId);
                    const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
                    passwordInput.attr('type', type);
                    $(this).find('i').toggleClass('fa-eye fa-eye-slash');
                });
            }
            // Initialize logins
            bindLogin('#formLoginDesktop', '#agreeCheckboxDesktop', '#loginStatus', '#passwordInputDesktop', '#togglePasswordIconDesktop');
            bindLogin('#formLoginMobile', '#agreeCheckboxMobile', '#loginStatusMobile', '#passwordInputMobile', '#togglePasswordIconMobile');
            // Privacy Consent Modal Checkbox Logic
            // Enable "Agree & Continue" only if checkbox checked
            $('#agreeCheckboxMobile').on('change', function () {
                $('#privacyConsentAgreeBtn').prop('disabled', !this.checked);
            });
            // On clicking Agree & Continue, close modal and redirect
            $('#privacyConsentAgreeBtn').on('click', function () {
                $('#privacyConsentModal').modal('hide');
            });
            // Prevent modal from closing if checkbox not checked when clicking X
            $('#privacyModalCloseX').on('click', function (e) {
                if (!$('#agreeCheckboxMobile').is(':checked')) {
                    e.preventDefault();
                    alert('You must agree to the Privacy Consent Notice to continue.');
                }
            });
            // Prevent modal from closing by backdrop click or ESC unless checkbox checked
            $('#privacyConsentModal').on('hide.bs.modal', function (e) {
                if (!$('#agreeCheckboxMobile').is(':checked')) {
                    e.preventDefault();
                    alert('You must agree to the Privacy Consent Notice to continue.');
                }
            });
            // When modal is hidden after agreeing, redirect to dashboard
            $('#privacyConsentModal').on('hidden.bs.modal', function () {
                if ($('#agreeCheckboxMobile').is(':checked')) {
                    window.location.href = '../pages/main_dashboard.php';
                }
            });
            // AJAX Forgot Password logic (kept as is)
            $('#verifyButton').on('click', function () {
                const username = $('#fp_username').val().trim();
                if (!username) {
                    $('#recoverStatus').html("<span style='color: red;'>Please enter a username.</span>");
                    return;
                }
                $('#recoverStatus').html("<span style='color: green;'><i class='fas fa-spinner fa-spin'></i> Processing...</span>");
                $.ajax({
                    url: 'process_forgot_password.php',
                    type: 'POST',
                    data: { username: username },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            $('#recoverStatus').html("<span style='color: green;'>" + response.message + "</span>");
                        } else {
                            $('#recoverStatus').html("<span style='color: red;'>" + response.message + "</span>");
                        }
                    },
                    error: function () {
                        $('#recoverStatus').html("<span style='color: red;'>An unexpected error occurred. Please try again later.</span>");
                    }
                });
            });
            // Toggle forgot password and login panels
            $('#signUp').on('click', function () {
                $('#container').addClass("right-panel-active");
            });
            $('#signIn').on('click', function () {
                $('#container').removeClass("right-panel-active");
            });
            // Forgot Password Modal AJAX
            $('#verifyButtonModal').on('click', function () {
                const username = $('#fp_username_modal').val().trim();
                if (!username) {
                    $('#recoverStatusModal').html("<span class='text-danger'>Please enter a username.</span>");
                    return;
                }
                $('#recoverStatusModal').html("<span class='text-success'><i class='fas fa-spinner fa-spin'></i> Processing...</span>");
                $.ajax({
                    url: 'process_forgot_password.php',
                    type: 'POST',
                    data: { username: username },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            $('#recoverStatusModal').html("<span class='text-success'>" + response.message + "</span>");
                        } else {
                            $('#recoverStatusModal').html("<span class='text-danger'>" + response.message + "</span>");
                        }
                    },
                    error: function () {
                        $('#recoverStatusModal').html("<span class='text-danger'>An unexpected error occurred. Please try again later.</span>");
                    }
                });
            });
        });
    </script>
</body>
<form id="recoverFormModal" action="#" method="post">
    <div id="forgotPasswordModal" class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="forgotPasswordModalLabel">Forgot Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span class="d-block mb-2">Username is required for account recovery</span>
                    <div id="recoverStatusModal" class="mb-2"></div>
                    <div class="form-group">
                        <input type="text" id="fp_username_modal" name="username" class="form-control"
                            placeholder="Username" required />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="verifyButtonModal" class="btn btn-primary">Submit</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- Privacy Consent Notice Modal -->
<div id="privacyConsentModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="privacyConsentModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="privacyConsentModalLabel">Privacy Consent Notice</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="privacyModalCloseX">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>PRIVACY CONSENT NOTICE</strong></p>
                <p class="privacy-consent" style="color: black;">
                    By using this System, you agree that the data/information submitted shall be used solely for
                    PVPMNHS report monitoring purposes.
                    We may likewise disclose establishment's or your personal information to the extent that we are
                    required to do so by the Data Privacy Act of 2012.
                </p>
                <p class="privacy-consent" style="color: black;">
                    As a general rule, we may only keep your information until such time that we have attained the
                    purpose by which we collect them.
                </p>
                <p class="privacy-consent" style="color: black;">
                    Under the foregoing circumstances and to the extent permissible by applicable law, you agree not
                    to take any action against the PVPMNHS for the disclosure and retention of your information.
                </p>
                <div class="checkbox-container mt-3">
                    <input type="checkbox" id="agreeCheckboxMobile" />
                    <label for="agreeCheckboxMobile" class="priv-label">Do you agree to the Privacy Consent
                        Notice?</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="privacyConsentAgreeBtn" class="btn btn-primary" disabled>Agree &
                    Continue</button>
            </div>
        </div>
    </div>
</div>
</html>