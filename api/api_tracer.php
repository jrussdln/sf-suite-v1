<?php
require_once('../includes/sessions.php');
require_once('../includes/db_config.php');
header('Content-type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['get_section_attendance'])) {
           
        }
        break;

    case 'POST':
        $data = $_POST;
        error_log(print_r($data, true)); // For debugging

        // === PERSONAL INFO UPDATE ===
        if (isset($data['uap_identifier'])) {
            $identifier = cleanIdentifier($data['uap_identifier']);
            $email = $data['uap_user_email'] ?? null;
            $fname = $data['uap_user_fname'] ?? '';
            $mname = $data['uap_user_mname'] ?? '';
            $lname = $data['uap_user_lname'] ?? '';
            $ename = $data['uap_user_ename'] ?? '';
            $gender = $data['uap_gender'] ?? '';
            $birthdate = $data['uap_birthdate'] ?? null;

            try {
                $sql = "UPDATE user_tbl 
                        SET email = :email,
                            UserFName = :fname,
                            UserMName = :mname,
                            UserLName = :lname,
                            UserEName = :ename,
                            Gender = :gender,
                            BirthDate = :birthdate
                        WHERE Identifier = :identifier";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':email' => $email,
                    ':fname' => $fname,
                    ':mname' => $mname,
                    ':lname' => $lname,
                    ':ename' => $ename,
                    ':gender' => $gender,
                    ':birthdate' => $birthdate,
                    ':identifier' => $identifier
                ]);

                if ($stmt->rowCount() > 0) {
                    echo json_encode(['success' => true, 'message' => 'Information updated successfully!']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No changes made or identifier not found.']);
                }
            } catch (PDOException $e) {
                error_log('Update Error: ' . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Error updating information: ' . $e->getMessage()]);
            }
            exit;
        }

        echo json_encode(['success' => false, 'message' => 'Identifier is required.']);
        exit;

    case 'PUT':
        parse_str(file_get_contents('php://input'), $data);
        // Handle PUT request if needed
        break;

    case 'DELETE':
        parse_str(file_get_contents("php://input"), $deleteData);
        // Handle DELETE request if needed
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        break;
}

// Helper: clean identifier
function cleanIdentifier($id)
{
    return strtoupper(trim($id));
}
?>