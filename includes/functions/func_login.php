<?php
function verify_login_admin($pdo, $username, $password)
{
    // Prepare the query to prevent SQL injection
    $statement = $pdo->prepare("SELECT * FROM user_tbl WHERE username = :username AND user_account_access is null ");
    $statement->execute(['username' => $username]);
    $row = $statement->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Verify the password using password_verify
        if (password_verify($password, $row['password'])) {
            // Store access level in session
            $_SESSION['access_level'] = $row['access_level'];
            return $row; // Return the user row on successful login
        } else {
            // Log failed login attempt due to incorrect password
            error_log('Failed login attempt for username: ' . $username . ' - Incorrect password');
        }
    } else {
        // Log failed login attempt due to nonexistent username
        error_log('Failed login attempt for username: ' . $username . ' - User not found');
    }

    return false; // Return false if login fails
}

function get_user_data_admin($pdo, $username)
{
    $statement = $pdo->prepare("SELECT Identifier, access_level, UserLName, UserFName, UserMName, UserEName FROM user_tbl WHERE username = :username");
    $statement->execute(['username' => $username]);
    $user_data = $statement->fetch(PDO::FETCH_ASSOC);

    if ($user_data) {
        return $user_data; // Return user data if found
    } else {
        // Log if no user data is found for the given username
        error_log('No user data found for username: ' . $username);
        return false; // Return false if no user data found
    }
}
function update_user_status($pdo, $username)
{
    $statement = $pdo->prepare("UPDATE user_tbl SET user_status = 'Active' WHERE username = :username");
    $statement->execute(['username' => $username]);
}

?>