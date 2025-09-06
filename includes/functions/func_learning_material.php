<?php

function get_lm_by_section($pdo, $sectionId)
{
    try {
        // Fetch the School Year for the section
        $stmt = $pdo->prepare("
            SELECT SchoolYear 
            FROM section_tbl 
            WHERE SectionId = :sectionId
        ");
        $stmt->bindParam(':sectionId', $sectionId, PDO::PARAM_INT);
        $stmt->execute();
        $section = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($section) {
            $schoolYear = $section['SchoolYear'];

            // Fetch learning materials and include Returned1-9 columns
            $stmt = $pdo->prepare("
                SELECT 
                    lm.learning_material_id,
                    lm.section_id,
                    lm.lrn,
                    lm.section, 
                    lm.Desc1, lm.Status1, lm.Returned1,
                    lm.Desc2, lm.Status2, lm.Returned2,
                    lm.Desc3, lm.Status3, lm.Returned3,
                    lm.Desc4, lm.Status4, lm.Returned4,
                    lm.Desc5, lm.Status5, lm.Returned5,
                    lm.Desc6, lm.Status6, lm.Returned6,
                    lm.Desc7, lm.Status7, lm.Returned7,
                    lm.Desc8, lm.Status8, lm.Returned8,
                    lm.Desc9, lm.Status9, lm.Returned9
                FROM 
                    learning_material_tbl lm
                WHERE 
                    lm.section_id = :sectionId 
                    AND lm.lm_term = :schoolYear
            ");

            $stmt->bindParam(':sectionId', $sectionId, PDO::PARAM_INT);
            $stmt->bindParam(':schoolYear', $schoolYear, PDO::PARAM_STR);

            $stmt->execute();

            $learningMaterials = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Add student names to each material record
            foreach ($learningMaterials as &$material) {
                $material['name'] = get_student_name_by_lrn($pdo, $material['lrn']);
            }

            return $learningMaterials;
        }

        return [];
    } catch (PDOException $e) {
        error_log("Error fetching learning materials: " . $e->getMessage());
        return [];
    }
}

function get_student_name_by_lrn($pdo, $lrn) {
    try {
        $stmt = $pdo->prepare("
            SELECT name 
            FROM student_tbl 
            WHERE lrn = :lrn
        ");
        $stmt->bindParam(':lrn', $lrn, PDO::PARAM_STR);
        $stmt->execute();
        
        // Fetch the name
        return $stmt->fetchColumn(); // Returns the name or false if not foun
    } catch (PDOException $e) {
        error_log("Error fetching student name: " . $e->getMessage());
        return null; // Return null on error
    }
}
function insertLmRecord($pdo, $lrn, $section, $school_year, $sectionId) {
    try {
        // Check if the combination of lrn and lm_term already exists
        $checkStmt = $pdo->prepare("
            SELECT COUNT(*) FROM learning_material_tbl 
            WHERE lrn = :lrn AND lm_term = :lm_term
        ");
        $checkStmt->execute([
            'lrn' => $lrn,
            'lm_term' => $school_year
        ]);
        $exists = $checkStmt->fetchColumn();

        if ($exists > 0) {
            // Skip insertion if the record with the same lrn and lm_term exists
            error_log("Skipped insertion: lrn '$lrn' with lm_term '$school_year' already exists.");
            return false;
        }

        // Insert the record if the combination does not exist
        $stmt = $pdo->prepare("
            INSERT INTO learning_material_tbl (
                section_id,
                lrn, 
                section, 
                lm_term
            ) VALUES (
                :section_id, 
                :lrn, 
                :section, 
                :lm_term
                
            )
        ");
        $stmt->execute([
            'section_id' => $sectionId,
            'lrn' => $lrn,
            'section' => $section,
            'lm_term' => $school_year
        ]);

        // Log success
        error_log("Successfully inserted record for lrn '$lrn' with lm_term '$school_year'.");

    } catch (PDOException $e) {
        error_log("Error inserting learning material record: " . $e->getMessage());
        return false; // Indicate failure
    }

    return true; // Indicate success
}

function getStudentsBySectionName(PDO $pdo, string $sectionName): array {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                lrn, 
                section, 
                school_year 
            FROM 
                student_tbl 
            WHERE 
                section = :sectionName
        ");
        $stmt->bindParam(':sectionName', $sectionName, PDO::PARAM_STR);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $results ?: []; // Return empty array if no results
    } catch (PDOException $e) {
        error_log('Error in getStudentsBySectionName: ' . $e->getMessage());
        return [];
    }
}
function get_lm_details($pdo, $lm_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                learning_material_id,
                Desc1, Desc2, Desc3, Desc4, Desc5, 
                Desc6, Desc7, Desc8, Desc9,
                Returned1, Returned2, Returned3, 
                Returned4, Returned5, Returned6, 
                Returned7, Returned8, Returned9
            FROM 
                learning_material_tbl
            WHERE 
                learning_material_id = :lm_id
        ");
        $stmt->bindParam(':lm_id', $lm_id, PDO::PARAM_INT);
        $stmt->execute();
        $learningMaterial = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($learningMaterial) {
            return [
                'success' => true,
                'data' => $learningMaterial
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Learning material not found.'
            ];
        }
    } catch (PDOException $e) {
        error_log("Error fetching learning material: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Failed to fetch learning material.'
        ];
    }
}
function update_return($pdo, $data) {
    try {
        // Check the control table first
        $controlStmt = $pdo->prepare("SELECT c_status FROM control_tbl WHERE c_id = 1");
        $controlStmt->execute();
        $control = $controlStmt->fetch(PDO::FETCH_ASSOC);

        // If c_status is not 0, return an error message
        if (!$control || $control['c_status'] != 0) {
            return [
                'success' => false,
                'message' => 'This operation is not available right now, contact the ICT Coordinator.'
            ];
        }

        // Extract the learning material ID
        $lm_id = $data['learning_material_id'] ?? null;

        // Log the incoming data for debugging
        error_log("Received data: " . print_r($data, true));

        // Validate that the learning material ID exists
        if (!$lm_id) {
            return [
                'success' => false,
                'message' => 'Learning material ID is missing.'
            ];
        }

        // Prepare the SQL query to update the Returned columns based on checkbox state
        $sql = "
            UPDATE learning_material_tbl SET 
                Desc1 = :Desc1, Returned1 = CASE WHEN :Returned1 = 1 THEN NOW() ELSE NULL END,
                Desc2 = :Desc2, Returned2 = CASE WHEN :Returned2 = 1 THEN NOW() ELSE NULL END,
                Desc3 = :Desc3, Returned3 = CASE WHEN :Returned3 = 1 THEN NOW() ELSE NULL END,
                Desc4 = :Desc4, Returned4 = CASE WHEN :Returned4 = 1 THEN NOW() ELSE NULL END,
                Desc5 = :Desc5, Returned5 = CASE WHEN :Returned5 = 1 THEN NOW() ELSE NULL END,
                Desc6 = :Desc6, Returned6 = CASE WHEN :Returned6 = 1 THEN NOW() ELSE NULL END,
                Desc7 = :Desc7, Returned7 = CASE WHEN :Returned7 = 1 THEN NOW() ELSE NULL END,
                Desc8 = :Desc8, Returned8 = CASE WHEN :Returned8 = 1 THEN NOW() ELSE NULL END,
                Desc9 = :Desc9, Returned9 = CASE WHEN :Returned9 = 1 THEN NOW() ELSE NULL END
            WHERE learning_material_id = :lm_id
        ";

        // Prepare the parameters
        $params = [
            ':lm_id' => $lm_id,
            ':Desc1' => $data['Desc1'] ?? null,
            ':Returned1' => !empty($data['Returned1']) ? 1 : null,
            ':Desc2' => $data['Desc2'] ?? null,
            ':Returned2' => !empty($data['Returned2']) ? 1 : null,
            ':Desc3' => $data['Desc3'] ?? null,
            ':Returned3' => !empty($data['Returned3']) ? 1 : null,
            ':Desc4' => $data['Desc4'] ?? null,
            ':Returned4' => !empty($data['Returned4']) ? 1 : null,
            ':Desc5' => $data['Desc5'] ?? null,
            ':Returned5' => !empty($data['Returned5']) ? 1 : null,
            ':Desc6' => $data['Desc6'] ?? null,
            ':Returned6' => !empty($data['Returned6']) ? 1 : null,
            ':Desc7' => $data['Desc7'] ?? null,
            ':Returned7' => !empty($data['Returned7']) ? 1 : null,
            ':Desc8' => $data['Desc8'] ?? null,
            ':Returned8' => !empty($data['Returned8']) ? 1 : null,
            ':Desc9' => $data['Desc9'] ?? null,
            ':Returned9' => !empty($data['Returned9']) ? 1 : null,
        ];

        // Log the SQL query and parameters for debugging
        error_log("Executing query: " . $sql);
        error_log("With parameters: " . print_r($params, true));

        // Prepare and execute the query
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Check if the query was successful
        if ($stmt->rowCount() > 0) {
            return [
                'success' => true,
                'message' => 'Learning material details updated successfully.'
            ];
        } else {
            // If no rows are updated, check if data is the same as before
            return [
                'success' => false,
                'message' => 'No changes detected or no records updated.'
            ];
        }
    } catch (PDOException $e) {
        // Log the error for debugging purposes
        error_log("Error updating learning material: " . $e->getMessage());

        // Return failure message
        return [
            'success' => false,
            'message' => 'Failed to update learning material.'
        ];
    }
}
function updateLearningMaterials($pdo, $learning_material_ids, $descriptions)
{
    // Check the control table first
    $controlStmt = $pdo->prepare("SELECT c_status FROM control_tbl WHERE c_id = 1");
    $controlStmt->execute();
    $control = $controlStmt->fetch(PDO::FETCH_ASSOC);

    // If c_status is not 0, return an error message
    if (!$control || $control['c_status'] != 0) {
        return ["success" => false, "message" => "This operation is not available right now, contact the ICT Coordinator."];
    }

    // Initialize SQL and parameters
    $sql = "UPDATE learning_material_tbl SET ";
    $params = [];
    $setParts = [];
    $isUpdated = false;

    // Loop through all 9 descriptions
    for ($i = 1; $i <= 9; $i++) {
        // Check if the description is set and not empty
        if (isset($descriptions[$i - 1])) {
            $description = trim($descriptions[$i - 1]);

            if ($description !== '') {
                // If the description is not empty, update the description and set the Status to NOW()
                $setParts[] = "Desc$i = ?";
                $setParts[] = "Status$i = NOW()";
                $params[] = $description;
                $isUpdated = true;
            } else {
                // If the description is empty, set the Status to NULL
                $setParts[] = "Desc$i = NULL";
                $setParts[] = "Status$i = NULL";
            }
        } else {
            // If description is not set, set both Desc$i and Status$i to NULL
            $setParts[] = "Desc$i = NULL";
            $setParts[] = "Status$i = NULL";
        }
    }

    // Ensure that at least one description was updated (non-empty descriptions or statuses)
    if (!$isUpdated) {
        return ["success" => false, "message" => "No descriptions were updated."];
    }

    // Append the WHERE clause to the SQL query
    $sql .= implode(", ", $setParts) . " WHERE learning_material_id = ?";

    // Prepare the update statement
    $stmt = $pdo->prepare($sql);
    $success = false;

    // Execute the update query for each learning material ID
    foreach ($learning_material_ids as $learning_material_id) {
        // Add the learning_material_id to the parameters
        $stmtParams = array_merge($params, [$learning_material_id]);
        $success = $stmt->execute($stmtParams);

        if (!$success) {
            // If execution fails, break the loop
            break;
        }
    }

    if ($success) {
        return ["success" => true, "message" => "Learning materials updated successfully!"];
    } else {
        // Get the error info if the execution failed
        $errorInfo = $stmt->errorInfo();
        return ["success" => false, "message" => "Failed to update learning materials.", "error" => $errorInfo];
    }
}


function getLearningMaterialsBySection($sectionName) {
    global $pdo; // Assuming you have a PDO instance available

    $stmt = $pdo->prepare("SELECT * FROM learning_materials WHERE section = :sectionName");
    $stmt->execute(['sectionName' => $sectionName]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all matching records
}


// Function to enable distribution
function enable_distribution($pdo, $data) {
    // Validate and sanitize input
    $c_id = isset($data['c_id']) ? (int)$data['c_id'] : null;
    $c_status = isset($data['c_status']) ? (int)$data['c_status'] : null;

    if ($c_id === null || $c_status === null) {
        return ['error' => 'Invalid input'];
    }

    // Prepare and execute the SQL statement
    $stmt = $pdo->prepare("UPDATE control_tbl SET c_status = :c_status WHERE c_id = :c_id");
    $stmt->bindParam(':c_status', $c_status);
    $stmt->bindParam(':c_id', $c_id);

    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Distribution status updated successfully'];
    } else {
        return ['error' => 'Failed to update distribution status'];
    }
}
function disable_distribution($pdo, $data) {
    // Validate and sanitize input
    $c_id = isset($data['c_id']) ? (int)$data['c_id'] : null;
    $c_status = isset($data['c_status']) ? (int)$data['c_status'] : null;

    if ($c_id === null || $c_status === null) {
        return ['error' => 'Invalid input'];
    }

    // Prepare and execute the SQL statement
    $stmt = $pdo->prepare("UPDATE control_tbl SET c_status = :c_status WHERE c_id = :c_id");
    $stmt->bindParam(':c_status', $c_status);
    $stmt->bindParam(':c_id', $c_id);

    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Distribution status updated successfully'];
    } else {
        return ['error' => 'Failed to update distribution status'];
    }
}
function check_status($pdo) {

    // Query to get the current status
    $stmt = $pdo->query("SELECT c_status FROM control_tbl WHERE c_id = 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        return ['c_status' => (int)$row['c_status']];
    } else {
        return ['error' => 'Control not found'];
    }

}
?>