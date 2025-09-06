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
                        School Form 7
                    </h1>
                </div>
            </div>
            <div class="row" style="margin: 0;">
                <div class="col-sm-6" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        School Personnel Assignment List and Basic Profile
                    </small>
                </div>
                <div class="col-sm-6 text-right" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        School Reports / School Forms 7
                    </small>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-body">
                        <form onsubmit="toggleSection(event)">
                            <div class="row">
                                <div class="col-12 d-flex justify-content-between">
                                    <div>
                                        <button type="submit" class="btn btn-success mr-2" id="searchSection"
                                            onclick="toggleSection()">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-info mr-2" id="exportSf7">
                                            <i class="fas fa-download">&nbsp;SF7</i>
                                        </button>
                                    </div>
                                    <div>
                                        <a href="school_forms.php" class="btn btn-info mr-2" id="returnReports">
                                            <i class="fas fa-reply"></i> Back
                                        </a>
                                        <?php
                                        if ($_SESSION['access_level'] == 'SA' || $_SESSION['access_level'] == 'SIC') {
                                            ?>
                                            <button type="button" class="btn btn-info mr-2" id="schoolInfoSection"
                                                data-toggle="modal" data-target="#updateSchoolInfoModal">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                            <?php
                                        }
                                        ?>
                                        <button id="toggleButton" type="button" class="btn btn-primary"
                                            onclick="toggleSection()">
                                            <i id="toggleIcon" class="fas fa-chevron-down"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="content" id="personnel_content" style="display: none;">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h1
                            style="text-transform: uppercase; margin-bottom: 0; font-weight: normal; color:rgb(255, 255, 255); font-size: 12px; margin-left: 10px; display: block;">
                            School Personnel List</h1>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="personnel_info_table" class="table table-bordered table-striped table-hover"
                                style="width: 100%;">
                                <thead class="bg-info" height="40">
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="content" id="archive_content" style="display: none;">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                    </div>
                    <div class="card-body">
                        <h1 style="font-size: 1rem; margin: 1px;">Section Archive</h1>
                        <div class="table-responsive">
                            <table id="archive_subjects_table" class="table table-bordered table-striped table-hover"
                                style="width: 100%;">
                                <thead class="bg-info" height="40">
                                </thead>
                            </table>
                        </div>
                    </div>
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
        //personnel table
        var perInfoTable = $('#personnel_info_table').DataTable({
            responsive: true,
            order: [[0, "asc"]],
            pageLength: 50,
            processing: true,
            dom: 'Bfrtip',
            buttons: [],
            ajax: {
                url: '../api/api_sf.php',
                data: function (d) {
                    d.get_personnel = true;
                },
                dataSrc: function (json) {
                    console.log(json); // Log the response to the console
                    return json; // Return the data to DataTable
                }
            },
            columnDefs: [
                { responsivePriority: 1, targets: 0 }, // Employee Number
                { responsivePriority: 2, targets: 1 }, // Last Name
                { responsivePriority: 3, targets: 2 }, // First Name
                { responsivePriority: 4, targets: 3 }    // Middle Name
            ],
            columns: [
                { data: 'EmpNo', title: 'Employee No.', className: 'align-middle' },
                {
                    data: null, // No direct data source for this column
                    title: 'Full Name',
                    className: 'align-middle',
                    render: function (data, type, row) {
                        return row.EmpLName + ', ' + row.EmpFName + ' ' + (row.EmpMName ? row.EmpMName.charAt(0) + '.' : '') + (row.EmpEName ? ' ' + row.EmpEName : '');
                    }
                },
                { data: 'Sex', title: 'Gender', className: 'align-middle' },
                { data: 'EmploymentStatus', title: 'Employment Status', className: 'align-middle' },
                { data: 'EducDegree', title: 'Highest Educational Degree', className: 'align-middle' }
            ]
        });
        $('#searchSection').click(function (event) {
            event.preventDefault();
            personnelInfoTable.ajax.reload();
        });
        $('#exportSf7').on('click', function () {
            // Show loading alert
            Swal.fire({
                title: 'Exporting...',
                text: 'Please wait while we export your data.',
                allowOutsideClick: false,
                onBeforeOpen: () => {
                    Swal.showLoading();
                }
            });
            // Perform AJAX request to export data
            $.ajax({
                url: '../includes/functions/func_export_sf7.php',
                type: 'GET',
                xhrFields: {
                    responseType: 'blob' // Expect a binary response (file download)
                },
                success: function (response, status, xhr) {
                    // Create a link element to download the file
                    var blob = new Blob([response], { type: xhr.getResponseHeader('Content-Type') });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = xhr.getResponseHeader('Content-Disposition').split('filename=')[1].replace(/"/g, '');
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Export Successful',
                        text: 'Your data has been exported successfully.'
                    });
                },
                error: function () {
                    // Handle error
                    Swal.fire({
                        icon: 'error',
                        title: 'Export Failed',
                        text: 'There was an error exporting your data.'
                    });
                }
            });
        });
    });
    document.getElementById("toggleButton").addEventListener("click", function () {
        let personnelContent = document.getElementById("personnel_content");
        let icon = document.getElementById("toggleIcon");
        if (personnelContent.style.display === "none" || personnelContent.style.display === "") {
            personnelContent.style.display = "block";
            icon.classList.remove("fa-chevron-down");
            icon.classList.add("fa-chevron-up");
        } else {
            personnelContent.style.display = "none";
            icon.classList.remove("fa-chevron-up");
            icon.classList.add("fa-chevron-down");
        }
    });
    function toggleSection(event) {
        if (event) event.preventDefault();
        let personnelContent = document.getElementById("personnel_content");
        let icon = document.getElementById("toggleIcon");
        // Only show the section, never hide it when searching
        if (personnelContent.style.display === "none") {
            personnelContent.style.display = "block";
            icon.classList.remove("fa-chevron-down");
            icon.classList.add("fa-chevron-up");
        }
    }
</script>
</body>
</html>