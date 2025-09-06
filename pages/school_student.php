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
                        Students
                    </h1>
                </div>
            </div>
            <div class="row" style="margin: 0;">
                <div class="col-sm-6" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        Browse and manage student records.
                    </small>
                </div>
                <div class="col-sm-6 text-right" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        Academics / Sections
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
                                        <select class="form-control" id="gradeLevel" name="gradeLevel"
                                            onchange="toggleTermOptions()">
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
                                <div class="col-2">
                                    <div class="form-group">
                                        <label for="studentSex"
                                            style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">Sex</label>
                                        <select class="form-control" id="studentSex" name="studentSex">
                                            <option value="">--</option>
                                            <option value="M">Male</option>
                                            <option value="F">Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-2" id="semesterDiv">
                                    <div class="form-group">
                                        <label for="studentSection"
                                            style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">Section</label>
                                        <select class="form-control" id="studentSection" name="studentSection">
                                            <option value="">--</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label for="studentRemarks"
                                            style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">Remarks</label>
                                        <select class="form-control" id="studentRemarks" name="studentRemarks">
                                            <option value="">--</option>
                                            <option value="T/O">Transferred Out</option>
                                            <option value="T/I">Transferred In</option>
                                            <option value="DRP">Dropped</option>
                                            <option value="LE">Late Enrollment</option>
                                            <option value="CCT">CCT Recipient</option>
                                            <option value="B/A">Balik Aral</option>
                                            <option value="SNED">Special Needs Education</option>
                                            <option value="ACL">Accelerated</option>
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
                                        <button type="submit" class="btn btn-success mr-2" id="searchArchive"
                                            style="display:none;">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-info mr-2" id="addSection"
                                            data-toggle="modal" data-target="#addStudentModal">
                                            <i class="fas fa-plus"></i> Add Student
                                        </button>
                                        <button type="button" class="btn btn-info mr-2" id="btnArchive">
                                            <i class="fas fa-medal"></i> Promotion & Achievement
                                        </button>
                                        <button type="button" class="btn btn-info mr-2" id="btnReturn"
                                            style="display: none;">
                                            <i class="fas fa-reply"></i> Back
                                        </button>
                                        <button type="button" class="btn btn-info mr-2" id="btnSmartPromote"
                                            style="display: none;">
                                            <i class="fas fa-medal"></i> Smart Promote
                                        </button>
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
    <section class="content" id="section_content" style="display: none;">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h1
                            style="text-transform: uppercase;  margin-bottom: 0; font-weight: normal; color:rgb(255, 255, 255); font-size: 12px; margin-left: 10px; display: block;">
                            Student's List</h1>
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
                        <h1
                            style="text-transform: uppercase;  margin-bottom: 0; font-weight: normal; color:rgb(255, 255, 255); font-size: 12px; margin-left: 10px; display: block;">
                            Promotion and Achievement</h1>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="promotion_table" class="table table-bordered table-striped table-hover"
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
include_once('../partials/modals/modal_import.php');
include_once('../partials/modals/modal_student_setup.php');
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
                    d.studentRemarks = $('#studentRemarks').val() || null;
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
                {
                    data: 'name',
                    title: 'Full Name',
                    className: 'align-middle',
                    render: function (data, type, row) {
                        let badgeColor = 'light'; // Default (no badge)
                        let badgeText = 'Not Set';
                        // Extract only the first word (e.g., "T/O" from "T/O DATE:2024/10/03")
                        let status = row.remarks ? row.remarks.split(' ')[0] : '';
                        switch (status) {
                            case 'T/O': badgeColor = 'danger'; badgeText = 'Transferred Out'; break;
                            case 'T/I': badgeColor = 'primary'; badgeText = 'Transferred In'; break;
                            case 'DRP': badgeColor = 'warning'; badgeText = 'Dropped'; break;
                            case 'LE': badgeColor = 'info'; badgeText = 'Late Enrollment'; break;
                            case 'CCT': badgeColor = 'secondary'; badgeText = 'CCT Recipient'; break;
                            case 'B/A': badgeColor = 'secondary'; badgeText = 'Balik Aral'; break;
                            case 'SNED': badgeColor = 'dark'; badgeText = 'Special Needs Education'; break;
                            case 'ACL': badgeColor = 'purple'; badgeText = 'Accelerated'; break;
                            default: badgeColor = 'light'; badgeText = 'Not Set';
                        }
                        return badgeColor
                            ? `${data} <span class="badge bg-${badgeColor}">${badgeText}</span>`
                            : data;
                    }
                },
                { data: 'section', title: 'Section', className: 'align-middle' },
                { data: 'sex', title: 'Sex', className: 'align-middle' },
                { data: 'age', title: 'Age', className: 'align-middle' },
                { data: 'school_year', title: 'School Year', className: 'align-middle' },
                {
                    data: null,
                    title: 'Address',
                    className: 'align-middle',
                    render: function (data, type, row) {
                        return row.province + ', ' + row.municipality_city + ', ' + row.barangay;
                    }
                },
                {
                    data: null,
                    title: 'Action',
                    className: 'text-center',
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                    <button class="btn btn-secondary btn-sm editStudent-btn" data-id="${row.id}" data-name="${row.name}">
                        <i class="fas fa-edit"></i>
                    </button>
                `;
                    }
                }
            ]
        });
        $('#searchSection, #searchArchive').click(function (event) {
            event.preventDefault();
            studentTable.ajax.reload();
            promotionTable.ajax.reload();
        });
        $('#student_table').on('click', '.editStudent-btn', function () {
            var studentId = $(this).data('id'); // Get student ID
            var studentName = $(this).data('name');
            $.ajax({
                url: '../api/api_student.php?get_student_data',
                type: 'GET',
                data: { id: studentId },
                dataType: 'json',
                success: function (response) {
                    if (response && Object.keys(response).length > 0) {
                        const {
                            lrn, name, section, grade_level, school_year, birth_date, sex, contact_number, hssp,
                            barangay, municipality_city, province, father_name, mother_maiden_name, guardian_name,
                            guardian_relationship, learning_modality, remarks, mother_tongue, ethnic_group, religion, strand_track
                        } = response;
                        const remarksCode = remarks ? remarks.split(' ')[0] : '';
                        // Populate fields
                        $('#es_studentId').val(studentId);
                        $('#es_lrn').val(lrn || '');
                        $('#es_name').val(name || '');
                        $('#es_section').val(section || '');
                        $('#es_grade_level').val(grade_level || '');
                        $('#es_school_year').val(school_year || '');
                        $('#es_birth_date').val(birth_date || '');
                        $('#es_gender').val(sex || '');
                        $('#es_contact_number').val(contact_number || '');
                        $('#es_hstp').val(hssp || '');
                        $('#es_barangay').val(barangay || '');
                        $('#es_city').val(municipality_city || '');
                        $('#es_province').val(province || '');
                        $('#es_father_name').val(father_name || '');
                        $('#es_mother_maiden_name').val(mother_maiden_name || '');
                        $('#es_guardian_name').val(guardian_name || '');
                        $('#es_guardian_relationship').val(guardian_relationship || '');
                        $('#es_learning_modality').val(learning_modality || '');
                        $('#es_remarks').val(remarks && remarks.trim() !== '' ? remarks.split(' ')[0] : '');
                        $('#es_mother_tongue').val(mother_tongue || '');
                        $('#es_ethnic_group').val(ethnic_group || '');
                        $('#es_religion').val(religion || '');
                        $('#es_strand_track').val(strand_track || '');
                        // Show the modal
                        $('#studentNameModalTitle').html(`Update Student Information / ${studentName}`);
                        $('#editStudentModal').modal('show');
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: "No data found for the selected student.",
                            icon: "error",
                        });
                    }
                },
                error: function (error) {
                    console.error('AJAX Error:', error);
                    Swal.fire({
                        title: "Error!",
                        text: "An unexpected error occurred while fetching student data.",
                        icon: "error",
                    });
                }
            });
        });
        $('#editStudentForm').on('submit', function (e) {
            e.preventDefault(); // Prevent the default form submission
            var formData = $(this).serialize();
            $.ajax({
                url: '../api/api_student.php?update_student_data',
                type: 'POST',
                data: formData, // Send the serialized form data
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            title: "Success!",
                            text: "Student data updated successfully.",
                            icon: "success",
                        }).then(() => {
                            $('#editStudentModal').modal('hide'); // Hide the modal
                            $('#student_table').DataTable().ajax.reload(); // Reload the DataTable
                            $('#promotion_table').DataTable().ajax.reload(); // Reload the DataTable
                        });
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: response.message || "Failed to update student data.",
                            icon: "error",
                        });
                    }
                },
                error: function (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        title: "Error!",
                        text: "Failed to update student data. No changes made.",
                        icon: "error",
                    });
                }
            });
        });
        $('#addStudentForm').submit(function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Information',
                text: "Are you sure you want to add this student?",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, save it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    let formData = new FormData(this);
                    $.ajax({
                        url: '../api/api_student.php?add_student=true',
                        type: 'POST',
                        processData: false,
                        contentType: false,
                        data: formData,
                        dataType: 'json',
                        success: function (response) {
                            Swal.fire({
                                title: response.status === 'success' ? "Success!" : "Error!",
                                text: response.message,
                                icon: response.status === 'success' ? "success" : "error",
                            }).then(() => {
                                if (response.status === 'success') {
                                    $('#addStudentModal').modal('hide'); // Close the modal
                                    $('#student_table').DataTable().ajax.reload(); // Reload the DataTable
                                }
                            });
                        },
                        error: function (xhr) {
                            console.error(xhr.responseText);
                            Swal.fire({
                                title: "Error!",
                                text: "An error occurred while processing the request.",
                                icon: "error",
                            });
                        }
                    });
                }
            });
        });
        var promotionTable = $('#promotion_table').DataTable({
            responsive: true,
            order: [[0, "asc"]],
            processing: true,
            dom: 'Bfrtip',
            pageLength: 50,
            buttons: [],
            ajax: {
                url: '../api/api_student.php',
                data: function (d) {
                    d.promotion_list = true;
                    d.schoolYear = $('#schoolYear').val();
                    d.gradeLevel = $('#gradeLevel').val();
                    d.studentSex = $('#studentSex').val();
                    d.studentSection = $('#studentSection').val();
                    d.studentRemarks = $('#studentRemarks').val();
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
                { data: 'school_year', title: 'School Year', className: 'align-middle' },
                { data: 'general_average', title: 'General Average', className: 'align-middle' },
                { data: 'action_taken', title: 'Action Taken', className: 'align-middle' },
                { data: 'cecs', title: 'Completed as of end of S.Y.', className: 'align-middle' },
                { data: 'ecs', title: 'As of the end of the current S.Y.', className: 'align-middle' },
                {
                    title: 'Action',
                    className: 'text-center',
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                        <button class="btn btn-secondary btn-sm editPromotion-btn" 
                                data-id="${row.pa_id}" data-name="${row.name}">
                            <i class="fas fa-edit"></i>
                        </button>
                    `;
                    }
                }
            ]
        });
        $('#promotion_table').on('click', '.editPromotion-btn', function () {
            var pa_id = $(this).data('id'); // Get the unique identifier (student ID)
            var studentName = $(this).data('name');
            // Fetch student data based on the identifier
            $.ajax({
                url: '../api/api_student.php', // Endpoint to fetch student data
                type: 'GET',
                data: { get_grade: true, id: pa_id }, // Send the student ID as a parameter
                dataType: 'json',
                success: function (response) {
                    if (response && response.success !== false) {
                        // Populate modal fields with student data
                        $('#ep_pa_id').val(pa_id);
                        $('#ep_name').val(response.name || '');
                        $('#ep_grade_level').val(response.grade_level || '');
                        $('#ep_section').val(response.section || '');
                        $('#ep_sex').val(response.sex || '');
                        $('#ep_ecs').val(response.ecs || '');
                        $('#ep_cecs').val(response.cecs || '');
                        // Show the update student modal
                        $('#editPromotionModalLabel').html(`Update Student Information / ${studentName}`);
                        $('#editPromotionModal').modal('show');
                        // Fetch the general average after the modal is shown
                        var lrn = response.lrn; // Assuming LRN is included in the response
                        var schoolYear = response.school_year; // Assuming school year is included
                        $.ajax({
                            url: '../api/api_student.php', // Endpoint to fetch the general average
                            type: 'GET',
                            data: { get_general_average: true, lrn: lrn, school_year: schoolYear }, // Send LRN and school year
                            dataType: 'json',
                            success: function (avgResponse) {
                                if (avgResponse.success) {
                                    var generalAverage = parseFloat(avgResponse.general_average); // Correct key
                                    // Determine action taken based only on general average
                                    var actionTaken = (generalAverage >= 73) ? "PROMOTED" : "RETAINED";
                                    // Populate the general average and action taken fields
                                    $('#ep_general_average').val(Math.round(generalAverage)); // Ensure proper formatting
                                    $('#ep_action_taken').val(actionTaken);
                                } else {
                                    $('#ep_general_average').val(avgResponse.message); // Show error message
                                    $('#ep_action_taken').val("--");
                                }
                            },
                            error: function (error) {
                                console.error('Error:', error);
                                $('#ep_general_average').val("Not Available."); // Show error message
                                $('#ep_action_taken').val("--");
                            }
                        });
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: response.message || "No data found for the selected student.",
                            icon: "error",
                        });
                    }
                },
                error: function (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        title: "Error!",
                        text: "An unexpected error occurred.",
                        icon: "error",
                    });
                }
            });
        });
        $('#editPromotionForm').submit(function (e) {
            e.preventDefault(); // Prevent the default form submission
            // Get form data
            const formData = $(this).serialize(); // Serialize the form data
            // Send form data via AJAX
            $.ajax({
                url: '../api/api_student.php?update_promotion', // Ensure this URL is correct for your server-side endpoint
                type: 'POST', // Use POST to submit form data
                data: formData,
                dataType: 'json', // Expect JSON response
                success: function (response) {
                    if (response.success) {
                        // Handle success
                        Swal.fire({
                            title: "Success!",
                            text: "Promotion information updated successfully.",
                            icon: "success",
                        }).then(function () {
                            $('#editPromotionModal').modal('hide'); // Close the modal
                            $('#editPromotionForm')[0].reset(); // Reset form fields
                            $('#promotion_table').DataTable().ajax.reload(); // Refresh DataTable
                        });
                    } else {
                        // Handle error from the server
                        Swal.fire({
                            title: "Error!",
                            text: response.message || "Failed to update promotion information.",
                            icon: "error",
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error); // Log any unexpected errors
                    Swal.fire({
                        title: "Error!",
                        text: "An unexpected error occurred. Please try again.",
                        icon: "error",
                    });
                }
            });
        });
        $(document).on('click', '#btnSmartPromote', function () {
            Swal.fire({
                title: 'Smart Promote All Students?',
                text: 'This will calculate general averages and determine promotion status.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Promote',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        html: 'Smart promotion is being applied. Please wait...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => Swal.showLoading()
                    });
                    $.ajax({
                        url: '../api/api_student.php?smart_promote',
                        method: 'POST',
                        dataType: 'json',
                        success: function (response) {
                            Swal.close();
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.message
                                });
                                $('#promotion_table').DataTable().ajax.reload(); // Refresh DataTabl
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed',
                                    text: response.message
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'AJAX Error',
                                text: 'An unexpected error occurred while promoting.'
                            });
                            console.error("AJAX Error:", error);
                        }
                    });
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
            url: '../api/api_select_options.php?get_school_year_list1',
            dataType: 'json'
        }).done(function (data) {
            if (Array.isArray(data) && data.length > 0) {
                // Clear the dropdowns without adding the default "--" option
                $('#e_schoolyear, #a_curriculumterm, #es_school_year, #as_school_year').empty();
                $.each(data, function (index, item) {
                    $('#e_schoolyear, #a_curriculumterm, #es_school_year, #as_school_year').append($('<option>', {
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
                $('#studentSection, #as_section, #es_section').html('<option value="">--</option>');
                $.each(data, function (index, item) {
                    $('#studentSection, #as_section, #es_section').append($('<option>', {
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
    function toggleTermOptions() {
        let gradeLevel = document.getElementById("gradeLevel").value;
        let semesterSelect = document.getElementById("semester");
        if (parseInt(gradeLevel) <= 10) {
            semesterSelect.disabled = true; // Disable the select input
        } else {
            semesterSelect.disabled = false; // Enable the select input
        }
    }
    document.getElementById("toggleButton").addEventListener("click", function () {
        let sectionContent = document.getElementById("section_content");
        let icon = document.getElementById("toggleIcon");
        if (sectionContent.style.display === "none" || sectionContent.style.display === "") {
            sectionContent.style.display = "block";
            icon.classList.remove("fa-chevron-down");
            icon.classList.add("fa-chevron-up");
        } else {
            sectionContent.style.display = "none";
            icon.classList.remove("fa-chevron-up");
            icon.classList.add("fa-chevron-down");
        }
    });
    function toggleSection(event) {
        if (event) event.preventDefault();
        let sectionContent = document.getElementById("section_content");
        let icon = document.getElementById("toggleIcon");
        // Only show the section, never hide it when searching
        if (sectionContent.style.display === "none") {
            sectionContent.style.display = "block";
            icon.classList.remove("fa-chevron-down");
            icon.classList.add("fa-chevron-up");
        }
    }
    document.getElementById('btnArchive').addEventListener('click', function () {
        let archiveContent = document.getElementById("archive_content");
        let sectionContent = document.getElementById("section_content");
        let searchSection = document.getElementById("searchSection");
        let searchArchive = document.getElementById("searchArchive");
        let addSection = document.getElementById("addSection");
        // Hide section content, search button, and add button
        sectionContent.style.display = "none";
        archiveContent.style.display = "inline-block";
        searchArchive.style.display = "inline-block";
        searchSection.style.display = "none";
        addSection.style.display = "none";
        // Hide archive button and show return button
        this.style.display = 'none';
        document.getElementById('btnReturn').style.display = 'inline-block';
        document.getElementById('btnSmartPromote').style.display = 'inline-block';
    });
    document.getElementById('btnReturn').addEventListener('click', function () {
        let archiveContent = document.getElementById("archive_content");
        let sectionContent = document.getElementById("section_content");
        let searchSection = document.getElementById("searchSection");
        let searchArchive = document.getElementById("searchArchive");
        let addSection = document.getElementById("addSection");
        // Show section content, search button, and add button
        sectionContent.style.display = "block";
        archiveContent.style.display = "none";
        searchArchive.style.display = "none";
        searchSection.style.display = "inline-block";
        addSection.style.display = "inline-block";
        // Hide return button and show archive button
        this.style.display = 'none';
        document.getElementById('btnArchive').style.display = 'inline-block';
        document.getElementById('btnSmartPromote').style.display = 'none';
    });
</script>
</body>

</html>