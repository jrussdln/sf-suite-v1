<?php
require_once('../includes/sessions.php');
require_once('../includes/db_config.php');
require_once('../includes/functions/func_users.php');
header('Content-type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        if (isset($_GET['user_id'])) { //MARKED
            $user_id = urldecode($_GET['user_id']);
            echo json_encode(['data' => get_user_by_id($pdo, $user_id)]);
            exit;
        } elseif (isset($_GET['users_table'])) { //USER TABLE
            $userRole = $_GET['userRole'] ?? null;
            $userStatus = $_GET['userStatus'] ?? null;
            $userStatus1 = $_GET['userStatus1'] ?? null;
            echo json_encode(['data' => get_all_usersinfo($pdo, $userRole, $userStatus, $userStatus1)]);
            exit;
        } elseif (isset($_GET['get_user_data'])) {//EDIT USER INFO
            $identifier = $_GET['identifier'];
            $userData = get_user_data($pdo, $identifier); // Implement this function to fetch user data
            if ($userData) {
                echo json_encode(['success' => true, 'data' => $userData]);
            } else {
                echo json_encode(['success' => false, 'message' => 'User not found.']);
            }
            exit;
        } elseif (isset($_GET['get_user_profile'])) {//USER PROFILE
            $identifier = $_GET['identifier'];
            $userData = get_user_data($pdo, $identifier); // Implement this function to fetch user data
            if ($userData) {
                echo json_encode(['success' => true, 'data' => $userData]);
            } else {
                echo json_encode(['success' => false, 'message' => 'User not found.']);
            }
            exit;
        } elseif (isset($_GET['get_username'])) {//MARKED
            $identifier = $_GET['identifier'];
            $userData = get_user_data($pdo, $identifier); // Implement this function to fetch user data
            if ($userData) {
                echo json_encode(['success' => true, 'data' => $userData]);
            } else {
                echo json_encode(['success' => false, 'message' => 'User not found.']);
            }
            exit;
        }
        break;
    case 'PUT':
        parse_str(file_get_contents('php://input'), $data);
        break;
    case 'POST':
        // Get the posted data
        $data = $_POST;
        if (isset($_GET['add_users'])) {
            // Collect form data
            $data = [
                'identifier' => $_POST["au_identifier"] ?? '',
                'user_lname' => $_POST["au_user_lname"] ?? '',
                'user_fname' => $_POST["au_user_fname"] ?? '',
                'user_mname' => $_POST["au_user_mname"] ?? '',
                'user_ename' => $_POST["au_user_ename"] ?? '',
                'gender' => $_POST["au_gender"] ?? '',
                'birthdate' => $_POST["au_birthdate"] ?? '',
                'role' => $_POST["au_role"] ?? '',
                'email' => $_POST["au_email"] ?? '',
                'username' => $_POST["au_username"] ?? '',
                'password' => $_POST["au_password"] ?? ''
            ];
            // Call the function to add the user
            $response = add_users($pdo, $data);
            // Return JSON response
            echo json_encode($response);
            exit;
        } elseif (isset($data['LRN'])) { // Verify LRN existence
            $LRN = $data['LRN'];
            $exists = verifyMatch($pdo, $LRN);
            echo json_encode(['exists' => $exists]);
            exit;
        } elseif (isset($_POST['action']) && $_POST['action'] === 'block_user' && isset($_POST['userId'])) {
            blockUser($pdo, $_POST['userId']);
        } elseif (isset($_POST['action']) && $_POST['action'] === 'unblock_user' && isset($_POST['userId'])) {
            unblockUser($pdo, $_POST['userId']);
        } elseif (isset($data['uap_identifier']) && !empty($data['uap_identifier'])) {
            $result = edit_user($pdo, $data);
            echo json_encode([
                'success' => $result['success'],
                'message' => $result['message']
            ]);
            exit;
        } elseif (isset($data['a_identifier']) && !empty($data['a_identifier'])) {
            $result = edit_user_profile($pdo, $data);
            echo json_encode([
                'success' => $result['success'],
                'message' => $result['message']
            ]);
            exit;
        } elseif (isset($_POST['new_password'], $_POST['retype_password'], $_POST['Identifier'])) {
            $newPassword = trim($_POST['new_password']);
            $retypePassword = trim($_POST['retype_password']);
            $identifier = trim($_POST['Identifier']);
            // Check if passwords match
            if ($newPassword !== $retypePassword) {
                echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
                exit;
            }
            // Continue with password update
            $data = [
                'new_password' => $newPassword,
                'Identifier' => $identifier,
            ];
            $result = edit_user_password($pdo, $data);
            echo json_encode([
                'success' => $result['success'],
                'message' => $result['message'],
            ]);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
            exit;
        }
    case 'DELETE':
        // Check if 'delete_reg' is set
        if (isset($_GET['delete_user'])) {
            $identifier = $_GET['identifier']; // Get the identifier (perhaps username or ID)
            $result = delete_user($pdo, $identifier); // Call the delete_user function (with correct identifier)
            if ($result['success']) {
                echo json_encode(['success' => true, 'message' => 'User account removed successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => $result['message']]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Identifier or LRN is required to unregister user.']);
        }
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        break;
}
?>