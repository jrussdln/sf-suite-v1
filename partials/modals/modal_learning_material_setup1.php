<!-- Update Class Modal -->
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
                                        <form>
                                            <div class="row align-items-center">
                                                <div class="col-1 text-left">
                                                    <label for="display_sectionname_lm"
                                                        class="font-weight-bold">Section</label>
                                                </div>
                                                <div class="col-4">
                                                    <input type="text" id="display_sectionname_lm" class="form-control"
                                                        disabled>
                                                </div>
                                                <div class="col-1 text-left">
                                                    <label for="" class="font-weight-bold"></label>
                                                </div>
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
                                                    <label for="display_schoolyear_lm"
                                                        class="font-weight-bold">S.Y.</label>
                                                </div>
                                                <div class="col-4">
                                                    <input type="text" id="display_schoolyear_lm" class="form-control"
                                                        disabled>
                                                </div>
                                                <div div class="col-1 text-left">
                                                    <label for="" class="font-weight-bold"></label>
                                                </div>
                                                <div class="col-1 text-left">
                                                    <label for="display_facility_lm"
                                                        class="font-weight-bold">Facility</label>
                                                </div>
                                                <div class="col-5">
                                                    <input type="text" id="display_facility_lm" class="form-control"
                                                        disabled>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- Class Info Table -->
                    <div class="table-responsive">
                        <table id="section_info_table_lm" class="table table-bordered table-striped table-hover w-100">
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
                    <button type="button" id="issueButton" class="btn btn-primary">Bulk/Non Issue</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
</form>
