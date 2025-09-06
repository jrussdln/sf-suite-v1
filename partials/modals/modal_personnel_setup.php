<form id="addPersonnelForm" action="../api/api_personnel.php" method="post">
    <div id="addPersonnelModal" class="modal fade" id="modal-xl">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title">Add New Personnel</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;
                    </button>
                </div>
                <div class="modal-body">
                    <input type="text" name="a_personnel_id" id="a_personnel_id" class="form-control" hidden>
                    <div class="form-group row">
                        <div class="col-6">
                            <label for="a_empno">Employee Number</label>
                            <input type="text" name="a_empno" id="a_empno" class="form-control" onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()" required>
                        </div>
                        <div class="col-6">
                            <label for="a_email">Email Address</label>
                            <input type="email" name="a_email" id="a_email" class="form-control" onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-3">
                            <label for="a_lname">Last Name</label>
                            <input type="text" name="a_lname" id="a_lname" class="form-control" onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()" required>
                        </div>
                        <div class="col-3">
                            <label for="a_mname">Middle Name</label>
                            <input type="text" name="a_mname" id="a_mname" class="form-control" onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()" >
                        </div>
                        <div class="col-3">
                            <label for="a_fname">First Name</label>
                            <input type="text" name="a_fname" id="a_fname" class="form-control" onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()" required>
                        </div>
                        <div class="col-3">
                            <label for="a_ename">Extension Name</label>
                            <input type="text" name="a_ename" id="a_ename" class="form-control" onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-2">
                            <label for="a_sex">Gender</label>
                            <select id="a_sex" name="a_sex" class="form-control" onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()" required>
                                <option value="">--</option>
                                <option value="F">Female</option>
                                <option value="M">Male</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="a_fund_source">FUND SOURCE</label>
                            <input type="text" name="a_fund_source" id="a_fund_source" class="form-control" onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
                        </div>
                        <div class="col-4">
                            <label for="birthdate">Birthdate</label>
                            <input type="date" name="a_birthdate" id="a_birthdate" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                      <div class="col-4">
                          <label for="a_employment_status">Employment Status</label>
                          <select name="a_employment_status" id="a_employment_status" class="form-control" required>
                              <option value="">--</option>
                              <option value="PERMANENT">PERMANENT</option>
                              <option value="PROBATIONARY">PROBATIONARY</option>
                              <option value="CONTRACTUAL">CONTRACTUAL</option>
                              <option value="JOB ORDER">JOB ORDER (JO) / CONTRACT OF SERVICE (COS)</option>
                              <option value="SUBSTITUTE">SUBSTITUTE TEACHER</option>
                              <option value="INACTIVE">INACTIVE</option>
                          </select>
                      </div>
                        <div class="col-4">
                            <label for="a_degree">Degree</label>
                            <input type="text" name="a_degree" id="a_degree" class="form-control" onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
                        </div>
                        <div class="col-4">
                            <label for="a_post_graduate">Post Graduate</label>
                            <input type="text" name="a_post_graduate" id="a_post_graduate" class="form-control" onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
                        </div>
                    </div>
                    <div class="form-group row">
                      <div class="col-4">
                        <label for="a_major">Major Field of Study</label>
                        <input type="text" name="a_major" id="a_major" class="form-control" onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
                      </div>
                      <div class="col-4">
                              <label for="a_specialization">Specialization</label>
                              <input type="text" name="a_specialization" id="a_specialization" class="form-control" onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
                      </div>
                      <div class="col-4">
                          <label for="a_minor">Minor Field of Study</label>
                          <input type="text" name="a_minor" id="a_minor" class="form-control" onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
                      </div>
                    </div>
                </div> <!-- modal body -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Add Personnel</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</form>
<form id="editPersonnelForm" action="../api/api_personnel.php" method="post">
    <div id="editPersonnelModal" class="modal fade" id="modal-xl">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title" id="editPersonnelModalLabel">Edit Personnel</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Hidden field for PersonnelId -->
                    <input type="text" name="PersonnelId" id="PersonnelId" class="form-control"  hidden>
                    <div class="form-group row">
                        <div class="col-6">
                            <label for="e_EmpNo">Employee Number</label>
                            <input type="text" name="e_EmpNo" id="e_EmpNo" class="form-control" onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()" required>
                        </div>
                        <div class="col-6">
                            <label for="e_email">Email Address</label>
                            <input type="email" name="e_email" id="e_email" class="form-control" onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-3">
                            <label for="e_lname">Last Name</label>
                            <input type="text" name="e_lname" id="e_lname" class="form-control" onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()" required>
                        </div>
                        <div class="col-3">
                            <label for="e_mname">Middle Name</label>
                            <input type="text" name="e_mname" id="e_mname" class="form-control" onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()" >
                        </div>
                        <div class="col-3">
                            <label for="e_fname">First Name</label>
                            <input type="text" name="e_fname" id="e_fname" class="form-control" onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()" required>
                        </div>
                        <div class="col-3">
                            <label for="e_ename">Extension Name</label>
                            <input type="text" name="e_ename" id="e_ename" class="form-control" onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-2">
                            <label for="e_sex">Gender</label>
                            <select id="e_sex" name="e_sex" class="form-control" onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()" required>
                                <option value="">--</option>
                                <option value="F">Female</option>
                                <option value="M">Male</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="e_fund_source">FUND SOURCE</label>
                            <input type="text" name="e_fund_source" id="e_fund_source" class="form-control" onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
                        </div>
                        <div class="col-4">
                            <label for="birthdate">Birthdate</label>
                            <input type="date" name="e_birthdate" id="e_birthdate" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                      <div class="col-4">
                            <label for="e_employment_status">Employment Status</label>
                            <select name="e_employment_status" id="e_employment_status" class="form-control" required>
                                <option value="">--</option>
                                <option value="PERMANENT">PERMANENT</option>
                                <option value="PROBATIONARY">PROBATIONARY</option>
                                <option value="CONTRACTUAL">CONTRACTUAL</option>
                                <option value="JOB ORDER">JOB ORDER (JO) / CONTRACT OF SERVICE (COS)</option>
                                <option value="SUBSTITUTE">SUBSTITUTE TEACHER</option>
                                <option value="INACTIVE">INACTIVE</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <label for="e_degree">Degree</label>
                            <input type="text" name="e_degree" id="e_degree" class="form-control" onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
                        </div>
                        <div class="col-4">
                            <label for="e_post_graduate">Post Graduate</label>
                            <input type="text" name="e_post_graduate" id="e_post_graduate" class="form-control" onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
                        </div>
                    </div>
                    <div class="form-group row">
                      <div class="col-4">
                        <label for="e_major">Major Field of Study</label>
                        <input type="text" name="e_major" id="e_major" class="form-control" onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
                    </div>
                      <div class="col-4">
                            <label for="e_specialization">Specialization</label>
                            <input type="text" name="e_specialization" id="e_specialization" class="form-control" onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
                      </div>
                      <div class="col-4">
                        <label for="e_minor">Minor Field of Study</label>
                        <input type="text" name="e_minor" id="e_minor" class="form-control" onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
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
<div class="modal fade" id="viewSubjectTaughtModal"  role="dialog" aria-labelledby="viewSubjectTaughtModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewSubjectTaughtModalLabel">Subjects Taught</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;
                </button>
            </div>
            <div class="modal-body">    
                <div class="table-responsive">
                    <table id="viewSubjectTaught_table" class="table table-bordered table-striped table-hover" style="width: 100%;">
                        <thead class="bg-info">
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <!-- Button for adding a subject -->
                <button type="button" class="btn btn-info addSubt-btn">
                    <i class="fas fa-plus"></i> Add Subject Taught
                </button>
                <!-- Button for adding a subject -->
                <button type="button" class="btn btn-info " id="smartCopy-btn">
                    <i class="fas fa-copy"></i> Smart Copy
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<form id="updateSubjectTaughtForm" action="../api/api_personnel.php" method="post">
  <!-- Update Subject Taught Modal -->
  <div class="modal fade" id="updateSubjectTaughtModal" tabindex="-1" aria-labelledby="updateSubjectTaughtModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h4 class="modal-title" id="updateSubjectTaughtModalLabel">Update Subject Taught</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;
          </button>
        </div>
        <div class="modal-body">
          <!-- Hidden Field for stac_id -->
          <div class="form-group row">
            <div class="col-12">
              <input type="text" class="form-control" id="u_stac_id" name="u_stac_id" hidden />
              <input type="text" class="form-control" id="u_PersonnelId" name="u_PersonnelId" hidden />
            </div>
          </div>
          <!-- Subject Taught -->
          <div class="form-group row">
            <div class="col-3">
              <label for="u_subjectTaught">Subject Taught</label>
              <select class="form-control" id="u_subjectTaught" name="u_subjectTaught" required>
              </select>
            </div>
            <div class="col-3">
            <label for="u_subject_code">Subject Code</label>
            <select class="form-control" id="u_subject_code" name="u_subject_code" required>
            </select>
            </div>
            <div class="col-3">
            <label for="u_section">Grade/Section</label>
            <select class="form-control" id="u_section" name="u_section" required>
            </select>
            </div>
            <div class="col-3">
              <label for="u_stDay">Day (M/T/W/Th/F)</label>
              <select class="form-control" id="u_stDay" name="u_stDay" required>
              <option value="">--</option>
              <!-- Single Days -->
              <option value="M">M</option>
              <option value="T">T</option>
              <option value="W">W</option>
              <option value="Th">Th</option>
              <option value="F">F</option>
              <!-- Pairs -->
              <option value="M, T">M, T</option>
              <option value="M, W">M, W</option>
              <option value="M, Th">M, Th</option>
              <option value="M, F">M, F</option>
              <option value="T, W">T, W</option>
              <option value="T, Th">T, Th</option>
              <option value="T, F">T, F</option>
              <option value="W, Th">W, Th</option>
              <option value="W, F">W, F</option>
              <option value="Th, F">Th, F</option>
              <!-- Triplets -->
              <option value="M, T, W">M, T, W</option>
              <option value="M, T, Th">M, T, Th</option>
              <option value="M, T, F">M, T, F</option>
              <option value="M, W, Th">M, W, Th</option>
              <option value="M, W, F">M, W, F</option>
              <option value="M, Th, F">M, Th, F</option>
              <option value="T, W, Th">T, W, Th</option>
              <option value="T, W, F">T, W, F</option>
              <option value="T, Th, F">T, Th, F</option>
              <option value="W, Th, F">W, Th, F</option>
              <!-- Quadruplets -->
              <option value="M, T, W, Th">M, T, W, Th</option>
              <option value="M, T, W, F">M, T, W, F</option>
              <option value="M, T, Th, F">M, T, Th, F</option>
              <option value="M, W, Th, F">M, W, Th, F</option>
              <option value="T, W, Th, F">T, W, Th, F</option>
              <!-- Full Combination -->
              <option value="M, T, W, Th, F">M, T, W, Th, F</option>
              </select>
            </div>
          </div>
          <!-- Time Fields -->
          <div class="form-group row">
            <div class="col-4">
              <label for="u_stFrom">From (00:00)</label>
              <input type="time" class="form-control" id="u_stFrom" name="u_stFrom" required />
            </div>
            <div class="col-4">
              <label for="u_stTo">To (00:00)</label>
              <input type="time" class="form-control" id="u_stTo" name="u_stTo" required />
            </div>
            <div class="col-4">
              <label for="u_tatMin">Total Actual Teaching Minutes</label>
              <input type="number" class="form-control" id="u_tatMin" name="u_tatMin" readonly/>
            </div>
          </div>
        </div> <!-- modal body -->
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Update</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>
<form id="addSubjectTaughtForm" action="../api/api_personnel.php" method="post">
  <!-- Add Subject Taught Modal -->
  <div class="modal fade" id="addSubjectTaughtModal" tabindex="-1" aria-labelledby="addSubjectTaughtModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h4 class="modal-title" id="addSubjectTaughtModalLabel">Add Subject Taught</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;
          </button>
        </div>
        <div class="modal-body">
          <!-- Hidden Field for a_stac_id -->
          <div class="form-group row">
            <div class="col-12">
              <input type="text" class="form-control" id="a_stac_id" name="a_stac_id" hidden />
              <input type="text" class="form-control" id="a_PersonnelId" name="a_PersonnelId" hidden />
            </div>
          </div>
          <!-- Subject Taught -->
          <div class="form-group row">
            <div class="col-3">
              <label for="a_subjectTaught">Subject Taught</label>
                <select class="form-control" id="a_subjectTaught" name="a_subjectTaught" required>
                </select>
              </div>
            <div class="col-3">
            <label for="a_subject_code">Subject Code</label>
            <select class="form-control" id="a_subject_code" name="a_subject_code" required>
            </select>
            </div>
            <div class="col-3">
            <label for="a_section">Grade/Section</label>
            <select class="form-control" id="a_section" name="a_section" required>
            </select>
            </div>
            <div class="col-3">
              <label for="a_stDay">Day (M/T/W/Th/F)</label>
              <select class="form-control" id="a_stDay" name="a_stDay" required>
              <option value="">--</option>
              <!-- Single Days -->
              <option value="M">M</option>
              <option value="T">T</option>
              <option value="W">W</option>
              <option value="Th">Th</option>
              <option value="F">F</option>
              <!-- Pairs -->
              <option value="M, T">M, T</option>
              <option value="M, W">M, W</option>
              <option value="M, Th">M, Th</option>
              <option value="M, F">M, F</option>
              <option value="T, W">T, W</option>
              <option value="T, Th">T, Th</option>
              <option value="T, F">T, F</option>
              <option value="W, Th">W, Th</option>
              <option value="W, F">W, F</option>
              <option value="Th, F">Th, F</option>
              <!-- Triplets -->
              <option value="M, T, W">M, T, W</option>
              <option value="M, T, Th">M, T, Th</option>
              <option value="M, T, F">M, T, F</option>
              <option value="M, W, Th">M, W, Th</option>
              <option value="M, W, F">M, W, F</option>
              <option value="M, Th, F">M, Th, F</option>
              <option value="T, W, Th">T, W, Th</option>
              <option value="T, W, F">T, W, F</option>
              <option value="T, Th, F">T, Th, F</option>
              <option value="W, Th, F">W, Th, F</option>
              <!-- Quadruplets -->
              <option value="M, T, W, Th">M, T, W, Th</option>
              <option value="M, T, W, F">M, T, W, F</option>
              <option value="M, T, Th, F">M, T, Th, F</option>
              <option value="M, W, Th, F">M, W, Th, F</option>
              <option value="T, W, Th, F">T, W, Th, F</option>
              <!-- Full Combination -->
              <option value="M, T, W, Th, F">M, T, W, Th, F</option>
              </select>
            </div>
          </div>
          <!-- Time Fields -->
          <div class="form-group row">
            <div class="col-4">
              <label for="a_stFrom">From (00:00)</label>
              <input type="time" class="form-control" id="a_stFrom" name="a_stFrom" required />
            </div>
            <div class="col-4">
              <label for="a_stTo">To (00:00)</label>
              <input type="time" class="form-control" id="a_stTo" name="a_stTo" required />
            </div>
            <div class="col-4">
              <label for="a_tatMin">Total Actual Teaching Minutes</label>
              <input type="number" class="form-control" id="a_tatMin" name="a_tatMin" readonly />
            </div>
          </div>
        </div> <!-- modal body -->
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Add</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>
<div class="modal fade" id="ancAssignmentModal"  role="dialog" aria-labelledby="ancAssignmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ancAssignmentModalLabel">Ancillary Assignments</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;
                </button>
            </div>
            <div class="modal-body">    
                <div class="table-responsive">
                    <table id="anc_assignment_table" class="table table-bordered table-striped table-hover" style="width: 100%;">
                        <thead class="bg-info">
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-info addAncAssignment-btn">
                  <i class="fas fa-plus"></i> Add Ancillary Assignment
              </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<form id="addAncAssignmentForm" action="../api/api_personnel.php" method="post">
  <!-- Add Ancillary Assignment Modal -->
  <div class="modal fade" id="addAncAssignmentModal" tabindex="-1" aria-labelledby="addAncAssignmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h4 class="modal-title" id="addAncAssignmentModalLabel">Add Ancillary Assignment</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;
          </button>
        </div>
        <div class="modal-body">
          <!-- Hidden Field for PersonnelId -->
          <div class="form-group row">
            <div class="col-12">
              <input type="text" class="form-control" id="aa_PersonnelId" name="aa_PersonnelId" hidden />
            </div>
          </div>
          <!-- Assignment Description -->
          <div class="form-group row">
            <div class="col-12">
            <label for="a_ass_desc">Assignment Description</label>
            <select class="form-control" id="a_ass_desc" name="a_ass_desc" required>
                <option value="">Select</option>
        </select>
            </div>
          </div>
        </div> <!-- modal body -->
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Add</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>
<!-- Ancillary Assignment List Modal -->
<div class="modal fade" id="ancAssListModal" role="dialog" aria-labelledby="ancAssListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ancAssListModalLabel">Ancillary Assignment List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;
                </button>
            </div>
            <div class="modal-body">    
                <div class="table-responsive">
                    <table id="anc_ass_list_table" class="table table-bordered table-striped table-hover" style="width: 100%;">
                        <thead class="bg-info">
                        </thead>
                        <tbody>
                            <!-- Data will be populated here via AJAX or server-side rendering -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info addAncAssList-btn">
                    <i class="fas fa-plus"></i> Add Ancillary Assignment
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Add Ancillary Assignment Modal -->
<div class="modal fade" id="addAncAssListModal" role="dialog" aria-labelledby="addAncAssListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAncAssListModalLabel">Ancillary Assignment List / Add</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;
                </button>
            </div>
            <div class="modal-body">
                <form id="addAncAssListForm">
                    <div class="form-group">
                        <label for="anc_ass_list">Assignment Description <span class="text-danger"></label>
                        <input type="text" class="form-control" id="anc_ass_list" name="anc_ass_list"  onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="saveAncAssListBtn">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="viewPlantillaModal"  role="dialog" aria-labelledby="viewPlantillaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewPlantillaModalLabel">Plantilla Position</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;
                </button>
            </div>
            <div class="modal-body">    
                <div class="table-responsive">
                    <table id="viewPlantilla_table" class="table table-bordered table-striped table-hover" style="width: 100%;">
                        <thead class="bg-info">
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-info addPlantilla-btn">
                  <i class="fas fa-plus"></i> Add Plantilla Position
              </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<form id="addPlantillaForm" action="../api/api_personnel.php" method="post">
  <!-- Add Plantilla Position Modal -->
  <div class="modal fade" id="addPlantillaModal" tabindex="-1" aria-labelledby="addPlantillaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h4 class="modal-title" id="addPlantillaModalLabel">Add Plantilla Position</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;
          </button>
        </div>
        <div class="modal-body">
          <!-- Hidden Field for PersonnelId -->
          <div class="form-group row">
            <div class="col-12">
              <input type="text" class="form-control" id="pp_PersonnelId" name="pp_PersonnelId" hidden />
            </div>
          </div>
          <!-- Plantilla Description -->
        <div class="form-group row">
          <div class="col-12">
            <label for="pp_pp_desc">Description </label>
            <select class="form-control" id="pp_pp_desc" name="pp_pp_desc" required>
              <option value="">--</option>
            </select>
          </div>
        </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Add</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>
<form id="updatePlantillaForm" action="../api/api_personnel.php" method="post">
  <!-- Add Plantilla Position Modal -->
  <div class="modal fade" id="updatePlantillaModal" tabindex="-1" aria-labelledby="updatePlantillaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h4 class="modal-title" id="updatePlantillaModalLabel">Update Plantilla Position</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group row">
            <div class="col-12">
              <input type="text" class="form-control" id="u_pp_PersonnelId" name="u_pp_PersonnelId" hidden />
              <input type="text" class="form-control" id="u_pp_pp_id" name="u_pp_pp_id" hidden />
            </div>
          </div>
          <div class="form-group row">
          <div class="col-12">
            <label for="u_pp_pp_desc">Description </label>
            <select class="form-control" id="u_pp_pp_desc" name="u_pp_pp_desc" required>
              <option value="">--</option>
            </select>
          </div>
        </div>
        </div> <!-- modal body -->
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Add</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>
<div class="modal fade" id="viewPlantillaPosListModal"  role="dialog" aria-labelledby="viewPlantillaPosListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewPlantillaPosListModalLabel">Plantilla Position List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;
                </button>
            </div>
            <div class="modal-body">    
                <div class="table-responsive">
                    <table id="viewPlantillaPosList_table" class="table table-bordered table-striped table-hover" style="width: 100%;">
                        <thead class="bg-info">
                        </thead>
                    </table>
                </div>
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-info" id="addPlantillaPosList-btn">
                    <i class="fas fa-plus"></i> Add Plantilla Position
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<form id="addPlantillaPosListForm" action="../api/api_personnel.php" method="post">
  <!-- Add Plantilla Position Modal -->
  <div class="modal fade" id="addPlantillaPosListModal" tabindex="-1" aria-labelledby="addPlantillaPosListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h4 class="modal-title" id="addPlantillaPosListModalLabel">Plantilla Position List / Add</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;
          </button>
        </div>
        <div class="modal-body">
          <!-- Hidden Field for PersonnelId -->
          <div class="form-group row">
            <div class="col-12">
              <input type="text" class="form-control" id="a_ppl_id" name="a_ppl_id" hidden />
            </div>
          </div>
          <!-- Plantilla Description -->
          <div class="form-group row">
            <div class="col-6">
              <label for="a_ppl_desc">Description</label>
              <input type="text" class="form-control" id="a_ppl_desc" name="a_ppl_desc" required />
            </div>
            <div class="col-6">
                <label for="a_ppl_code">Code</label>
                <input type="text" class="form-control" id="a_ppl_code" name="a_ppl_code" required />
            </div>
        </div>
        <div class="form-group row">
            <div class="col-6">
                <label for="a_ppl_rank">Rank</label>
                <select class="form-control" id="a_ppl_rank" name="a_ppl_rank" required>
                    <option value="">--</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                </select>
            </div>
            <div class="col-6">
                <label for="a_ppl_category">Category</label>
                <select class="form-control" id="a_ppl_category" name="a_ppl_category" required>
                    <option value="">--</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                </select>
            </div>
        </div>
        <div class="form-group row mb-0">
          <div class="col-12">
              <p style="font-size: 14px; color:rgb(255, 0, 0);">Plantilla Position Category Guidelines:</p> <!-- Lighter red color -->
              <p style="font-size: 14px; color:rgb(255, 0, 0);"><i>
                - (A) Nationally-Funded Teaching & Teaching Related Items<br>		
                - (B) Nationally-Funded Non Teaching Items<br>
                - (C) Other Appointments and Funding Sources<br>
                </i>		
              </p>
            </div>
      </div>
        </div> <!-- modal body -->
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Add</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>
<form id="updatePlantillaPosListForm" action="../api/api_personnel.php" method="post">
  <!-- Update Plantilla Position Modal -->
  <div class="modal fade" id="updatePlantillaPosListModal" tabindex="-1" aria-labelledby="updatePlantillaPosListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-warning">
          <h4 class="modal-title" id="updatePlantillaPosListModalLabel">Plantilla Position List / Edit</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;
          </button>
        </div>
        <div class="modal-body">
          <!-- Hidden Field for PersonnelId -->
          <div class="form-group row">
            <div class="col-12">
              <input type="text" class="form-control" id="u_ppl_id" name="u_ppl_id" hidden />
            </div>
          </div>
          <!-- Plantilla Description -->
          <div class="form-group row">
            <div class="col-12">
              <label for="u_ppl_desc">Description</label>
              <input type="text" class="form-control" id="u_ppl_desc" name="u_ppl_desc" required />
            </div>
          </div>
          <div class="form-group row">
            <div class="col-12">
                <label for="u_ppl_code">Code</label>
                <input type="text" class="form-control" id="u_ppl_code" name="u_ppl_code" required />
            </div>
        </div>
        <div class="form-group row">
            <div class="col-12">
                <label for="u_ppl_rank">Rank</label>
                <select class="form-control" id="u_ppl_rank" name="u_ppl_rank" required>
                    <option value="">--</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-12">
                <label for="u_ppl_category">Category</label>
                <select class="form-control" id="u_ppl_category" name="u_ppl_category" required>
                    <option value="">--</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                </select>
            </div>
        </div>
        <div class="form-group row mb-0">
          <div class="col-12">
              <p style="font-size: 14px; color:rgb(255, 0, 0);">Plantilla Position Category Guidelines:</p> <!-- Lighter red color -->
              <p style="font-size: 14px; color:rgb(255, 0, 0);"><i>
                - (A) Nationally-Funded Teaching & Teaching Related Items<br>		
                - (B) Nationally-Funded Non Teaching Items<br>
                - (C) Other Appointments and Funding Sources<br>
                </i>		
              </p>
            </div>
      </div>
        </div> <!-- modal body -->
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Update</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>
