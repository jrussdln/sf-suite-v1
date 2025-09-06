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
                        School Form 1
                    </h1>
                </div>
            </div>
            <div class="row" style="margin: 0;">
                <div class="col-sm-6" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        School Register – Official list of enrolled learners with basic details
                    </small>
                </div>
                <div class="col-sm-6 text-right" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        School Reports / School Forms 1
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
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="schoolYear"
                                            style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">School
                                            Year</label>
                                        <select class="form-control" id="schoolYear" name="schoolYear" disabled>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="gradeLevel"
                                            style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">Grade
                                            Level</label>
                                        <select class="form-control" id="gradeLevel" name="gradeLevel">
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
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="studentSex"
                                            style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">Sex
                                            <i>(Not affect the exportation)</i></label>
                                        <select class="form-control" id="studentSex" name="studentSex">
                                            <option value="">--</option>
                                            <option value="M">Male</option>
                                            <option value="F">Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-3" id="semesterDiv">
                                    <div class="form-group">
                                        <label for="studentSection"
                                            style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">Section</label>
                                        <select class="form-control" id="studentSection" name="studentSection">
                                            <option value="">--</option>
                                        </select>
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
                                        <button type="button" class="btn btn-info mr-2" id="exportSf1">
                                            <i class="fas fa-download">&nbsp;SF1</i>
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
    <section class="content" id="student_content" style="display: none; ">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h1
                            style="text-transform: uppercase; margin-bottom: 0; font-weight: normal; color:rgb(255, 255, 255); font-size: 12px; margin-left: 10px; display: block;">
                            Student's Basic Profile Record List</h1>
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
        var studentTable = $('#student_table').DataTable({
            responsive: true,
            order: [[0, "asc"]],
            pageLength: 50,
            processing: true,
            dom: 'Bfrtip',
            buttons: [],
            ajax: {
                url: '../api/api_student.php',
                data: function (d) {
                    d.student_list = true;
                    d.schoolYear = $('#schoolYear').val();
                    d.gradeLevel = $('#gradeLevel').val();
                    d.studentSex = $('#studentSex').val();
                    d.studentSection = $('#studentSection').val();
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
        $('#exportSf1').on('click', function () {
            // Get the filter values
            var schoolYear = $('#schoolYear').val();
            var gradeLevel = $('#gradeLevel').val();
            var studentSection = $('#studentSection').val();
            var identifier = '<?php echo htmlspecialchars($_SESSION['Identifier']); ?>';
            // Check if any of the fields are empty
            if (!schoolYear || !gradeLevel || !studentSection) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please fill in all fields before exporting.'
                });
                return; // Stop the function if any field is empty
            }
            // Show SweetAlert loading spinner
            Swal.fire({
                title: 'Exporting...',
                text: 'Please wait while we export your data.',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); // Show the loading spinner
                }
            });
            // Perform AJAX request to export data
            $.ajax({
                url: '../includes/functions/func_export.php',
                type: 'GET',
                data: {
                    schoolYear: schoolYear,
                    gradeLevel: gradeLevel,
                    studentSection: studentSection,
                    identifier: identifier // Include the identifier
                },
                xhrFields: {
                    responseType: 'blob' // Expect a binary response (file download)
                },
                success: function (response, status, xhr) {
                    // Create a link element to download the file
                    var blob = new Blob([response], { type: xhr.getResponseHeader('Content-Type') });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    // Get the filename from the Content-Disposition header and set it for download
                    var disposition = xhr.getResponseHeader('Content-Disposition');
                    var filename = disposition && disposition.indexOf('filename=') !== -1
                        ? disposition.split('filename=')[1].replace(/"/g, '')
                        : 'exported_file.xlsx';
                    link.download = filename;
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
                },
                complete: function () {
                    // Close the loading spinner when the AJAX request is complete
                    Swal.close();
                }
            });
        });
        
        $.ajax({
            method: "GET",
            url: '../api/api_select_options.php?get_school_year_list',
            dataType: 'json'
        }).done(function (data) {
            if (Array.isArray(data) && data.length > 0) {
                // Clear the dropdowns without adding the default "--" option
                $('#schoolYear').empty();
                $.each(data, function (index, item) {
                    $('#schoolYear').append($('<option>', {
                        value: item.sy_term,
                        text: item.sy_term
                    }));
                });
            } else {
                console.warn('No school year data found.');
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.error('AJAX request failed:', textStatus, errorThrown);
        });
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