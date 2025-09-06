<?php
require_once('../includes/db_config.php');
$message = '';
// Helper: clean identifier
function cleanIdentifier($id)
{
  return strtoupper(trim($id));
}
// === PERSONAL INFO UPDATE ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['uap_identifier'])) {
  $identifier = cleanIdentifier($_POST['uap_identifier']);
  $email = $_POST['uap_user_email'] ?? null;
  $fname = $_POST['uap_user_fname'] ?? '';
  $mname = $_POST['uap_user_mname'] ?? '';
  $lname = $_POST['uap_user_lname'] ?? '';
  $ename = $_POST['uap_user_ename'] ?? '';
  $gender = $_POST['uap_gender'] ?? '';
  $birthdate = $_POST['uap_birthdate'] ?? null;
  
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
    $message = '<div class="message success">Information updated successfully!</div>';
  } catch (PDOException $e) {
    error_log('Update Error: ' . $e->getMessage());
    $message = '<div class="message error">Error updating information: ' . htmlspecialchars($e->getMessage()) . '</div>';
  }
}
// === QUESTIONNAIRE SUBMISSION ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['questionnaire_identifier'])) {
  $response_name = cleanIdentifier($_POST['questionnaire_identifier']);
  $answers = $_POST['answers'] ?? [];
  if ($response_name && !empty($answers)) {
    try {
      $pdo->beginTransaction();
      // Get active school year
      $syStmt = $pdo->prepare("SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active' LIMIT 1");
      $syStmt->execute();
      $activeSY = $syStmt->fetchColumn();
      if (!$activeSY) {
        throw new Exception("No active school year found.");
      }
      // Check for duplicate submission
      $checkStmt = $pdo->prepare("SELECT identifier FROM response_log_tbl 
                                  WHERE UPPER(TRIM(identifier)) = :identifier 
                                  AND school_year = :school_year FOR UPDATE");
      $checkStmt->execute([
        ':identifier' => $response_name,
        ':school_year' => $activeSY
      ]);
      if ($checkStmt->fetchColumn()) {
        $pdo->rollBack();
        $message = "<p class='message error'>You have already submitted your responses for this school year.</p>";
      } else {
        // Insert responses
        $stmt = $pdo->prepare("INSERT INTO responses (identifier, question_id, choice_id) 
                               VALUES (:identifier, :question_id, :choice_id)");
        foreach ($answers as $question_id => $choice_id) {
          if (!empty($choice_id)) {
            $stmt->execute([
              ':identifier' => $response_name,
              ':question_id' => $question_id,
              ':choice_id' => $choice_id
            ]);
          }
        }
        // Log submission
        $logStmt = $pdo->prepare("INSERT INTO response_log_tbl (identifier, submitted_at, school_year) 
                                  VALUES (:identifier, NOW(), :school_year)");
        $logStmt->execute([
          ':identifier' => $response_name,
          ':school_year' => $activeSY
        ]);
        $pdo->commit();
        $message = "<p class='message success'>Thank you, your response has been recorded!</p>";
      }
    } catch (Exception $e) {
      if ($pdo->inTransaction())
        $pdo->rollBack();
      $message = "<p class='message error'>Failed to record response: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
  } else {
    $message = "<p class='message error'>Please complete all fields before submitting.</p>";
  }
}
// === FETCH QUESTIONS AND CHOICES ===
$questions = [];
try {
  $syStmt = $pdo->prepare("SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active' LIMIT 1");
  $syStmt->execute();
  $activeSchoolYear = $syStmt->fetchColumn();
  if (!$activeSchoolYear) {
    echo json_encode(['success' => false, 'message' => 'No active school year found.']);
    exit;
  }
  $sql = "SELECT q.question_id, q.question_desc, c.choices_id, c.choices_content
          FROM quest_tracer_tbl q
          LEFT JOIN choices_tracer_tbl c ON q.question_id = c.question_id
          WHERE q.school_year = :school_year
          ORDER BY q.question_id, c.choices_id";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':school_year', $activeSchoolYear);
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $qid = $row['question_id'];
    if (!isset($questions[$qid])) {
      $questions[$qid] = [
        'text' => $row['question_desc'],
        'options' => []
      ];
    }
    if (!empty($row['choices_id'])) {
      $questions[$qid]['options'][] = [
        'id' => $row['choices_id'],
        'text' => $row['choices_content']
      ];
    }
  }
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Error fetching questions and choices: ' . htmlspecialchars($e->getMessage())]);
}
?>
<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f7f9fc;
    margin: 0;
    padding: 0;
  }
  .container {
    max-width: 650px;
    margin: 30px auto 40px;
    background: #fff;
    padding: 25px 30px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  }

  h3 {
    margin-bottom: 25px;
    color: #333;
    text-align: center;
  }

  label {
    font-weight: 600;
    color: #555;
    display: block;
    margin-bottom: 6px;
  }

  input[type="text"],
  input[type="email"],
  input[type="date"],
  select {
    width: 100%;
    padding: 10px 12px;
    margin-bottom: 20px;
    border: 1.8px solid #ccc;
    border-radius: 5px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
  }

  input[type="text"]:focus,
  input[type="email"]:focus,
  input[type="date"]:focus,
  select:focus {
    outline: none;
    border-color: #2E86C1;
    box-shadow: 0 0 5px rgba(46, 134, 193, 0.4);
  }

  .form-group.row {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
  }

  .col-6,
  .col-3,
  .col-2 {
    flex: 1;
    min-width: 48%;
  }

  .col-3 {
    min-width: 30%;
  }

  .col-2 {
    min-width: 20%;
  }

  input[type="submit"],
  .btn-primary {
    background-color: #2E86C1;
    color: white;
    border: none;
    padding: 12px 24px;
    font-size: 1.1rem;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.25s ease;
    margin-top: 10px;
  }

  input[type="submit"]:hover,
  .btn-primary:hover {
    background-color: #1B4F72;
  }

  .message {
    padding: 15px 20px;
    margin: 15px auto;
    max-width: 600px;
    border-radius: 5px;
    font-weight: 600;
    text-align: center;
  }

  .message.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
  }

  .message.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
  }

  .header-img,
  .footer-img {
    width: 100%;
    /* Force full width */
    max-width: 100%;
    /* Ensure it doesn't overflow */
    margin: 0;
    /* Remove auto margin */
    display: block;
    height: auto;
    /* Maintain aspect ratio */
  }
</style>
<!-- Header Image -->
<div>
  <img src="https://sfsuite.fwh.is/dist/img/school_header.png" alt="School Header" class="header-img">
</div>
<!-- Message -->
<?php if ($message)
  echo $message; ?>
<!-- Personal Info Section -->
<section class="container" id="personnalInfoSection">
  <h3>Personal Information</h3>
  <form id="userForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" autocomplete="off">
    <div class="form-group row">
      <div class="col-6">
        <label for="uap_identifier">Identifier (LRN)</label>
        <input type="text" name="uap_identifier" id="uap_identifier" class="form-control"
          onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()" required>
      </div>
      <div class="col-6">
        <label for="uap_user_email">Email Address</label>
        <input type="email" name="uap_user_email" id="uap_user_email" class="form-control">
      </div>
    </div>
    <div class="form-group row">
      <div class="col-3">
        <label for="uap_user_fname">First Name</label>
        <input type="text" name="uap_user_fname" id="uap_user_fname" class="form-control"
          onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()" required>
      </div>
      <div class="col-3">
        <label for="uap_user_mname">Middle Name</label>
        <input type="text" name="uap_user_mname" id="uap_user_mname" class="form-control"
          onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
      </div>
      <div class="col-3">
        <label for="uap_user_lname">Last Name</label>
        <input type="text" name="uap_user_lname" id="uap_user_lname" class="form-control"
          onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()" required>
      </div>
      <div class="col-3">
        <label for="uap_user_ename">Extension Name</label>
        <input type="text" name="uap_user_ename" id="uap_user_ename" class="form-control"
          onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
      </div>
    </div>
    <div class="form-group row">
      <div class="col-2">
        <label for="uap_gender">Gender</label>
        <select id="uap_gender" name="uap_gender" class="form-control" required>
          <option value="">--</option>
          <option value="F">Female</option>
          <option value="M">Male</option>
        </select>
      </div>
      <div class="col-2">
        <label for="uap_birthdate">Birth Date</label>
        <input type="date" name="uap_birthdate" id="uap_birthdate" class="form-control" required>
      </div>
    </div>
    <div style="text-align: end;">
      <button type="submit" class="btn btn-primary">Update Information</button>
      <!-- Button -->
      <button id="continueBtn" class="btn btn-primary" style="display: none;">Continue</button>
    </div>
  </form>
</section>
<!-- Questionnaire Section -->
<section class="container" id="questionnaireSection" style="display: none;">
  <h3>Questionnaire</h3>
  <form action="" method="POST" autocomplete="off">
    <input type="hidden" name="questionnaire_identifier" id="questionnaire_identifier">
    <?php foreach ($questions as $qid => $q): ?>
      <label for="question_<?php echo $qid; ?>">
        <?php echo htmlspecialchars($q['text']); ?>
      </label>
      <select name="answers[<?php echo $qid; ?>]" id="question_<?php echo $qid; ?>" required>
        <option value="">-- Select an answer --</option>
        <?php foreach ($q['options'] as $opt): ?>
          <option value="<?php echo $opt['id']; ?>">
            <?php echo htmlspecialchars($opt['text']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    <?php endforeach; ?>
    <input type="submit" value="Submit">
  </form>
</section>
<!-- Footer Image -->
<div>
  <img src="https://sfsuite.fwh.is/dist/img/school_footer.png" alt="School Footer" class="footer-img">
</div>
<script>
  
  window.addEventListener('DOMContentLoaded', function () {
    const identifierInput = document.getElementById('uap_identifier');
    const firstNameInput = document.getElementById('uap_user_fname');
    const lastNameInput = document.getElementById('uap_user_lname');
    const continueBtn = document.getElementById('continueBtn');
    function validateFields() {
      const identifier = identifierInput.value.trim();
      const firstName = firstNameInput.value.trim();
      const lastName = lastNameInput.value.trim();
      if (identifier && firstName && lastName) {
        continueBtn.style.display = 'inline-block'; // or 'block' depending on your layout
      } else {
        continueBtn.style.display = 'none';
      }
    }
    identifierInput.addEventListener('input', validateFields);
    firstNameInput.addEventListener('input', validateFields);
    lastNameInput.addEventListener('input', validateFields);
    // Optional: Run once on page load
    validateFields();
    document.getElementById('continueBtn').addEventListener('click', function (e) {
      e.preventDefault(); // Prevent default form submission
      const form = document.getElementById('userForm');
      const identifier = identifierInput.value.trim().toUpperCase();
      const firstName = firstNameInput.value.trim();
      const lastName = lastNameInput.value.trim();
      if (!identifier || !firstName || !lastName) {
        alert('Please complete all required fields.');
        return;
      }
      const formData = new FormData(form);
      fetch('', {
        method: 'POST',
        body: formData
      })
        .then(response => response.text())
        .then(html => {
          if (html.includes('Information updated successfully')) {
            document.getElementById('questionnaire_identifier').value = identifier;
            document.getElementById('personnalInfoSection').style.display = 'none';
            document.getElementById('questionnaireSection').style.display = 'block';
          } else {
            alert('Please make sure your information is correct and try again.');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while updating your information.');
        });
    });
  });
  // Optional: Fetch user info if identifier changes (you can implement fetch_user.php accordingly)
  document.getElementById('uap_identifier').addEventListener('change', function () {
    const identifier = this.value.trim().toUpperCase();
    if (!identifier) return;
    fetch('fetch_user.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ identifier })
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          document.getElementById('uap_user_lname').value = data.user.UserLName || '';
          document.getElementById('uap_user_fname').value = data.user.UserFName || '';
          document.getElementById('uap_user_mname').value = data.user.UserMName || '';
          document.getElementById('uap_user_ename').value = data.user.UserEName || '';
          document.getElementById('uap_gender').value = data.user.Gender || '';
          document.getElementById('uap_birthdate').value = data.user.BirthDate || '';
          document.getElementById('uap_user_email').value = data.user.email || '';
          document.getElementById('uap_identifier').readOnly = true;
        } else {
          alert('Identifier not found.');
          ['uap_user_lname', 'uap_user_fname', 'uap_user_mname', 'uap_user_ename', 'uap_gender', 'uap_birthdate', 'uap_user_email'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = '';
          });
        }
      })
      .catch(console.error);
  });
</script>