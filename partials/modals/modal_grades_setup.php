<div id="viewSectionGradeModal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable " role="document">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header bg-primary text-white">
        <h4 class="modal-title" style="font-size: 1rem;" id="viewSectionModalLabel">Grades</h4>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <!-- Modal Body -->
      <div class="modal-body">
        <section class="content">
          <div class="row">
            <div class="col-12">
              <div class="card card-primary">
                <div class="card-body">
                  <div class="row">
                    <!-- Left: Image Column -->
                    <div class="col-2 d-flex justify-content-center align-items-center">
                      <img src="../dist/img/image.png" alt="School Logo" class="img-fluid"
                        style="max-height: 120px; border: none; box-shadow: none; margin:0;">
                    </div>
                    <!-- Right: Labels and Inputs in one column -->
                    <div class="col-10">
                      <!-- Labels Row -->
                      <div class="row">
                        <div class="col-3 text-left">
                          <label for="display_sectionname_g" class="font-weight-bold">Section</label>
                        </div>
                        <div class="col-2 text-left">
                          <label for="display_gradelevel_g" class="font-weight-bold">Grade</label>
                        </div>
                        <div class="col-3 text-left">
                          <label for="display_schoolyear_g" class="font-weight-bold">S.Y.</label>
                        </div>
                        <div class="col-2 text-left">
                          <label for="display_facility_g" class="font-weight-bold">Facility</label>
                        </div>
                        <div class="col-2 text-left">
                          <label for="display_strand_g" class="font-weight-bold">Strand</label>
                        </div>
                      </div>
                      <!-- Inputs Row -->
                      <div class="row mt-1">
                        <div class="col-3">
                          <input type="text" id="display_sectionname_g" class="form-control text-center" disabled>
                        </div>
                        <div class="col-2">
                          <input type="text" id="display_gradelevel_g" class="form-control text-center" disabled>
                        </div>
                        <div class="col-3">
                          <input type="text" id="display_schoolyear_g" class="form-control text-center" disabled>
                        </div>
                        <div class="col-2">
                          <input type="text" id="display_facility_g" class="form-control text-center" disabled>
                        </div>
                        <div class="col-2">
                          <input type="text" id="display_strand_g" class="form-control text-center" disabled>
                        </div>
                      </div>
                      <div class="row mt-1">
                        <div class="col-md-4">
                          <select id="grading_select" class="form-control">
                            <option value="fstq_grade_tr">1st Quarter</option>
                            <option value="scndq_grade_tr">2nd Quarter</option>
                            <option value="trdq_grade_tr">3rd Quarter</option>
                            <option value="fthq_grade_tr">4th Quarter</option>
                          </select>
                        </div>
                        <div class="col-md-3">
                          <select id="grade_set" class="form-control">
                            <option value="1">1st Set</option>
                            <option value="2">2nd Set</option>
                          </select>
                        </div>
                      </div>
                    </div> <!-- End col-10 -->
                  </div> <!-- End row -->
                </div>
              </div>
            </div>
          </div>
        </section>
        <div class="table-responsive">
          <table id="section_info_table_grade" class="table table-bordered table-striped table-hover w-100">
            <thead class="thead-dark">
            </thead>
            <tbody>
              <!-- Table content will be dynamically populated -->
            </tbody>
          </table>
        </div>
      </div>
      <!-- Modal Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="gradeInfoModal" tabindex="-1" role="dialog" aria-labelledby="gradeInfoModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="gradeInfoModalLabel">Student Grades</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Content for displaying student grades goes here -->
        <table id="grade_info_table" class="table table-bordered table-striped table-hover w-100">
          <thead class="thead-dark">
          </thead>
          <tbody>
            <!-- Table content will be dynamically populated -->
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="gradeInfoShsModal" tabindex="-1" role="dialog" aria-labelledby="gradeInfoShsModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="gradeShsInfoModalLabel">Student Grades</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <section class="content_sy">
          <div class="d-flex align-items-center justify-content-end">
            <label for="semester" class="font-weight-light mr-2">Semester:</label>
            <select class="form-control w-auto" id="semester" name="semester">
              <option value="1">1</option>
              <option value="2">2</option>
            </select>
          </div>
        </section>
        <table id="grade_info_shs_table" class="table table-bordered table-striped table-hover w-100">
          <thead class="thead-dark">
          </thead>
          <tbody>
            <!-- Table content will be dynamically populated -->
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<form id="editStudentGradeForm" action="../api/api_grade.php" method="post">
  <!-- Edit Student Grade Modal -->
  <div class="modal fade" id="editStudentGradeModal" tabindex="-1" aria-labelledby="editStudentGradeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h4 class="modal-title" id="editStudentGradeModalLabel">Edit Student Grades</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group row">
            <div class="col-12">
              <input type="text" class="form-control" id="u_sg_id" name="u_sg_id" hidden />
              <input type="text" class="form-control" id="u_sg_lrn" name="u_sg_lrn" hidden />
            </div>
          </div>
          <div class="form-group row">
            <div class="col-3">
              <label for="u_sg_fstq_grade">1st Quarter</label>
              <input type="number" class="form-control" id="u_sg_fstq_grade" name="u_sg_fstq_grade" />
            </div>
            <div class="col-3">
              <label for="u_sg_scndq_grade">2nd Quarter</label>
              <input type="number" class="form-control" id="u_sg_scndq_grade" name="u_sg_scndq_grade" />
            </div>
            <div class="col-3">
              <label for="u_sg_trdq_grade">3rd Quarter</label>
              <input type="number" class="form-control" id="u_sg_trdq_grade" name="u_sg_trdq_grade" />
            </div>
            <div class="col-3">
              <label for="u_sg_fthq_grade">4th Quarter</label>
              <input type="number" class="form-control" id="u_sg_fthq_grade" name="u_sg_fthq_grade" />
            </div>
          </div>
          <label>Transmutation<span class="h5 text-danger">*</span></label>
          <div class="form-group row">
            <div class="col-3">
              <input type="text" class="form-control" id="u_sg_fstq_grade_tr" name="u_sg_fstq_grade_tr" readonly />
            </div>
            <div class="col-3">
              <input type="text" class="form-control" id="u_sg_scndq_grade_tr" name="u_sg_scndq_grade_tr" readonly />
            </div>
            <div class="col-3">
              <input type="text" class="form-control" id="u_sg_trdq_grade_tr" name="u_sg_trdq_grade_tr" readonly />
            </div>
            <div class="col-3">
              <input type="text" class="form-control" id="u_sg_fthq_grade_tr" name="u_sg_fthq_grade_tr" readonly />
            </div>
          </div>
        </div> <!-- modal body -->
        <div class="modal-footer">
          <button type="button" class="btn btn-warning" id="updateNestedBtn">Update Nested</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>
<form id="editStudentGradeShsForm" action="../api/api_grade.php" method="post">
  <!-- Edit Student Grade Modal -->
  <div class="modal fade" id="editStudentGradeShsModal" tabindex="-1" aria-labelledby="editStudentGradeShsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h4 class="modal-title" id="editStudentGradeShsModalLabel">Edit Student Grades</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group row">
            <div class="col-12">
              <input type="text" class="form-control" id="s_sg_id" name="s_sg_id" hidden />
            </div>
          </div>
          <div class="form-group row">
            <div class="col-6">
              <label for="s_sg_fsts_grade">1st Quarter</label>
              <input type="number" class="form-control" id="s_sg_fsts_grade" name="s_sg_fsts_grade" />
            </div>
            <div class="col-6">
              <label for="s_sg_scnds_grade">2nd Quarter</label>
              <input type="number" class="form-control" id="s_sg_scnds_grade" name="s_sg_scnds_grade" />
            </div>
          </div>
          <label>Transmutation</label>
          <div class="form-group row">
            <div class="col-6">
              <input type="text" class="form-control" id="s_sg_fsts_grade_tr" name="s_sg_fsts_grade_tr" readonly />
            </div>
            <div class="col-6">
              <input type="text" class="form-control" id="s_sg_scnds_grade_tr" name="s_sg_scnds_grade_tr" readonly />
            </div>
          </div>
        </div> <!-- modal body -->
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save Changes</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>
<!-- Modal Structure -->
<div class="modal fade" id="gradeRecordControlModal" tabindex="-1" role="dialog"
  aria-labelledby="gradeRecordControlModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="gradeRecordControlModalLabel">Grade Record Control</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Junior High School Section -->
        <div class="section">
          <label for="juniorHighSchool" class="font-weight-bold">Junior High School</label>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Grading Period</th>
                <th>Control</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>First Grading</td>
                <td><input type="checkbox" id="firstGrading" name="firstGrading"></td>
              </tr>
              <tr>
                <td>Second Grading</td>
                <td><input type="checkbox" id="secondGrading" name="secondGrading"></td>
              </tr>
              <tr>
                <td>Third Grading</td>
                <td><input type="checkbox" id="thirdGrading" name="thirdGrading"></td>
              </tr>
              <tr>
                <td>Fourth Grading</td>
                <td><input type="checkbox" id="fourthGrading" name="fourthGrading"></td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- Senior High School Section -->
        <div class="section">
          <label for="seniorHighSchool" class="font-weight-bold">Senior High School</label>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Grading Period</th>
                <th>Control</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>First Semester</td>
                <td><input type="checkbox" id="firstSemester" name="firstSemester"></td>
              </tr>
              <tr>
                <td>Second Semester</td>
                <td><input type="checkbox" id="secondSemester" name="secondSemester"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="viewTransmutationModal" tabindex="-1" role="dialog"
  aria-labelledby="viewTransmutationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewTransmutationModalLabel">View Transmutation Information</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="transmutation_info_table" class="table table-bordered table-striped table-hover w-100">
          <thead class="thead-dark">
          </thead>
          <tbody>
            <!-- Table content will be dynamically populated with data -->
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="updateTransmutationModal" tabindex="-1" role="dialog"
  aria-labelledby="updateTransmutationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="updateTransmutationModalLabel">Update Transmutation Information</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="updateTransmutationForm">
          <input type="text" class="form-control" id="trans_id" name="trans_id" hidden>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="minGradeInput">Min Grade</label>
              <input type="number" class="form-control" id="min_grade" name="min_grade" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="transmutedGradeInput">Transmuted Grade</label>
              <input type="number" class="form-control" id="trans_grade" name="trans_grade" required>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="updateTransmutationBtn">Save Changes</button>
      </div>
    </div>
  </div>
</div>