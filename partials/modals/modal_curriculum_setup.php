<form id="addSchoolYearForm" action="../api/api_curriculum.php" method="post">
  <div id="addSchoolYearModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addSchoolYearModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addSchoolYearModalLabel">Add New School Year</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="a_sy_term">Term<span class="h5 text-danger">*</span></label>
            <input type="text" name="a_sy_term" id="a_sy_term" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Create</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>
<form id="addCurriculumForm" action="../api/api_curriculum.php" method="post">
  <div id="addCurriculumModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addCurriculumModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addCurriculumModalLabel">Add New Curriculum</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="a_curriculum_desc">Curriculum Description<span class="h5 text-danger">*</span></label>
            <input type="text" name="a_curriculum_desc" id="a_curriculum_desc" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Create</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>
<form id="updateCurriculumForm" action="../api/api_curriculum.php" method="post">
  <div id="updateCurriculumModal" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="updateCurriculumModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="updateCurriculumModalLabel">Edit Curriculum</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="curriculum_id" id="curriculum_id" class="form-control" required>
          <!-- Curriculum Description -->
          <div class="form-group">
            <label for="u_curriculum_desc">Curriculum Description</label>
            <input type="text" name="u_curriculum_desc" id="u_curriculum_desc" class="form-control" required>
          </div>
          <!-- Junior High School -->
          <label class="mt-3">Junior High School</label>
          <!-- Grade 7 -->
          <label class="mt-2">Grade 7</label>
          <p id="subjects-grade-7" class="custom-text-right"></p>
          <!-- Grade 8 -->
          <label class="mt-3">Grade 8</label>
          <p id="subjects-grade-8" class="custom-text-right"></p>
          <!-- Grade 9 -->
          <label class="mt-3">Grade 9</label>
          <p id="subjects-grade-9" class="custom-text-right"></p>
          <!-- Grade 10 -->
          <label class="mt-3">Grade 10</label>
          <p id="subjects-grade-10" class="custom-text-right"></p>
          <!-- Senior High School -->
          <label class="mt-3">Senior High School</label>
          <!-- Grade 11 -->
          <label class="mt-2">Grade 11</label>
          <p id="subjects-grade-11" class="custom-text-right"></p>
          <!-- Grade 12 -->
          <label class="mt-2">Grade 12</label>
          <p id="subjects-grade-12" class="custom-text-right"></p>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Update</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>
<form id="addStrandTrackForm" action="../api/api_curriculum.php" method="post">
  <div id="addStrandTrackModal" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="addStrandTrackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addStrandTrackModalLabel">Add New School Year</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group row">
            <div class="col-6">
              <label for="ast_strand_track">Strand/Track<span class="h5 text-danger">*</span></label>
              <input type="text" name="ast_strand_track" id="ast_strand_track" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()"
                required>
            </div>
            <div class="col-6">
              <label for="ast_description">Description<span class="h5 text-danger">*</span></label>
              <input type="text" name="ast_description" id="ast_description" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()"
                required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Create</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>
<form id="editStrandTrackForm" action="../api/api_curriculum.php" method="post">
  <div id="editStrandTrackModal" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="editStrandTrackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editStrandTrackModalLabel">Edit Strand/Track</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="ust_id" id="ust_id">
          <div class="form-group row">
            <div class="col-6">
              <label for="ust_strand_track">Strand/Track<span class="h5 text-danger">*</span></label>
              <input type="text" name="ust_strand_track" id="ust_strand_track" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
            </div>
            <div class="col-6">
              <label for="ust_description">Description<span class="h5 text-danger">*</span></label>
              <input type="text" name="ust_description" id="ust_description" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()" onpaste="this.value = this.value.toUpperCase()">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Create</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>
<form id="addSubjectForm" action="../api/api_curriculum.php" method="post">
  <div class="modal fade" id="addSubjectModal" tabindex="-1" aria-labelledby="addSubjectModalLabel">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addSubjectModalLabel">Add New Subject</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group row">
            <div class="col-6">
              <label for="a_subject_code">Subject Code</label>
              <input type="text" name="a_subject_code" id="a_subject_code" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()">
            </div>
            <div class="col-6">
              <label for="a_curriculum">Curriculum</label>
              <select id="a_curriculum" name="a_curriculum" class="form-control">
                <option value="">--</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-4">
              <label for="a_grade_level">Grade Level</label>
              <select id="a_grade_level" name="a_grade_level" class="form-control" onchange="toggleSubGrade()" required>
                <option value="">--</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
              </select>
            </div>
            <div class="col-4">
              <label for="a_strand">Strand</label>
              <select id="a_strand" name="a_strand" class="form-control" disabled>
                <option value="">--</option>
                <option value="GAS">GAS</option>
                <option value="STEM">STEM</option>
                <option value="HUMS">HUMS</option>
                <option value="ABM">ABM</option>
                <option value="TVL-HE">TVL-HE</option>
                <option value="TVL-ICT">TVL-ICT</option>
                <option value="OTHERS">OTHERS</option>
              </select>
            </div>
            <div class="col-4">
              <label for="a_subject_name">Subject Name</label>
              <input type="text" name="a_subject_name" id="a_subject_name" class="form-control" required>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-12">
              <label for="a_subject_desc">Description</label>
              <textarea name="a_subject_desc" id="a_subject_desc" class="form-control" rows="3"
                onkeyup="this.value = this.value.toUpperCase()"></textarea>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-3">
              <label for="a_weekly_hours">Weekly Hours</label>
              <input type="number" name="a_weekly_hours" id="a_weekly_hours" class="form-control" min="1">
            </div>
            <div class="col-3">
              <label for="a_subject_type">Subject Type</label>
              <select id="a_subject_type" name="a_subject_type" class="form-control">
                <option value="">--</option>
                <option value="Specialized">Specialized</option>
                <option value="Core">Core</option>
                <option value="Applied">Applied</option>
              </select>
            </div>
            <div class="col-3" id="subSemAdd">
              <label for="a_subject_quarter">Semester</label>
              <select id="a_subject_quarter" name="a_subject_quarter" class="form-control" disabled>
                <option value="">--</option>
                <option value="1">First Semester</option>
                <option value="2">Second Semester</option>
              </select>
            </div>
            <div class="col-3">
              <label for="a_subject_order">Subject Order</label>
              <select id="a_subject_order" name="a_subject_order" class="form-control">
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
            <div class="col-9"
              style="display: flex; justify-content: flex-end; align-items: center; padding-right: 5px;"></div>
            <div class="col-2" style="display: flex; flex-direction: column; align-items: flex-end; padding-left: 0;">
              <label for="a_checkbox" class="form-check-label" style="margin-bottom: 0;">Nested Subject?</label>
            </div>
            <div class="col-1"
              style="display: flex; justify-content: flex-end; align-items: center; padding-right: 5px;">
              <input type="checkbox" id="a_checkbox" name="a_checkbox" class="form-check-input">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Create</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>
<form id="updateSubjectForm" action="../api/api_curriculum.php" method="post">
  <div class="modal fade" id="updateSubjectModal" tabindex="-1" aria-labelledby="updateSubjectModalLabel">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-warning text-white">
          <h5 class="modal-title" id="updateSubjectModalLabel">Update Subject</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- Hidden input for subject_id -->
          <input type="hidden" name="u_subject_id" id="u_subject_id" value="">
          <div class="form-group row">
            <div class="col-6">
              <label for="u_subject_code">Subject Code</label>
              <input type="text" name="u_subject_code" id="u_subject_code" class="form-control"
                onkeyup="this.value = this.value.toUpperCase()">
            </div>
            <div class="col-6">
              <label for="u_curriculum">Curriculum</label>
              <select id="u_curriculum" name="u_curriculum" class="form-control">
                <option value="">--</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-4">
              <label for="u_grade_level">Grade Level</label>
              <select id="u_grade_level" name="u_grade_level" class="form-control" onchange="toggleSubGradeEdit()">
                <option value="">--</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
              </select>
            </div>
            <div class="col-4">
              <label for="u_strand">Strand</label>
              <select id="u_strand" name="u_strand" class="form-control">
                <option value="">--</option>
                <option value="GAS">GAS</option>
                <option value="STEM">STEM</option>
                <option value="HUMS">HUMS</option>
                <option value="ABM">ABM</option>
                <option value="TVL-HE">TVL-HE</option>
                <option value="TVL-ICT">TVL-ICT</option>
                <option value="OTHERS">OTHERS</option>
              </select>
            </div>
            <div class="col-4">
              <label for="u_subject_name">Subject Name</label>
              <input type="text" name="u_subject_name" id="u_subject_name" class="form-control">
            </div>
          </div>
          <div class="form-group row">
            <div class="col-12">
              <label for="u_subject_desc">Description</label>
              <textarea name="u_subject_desc" id="u_subject_desc" class="form-control" rows="3"
                onkeyup="this.value = this.value.toUpperCase()"></textarea>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-2">
              <label for="u_weekly_hours">Weekly Hours</label>
              <input type="number" name="u_weekly_hours" id="u_weekly_hours" class="form-control" min="1">
            </div>
            <div class="col-2">
              <label for="u_subject_type">Type</label>
              <select id="u_subject_type" name="u_subject_type" class="form-control">
                <option value="">--</option>
                <option value="Specialized">Specialized</option>
                <option value="Core">Core</option>
                <option value="Applied">Applied</option>
              </select>
            </div>
            <div class="col-4">
              <label for="u_subject_term">Term</label>
              <select name="u_subject_term" id="u_subject_term" class="form-control">
                
              </select>
            </div>
            <div class="col-2" id="subSemEdit">
              <label for="u_subject_quarter">Semester</label>
              <select id="u_subject_quarter" name="u_subject_quarter" class="form-control">
                <option value="">--</option>
                <option value="1">First Semester</option>
                <option value="2">Second Semester</option>
              </select>
            </div>
            <div class="col-2">
              <label for="u_subject_order">Order</label>
              <select id="u_subject_order" name="u_subject_order" class="form-control">
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
                <option value="11">11</option>
                <option value="12">12</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-9"
              style="display: flex; justify-content: flex-end; align-items: center; padding-right: 5px;"></div>
            <div class="col-2" style="display: flex; flex-direction: column; align-items: flex-end; padding-left: 0;">
              <label for="u_checkbox" class="form-check-label" style="margin-bottom: 0;">Nested Subject?</label>
            </div>
            <div class="col-1"
              style="display: flex; justify-content: flex-end; align-items: center; padding-right: 5px;">
              <input type="checkbox" id="u_checkbox" name="u_checkbox" class="form-check-input">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Update</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>
<form id="copySubjectForm" action="../api/api_curriculum.php" method="post">
  <div id="copySubjectModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="copySubjectModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="copySubjectModalLabel">Copy Subjects by Term</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group row">
            <div class="col-12">
              <label for="copy_curriculum">Curriculum</label>
              <select name="copy_curriculum" id="copy_curriculum" class="form-control" required>
                <option value="">--</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-6">
              <label for="copy_sy_from">Term (form)</label>
              <select name="copy_sy_from" id="copy_sy_from" class="form-control" required>
                <option value="">--</option>
              </select>
            </div>
            <div class="col-6">
              <label for="copy_sy_to">Term (to)</label>
              <select name="copy_sy_to" id="copy_sy_to" class="form-control" required>
                <option value="">--</option>
              </select>
            </div>
          </div>
          <div class="table-responsive">
            <table id="copy_subject_table" class="table table-bordered table-striped table-hover" style="width: 100%;">
              <thead class="bg-info">
                <tr>
                  <th><input type="checkbox" id="selectAllSubjects"></th>
                  <th>Subject Code</th>
                  <th>Subject Name</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Create</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>