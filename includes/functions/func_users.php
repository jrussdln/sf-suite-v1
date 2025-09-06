<?php

function get_user_data($pdo, $identifier) {
    try {
        $sql = "SELECT * FROM user_tbl WHERE Identifier = :identifier"; // Adjust the table name if necessary
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':identifier', $identifier);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the user data as an associative array
    } catch (PDOException $e) {
        return false; // Handle error appropriately
    }
}

function add_users($pdo, $data) {
    try {
        // Extract the data array into variables
        extract($data);

        // Validate required fields
        if (empty($identifier) || empty($user_lname) || empty($user_fname) || empty($username) || empty($password)) {
            throw new Exception("Missing required fields.");
        }

        // Prepare the SQL query with access_level
        $query = "INSERT INTO user_tbl 
                  (Identifier, UserLName, UserFName, UserMName, UserEName, Gender, BirthDate, Role, email, username, password, access_level) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Begin the transaction
        $pdo->beginTransaction();

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Set the access_level equal to the Role
        $access_level = $role; // Assuming Role is a valid column

        // Prepare and execute the statement
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            $identifier, $user_lname, $user_fname, $user_mname, $user_ename,
            $gender, $birthdate, $role, $email, $username, $hashed_password, $access_level
        ]);

        // Commit the transaction
        $pdo->commit();

        return ['status' => 'success', 'message' => 'User added successfully.'];
    } catch (Exception $e) {
        // Rollback the transaction on error
        $pdo->rollBack();
        return ['status' => 'error', 'message' => 'Failed to add user: ' . $e->getMessage()];
    }
}



function edit_user($pdo, $data) {
    try {
        // Start transaction
        $pdo->beginTransaction();

        // Update user_tbl
        $sql1 = "UPDATE user_tbl SET
                    UserFName = :UserFName,
                    UserLName = :UserLName,
                    UserMName = :UserMName,
                    UserEName = :UserEName,
                    Gender = :Gender,
                    BirthDate = :BirthDate,
                    Role = :Role,
                    Email = :Email,
                    username = :username,
                    updated_at = NOW()
                WHERE Identifier = :Identifier";

        $stmt1 = $pdo->prepare($sql1);
        $stmt1->execute([
            ':Identifier' => $data['uap_identifier'],
            ':UserFName' => $data['uap_user_fname'],
            ':UserLName' => $data['uap_user_lname'],
            ':UserMName' => $data['uap_user_mname'],
            ':UserEName' => $data['uap_user_ename'],
            ':Gender' => $data['uap_gender'],
            ':BirthDate' => $data['uap_birthdate'],
            ':Role' => $data['uap_role'],
            ':Email' => $data['uap_user_email'],
            ':username' => $data['uap_username']
        ]);

        // Update school_per_tbl using Identifier as EmpNo
        $sql2 = "UPDATE school_per_tbl SET
                    EmpFName = :UserFName,
                    EmpLName = :UserLName,
                    EmpMName = :UserMName,
                    EmpEName = :UserEName,
                    Sex = :Gender,
                    BirthDate = :BirthDate,
                    Email = :Email,
                    updated_at = NOW()
                WHERE EmpNo = :Identifier";

        $stmt2 = $pdo->prepare($sql2);
        $stmt2->execute([
            ':Identifier' => $data['uap_identifier'],
            ':UserFName' => $data['uap_user_fname'],
            ':UserLName' => $data['uap_user_lname'],
            ':UserMName' => $data['uap_user_mname'],
            ':UserEName' => $data['uap_user_ename'],
            ':Gender' => $data['uap_gender'],
            ':BirthDate' => $data['uap_birthdate'],
            ':Email' => $data['uap_user_email']
        ]);

        // Commit transaction if everything is successful
        $pdo->commit();

        return ['success' => true, 'message' => 'User Profile updated successfully.'];
    } catch (PDOException $e) {
        // Rollback if an error occurs
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

function edit_user_profile($pdo, $data) {
    try {
        // Start transaction
        $pdo->beginTransaction();

        // Update user_tbl
        $sql1 = "UPDATE user_tbl SET
                    UserFName = :UserFName,
                    UserLName = :UserLName,
                    UserMName = :UserMName,
                    UserEName = :UserEName,
                    Gender = :Gender,
                    BirthDate = :BirthDate,
                    Role = :Role,
                    Email = :Email,
                    username = :username,
                    updated_at = NOW()
                WHERE Identifier = :Identifier";

        $stmt1 = $pdo->prepare($sql1);
        $stmt1->execute([
            ':Identifier' => $data['a_identifier'],
            ':UserFName' => $data['a_user_fname'],
            ':UserLName' => $data['a_user_lname'],
            ':UserMName' => $data['a_user_mname'],
            ':UserEName' => $data['a_user_ename'],
            ':Gender' => $data['a_gender'],
            ':BirthDate' => $data['a_birthdate'],
            ':Role' => $data['a_role'],
            ':Email' => $data['a_user_email'],
            ':username' => $data['a_username']
        ]);

        // Update school_per_tbl using Identifier as EmpNo
        $sql2 = "UPDATE school_per_tbl SET
                    EmpFName = :UserFName,
                    EmpLName = :UserLName,
                    EmpMName = :UserMName,
                    EmpEName = :UserEName,
                    Sex = :Gender,
                    BirthDate = :BirthDate,
                    Email = :Email,
                    updated_at = NOW()
                WHERE EmpNo = :Identifier";

        $stmt2 = $pdo->prepare($sql2);
        $stmt2->execute([
            ':Identifier' => $data['a_identifier'],
            ':UserFName' => $data['a_user_fname'],
            ':UserLName' => $data['a_user_lname'],
            ':UserMName' => $data['a_user_mname'],
            ':UserEName' => $data['a_user_ename'],
            ':Gender' => $data['a_gender'],
            ':BirthDate' => $data['a_birthdate'],
            ':Email' => $data['a_user_email']
        ]);

        // Commit transaction if everything is successful
        $pdo->commit();

        return ['success' => true, 'message' => 'User Profile updated successfully.'];
    } catch (PDOException $e) {
        // Rollback if an error occurs
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

function edit_user_password($pdo, $data) {
    try {
        // Hash the new password
        $hashedPassword = password_hash($data['new_password'], PASSWORD_DEFAULT);

        // Update query
        $sql = "UPDATE user_tbl SET password = :password WHERE Identifier = :Identifier";

        // Prepare the statement
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':Identifier', $data['Identifier']);

        // Execute the statement
        $stmt->execute();

        // Check if rows were affected
        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'Password updated successfully.'];
        } else {
            return ['success' => false, 'message' => 'No changes made or user not found.'];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

function delete_user($pdo, $identifier) {
    try {
        $sql = "DELETE FROM user_tbl WHERE Identifier = :identifier";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':identifier', $identifier);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'User deleted successfully.'];
        } else {
            return ['success' => false, 'message' => 'User not found.'];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}


function get_user_by_id($pdo, $user_id) {
  $query = "SELECT * FROM reg_tbl WHERE LRN = '".$user_id."'";
  $statement = $pdo->query($query);
  return $statement->fetch();
}

function get_all_usersinfoe($pdo, $userRole, $userStatus) {//marked
  $datenow = date('Y-m-d');
  $query = "SELECT * FROM user_tbl";  
  $statement = $pdo->query($query);
  return $statement->fetchAll();
}
function get_all_usersinfo($pdo, $userRole, $userStatus, $userStatus1) {
    // Base query
    $query = "SELECT * FROM user_tbl WHERE 1=1";
    
    // Apply filters based on role if provided
    if (!empty($userRole)) {
        $query .= " AND Role = :userRole";
    }

    // Apply filters based on user status if provided
    if (!empty($userStatus)) {
        if ($userStatus === 'BLOCKED') {
            // If status is BLOCKED, filter for 'BLOCKED' status
            $query .= " AND user_account_access = 'BLOCKED' ";
        } else {
            // Default to ACTIVE if status is not provided or is not BLOCKED
            $query .= " AND COALESCE(user_account_access, 'ACTIVE') = 'ACTIVE' ";
        }
    }
    if (!empty($userStatus1)) {
        $query .= " AND user_status = :userStatus1";
    }

    // Prepare the query
    $stmt = $pdo->prepare($query);

    // Bind the parameters only if the role is provided
    if (!empty($userRole)) {
        $stmt->bindValue(':userRole', $userRole, PDO::PARAM_STR);
    }
    if (!empty($userStatus1)) {
    $stmt->bindValue(':userStatus1', $userStatus1, PDO::PARAM_STR);
    }


    // Execute the query and return the result as an associative array
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function verifyMatch($pdo, $LRN) {
  try {
      // Prepare and execute the query to check if the LRN matches any Identifier in user_tbl
      $stmt = $pdo->prepare("SELECT COUNT(*) AS MatchCount FROM user_tbl WHERE Identifier = :LRN");
      $stmt->execute(['LRN' => $LRN]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      // Return true if a match exists, otherwise false
      return $result['MatchCount'] > 0;
  } catch (Exception $e) {
      // Log or handle the exception as needed
      throw new Exception("Database error: " . $e->getMessage());
  }
}
function blockUser($pdo, $userId) {
    try {
        $stmt = $pdo->prepare("UPDATE user_tbl SET user_account_access = 'BLOCKED' WHERE identifier = ?");
        $success = $stmt->execute([$userId]);

        echo json_encode(["success" => $success]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
    exit;
}

// Function to unblock a user
function unblockUser($pdo, $userId) {
    try {
        $stmt = $pdo->prepare("UPDATE user_tbl SET user_account_access = NULL WHERE identifier = ?");
        $success = $stmt->execute([$userId]);

        echo json_encode(["success" => $success]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
    exit;
}