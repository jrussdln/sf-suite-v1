<?php
require_once('../includes/sessions.php');
require_once('../includes/db_config.php');
//require_once('../includes/functions/func_calendar.php');
header('Content-type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        if (isset($_GET['get_holidays'])) {
            try {
                // Query to join dble_holidays_tbl and holidays_tbl on holiday_id
                $sql = "SELECT dble_holidays_tbl.holiday_id, holidays_tbl.holiday_name
                FROM dble_holidays_tbl
                INNER JOIN holidays_tbl ON dble_holidays_tbl.holiday_id = holidays_tbl.holiday_id";
                // Execute the query
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                // Fetch the results
                $holidays = $stmt->fetchAll(PDO::FETCH_ASSOC);
                // Return the results as a JSON response
                echo json_encode(['holidays' => $holidays]);
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
            exit;
        } elseif (isset($_GET['get_full_holidays'])) {
            // Query to fetch holiday_name and holiday_date from holidays_tbl
            $stmt = $pdo->prepare("SELECT holiday_id, holiday_name, holiday_date FROM holidays_tbl WHERE holiday_date is not null");
            $stmt->execute();
            // Fetch the results
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // Return the events as JSON
            echo json_encode($events);
        } elseif (isset($_GET['get_full_holidays_card'])) {
            $stmt = $pdo->prepare("SELECT holiday_id, holiday_name FROM holidays_tbl WHERE holiday_date is not null");
            $stmt->execute();
            // Fetch the results
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // Wrap the events in a 'holidays' property
            echo json_encode(['holidays' => $events]);
        }
        exit;
    case 'POST':
        if (isset($_GET['delete_full_holidays_cards'])) {
            if (!empty($_POST['holiday_id'])) {
                $holidayId = $_POST['holiday_id'];
                // Debugging: Log the holiday_id
                error_log('Holiday ID: ' . $holidayId);
                try {
                    // Start a transaction to ensure both deletes happen together
                    $pdo->beginTransaction();
                    // Prepare and execute the delete query for holidays_tbl
                    $sql1 = "DELETE FROM holidays_tbl WHERE holiday_id = :holiday_id";
                    $stmt1 = $pdo->prepare($sql1);
                    $stmt1->bindParam(':holiday_id', $holidayId, PDO::PARAM_INT);
                    $stmt1->execute();
                    // Prepare and execute the delete query for dble_holiday_tbl
                    $sql2 = "DELETE FROM dble_holidays_tbl WHERE holiday_id = :holiday_id";
                    $stmt2 = $pdo->prepare($sql2);
                    $stmt2->bindParam(':holiday_id', $holidayId, PDO::PARAM_INT);
                    $stmt2->execute();
                    // Commit the transaction if both queries were successful
                    if ($stmt1->rowCount() > 0 || $stmt2->rowCount() > 0) {
                        $pdo->commit();  // Commit the transaction
                        echo json_encode(['success' => true, 'message' => 'Event and corresponding record removed from database.']);
                    } else {
                        $pdo->rollBack();  // Rollback if no rows were affected
                        echo json_encode(['success' => false, 'message' => 'Event not found or already deleted in both tables.']);
                    }
                } catch (PDOException $e) {
                    $pdo->rollBack();  // Rollback in case of an error
                    error_log('Error: ' . $e->getMessage());
                    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'No holiday ID provided.']);
            }
            exit;
        } elseif (isset($_GET['add_event'])) {
            // Get the holiday name from the POST request
            $holiday_name = isset($_POST['holiday_name']) ? $_POST['holiday_name'] : '';
            if (!empty($holiday_name)) {
                // Start a transaction to ensure both insertions are handled atomically
                $pdo->beginTransaction();
                try {
                    // Insert the holiday name into the holidays_tbl
                    $stmt = $pdo->prepare("INSERT INTO holidays_tbl (holiday_name) VALUES (:holiday_name)");
                    $stmt->bindParam(':holiday_name', $holiday_name, PDO::PARAM_STR);
                    $stmt->execute();
                    // Get the last inserted holiday_id
                    $holiday_id = $pdo->lastInsertId();
                    // Now, insert the holiday_id into dble_holidays_tbl
                    $stmt2 = $pdo->prepare("INSERT INTO dble_holidays_tbl (holiday_id) VALUES (:holiday_id)");
                    $stmt2->bindParam(':holiday_id', $holiday_id, PDO::PARAM_INT);
                    $stmt2->execute();
                    // Commit the transaction
                    $pdo->commit();
                    // Return success response
                    echo json_encode(['success' => true, 'message' => 'Event added successfully']);
                } catch (Exception $e) {
                    // Rollback the transaction in case of any error
                    $pdo->rollBack();
                    // Return failure response
                    echo json_encode(['success' => false, 'message' => 'Failed to add event: ' . $e->getMessage()]);
                }
            } else {
                // Return error if holiday_name is missing
                echo json_encode(['success' => false, 'message' => 'Holiday name is required']);
            }
            exit;
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if holiday_id and holiday_date are provided
            if (!isset($_POST['holiday_id']) || !isset($_POST['holiday_date'])) {
                echo json_encode(['success' => false, 'message' => 'Missing holiday_id or holiday_date.']);
                exit;
            }
            // Get the holiday_id and holiday_date from the POST data
            $holidayId = $_POST['holiday_id'];
            $holidayDate = $_POST['holiday_date'];
            try {
                // Prepare and execute the UPDATE query
                $sql = "UPDATE holidays_tbl SET holiday_date = :holiday_date WHERE holiday_id = :holiday_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':holiday_id', $holidayId, PDO::PARAM_INT);
                $stmt->bindParam(':holiday_date', $holidayDate, PDO::PARAM_STR);
                $stmt->execute();
                // Delete from dble_holidays_tbl after updating holidays_tbl
                $sql = "DELETE FROM dble_holidays_tbl WHERE holiday_id = :holiday_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':holiday_id', $holidayId, PDO::PARAM_INT);
                $stmt->execute();
                // Check if any row was updated
                if ($stmt->rowCount() > 0) {
                    echo json_encode(['success' => true, 'message' => 'Event date updated successfully.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error updating event date.']);
                }
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            exit;
        }
        break;
    case 'DELETE':
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            parse_str(file_get_contents("php://input"), $input);  // Get the raw POST data
            if (isset($input['holiday_id'])) {
                $holidayId = $input['holiday_id'];
                try {
                    // Prepare and execute the DELETE query
                    $sql = "DELETE FROM dble_holidays_tbl WHERE holiday_id = :holiday_id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':holiday_id', $holidayId, PDO::PARAM_INT);
                    $stmt->execute();
                    // Check if a row was affected
                    if ($stmt->rowCount() > 0) {
                        echo json_encode(['success' => true, 'message' => 'Event removed from database.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Event not found or already deleted.']);
                    }
                } catch (PDOException $e) {
                    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'No holiday ID provided.']);
            }
            exit;
        }
    default:
        // Handle invalid method
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        break;
}
