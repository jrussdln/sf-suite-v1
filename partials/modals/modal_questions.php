<form id="addQuestionForm" action="../api/api_announcement.php" method="post">
  <div id="addQuestionModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addQuestionModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addQuestionModalLabel">Add New Question</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="question_desc">Question Description <span class="h5 text-danger">*</span></label>
            <textarea name="question_desc" id="question_desc" class="form-control" rows="4" required
              onkeyup="this.value = this.value.toUpperCase()"
              onpaste="this.value = this.value.toUpperCase()"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Add Question</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>
<form id="editQuestionForm" action="../api/api_announcement.php" method="post">
  <div id="editQuestionModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editQuestionModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editQuestionModalLabel">Edit Question</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- This is hidden -->
          <input type="hidden" name="edit_question_id" id="edit_question_id" value="">
          <div class="form-group">
            <label for="edit_question_desc">Question Description <span class="h5 text-danger">*</span></label>
            <textarea name="edit_question_desc" id="edit_question_desc" class="form-control" rows="4" required
              onkeyup="this.value = this.value.toUpperCase()"
              onpaste="this.value = this.value.toUpperCase()"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save Changes</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>
<!-- Modal for viewing choices -->
<div id="choicesModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="choicesModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="choicesModalLabel">Choices</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="outline:none;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="choicesTable" class="table table-striped table-bordered" style="width:100%">
          <thead>
            <tr>
              <!-- Table headers go here -->
            </tr>
          </thead>
          <tbody>
            <!-- Table body goes here -->
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addChoicesModal"
          style="margin-right: 10px;">
          Add Choice
        </button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal for adding choices -->
<div id="addChoicesModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addChoicesModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addChoicesModalLabel">Add Choices</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="outline:none;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="addChoiceForm">
          <input type="hidden" name="question_id" id="modal_question_id" value="">
          <div class="form-group">
            <label for="choices_content">Choice Content <span class="h5 text-danger">*</span></label>
            <input type="text" class="form-control" id="choices_content" name="choices_content" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" id="submitChoiceBtn">Add Choice</button>
      </div>
    </div>
  </div>
</div>