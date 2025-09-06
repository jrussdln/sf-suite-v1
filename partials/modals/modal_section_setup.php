<!-- Add Class Modal -->
<form id="addSectionForm" action="../api/api_section.php" method="post">
  <div id="addSectionModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header bg-primary text-white">
          <h4 class="modal-title" style="font-size: 1rem;">Sections / Add Section</h4>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <!-- Modal Body -->
        <div class="modal-body">
          <!-- Class Details -->
          <div class="form-group row">
            <div class="col-md-12">
              <label for="a_sectionname">Section Name</label>
              <input type="text" name="a_sectionname" id="a_sectionname" class="form-control" value="" required>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-md-6">
              <label for="a_gradelevel">Grade Level</label>
              <select name="a_gradelevel" id="a_gradelevel" class="form-control" onchange="toggleTermAdd()" required>
                <option value="">--</option>
                <option value="7">Grade 7</option>
                <option value="8">Grade 8</option>
                <option value="9">Grade 9</option>
                <option value="10">Grade 10</option>
                <option value="11">Grade 11</option>
                <option value="12">Grade 12</option>
              </select>
            </div>
            <div class="col-6">
              <label for="a_curriculumterm">Term/Quarter</label>
              <select name="a_curriculumterm" id="a_curriculumterm" class="form-control" required>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-md-6">
              <label for="a_facility">Facility</label>
              <input type="text" name="a_facility" id="a_facility" class="form-control" value="" required>
            </div>
            <div class="col-md-6">
              <label for="a_sectionstrand">Track/Strand</label>
              <select class="form-control" id="a_sectionstrand" name="a_sectionstrand">
              </select>
            </div>
          </div>
        </div>
        <!-- Modal Footer -->
        <div class="modal-footer w-100 justify-content-end">
          <!-- Right-aligned buttons: Add Class, Close -->
          <button type="submit" class="btn btn-success">Add Class</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>
<!-- Update Class Modal -->
<form id="updateSectionForm" action="../api/api_section.php" method="post">
  <div id="updateSectionModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header bg-primary text-white">
          <h4 class="modal-title" style="font-size: 1rem;">Sections / Edit</h4>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <!-- Modal Body -->
        <div class="modal-body">
          <div class="form-group row">
            <div class="col-12">
              <input type="hidden" name="e_SectionId" id="e_SectionId" class="form-control" required>
            </div>
          </div>
          <!-- Class Details -->
          <div class="form-group row">
            <div class="col-md-6">
              <label for="e_sectionname">Section Name</label>
              <input type="text" name="e_sectionname" id="e_sectionname" class="form-control" value="" required>
            </div>
            <div class="col-md-6">
              <label for="e_gradelevel">Grade Level</label>
              <select name="e_gradelevel" id="e_gradelevel" class="form-control" onchange="toggleTermEdit()" required>
                <option value="">--</option>
                <option value="7">Grade 7</option>
                <option value="8">Grade 8</option>
                <option value="9">Grade 9</option>
                <option value="10">Grade 10</option>
                <option value="11">Grade 11</option>
                <option value="12">Grade 12</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-4">
              <label for="e_schoolyear">Term/Quarter</label>
              <select name="e_schoolyear" id="e_schoolyear" class="form-control" required>
              </select>
            </div>
            <div class="col-md-4">
              <label for="e_facility">Facility</label>
              <input type="text" name="e_facility" id="e_facility" class="form-control" value="" required>
            </div>
            <div class="col-md-4">
              <label for="e_sectionstrand">Track/Strand</label>
              <select class="form-control" id="e_sectionstrand" name="e_sectionstrand">
              </select>
            </div>
          </div>
        </div>
        <!-- Modal Footer -->
        <div class="modal-footer w-100 justify-content-end">
          <!-- Right-aligned buttons: Update Class, Close -->
          <button type="submit" class="btn btn-success">Update Class</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>
<!-- Update Class Modal -->
<form id="viewSectionForm" action="../api/api_section.php" method="post">
  <div id="viewSectionModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header bg-primary text-white">
          <h4 class="modal-title" style="font-size: 1rem;" id="viewSectionModalLabel">Section Details</h4>
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
                    <form>
                      <div class="row align-items-center">
                        <div class="col-1 text-left">
                          <label for="display_sectionname" class="font-weight-bold">Section</label>
                        </div>
                        <div class="col-4">
                          <input type="text" id="display_sectionname" class="form-control" disabled>
                        </div>
                        <div class="col-1 text-left">
                          <label for="" class="font-weight-bold"></label>
                        </div>
                        <div class="col-1 text-left">
                          <label for="display_gradelevel" class="font-weight-bold">Grade</label>
                        </div>
                        <div class="col-5">
                          <input type="text" id="display_gradelevel" class="form-control" disabled>
                        </div>
                      </div>
                      <div class="row align-items-center mt-3">
                        <div class="col-1 text-left">
                          <label for="display_schoolyear" class="font-weight-bold">S.Y.</label>
                        </div>
                        <div class="col-4">
                          <input type="text" id="display_schoolyear" class="form-control" disabled>
                        </div>
                        <div div class="col-1 text-left">
                          <label for="" class="font-weight-bold"></label>
                        </div>
                        <div class="col-1 text-left">
                          <label for="display_facility" class="font-weight-bold">Facility</label>
                        </div>
                        <div class="col-2">
                          <input type="text" id="display_facility" class="form-control" disabled>
                        </div>
                        <div class="col-1 text-left">
                          <label for="display_sectionstrand" class="font-weight-bold">Strand</label>
                        </div>
                        <div class="col-2">
                          <input type="text" id="display_sectionstrand" class="form-control" disabled>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </section>
          <div class="table-responsive">
            <table id="section_info_table" class="table table-bordered table-striped table-hover w-100">
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
</form>
<form id="copySectionForm" action="../api/api_section.php" method="post">
  <div id="copySectionModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="copySectionModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="copySectionModalLabel">Copy Sections by Term</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          
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
            <table id="copy_section_table" class="table table-bordered table-striped table-hover" style="width: 100%;">
              <thead class="bg-info">
                <tr>
                  <th><input type="checkbox" id="selectAllSections"></th>
                  <th>Section Name</th>
                  <th>Grade Level</th>
                  <th>Facility</th>
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