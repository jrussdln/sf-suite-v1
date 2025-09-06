<form id="editStudentForm" action="../api/api_student.php" method="post">
  <div id="editStudentModal" class="modal fade" id="modal-xl">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
        <div class="modal-header bg-success">
          <h4 class="modal-title" id="studentNameModalTitle">Edit Student Information</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="es_studentId" id="es_studentId" class="form-control" required>
          <div class="form-group row">
            <div class="col-3">
              <label for="es_lrn">Learner Reference Number</label>
              <input type="text" name="es_lrn" id="es_lrn" class="form-control" required>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-3">
              <label for="es_name">Name (Last Name, First Name, Middle Name, Ext.)</label>
              <input type="text" name="es_name" id="es_name" class="form-control" required>
            </div>
            <div class="col-3">
              <label for="es_section">Section</label>
              <select name="es_section" id="es_section" class="form-control" required>
                <option value="">--</option>
              </select>
            </div>
            <div class="col-3">
              <label for="es_grade_level">Grade Level</label>
              <select name="es_grade_level" id="es_grade_level" class="form-control" required>
                <option value="">--</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
              </select>
            </div>
            <div class="col-3">
              <label for="es_school_year">School Year</label>
              <select name="es_school_year" id="es_school_year" class="form-control" required>
                <option value="">--</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-4">
              <label for="es_birth_date">Birthdate</label>
              <input type="date" name="es_birth_date" id="es_birth_date" class="form-control">
            </div>
            <div class="col-4">
              <label for="es_gender">Sex</label>
              <select id="es_gender" name="es_gender" class="form-control" required>
                <option value="">--</option>
                <option value="F">Female</option>
                <option value="M">Male</option>
              </select>
            </div>
            <div class="col-4">
              <label for="es_learning_modality">Learning Modality</label>
              <input type="text" name="es_learning_modality" id="es_learning_modality" class="form-control">
            </div>
          </div>
          <div class="form-group row">
            <div class="col-3">
              <label for="es_hstp">House No. / Sitio / Purok</label>
              <input type="text" name="es_hstp" id="es_hstp" class="form-control">
            </div>
            <div class="col-3">
              <label for="es_barangay">Barangay</label>
              <input type="text" name="es_barangay" id="es_barangay" class="form-control" required>
            </div>
            <div class="col-3">
              <label for="es_city">City</label>
              <input type="text" name="es_city" id="es_city" class="form-control" required>
            </div>
            <div class="col-3">
              <label for="es_province">Province</label>
              <input type="text" name="es_province" id="es_province" class="form-control" required>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-6">
              <label for="es_father_name">Father's Name</label>
              <input type="text" name="es_father_name" id="es_father_name" class="form-control">
            </div>
            <div class="col-6">
              <label for="es_mother_maiden_name">Mother's Maiden Name</label>
              <input type="text" name="es_mother_maiden_name" id="es_mother_maiden_name" class="form-control">
            </div>
          </div>
          <div class="form-group row">
            <div class="col-4">
              <label for="es_guardian_name">Guardian's Name</label>
              <input type="text" name="es_guardian_name" id="es_guardian_name" class="form-control">
            </div>
            <div class="col-4">
              <label for="es_guardian_relationship">Guardian's Relationship</label>
              <input type="text" name="es_guardian_relationship" id="es_guardian_relationship" class="form-control">
            </div>
            <div class="col-4">
              <label for="es_contact_number">Contact Number</label>
              <input type="text" name="es_contact_number" id="es_contact_number" class="form-control">
            </div>
          </div>
          <div class="form-group row">
            <div class="col-4">
              <label for="es_mother_tongue">Mother Tongue</label>
              <input type="text" name="es_mother_tongue" id="es_mother_tongue" class="form-control">
            </div>
            <div class="col-4">
              <label for="es_ethnic_group">Ethnic Group</label>
              <input type="text" name="es_ethnic_group" id="es_ethnic_group" class="form-control">
            </div>
            <div class="col-4">
              <label for="es_religion">Religion</label>
              <input type="text" name="es_religion" id="es_religion" class="form-control">
            </div>
          </div>
          <div class="form-group row">
            <div class="col-6">
              <label for="es_remarks">Remarks</label>
              <select name="es_remarks" id="es_remarks" class="form-control">
                <option value="">--</option>
                <option value="T/O">Transferred Out</option>
                <option value="T/I">Transferred In</option>
                <option value="DRP">Dropped</option>
                <option value="LE">Late Enrollment</option>
                <option value="CCT">CCT Recipient</option>
                <option value="B/A">Balik Aral</option>
                <option value="SNED">Special Needs Education</option>
                <option value="ACL">Accelerated</option>
              </select>
            </div>
            <div class="col-6">
              <label for="es_strand_track">Strand/Track</label>
              <input type="text" name="es_strand_track" id="es_strand_track" class="form-control">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>
<form id="addStudentForm" action="../api/api_student.php" method="post">
  <div id="addStudentModal" class="modal fade" id="modal-xl">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
        <div class="modal-header bg-success">
          <h4 class="modal-title">Add Student Information</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group row">
            <div class="col-3">
              <label for="as_lrn">Learner Reference Number</label>
              <input type="text" name="as_lrn" id="as_lrn" class="form-control" required>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-3">
              <label for="as_name">Name (Last Name, First Name, Middle Name, Ext.)</label>
              <input type="text" name="as_name" id="as_name" class="form-control" required>
            </div>
            <div class="col-3">
              <label for="as_section">Section</label>
              <select name="as_section" id="as_section" class="form-control" required>
                <option value="">--</option>
              </select>
            </div>
            <div class="col-3">
              <label for="as_grade_level">Grade Level</label>
              <select name="as_grade_level" id="as_grade_level" class="form-control" required>
                <option value="">--</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
              </select>
            </div>
            <div class="col-3">
              <label for="as_school_year">School Year</label>
              <select name="as_school_year" id="as_school_year" class="form-control" required>
                <option value="">--</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-4">
              <label for="as_birth_date">Birthdate</label>
              <input type="date" name="as_birth_date" id="as_birth_date" class="form-control">
            </div>
            <div class="col-4">
              <label for="as_sex">Sex</label>
              <select id="as_sex" name="as_sex" class="form-control" required>
                <option value="">--</option>
                <option value="F">Female</option>
                <option value="M">Male</option>
              </select>
            </div>
            <div class="col-4">
              <label for="as_learning_modality">Learning Modality</label>
              <input type="text" name="as_learning_modality" id="as_learning_modality" class="form-control">
            </div>
          </div>
          <div class="form-group row">
            <div class="col-3">
              <label for="es_hssp">House No. / Sitio / Purok</label>
              <input type="text" name="as_hssp" id="as_hssp" class="form-control">
            </div>
            <div class="col-3">
              <label for="as_barangay">Barangay</label>
              <input type="text" name="as_barangay" id="as_barangay" class="form-control" required>
            </div>
            <div class="col-3">
              <label for="as_municipality_city">City</label>
              <input type="text" name="as_municipality_city" id="as_municipality_city" class="form-control" required>
            </div>
            <div class="col-3">
              <label for="as_province">Province</label>
              <input type="text" name="as_province" id="as_province" class="form-control" required>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-6">
              <label for="as_father_name">Father's Name</label>
              <input type="text" name="as_father_name" id="as_father_name" class="form-control">
            </div>
            <div class="col-6">
              <label for="as_mother_maiden_name">Mother's Maiden Name</label>
              <input type="text" name="as_mother_maiden_name" id="as_mother_maiden_name" class="form-control">
            </div>
          </div>
          <div class="form-group row">
            <div class="col-4">
              <label for="as_guardian_name">Guardian's Name</label>
              <input type="text" name="as_guardian_name" id="as_guardian_name" class="form-control">
            </div>
            <div class="col-4">
              <label for="as_guardian_relationship">Guardian's Relationship</label>
              <input type="text" name="as_guardian_relationship" id="as_guardian_relationship" class="form-control">
            </div>
            <div class="col-4">
              <label for="as_contact_number">Contact Number</label>
              <input type="text" name="as_contact_number" id="as_contact_number" class="form-control">
            </div>
          </div>
          <div class="form-group row">
            <div class="col-4">
              <label for="as_mother_tongue">Mother Tongue</label>
              <input type="text" name="as_mother_tongue" id="as_mother_tongue" class="form-control">
            </div>
            <div class="col-4">
              <label for="as_ethnic_group">Ethnic Group</label>
              <input type="text" name="as_ethnic_group" id="as_ethnic_group" class="form-control">
            </div>
            <div class="col-4">
              <label for="as_religion">Religion</label>
              <input type="text" name="as_religion" id="as_religion" class="form-control">
            </div>
          </div>
          <div class="form-group row">
            <div class="col-4">
              <label for="as_remarks">Remarks</label>
              <select name="as_remarks" id="as_remarks" class="form-control">
                <option value="">--</option>
                <option value="T/O">Transferred Out</option>
                <option value="T/I">Transferred In</option>
                <option value="DRP">Dropped</option>
                <option value="LE">Late Enrollment</option>
                <option value="CCT">CCT Recipient</option>
                <option value="B/A">Balik Aral</option>
                <option value="SNED">Special Needs Education</option>
                <option value="ACL">Accelerated</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>
<form id="editPromotionForm" action="../api/api_student.php" method="post">
  <div id="editPromotionModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editPromotionModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h4 class="modal-title" id="editPromotionModalLabel">Edit Promotion Information</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="ep_pa_id" name="ep_pa_id">
          <div class="form-group row">
            <div class="col-6">
              <label for="ep_name">Full Name</label>
              <input type="text" name="ep_name" id="ep_name" class="form-control" readonly>
            </div>
            <div class="col-6">
              <label for="ep_section">Section</label>
              <input type="text" name="ep_section" id="ep_section" class="form-control" readonly>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-4">
              <label for="ep_sex">Sex</label>
              <select id="ep_sex" name="ep_sex" class="form-control" readonly>
                <option value="">--</option>
                <option value="F">Female</option>
                <option value="M">Male</option>
              </select>
            </div>
            <div class="col-4">
              <label for="ep_general_average">General Average</label>
              <input type="text" name="ep_general_average" id="ep_general_average" class="form-control">
            </div>
            <div class="col-4">
              <label for="ep_action_taken">Action Taken </label>
              <select name="ep_action_taken" id="ep_action_taken" class="form-control">
                <option value="">-- Select Action --</option>
                <option value="PROMOTED">PROMOTED</option>
                <option value="IRREGULAR">IRREGULAR</option>
                <option value="RETAINED">RETAINED</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-6">
              <label for="ep_cecs">Completed as of end of S.Y.</label>
              <input type="text" name="ep_cecs" id="ep_cecs" class="form-control">
            </div>
            <div class="col-6">
              <label for="ep_ecs">As of the end of the current S.Y.</label>
              <input type="text" name="ep_ecs" id="ep_ecs" class="form-control">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save Changes</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>