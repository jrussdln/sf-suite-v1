<?php
require_once('page_header_sessions.php');
include('../includes/navbar.php');
?>
<div class="content-wrapper">
    <section class="content-header" style="margin: 0; padding: 8px 10px; background-color: #f8f9fa;">
        <div class="container-fluid" style="padding: 0;">
            <div class="row align-items-center" style="margin: 0;">
                <div class="col-sm-6" style="padding: 0;">
                    <h1 style="font-size: 1.2rem; margin: 2px 0; color: #343a40;">
                        School Reports
                    </h1>
                </div>
            </div>
            <div class="row" style="margin: 0;">
                <div class="col-sm-6" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        Browse and manage school report generation from School Forms 1 to School Forms 10.
                    </small>
                </div>
                <div class="col-sm-6 text-right" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        School Reports /
                    </small>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-7">
                <div class="card card-primary" style="min-height: 110%;">
                    <div class="card-header">
                        <h3 class="card-title"
                            style="text-transform: uppercase; font-weight: normal; color:rgb(255, 255, 255); font-size: 12px; margin-left: 10px; display: block;">
                            All School Forms</h3>
                    </div>
                    <section class="content">
                        <div class="container">
                            <div class="row justify-content-center">
                                <?php
                                if ($_SESSION['access_level'] == 'TEACHER' || $_SESSION['access_level'] == 'SIC' || $_SESSION['access_level'] == 'SA') {
                                    ?>
                                    <div class="col-md-3">
                                        <div class="sf-boxes">
                                            <i class="fas fa-file-alt"></i>
                                            <a href="sf1.php" class="btn btn-primary btn-view mt-3">SF-1</a>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="sf-boxes">
                                            <i class="fas fa-file-alt"></i> <!-- Document icon -->
                                            <a href="sf2.php" class="btn btn-success btn-view mt-3 schoolForms">SF-2</a>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                                <?php
                                if ($_SESSION['access_level'] == 'LMP' || $_SESSION['access_level'] == 'SIC' || $_SESSION['access_level'] == 'SA') {
                                    ?>
                                    <div class="col-md-3">
                                        <div class="sf-boxes">
                                            <i class="fas fa-file-alt"></i> <!-- Document icon -->
                                            <a href="sf3.php" class="btn btn-primary btn-view mt-3 schoolForms">SF-3</a>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                                <?php
                                if ($_SESSION['access_level'] == 'TEACHER' || $_SESSION['access_level'] == 'SIC' || $_SESSION['access_level'] == 'SA') {
                                    ?>
                                    <div class="col-md-3">
                                        <div class="sf-boxes">
                                            <i class="fas fa-file-alt"></i> <!-- Document icon -->
                                            <a href="sf5.php" class="btn btn-success btn-view mt-3 schoolForms">SF-5</a>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                                <?php
                                if ($_SESSION['access_level'] == 'SIC' || $_SESSION['access_level'] == 'SA') {
                                    ?>
                                    <div class="col-md-3">
                                        <div class="sf-boxes">
                                            <i class="fas fa-file-alt"></i> <!-- Document icon -->
                                            <a href="sf7.php" class="btn btn-primary btn-view mt-3 schoolForms">SF-7</a>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                                <?php
                                if ($_SESSION['access_level'] == 'HNP' || $_SESSION['access_level'] == 'SIC' || $_SESSION['access_level'] == 'SA') {
                                    ?>
                                    <div class="col-md-3">
                                        <div class="sf-boxes">
                                            <i class="fas fa-file-alt"></i> <!-- Document icon -->
                                            <a href="sf8.php" class="btn btn-primary btn-view mt-3 schoolForms">SF-8</a>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                                <?php
                                if ($_SESSION['access_level'] == 'TEACHER' || $_SESSION['access_level'] == 'SIC' || $_SESSION['access_level'] == 'SA') {
                                    ?>
                                    <div class="col-md-3">
                                        <div class="sf-boxes">
                                            <i class="fas fa-file-alt"></i> <!-- Document icon -->
                                            <a href="sf9.php" class="btn btn-success btn-view mt-3 schoolForms">SF-9</a>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <div class="col-5">
                <div class="card card-primary" style="min-height: 110%;">
                    <div class="card-header">
                        <h3 class="card-title"
                            style="text-transform: uppercase; font-weight: normal; color:rgb(255, 255, 255); font-size: 12px; margin-left: 10px; display: block;">
                            History</h3>
                    </div>
                    <section class="content" id="export_history_content">
                        <div class="row">
                            <div class="col-12">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="export_record_table"
                                            class="table table-bordered table-striped table-hover" style="width: 100%;">
                                            <thead class="bg-info" height="40">
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
include_once('../partials/modals/modal_user_setup.php');
include_once('../partials/footer.php');
?>
<script>
    $(document).ready(function () {
        $('#registrationForm').on('submit', function (e) {
            e.preventDefault();  // Prevent the default form submission
            $.ajax({
                url: '../api/api_users.php?registration',  // Update this URL as necessary
                type: 'post',
                data: $(this).serialize(),  // Serialize the form data
                dataType: 'json'  // Expect JSON response
            }).then(function (response) {
                if (response.success) {
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success",
                    }).then(() => {
                        $('#registrationModal').modal('hide');  // Hide the modal
                        // Optionally, refresh the page or update the UI
                    });
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: response.message,
                        icon: "error",
                    });
                }
            }).catch(function (error) {
                console.error('Error:', error);
                Swal.fire({
                    title: "Error!",
                    text: "An unexpected error occurred.",
                    icon: "error",
                });
            });
        });
        var exportRecordTable = $('#export_record_table').DataTable({
            responsive: true,
            order: [[0, "asc"]],
            processing: true,
            dom: 'Bfrtip',
            buttons: [],
            searching: false, // Disable the search bar
            paging: false,
            info: false,
            ajax: {
                url: '../api/api_sf.php?get_export_record', // The API URL
                error: function (xhr, error, thrown) {
                    console.error('Error:', error);
                    console.error('Response:', xhr.responseText);
                    alert('Failed to load data');
                }
            },
            columns: [
                { data: 'er_desc', title: 'Description', className: 'align-middle' },
                { data: 'status', title: 'Status', className: 'align-middle' },
                { data: 'exported_at', title: 'Date & Time', className: 'align-middle' }
            ],
        });
    });
</script>
</body>
</html>