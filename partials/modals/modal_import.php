<!-- Modal for File Upload -->
<form id="uploadFileForm" action="code.php" method="POST" enctype="multipart/form-data">
  <div id="uploadFileModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="uploadFileModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="uploadFileModalLabel">Upload a File</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"
            style="border: none; background: transparent; font-size: 24px;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="file" style="font-weight: bold;">Choose a file (CSV or Excel):</label>
            <div class="custom-file" style="width: 100%;">
              <input type="file" id="file" name="file" accept=".csv, .xlsx" class="custom-file-input" required
                style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 5px;">
              <label class="custom-file-label" for="file"
                style="padding: 8px; border-radius: 5px; background-color: #f8f9fa;">Choose file...</label>
            </div>
            <small style="color: #6c757d; display: block; margin-top: 5px;">Please upload a valid CSV or Excel
              file.</small>
          </div>
          <div id="result"></div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="save_excel" class="btn btn-primary"
            style="padding: 10px 20px; border-radius: 5px;">Upload</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal"
            style="padding: 10px 20px; border-radius: 5px;">Close</button>
        </div>
      </div>
    </div>
  </div>
</form>