<form id="viewSectionHnrForm" action="../api/api_school_forms.php" method="post">
    <div id="viewSectionHnrModal" class="modal fade" tabindex="-1" role="dialog">
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
                                                    <label for="display_sectionname_hnr"
                                                        class="font-weight-bold">Section</label>
                                                </div>
                                                <div class="col-4">
                                                    <input type="text" id="display_sectionname_hnr" class="form-control"
                                                        disabled>
                                                </div>
                                                <div class="col-1 text-left">
                                                    <label for="" class="font-weight-bold"></label>
                                                </div>
                                                <div class="col-1 text-left">
                                                    <label for="display_gradelevel_hnr"
                                                        class="font-weight-bold">Grade</label>
                                                </div>
                                                <div class="col-5">
                                                    <input type="text" id="display_gradelevel_hnr" class="form-control"
                                                        disabled>
                                                </div>
                                            </div>
                                            <div class="row align-items-center mt-3">
                                                <div class="col-1 text-left">
                                                    <label for="display_schoolyear_hnr"
                                                        class="font-weight-bold">S.Y.</label>
                                                </div>
                                                <div class="col-4">
                                                    <input type="text" id="display_schoolyear_hnr" class="form-control"
                                                        disabled>
                                                </div>
                                                <div div class="col-1 text-left">
                                                    <label for="" class="font-weight-bold"></label>
                                                </div>
                                                <div class="col-1 text-left">
                                                    <label for="display_facility_hnr"
                                                        class="font-weight-bold">Facility</label>
                                                </div>
                                                <div class="col-5">
                                                    <input type="text" id="display_facility_hnr" class="form-control"
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
                        <table id="section_info_table_hnr" class="table table-bordered table-striped table-hover w-100">
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
<form id="editHealthInfoForm" action="../api/api_school_forms.php" method="post">
    <div class="modal fade" id="editHealthInfoModal" tabindex="-1" role="dialog"
        aria-labelledby="editHealthInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editHealthInfoModalLabel">Edit Learner Information</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" class="form-control" id="hnr_health_nutrition_id"
                            name="hnr_health_nutrition_id">
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="hnr_birthdate">Birthdate</label>
                                <input type="date" class="form-control" id="hnr_birthdate" name="hnr_birthdate"
                                    onchange="calculateAge()">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="hnr_age">Age</label>
                                <input type="text" class="form-control" id="hnr_age" name="hnr_age" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="hnr_weight">Weight (kg)</label>
                                <input type="number" class="form-control" id="hnr_weight" name="hnr_weight" step="0.01"
                                    oninput="calculateBMI()">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="hnr_height">Height (m)</label>
                                <input type="number" class="form-control" id="hnr_height" name="hnr_height" step="0.01"
                                    oninput="calculateBMI()">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="hnr_heightSquared">Height Squared (m²)</label>
                                <input type="text" class="form-control" id="hnr_heightSquared" name="hnr_heightSquared"
                                    readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="hnr_nutritionalStatus">Nutritional Status</label>
                                <input type="text" class="form-control" id="hnr_nutritionalStatus"
                                    name="hnr_nutritionalStatus" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="hnr_bmi">Body Mass Index (BMI)</label>
                                <input type="text" class="form-control" id="hnr_bmi" name="hnr_bmi" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="hnr_heightForAge">Height for Age</label>
                                <input type="text" class="form-control" id="hnr_heightForAge" name="hnr_heightForAge"
                                    readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="hnr_remarks">Remarks</label>
                                <textarea class="form-control" id="hnr_remarks" name="hnr_remarks" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</form>