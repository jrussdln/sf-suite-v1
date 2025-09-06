<form id="addUserForm" action="../api/api_users.php" method="post">
  <div id="addUserModal" class="modal fade" id="modal-xl">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h4 class="modal-title">Add New User</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group row">
            <div class="col-6">
              <label for="au_identifier">Identifier</label>
              <input type="text" name="au_identifier" id="au_identifier" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()"
                required>
            </div>
            <div class="col-6">
              <label for="au_email">Email Address</label>
              <input type="email" name="au_email" id="au_email" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()"
                required>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-3">
              <label for="au_user_fname">First Name</label>
              <input type="text" name="au_user_fname" id="au_user_fname" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()"
                required>
            </div>
            <div class="col-3">
              <label for="au_user_mname">Middle Name</label>
              <input type="text" name="au_user_mname" id="au_user_mname" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
            </div>
            <div class="col-3">
              <label for="au_user_lname">Last Name</label>
              <input type="text" name="au_user_lname" id="au_user_lname" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()"
                required>
            </div>
            <div class="col-3">
              <label for="au_user_ename">Extension Name</label>
              <input type="text" name="au_user_ename" id="au_user_ename" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
            </div>
          </div>
          <div class="form-group row">
            <div class="col-2">
              <label for="au_gender">Gender</label>
              <select id="au_gender" name="au_gender" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()"
                required>
                <option value="">--</option>
                <option value="M">Female</option>
                <option value="F">Male</option>
              </select>
            </div>
            <div class="col-2">
              <label for="au_birthdate">Birth Date</label>
              <input type="date" name="au_birthdate" id="au_birthdate" class="form-control" required>
            </div>
            <div class="col-4">
              <label for="au_role">Role</label>
              <select id="au_role" name="au_role" class="form-control" onkeyup="this.value = this.value.toUpperCase()"
                onpaste="this.value = this.value.toUpperCase()" required>
                <option value="">--</option>
                <option value="TEACHER">TEACHER</option>
                <option value="SA">SCHOOL ADMINISTRATOR</option>
                <option value="STUDENT">STUDENT</option>
                <option value="SIC">SCHOOL ICT COORDINATOR</option>
                <option value="LMP">LEARNING MATERIAL PERSONNEL</option>
                <option value="HNP">HEALTH AND NUTRITION PERSONNEL</option>
              </select>
            </div>
            <div class="col-4">
              <label for="au_username">Username</label>
              <input type="text" name="au_username" id="au_username" class="form-control" required>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-6">
              <label for="au_password">Password</label>
              <input type="password" name="au_password" id="au_password" class="form-control" required>
            </div>
          </div>
        </div> <!-- modal body -->
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Create</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>
<form id="userAccProfileForm" action="../api/api_users.php" method="post">
  <div id="userAccProfileModal" class="modal fade" id="modal-xl">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h4 class="modal-title">Edit Profile</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group row">
            <div class="col-6">
              <label for="uap_identifier">Identifier</label>
              <input type="text" name="uap_identifier" id="uap_identifier" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()"
                readonly>
            </div>
            <div class="col-6">
              <label for="uap_user_email">Email Address</label>
              <input type="email" name="uap_user_email" id="uap_user_email" class="form-control" required>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-3">
              <label for="uap_user_fname">First Name</label>
              <input type="text" name="uap_user_fname" id="uap_user_fname" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()"
                required>
            </div>
            <div class="col-3">
              <label for="uap_user_mname">Middle Name</label>
              <input type="text" name="uap_user_mname" id="uap_user_mname" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
            </div>
            <div class="col-3">
              <label for="uap_user_lname">Last Name</label>
              <input type="text" name="uap_user_lname" id="uap_user_lname" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()"
                required>
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
                <option value="M">Female</option>
                <option value="F">Male</option>
              </select>
            </div>
            <div class="col-2">
              <label for="uap_birthdate">Birth Date</label>
              <input type="date" name="uap_birthdate" id="uap_birthdate" class="form-control" required>
            </div>
            <div class="col-4">
              <label for="uap_role">Role</label>
              <select id="uap_role" name="uap_role" class="form-control" required>
                <option value="">--</option>
                <option value="TEACHER">TEACHER</option>
                <option value="SA">SCHOOL ADMINISTRATOR</option>
                <option value="STUDENT">STUDENT</option>
                <option value="SIC">SCHOOL ICT COORDINATOR</option>
                <option value="LMP">LEARNING MATERIAL PERSONNEL</option>
                <option value="HNP">HEALTH AND NUTRITION PERSONNEL</option>
              </select>
            </div>
            <div class="col-4">
              <label for="uap_username">Username</label>
              <input type="text" name="uap_username" id="uap_username" class="form-control" required>
            </div>
          </div>
        </div> <!-- modal body -->
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Update</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>
<form id="userProfileForm" action="../api/api_users.php" method="post">
  <div id="userProfileModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header bg-white text-white">
          <h4 class="modal-title" style="font-size: 1rem;;">User Profile</h4>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <!-- Modal Body -->
        <div class="modal-body">
          <div class="alert alert-light p-1 mb-3 d-flex align-items-center flex-column justify-content-center"
            style="height: 30vh;">
            <!-- Profile Icon -->
            <div style="width: 80px; height: 80px; margin-bottom: 0;">
              <img src="../dist/img/profile_icon.png" alt="Profile Icon" class="rounded-circle"
                style="width: 100%; height: 100%;">
            </div>
            <!-- Full Name -->
            <div class="text-center font-weight-bold" style="margin: 0; font-size: 1.50rem;">
              <?php
              // Check if UserFullName is set and not empty, otherwise display "No Name Available"
              echo !empty($_SESSION['UserFullName']) ? htmlspecialchars($_SESSION['UserFullName']) : 'No Name Available';
              ?>
            </div>
            <!-- Access Level -->
            <div class="text-center">
              <?php
              // Check if Access Level is set and not empty, otherwise display "No Access Level"
              echo !empty($_SESSION['access_level']) ? htmlspecialchars($_SESSION['access_level']) : 'No Access Level';
              ?>
            </div>
            <!-- Identifier -->
            <div class="text-center">
              <?php
              // Check if Identifier is set and not empty, otherwise display "No Account"
              echo !empty($_SESSION['Identifier']) ? htmlspecialchars($_SESSION['Identifier']) : 'No Account';
              ?>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-6">
              <label for="a_identifier">Identifier</label>
              <input type="text" name="a_identifier" id="a_identifier" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()"
                readonly>
            </div>
            <div class="col-6">
              <label for="a_user_email">Email Address</label>
              <input type="email" name="a_user_email" id="a_user_email" class="form-control" required>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-3">
              <label for="a_user_fname">First Name</label>
              <input type="text" name="a_user_fname" id="a_user_fname" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()"
                required>
            </div>
            <div class="col-3">
              <label for="a_user_mname">Middle Name</label>
              <input type="text" name="a_user_mname" id="a_user_mname" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
            </div>
            <div class="col-3">
              <label for="a_user_lname">Last Name</label>
              <input type="text" name="a_user_lname" id="a_user_lname" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()"
                required>
            </div>
            <div class="col-3">
              <label for="a_user_ename">Extension Name</label>
              <input type="text" name="a_user_ename" id="a_user_ename" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
            </div>
          </div>
          <div class="form-group row">
            <div class="col-2">
              <label for="a_gender">Gender</label>
              <select id="a_gender" name="a_gender" class="form-control" required>
                <option value="">--</option>
                <option value="M">Female</option>
                <option value="F">Male</option>
              </select>
            </div>
            <div class="col-2">
              <label for="a_birthdate">Birth Date</label>
              <input type="date" name="a_birthdate" id="a_birthdate" class="form-control" required>
            </div>
            <div class="col-4">
              <label for="a_role">Role</label>
              <select id="a_role" name="a_role" class="form-control" required>
                <option value="">--</option>
                <option value="TEACHER">TEACHER</option>
                <option value="SA">SCHOOL ADMINISTRATOR</option>
                <option value="STUDENT">STUDENT</option>
                <option value="SIC">SCHOOL ICT COORDINATOR</option>
                <option value="LMP">LEARNING MATERIAL PERSONNEL</option>
                <option value="HNP">HEALTH AND NUTRITION PERSONNEL</option>
              </select>
            </div>
            <div class="col-4">
              <label for="a_username">Username</label>
              <input type="text" name="a_username" id="a_username" class="form-control" required>
            </div>
          </div>
        </div> <!-- modal body -->
        <!-- Modal Footer -->
        <div class="modal-footer d-flex justify-content-between w-100">
          <!-- Reset Password button on the left -->
          <button type="resetPassword" class="btn btn-danger resetPassword-btn"
            data-id="<?php echo !empty($_SESSION['Identifier']) ? htmlspecialchars($_SESSION['Identifier']) : 'No Account'; ?>">Reset
            Password</button>
          <!-- Right-aligned buttons: View Personal Information, Update, Close -->
          <div>
            <button type="submit" class="btn btn-primary">Update</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
<form id="resetPasswordForm" action="../api/api_users.php" method="post">
  <div id="resetPasswordModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header bg-white text-white">
          <h4 class="modal-title" style="font-size: 1rem;">Reset Password</h4>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <!-- Modal Body -->
        <div class="modal-body">
          <input type="hidden" name="Identifier" value="<?php echo htmlspecialchars($_SESSION['Identifier']); ?>">
          <div class="form-group row">
            <div class="col-md-12">
              <label for="reset_username">Username<span class="text-danger">*</span></label>
              <input type="text" name="reset_username" id="reset_username" class="form-control" readonly>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-md-6">
              <label for="new_password">New Password<span class="text-danger">*</span></label>
              <input type="password" name="new_password" id="new_password" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label for="retype_password">Retype New Password<span class="text-danger">*</span></label>
              <input type="password" name="retype_password" id="retype_password" class="form-control" required>
            </div>
          </div>
        </div>
        <!-- Modal Footer -->
        <div class="modal-footer d-flex justify-content-between w-100">
          <!-- Left-aligned Reset Password button -->
          <button type="submit" class="btn btn-warning resetPass-btn">Save</button>
          <!-- Right-aligned Close button -->
          <div>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
<form id="updateSchoolInfoForm" action="../api/api_curriculum.php" method="post">
  <div class="modal fade" id="updateSchoolInfoModal" tabindex="-1" aria-labelledby="updateSchoolInfoModalLabel">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="updateSchoolInfoModalLabel">Update School Information</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- Hidden input for school_id -->
          <input type="hidden" name="id" id="id" value="">
          <div class="form-group row">
            <div class="col-6">
              <label for="school_name">School Name</label>
              <input type="text" name="school_name" id="school_name" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()"
                required>
            </div>
            <div class="col-6">
              <label for="school_id">School ID</label>
              <input type="text" name="school_id" id="school_id" class="form-control" required>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-4">
              <label for="region">Region</label>
              <input type="text" name="region" id="region" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
            </div>
            <div class="col-4">
              <label for="division">Division</label>
              <input type="text" name="division" id="division" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
            </div>
            <div class="col-4">
              <label for="district">District</label>
              <input type="text" name="district" id="district" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
            </div>
          </div>
          <div class="form-group row">
            <div class="col-6">
              <label for="bosy_date">BoSY Date</label>
              <input type="date" name="bosy_date" id="bosy_date" class="form-control">
            </div>
            <div class="col-6">
              <label for="eosy_date">EoSY Date</label>
              <input type="date" name="eosy_date" id="eosy_date" class="form-control">
            </div>
          </div>
          <div class="form-group row">
            <div class="col-6">
              <label for="school_head">School Head</label>
              <input type="text" name="school_head" id="school_head" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
            </div>
            <div class="col-6">
              <label for="school_curriculum">Curriculum</label>
              <select name="school_curriculum" id="school_curriculum" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
                <option value="">--</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Update</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>