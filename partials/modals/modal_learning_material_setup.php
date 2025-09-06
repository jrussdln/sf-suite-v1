<!-- View Section Modal -->
<form id="viewSectionLmForm" action="../api/api_learning_material.php" method="post">
    <div id="viewSectionLmModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title" style="font-size: 1rem;">Section Details</h4>
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
                                        <div class="row align-items-center">
                                            <div class="col-1 text-left">
                                                <label for="display_sectionname_lm"
                                                    class="font-weight-bold">Section</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="text" id="display_sectionname_lm" class="form-control"
                                                    disabled>
                                            </div>
                                            <div class="col-1 text-left"></div>
                                            <div class="col-1 text-left">
                                                <label for="display_gradelevel_lm"
                                                    class="font-weight-bold">Grade</label>
                                            </div>
                                            <div class="col-5">
                                                <input type="text" id="display_gradelevel_lm" class="form-control"
                                                    disabled>
                                            </div>
                                        </div>
                                        <div class="row align-items-center mt-3">
                                            <div class="col-1 text-left">
                                                <label for="display_schoolyear_lm" class="font-weight-bold">S.Y.</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="text" id="display_schoolyear_lm" class="form-control"
                                                    disabled>
                                            </div>
                                            <div class="col-1 text-left"></div>
                                            <div class="col-1 text-left">
                                                <label for="display_facility_lm"
                                                    class="font-weight-bold">Facility</label>
                                            </div>
                                            <div class="col-5">
                                                <input type="text" id="display_facility_lm" class="form-control"
                                                    disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- Class Info Table -->
                    <div class="table-responsive">
                        <table id="section_info_table_lm" class="table table-bordered table-striped table-hover w-100">
                            <thead class="thead-dark"></thead>
                            <tbody>
                                <!-- Populated dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" id="issueButton" class="btn btn-primary">Bulk/Non Issue</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- Return Learning Material Modal -->
<form id="editReturnLmForm" action="../api/api_learning_material.php" method="post">
    <div class="modal fade" id="editReturnLmModal" tabindex="-1" role="dialog" aria-labelledby="editReturnLmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-l modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editReturnLmModalLabel">Return Learning Material</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- Modal Body -->
                <div class="modal-body">
                    <input type="hidden" name="learning_material_id" id="learning_material_id">
                    <?php for ($i = 1; $i <= 9; $i++): ?>
                        <div class="row mb-0">
                            <div class="col-12">
                                <div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-2">
                                    <label for="r_Desc<?= $i ?>" class="mb-0">Description <?= $i ?></label>
                                    <label class="mb-0">CHECK THIS IF RETURNED</label>
                                </div>
                            </div>
                            <div class="col-11 mt-1">
                                <input type="text" class="form-control" id="r_Desc<?= $i ?>" name="r_Desc<?= $i ?>">
                            </div>
                            <div class="col-1 d-flex align-items-center justify-content-center">
                                <input type="checkbox" class="form-check-input" id="r_check<?= $i ?>"
                                    name="r_check<?= $i ?>">
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveChangesBtn">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- Issue Learning Material Modal -->
<form id="updateLmForm" action="../api/api_learning_material.php" method="post">
    <div class="modal fade" id="updateLmModal" tabindex="-1" role="dialog" aria-labelledby="updateLmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-l modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="updateLmModalLabel">Issue Learning Material</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- Modal Body -->
                <div class="modal-body">
                    <input type="hidden" name="learning_material_id" id="learning_material_id">
                    <?php for ($i = 1; $i <= 9; $i++): ?>
                        <div class="form-group">
                            <label for="Desc<?= $i ?>">Description <?= $i ?></label>
                            <input type="text" class="form-control" id="Desc<?= $i ?>" name="Desc<?= $i ?>">
                        </div>
                    <?php endfor; ?>
                </div>
                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveChangesBtnn">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</form>