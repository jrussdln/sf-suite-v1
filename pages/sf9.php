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
                        School Form 9
                    </h1>
                </div>
            </div>
            <div class="row" style="margin: 0;">
                <div class="col-sm-6" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        Progress Report Card – Learner’s report card | Learner’s Permanent Academic Record
                    </small>
                </div>
                <div class="col-sm-6 text-right" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        School Reports / School Forms 9
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
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="schoolYear"
                                            style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">School
                                            Year</label>
                                        <select class="form-control" id="schoolYear" name="schoolYear" disabled>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="lrn"
                                            style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">Learner's
                                            Reference No.</label>
                                        <input type="text" class="form-control" id="lrn" name="lrn">
                                        </input>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 d-flex justify-content-between">
                                    <div>
                                        <button type="submit" class="btn btn-success mr-2" id="searchSection"
                                            onclick="toggleSection()">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <button type="button" class="btn btn-info mr-2" id="exportSf9">
                                            <i class="fas fa-download">&nbsp;Report Card</i>
                                        </button>
                                        <button type="button" class="btn btn-info mr-2" id="exportSf10">
                                            <i class="fas fa-download">&nbsp;Form 137</i>
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
    <section class="content" id="student_content" style="display: none;">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h1
                            style="text-transform: uppercase; margin-bottom: 0; font-weight: normal; color:rgb(255, 255, 255); font-size: 12px; margin-left: 10px; display: block;">
                            Student's Grade List</h1>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="student_table" class="table table-bordered table-striped table-hover"
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
        var studentTable = $('#student_table').DataTable({
            responsive: true,
            order: [[0, "asc"]],
            pageLength: 50,
            processing: true,
            dom: 'Bfrtip',
            buttons: [],
            ajax: {
                url: '../api/api_sf.php',
                data: function (d) {
                    d.rc_grade = true;
                    d.schoolYear = $('#schoolYear').val();
                    d.lrn = $('#lrn').val();
                },
                dataSrc: ''
            },
            columnDefs: [
                { responsivePriority: 1, targets: 0 }, // LRN
                { responsivePriority: 2, targets: 1 }, // Full Name
                { responsivePriority: 3, targets: 2 }, // Grade Level
                { responsivePriority: 4, targets: 3 }, // Section
                { responsivePriority: 5, targets: 4 } // Action column
            ],
            columns: [
                { data: 'lrn', title: 'LRN', className: 'align-middle' },
                { data: 'name', title: 'Full Name', className: 'align-middle' },
                { data: 'section', title: 'Section', className: 'align-middle' },
                { data: 'sex', title: 'Sex', className: 'align-middle' },
                { data: 'age', title: 'Age', className: 'align-middle' },
                { data: 'school_year', title: 'School Year', className: 'align-middle' },
                {
                    data: null,
                    title: 'Address',
                    className: 'align-middle',
                    render: function (data, type, row) {
                        // Combine the name, grade_level, and section into one string
                        return row.province + ', ' + row.municipality_city + ', ' + row.barangay;
                    }
                }
            ]
        });
        $('#searchSection').click(function (event) {
            event.preventDefault();
            studentTable.ajax.reload();
        });
        $('#exportSf9').on('click', function () {
            // Get the filter values
            var schoolYear = $('#schoolYear').val();
            var lrn = $('#lrn').val();
            var identifier = '<?php echo htmlspecialchars($_SESSION['Identifier']); ?>';
            // Check if fields are filled
            if (!schoolYear || !lrn) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Information',
                    text: 'Please select a School Year and enter an LRN before exporting.'
                });
                return;
            }
            // Show confirmation dialog
            Swal.fire({
                title: "Export SF9?",
                text: "Are you sure you want to generate and download SF9?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Export Now"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading alert
                    Swal.fire({
                        title: 'Fetching Student Data...',
                        text: 'Please wait while we retrieve the student grade level.',
                        icon: 'info',  // This will show the default loading spinner
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    // Retrieve student's grade level using AJAX
                    $.ajax({
                        url: '../api/api_sf.php?get_student_grade_level',
                        method: 'GET',
                        data: {
                            lrn: lrn,
                            schoolYear: schoolYear
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response && response.grade_level) {
                                var gradeLevel = parseInt(response.grade_level, 10);
                                var exportUrl = (gradeLevel >= 7 && gradeLevel <= 10)
                                    ? '../includes/functions/func_export_sf9.php'
                                    : '../includes/functions/func_export_sf9-shs.php';
                                // Export the file via AJAX
                                $.ajax({
                                    url: exportUrl,
                                    type: 'GET',
                                    data: {
                                        schoolYear: schoolYear,
                                        lrn: lrn,
                                        identifier: identifier
                                    },
                                    xhrFields: {
                                        responseType: 'blob' // Expect binary response (file download)
                                    },
                                    success: function (response, status, xhr) {
                                        Swal.close(); // Close loading alert
                                        // Create a downloadable link
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
                                            text: 'Your SF9 data has been exported successfully.'
                                        });
                                    },
                                    error: function () {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Export Failed',
                                            text: 'There was an error exporting your data.'
                                        });
                                    }
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Failed to retrieve student grade level. Please try again later.'
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'AJAX Error',
                                text: 'An error occurred while retrieving grade level: ' + error
                            });
                        }
                    });
                }
            });
        });
        $('#exportSf10').on('click', function () {
            // Get the filter values
            var schoolYear = $('#schoolYear').val();
            var lrn = $('#lrn').val();
            var identifier = '<?php echo htmlspecialchars($_SESSION['Identifier']); ?>';
            // Check if fields are filled
            if (!schoolYear || !lrn) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Information',
                    text: 'Please select a School Year and enter an LRN before exporting.'
                });
                return;
            }
            // Show confirmation dialog
            Swal.fire({
                title: "Export SF10?",
                text: "Are you sure you want to generate and download SF10?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Export Now"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading indicator before making the AJAX request
                    Swal.fire({
                        title: 'Retrieving Student Data...',
                        text: 'Please wait while we fetch the grade level.',
                        icon: 'info',  // This will show the default loading spinner
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    // Retrieve the student's grade level using an AJAX call
                    $.ajax({
                        url: '../api/api_sf.php?get_student_grade_level',
                        method: 'GET',
                        data: {
                            lrn: lrn,
                            schoolYear: schoolYear
                        },
                        dataType: 'json',
                        success: function (response) {
                            Swal.close(); // Close loading alert
                            if (response && response.grade_level) {
                                var gradeLevel = parseInt(response.grade_level, 10);
                                var exportUrl = (gradeLevel >= 7 && gradeLevel <= 10)
                                    ? '../includes/functions/func_export_sf10-jhs.php'
                                    : '../includes/functions/func_export_sf10-shs.php';
                                // Perform file export via AJAX
                                $.ajax({
                                    url: exportUrl,
                                    type: 'GET',
                                    data: {
                                        schoolYear: schoolYear,
                                        lrn: lrn,
                                        identifier: identifier
                                    },
                                    xhrFields: {
                                        responseType: 'blob' // Expect binary response (file download)
                                    },
                                    success: function (response, status, xhr) {
                                        // Create a downloadable link for the file
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
                                            text: 'Your SF10 file has been downloaded successfully.'
                                        });
                                    },
                                    error: function () {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Export Failed',
                                            text: 'An error occurred while exporting SF10.'
                                        });
                                    }
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Failed to retrieve student grade level. Please try again later.'
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'AJAX Error',
                                text: 'An error occurred while retrieving grade level: ' + error
                            });
                        }
                    });
                }
            });
        });
        //TERM
        
        $.ajax({
            method: "GET",
            url: '../api/api_select_options.php?get_section_list',
            dataType: 'json'
        }).done(function (data) {
            if (Array.isArray(data) && data.length > 0) {
                $('#studentSection').html('<option value="">--</option>');
                $.each(data, function (index, item) {
                    $('#studentSection').append($('<option>', {
                        value: item.SectionName,
                        text: item.SectionName
                    }));
                });
            } else {
                console.warn('No school year data found.');
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.error('AJAX request failed:', textStatus, errorThrown);
        });
    });
    document.getElementById("toggleButton").addEventListener("click", function () {
        let studentContent = document.getElementById("student_content");
        let icon = document.getElementById("toggleIcon");
        if (studentContent.style.display === "none" || studentContent.style.display === "") {
            studentContent.style.display = "block";
            icon.classList.remove("fa-chevron-down");
            icon.classList.add("fa-chevron-up");
        } else {
            studentContent.style.display = "none";
            icon.classList.remove("fa-chevron-up");
            icon.classList.add("fa-chevron-down");
        }
    });
    function toggleSection(event) {
        if (event) event.preventDefault();
        let studentContent = document.getElementById("student_content");
        let icon = document.getElementById("toggleIcon");
        // Only show the section, never hide it when searching
        if (studentContent.style.display === "none") {
            studentContent.style.display = "block";
            icon.classList.remove("fa-chevron-down");
            icon.classList.add("fa-chevron-up");
        }
    }
</script>
</body>
</html>