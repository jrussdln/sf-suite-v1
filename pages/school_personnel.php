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
                        School Personnel
                    </h1>
                </div>
            </div>
            <div class="row" style="margin: 0;">
                <div class="col-sm-6" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        Browse and manage records of personnels.
                    </small>
                </div>
                <div class="col-sm-6 text-right" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        Academics / School Personnel
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
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="empStatus"
                                            style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">Employment
                                            Status</label>
                                        <select class="form-control" id="empStatus" name="empStatus">
                                            <option value="" disabled selected>--</option>
                                            <option value="PERMANENT">PERMANENT</option>
                                            <option value="PROBATIONARY">PROBATIONARY</option>
                                            <option value="CONTRACTUAL">CONTRACTUAL</option>
                                            <option value="JOB ORDER">JOB ORDER (JO) / CONTRACT OF SERVICE (COS)
                                            </option>
                                            <option value="SUBSTITUTE">SUBSTITUTE TEACHER</option>
                                            <option value="INACTIVE">INACTIVE</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="personnelSex"
                                            style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">Sex</label>
                                        <select class="form-control" id="personnelSex" name="personnelSex">
                                            <option value="">--</option>
                                            <option value="M">Male</option>
                                            <option value="F">Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="plaPos"
                                            style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">Plantilla</label>
                                        <select class="form-control" id="plaPos" name="plaPos">
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
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-info mr-2" data-toggle="modal"
                                            data-target="#addPersonnelModal">
                                            <i class="fas fa-plus"></i> Add Personnel
                                        </button>
                                        <button id="viewPlantillaPosList-btn" type="button" class="btn btn-info ml-2"
                                            data-toggle="modal" data-target="#viewPlantillaPosListModal">
                                            <i class="fas fa-user-tie"></i>Plantilla List
                                        </button>
                                        <button type="button" class="btn btn-info ml-2" id="viewAncAssListBtn">
                                            <i class="fas fa-tags"></i>Ancillary List
                                        </button>
                                        <button id="toggleButton" type="button" class="btn btn-primary ml-2"
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
                            style="text-transform: uppercase; font-weight: normal; color:rgb(255, 255, 255); font-size: 12px; margin-bottom: 0; margin-left: 10px; display: block;">
                            School Personnel List</h1>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="school_per_table" class="table table-bordered table-striped table-hover"
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
include_once('../partials/modals/modal_personnel_setup.php');
include_once('../partials/modals/modal_user_setup.php');
include_once('../partials/footer.php');
?>
<script>
    $(document).ready(function () {
        var schoolPerTable = $('#school_per_table').DataTable({
            responsive: true,
            order: [[0, "asc"]],
            pageLength: 50,
            processing: true,
            dom: 'Bfrtip',
            buttons: [],
            ajax: {
                url: '../api/api_personnel.php',
                data: function (d) {
                    d.personnel_list = true;
                    d.personnelSex = $('#personnelSex').val();
                    d.empStatus = $('#empStatus').val();
                    d.plaPos = $('#plaPos').val();
                },
                dataSrc: ''
            },
            columnDefs: [
                { responsivePriority: 1, targets: 0 }, // Employee Number
                { responsivePriority: 2, targets: 1 }, // Full Name
                { responsivePriority: 3, targets: 2 }, // Gender
                { responsivePriority: 4, targets: 3 }, // Email
                { responsivePriority: 5, targets: 4 }, // BirthDate
                { responsivePriority: 6, targets: 5 }, // Employment Status
                { responsivePriority: 7, targets: 6 }  // Highest Educational Degree
            ],
            columns: [
                { data: 'EmpNo', title: 'Employer No.', className: 'align-middle' },
                {
                    data: null,
                    title: 'Full Name',
                    className: 'align-middle text-start',
                    render: function (data, type, row) {
                        // Compute Full Name
                        row.fullName = `${row.EmpLName}, ${row.EmpFName} ${row.EmpMName ? row.EmpMName.charAt(0) + '.' : ''} ${row.EmpEName ? row.EmpEName : ''}`.trim();
                        // Assign badge colors based on employment status
                        let statusClass = 'bg-secondary';
                        switch (row.EmploymentStatus) {
                            case 'PERMANENT': statusClass = 'bg-success'; break;
                            case 'PROBATIONARY': statusClass = 'bg-primary'; break;
                            case 'CONTRACTUAL': statusClass = 'bg-warning text-dark'; break;
                            case 'JOB ORDER':
                            case 'CONTRACT OF SERVICE': statusClass = 'bg-info text-dark'; break;
                            case 'SUBSTITUTE': statusClass = 'bg-danger'; break;
                        }
                        // Create Employment Status badge
                        let statusBadge = `<span class="badge ${statusClass} ms-2">${row.EmploymentStatus}</span>`;
                        // Create Subject Count badge
                        let subjectCountBadge = `<span class="badge bg-info ms-2">${row.subject_count} Teaching Loads</span>`;
                        return `<div class="d-inline-block text-start">${row.fullName} ${statusBadge} ${subjectCountBadge}</div>`;
                    }
                },
                { data: 'Sex', title: 'Gender', className: 'align-middle' },
                { data: 'email', title: 'Email', className: 'align-middle' },
                { data: 'BirthDate', title: 'BirthDate', className: 'align-middle' },
                { data: 'EmploymentStatus', title: 'Employment Status', className: 'align-middle' },
                {
                    data: null,
                    title: 'Action',
                    className: 'align-middle',
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                <button class="btn btn-primary btn-sm editPersonnel-btn" data-id="${row.PersonnelId}" data-name="${row.fullName}">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-secondary btn-sm viewSubjectTaught-btn" data-id="${row.PersonnelId}" data-name="${row.fullName}">
                    <i class="fas fa-tasks"></i>
                </button>
                <button class="btn btn-secondary btn-sm viewAncAssignment-btn" data-id="${row.PersonnelId}" data-name="${row.fullName}">
                    <i class="fas fa-tags"></i>
                </button>
                <button class="btn btn-primary btn-sm viewPlantilla-btn" data-id="${row.PersonnelId}" data-name="${row.fullName}">
                    <i class="fas fa-user-tie"></i>
                </button>
                <button class="btn btn-primary btn-sm createAccount-btn" data-id="${row.PersonnelId}">
                    <i class="fas fa-user-plus"></i>
                </button>
            `;
                    }
                }
            ]
        });
        //search button
        $('#searchSection').click(function (event) {
            event.preventDefault();
            schoolPerTable.ajax.reload();
        });
        //add personnel submission
        $('#addPersonnelForm').submit(function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Information',
                text: "Are you sure you want to add this user?",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, save it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../api/api_personnel.php?add_personnel',
                        type: 'POST',
                        processData: false,
                        contentType: false,
                        data: new FormData(this),
                        success: function (response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: "Success!",
                                    text: response.message,
                                    icon: "success",
                                }).then(() => {
                                    $('#addPersonnelModal').modal('hide'); // Hide the modal
                                    $('#addPersonnelForm')[0].reset();  // Reset the form field
                                    // Optionally, refresh the DataTable to reflect changes
                                    $('#school_per_table').DataTable().ajax.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: "Error!",
                                    text: response.message,
                                    icon: "error",
                                });
                            }
                        },
                        error: function () {
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
        //display personnel info
        $('#school_per_table').on('click', '.editPersonnel-btn', function () {
            var PersonnelId = $(this).data('id'); // Get the unique identifier (e.g., EmpNo or other)
            var fullName = $(this).data('name');
            // Fetch user data based on the identifier (EmpNo)
            $.ajax({
                url: '../api/api_personnel.php?get_personnel_data', // Endpoint to fetch personnel data
                type: 'GET',
                data: { PersonnelId: PersonnelId },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        // Dynamically populate the modal fields with the returned data
                        $('#PersonnelId').val(response.data.PersonnelId);
                        $('#e_EmpNo').val(response.data.EmpNo);
                        $('#e_email').val(response.data.email);
                        $('#e_lname').val(response.data.EmpLName);
                        $('#e_fname').val(response.data.EmpFName);
                        $('#e_mname').val(response.data.EmpMName);
                        $('#e_ename').val(response.data.EmpEName);
                        $('#e_sex').val(response.data.Sex);
                        $('#e_fund_source').val(response.data.FundSource);
                        $('#e_birthdate').val(response.data.BirthDate);
                        $('#e_employment_status').val(response.data.EmploymentStatus);
                        $('#e_degree').val(response.data.EducDegree);
                        $('#e_major').val(response.data.EducMajor);
                        $('#e_minor').val(response.data.EducMinor);
                        $('#e_post_graduate').val(response.data.PostGraduate);
                        $('#e_specialization').val(response.data.Specialization);
                        // Show the update personnel modal
                        $('#editPersonnelModalLabel').html(`Edit Personnel / ${fullName}`);
                        $('#editPersonnelModal').modal('show');
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: response.message,
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
        //edit personnel info
        $(document).on('submit', '#editPersonnelForm', function (event) {
            event.preventDefault();  // Prevent the default form submission
            $.ajax({
                url: '../api/api_personnel.php?edit_personnel',  // API endpoint
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    console.log(response); // Debugging response
                    if (response.success) {
                        Swal.fire({
                            title: "Success!",
                            text: response.message,
                            icon: "success",
                        }).then(() => {
                            $('#editPersonnelModal').modal('hide'); // Hide modal
                            $('#editPersonnelForm')[0].reset(); // Reset form
                            $('#school_per_table').DataTable().ajax.reload(); // Reload table
                        });
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: response.message,
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
        //subject taught table
        $('#school_per_table').on('click', '.viewSubjectTaught-btn', function () {
            var PersonnelId = $(this).data('id');
            var fullName = $(this).data('name');
            // Set PersonnelId for smart copy button
            $('#smartCopy-btn').data('id', PersonnelId);
            // Show the modal
            $('#viewSubjectTaughtModal').modal('show');
            $('#viewSubjectTaughtModalLabel').html(`Subjects Taught / ${fullName}`);
            $('#updateSubjectTaughtModalLabel').html(`Subjects Taught / Edit / ${fullName}`);
            $('#addSubjectTaughtModalLabel').html(`Subjects Taught / Add / ${fullName}`);
            // Check if the DataTable already exists
            if ($.fn.dataTable.isDataTable('#viewSubjectTaught_table')) {
                $('#viewSubjectTaught_table').DataTable().clear().destroy();
            }
            // Initialize DataTable for viewing subjects taught
            var viewSubjectTaughtTable = $('#viewSubjectTaught_table').DataTable({
                responsive: true,
                order: [[0, "asc"]],
                processing: true,
                dom: 'Bfrtip',
                buttons: [], // Empty buttons as we're adding the Add button outside the table
                searching: false, // Disable search bar
                ajax: {
                    url: '../api/api_personnel.php?get_subject_taught_data',
                    data: { PersonnelId: PersonnelId },
                    dataSrc: function (json) {
                        return json.success ? json.data : []; // Return data or empty array
                    },
                    error: function (xhr, error, thrown) {
                        console.error('Error:', error);
                        console.error('Response:', xhr.responseText);
                        alert('Failed to load data');
                    }
                },
                language: {
                    emptyTable: "No data available"
                },
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 1 },
                    { responsivePriority: 3, targets: 2 },
                    { responsivePriority: 4, targets: 3 },
                ],
                columns: [
                    { data: 'subject_taught', title: 'Subject Taught', className: 'align-middle' },
                    { data: 'subject_code', title: 'Subject Code', className: 'align-middle' },
                    { data: 'section', title: 'Section', className: 'align-middle' },
                    { data: 'st_day', title: 'Day (M/T/W/Th/F)', className: 'align-middle' },
                    { data: 'st_from', title: 'From (00:00)', className: 'align-middle' },
                    { data: 'st_to', title: 'To (00:00)', className: 'align-middle' },
                    { data: 'tat_min', title: 'Total Actual Teaching Minute', className: 'align-middle' },
                    {
                        data: null,
                        title: 'Action',
                        className: 'text-center',
                        orderable: false,
                        render: function (data, type, row) {
                            return `
                                <button class="btn btn-info btn-sm editSubt-btn" data-id="${row.stac_id}">
                                    <i class="fas fa-edit"></i>
                                </button>
                            `;
                        }
                    }
                ]
            });
            // Handle Add Subject button outside of DataTable
            $('.addSubt-btn').off('click').on('click', function () {
                openAddSubjectModal(PersonnelId);
            });
        });
        $('#smartCopy-btn').on('click', function () {
            var PersonnelId = $(this).data('id'); // Now, this retrieves the PersonnelId from the viewSubjectTaught button
            // Ask for confirmation before proceeding with the copy
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to copy the subject taught from the previous academic year?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, copy it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send the request to the API endpoint using jQuery AJAX
                    $.ajax({
                        url: '../api/api_personnel.php?smartCopy_subject_taught', // Ensure this is correct
                        type: 'POST', // Use POST method
                        data: { PersonnelId: PersonnelId },
                        success: function (response) {
                            console.log('Response:', response); // Log the response to check
                            if (response.status === 'success') {
                                Swal.fire("Success", response.message, "success");
                                // Optionally, refresh the DataTable to reflect changes
                                $('#viewSubjectTaught_table').DataTable().ajax.reload();
                            } else {
                                Swal.fire("Error", response.message, "error");
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX error:', error);
                            Swal.fire("Error", "An error occurred while copying subjects.", "error");
                        }
                    });
                } else {
                    Swal.fire("Cancelled", "The copy action was cancelled.", "info");
                }
            });
        });
        //add subject taght
        $('#addSubjectTaughtForm').submit(function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Information',
                text: "Are you sure you want to add this subject taught?",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, save it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../api/api_personnel.php?add_subject_taught', // Update the URL as needed
                        type: 'POST',
                        processData: false,
                        contentType: false,
                        data: new FormData(this),
                        success: function (response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: "Success!",
                                    text: response.message,
                                    icon: "success",
                                }).then(() => {
                                    $('#addSubjectTaughtModal').modal('hide'); // Hide the modal
                                    $('#addSubjectTaughtForm')[0].reset();  // Reset the form fields
                                    // Optionally, refresh the DataTable to reflect changes
                                    $('#viewSubjectTaught_table').DataTable().ajax.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: "Error!",
                                    text: response.message,
                                    icon: "error",
                                });
                            }
                        },
                        error: function () {
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
        //display subject taught details
        $('#viewSubjectTaught_table').on('click', '.editSubt-btn', function () {
            var stac_id = $(this).data('id'); // Get the subject ID
            var fullName = $(this).data('name');
            // Make an AJAX call to get the subject details
            $.ajax({
                url: '../api/api_personnel.php?get_subt_details', // API endpoint
                data: { stac_id: stac_id }, // Send the necessary parameters
                method: 'GET',
                success: function (response) {
                    // Assuming the response is a JSON object
                    var data = response.data;
                    // Populate the modal fields with the fetched data
                    $('#u_PersonnelId').val(data.PersonnelId);
                    $('#u_stac_id').val(data.stac_id);
                    $('#u_subjectTaught').val(data.subject_taught);
                    $('#u_subject_code').val(data.subject_code);
                    $('#u_section').val(data.section);
                    $('#u_stDay').val(data.st_day);
                    $('#u_stFrom').val(data.st_from);
                    $('#u_stTo').val(data.st_to);
                    $('#u_tatMin').val(data.tat_min);
                    // Show the modal
                    $('#updateSubjectTaughtModal').modal('show');
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching subject details:", error);
                }
            });
        });
        //update subject taught
        $('#updateSubjectTaughtForm').submit(function (e) {
            e.preventDefault(); // Prevent the default form submission
            Swal.fire({
                title: 'Information',
                text: "Are you sure you want to update this subject taught?",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../api/api_personnel.php?update_subject_taught', // Update the URL as needed
                        type: 'POST',
                        processData: false,
                        contentType: false,
                        data: new FormData(this), // Send the form data
                        success: function (response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: "Success!",
                                    text: response.message,
                                    icon: "success",
                                }).then(() => {
                                    $('#updateSubjectTaughtModal').modal('hide'); // Hide the modal
                                    $('#updateSubjectTaughtForm')[0].reset();  // Reset the form fields
                                    // Optionally, refresh the DataTable to reflect changes
                                    $('#viewSubjectTaught_table').DataTable().ajax.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: "Error!",
                                    text: response.message,
                                    icon: "error",
                                });
                            }
                        },
                        error: function () {
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
        $('#school_per_table').on('click', '.viewAncAssignment-btn', function () {
            var PersonnelId = $(this).data('id');
            var fullName = $(this).data('name');
            // Set modal labels
            $('#ancAssignmentModalLabel').html(`Ancillary Assignments / ${fullName}`);
            $('#addAncAssignmentModalLabel').html(`Ancillary Assignments / Add / ${fullName}`);
            // Show the modal
            $('#ancAssignmentModal').modal('show');
            // Check if the DataTable already exists and destroy it if it does
            if ($.fn.dataTable.isDataTable('#anc_assignment_table')) {
                $('#anc_assignment_table').DataTable().clear().destroy();
            }
            // Initialize the DataTable for ancillary assignments
            var ancAssignmentTable = $('#anc_assignment_table').DataTable({
                responsive: true,
                order: [[0, "asc"]],
                pageLength: 50,
                processing: true,
                searching: false, // Disable the search bar
                dom: 'Bfrtip',
                buttons: [], // Empty buttons as we are handling the "Add" button outside the table
                ajax: {
                    url: '../api/api_personnel.php?get_anc_assignment_data', // API endpoint
                    data: { PersonnelId: PersonnelId }, // Pass PersonnelId for filtering the data
                    dataSrc: function (json) {
                        return json.data; // Return data directly from the API
                    },
                    error: function (xhr, error, thrown) {
                        console.error('Error:', error);
                        console.error('Response:', xhr.responseText);
                        alert('Failed to load data');
                    }
                },
                language: {
                    emptyTable: "No data available" // Message when no data is available
                },
                columnDefs: [
                    { responsivePriority: 1, targets: 0 }, // Ancillary Assignment Description
                ],
                columns: [
                    { data: 'anc_ass_desc', title: 'Ancillary Assignments', className: 'align-middle' },
                    { data: 'anc_ass_term', title: 'Term', className: 'align-middle' },
                    {
                        data: null,
                        title: 'Action',
                        className: 'text-center',
                        orderable: false,
                        render: function (data, type, row) {
                            return `
                        <button class="btn btn-info btn-sm deleteAncAss-btn" data-id="${row.anc_ass_id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                        }
                    }
                ]
            });
            // Handle the Add Ancillary Assignment button outside the DataTable
            $('.addAncAssignment-btn').off('click').on('click', function () {
                openAddAncAssignmentModal(PersonnelId); // Open the add modal with PersonnelId
            });
        });
        //add anc ass
        $('#addAncAssignmentForm').submit(function (e) {
            e.preventDefault(); // Prevent the default form submission
            Swal.fire({
                title: 'Information',
                text: "Are you sure you want to add this ancillary assignment?",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, save it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../api/api_personnel.php?add_anc_assignment', // Update the URL as needed
                        type: 'POST',
                        processData: false,
                        contentType: false,
                        data: new FormData(this), // Send the form data
                        success: function (response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: "Success!",
                                    text: response.message,
                                    icon: "success",
                                }).then(() => {
                                    $('#addAncAssignmentModal').modal('hide'); // Hide the modal
                                    $('#addAncAssignmentForm')[0].reset();  // Reset the form fields
                                    // Optionally, refresh the DataTable to reflect changes
                                    $('#anc_assignment_table').DataTable().ajax.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: "Error!",
                                    text: response.message,
                                    icon: "error",
                                });
                            }
                        },
                        error: function () {
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
        //display anc ass list details
        $('#viewAncAssListBtn').on('click', function () {
            $('#ancAssListModal').modal('show');
            if ($.fn.dataTable.isDataTable('#anc_ass_list_table')) {
                $('#anc_ass_list_table').DataTable().clear().destroy();
            }
            var ancAssListTable = $('#anc_ass_list_table').DataTable({
                responsive: true,
                order: [[0, "asc"]],
                processing: true,
                pageLength: 50,
                dom: "<'d-flex justify-content-between'Bf>rtip", // Custom layout: Buttons (left) + Search (right)
                buttons: [], // No button here now
                ajax: {
                    url: '../api/api_personnel.php?get_anc_ass_list_details', // The API endpoint to fetch all ancillary assignment list data
                    dataSrc: function (json) {
                        return json.data; // Return the array of data directly
                    },
                    error: function (xhr, error, thrown) {
                        console.error('Error:', error);
                        console.error('Response:', xhr.responseText);
                        alert('Failed to load data');
                    }
                },
                language: {
                    emptyTable: "No data available" // Custom message when no data is available
                },
                columnDefs: [
                    { responsivePriority: 1, targets: 0 }, // Ancillary Assignment ID
                ],
                columns: [
                    { data: 'anc_ass_list', title: 'Assignment Description', className: 'align-middle' },
                    {
                        data: null,
                        title: 'Action',
                        className: 'text-center',
                        orderable: false,
                        render: function (data, type, row) {
                            return `
                            <button class="btn btn-info btn-sm deleteAncAssList-btn" data-id="${row.anc_ass_list_id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                        }
                    }
                ]
            });
            // Event listener to handle the "Add Ancillary Assignment" button inside the modal
            $('.addAncAssList-btn').on('click', function () {
                openAddAncAssListModal(); // Open the modal when the button is clicked
            });
        });
        //add ng anc ass list
        $('#saveAncAssListBtn').on('click', function () {
            var formData = $('#addAncAssListForm').serialize();
            $.ajax({
                url: '../api/api_personnel.php?add_anc_ass_list', // Update with your API endpoint
                type: 'POST',
                data: formData,
                success: function (response) {
                    if (response.success) {
                        // Close the modal
                        $('#addAncAssListModal').modal('hide');
                        // Optionally, refresh the DataTable or update the UI
                        $('#anc_ass_list_table').DataTable().ajax.reload();
                        // Show success message using SweetAlert2
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Ancillary assignment added successfully.',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        // Show error message using SweetAlert2
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message,
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function (xhr, error, thrown) {
                    console.error('Error:', error);
                    // Show error message using SweetAlert2
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed!',
                        text: 'Failed to add ancillary assignment.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
        //delete ng anc ass list
        $(document).on('click', '.deleteAncAssList-btn', function () {
            var ancAssListId = $(this).data('id'); // Get the ID of the ancillary assignment to delete
            // Show confirmation dialog using SweetAlert2
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // AJAX request to delete the ancillary assignment
                    $.ajax({
                        url: '../api/api_personnel.php?delete_anc_ass_list', // Update with your API endpoint
                        type: 'POST',
                        data: { id: ancAssListId }, // Send the ID of the assignment to delete
                        success: function (response) {
                            if (response.success) {
                                // Refresh the DataTable to reflect the changes
                                $('#anc_ass_list_table').DataTable().ajax.reload();
                                // Show success message
                                Swal.fire(
                                    'Deleted!',
                                    'The ancillary assignment has been deleted.',
                                    'success'
                                );
                            } else {
                                // Show error message
                                Swal.fire(
                                    'Error!',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function (xhr, error, thrown) {
                            console.error('Error:', error);
                            // Show error message
                            Swal.fire(
                                'Failed!',
                                'Failed to delete the ancillary assignment.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
        //delete ng anc ass
        $(document).on('click', '.deleteAncAss-btn', function () {
            var ancAssId = $(this).data('id'); // Get the ID of the ancillary assignment to delete
            // Show confirmation dialog using SweetAlert2
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // AJAX request to delete the ancillary assignment
                    $.ajax({
                        url: '../api/api_personnel.php?delete_anc_ass', // Update with your API endpoint
                        type: 'POST',
                        data: { id: ancAssId }, // Send the ID of the assignment to delete
                        success: function (response) {
                            if (response.success) {
                                // Refresh the DataTable to reflect the changes
                                $('#anc_assignment_table').DataTable().ajax.reload();
                                // Show success message
                                Swal.fire(
                                    'Deleted!',
                                    'The ancillary assignment has been deleted.',
                                    'success'
                                );
                            } else {
                                // Show error message
                                Swal.fire(
                                    'Error!',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function (xhr, error, thrown) {
                            console.error('Error:', error);
                            // Show error message
                            Swal.fire(
                                'Failed!',
                                'Failed to delete the ancillary assignment.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
        $('#school_per_table').on('click', '.viewPlantilla-btn', function () {
            var PersonnelId = $(this).data('id');
            var fullName = $(this).data('name');
            // Set modal labels
            $('#viewPlantillaModalLabel').html(`Plantilla Position / ${fullName}`);
            $('#addPlantillaModalLabel').html(`Plantilla Position / Add / ${fullName}`);
            $('#updatePlantillaModalLabel').html(`Plantilla Position / Edit / ${fullName}`);
            // Show the modal
            $('#viewPlantillaModal').modal('show');
            // Check if the DataTable already exists and destroy it if it does
            if ($.fn.dataTable.isDataTable('#viewPlantilla_table')) {
                $('#viewPlantilla_table').DataTable().clear().destroy();
            }
            // Initialize the DataTable for viewing plantilla positions
            var viewPlantillaTable = $('#viewPlantilla_table').DataTable({
                responsive: true,
                order: [[0, "asc"]],
                processing: true,
                paging: false,
                info: false,
                searching: false,
                dom: 'Bfrtip',
                buttons: [], // Empty buttons as we are handling the "Add" button outside the table
                ajax: {
                    url: '../api/api_personnel.php?get_plantilla_data', // API endpoint
                    data: { PersonnelId: PersonnelId }, // Pass the PersonnelId to the API
                    dataSrc: function (json) {
                        if (json.success) {
                            return json.data; // Return the data if successful
                        } else {
                            console.error('Error fetching data:', json.message);
                            return []; // Return empty array if no data
                        }
                    },
                },
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 1 }
                ],
                columns: [
                    { data: 'pp_desc', title: 'Description', className: 'align-middle' },
                    {
                        data: null,
                        title: 'Action',
                        className: 'text-center',
                        orderable: false,
                        render: function (data, type, row) {
                            return `
                        <button class="btn btn-info btn-sm editPlantilla-btn" data-id="${row.pp_id}">
                            <i class="fas fa-edit"></i>
                        </button>
                    `;
                        }
                    }
                ]
            });
            // Handle the Add Plantilla button outside the DataTable
            $('.addPlantilla-btn').off('click').on('click', function () {
                openAddPlantillaModal(PersonnelId); // Open the add plantilla modal
            });
        });
        $('#addPlantillaForm').submit(function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Information',
                text: "Are you sure you want to add this plantilla position?",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, save it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../api/api_personnel.php?add_plantilla',
                        type: 'POST',
                        processData: false,
                        contentType: false,
                        data: new FormData(this),
                        success: function (response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: "Success!",
                                    text: response.message,
                                    icon: "success",
                                }).then(() => {
                                    $('#addPlantillaModal').modal('hide'); // Hide the modal
                                    $('#addPlantillaForm')[0].reset();  // Reset the form fields
                                    // Optionally, refresh the DataTable to reflect changes
                                    $('#viewPlantilla_table').DataTable().ajax.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: "Error!",
                                    text: response.message,
                                    icon: "error",
                                });
                            }
                        },
                        error: function () {
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
        $('#viewPlantilla_table').on('click', '.editPlantilla-btn', function () {
            var pp_id = $(this).data('id'); // Get the subject ID
            // Make an AJAX call to get the subject details
            $.ajax({
                url: '../api/api_personnel.php?get_plantilla_details', // API endpoint
                data: { pp_id: pp_id }, // Send the necessary parameters
                method: 'GET',
                success: function (response) {
                    // Assuming the response is a JSON object
                    var data = response.data;
                    // Populate the modal fields with the fetched data
                    $('#u_pp_PersonnelId').val(data.PersonnelId);
                    $('#u_pp_pp_id').val(data.pp_id);
                    $('#u_pp_pp_desc').val(data.pp_desc);
                    // Show the modal
                    $('#updatePlantillaModal').modal('show');
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching subject details:", error);
                }
            });
        });
        $('#updatePlantillaForm').submit(function (e) {
            e.preventDefault(); // Prevent the default form submission
            Swal.fire({
                title: 'Information',
                text: "Are you sure you want to update this?",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../api/api_personnel.php?update_plantilla', // Update the URL as needed
                        type: 'POST',
                        data: $(this).serialize(), // Serialize the form data
                        success: function (response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: "Success!",
                                    text: response.message,
                                    icon: "success",
                                }).then(() => {
                                    $('#updatePlantillaModal').modal('hide'); // Hide the modal
                                    $('#updatePlantillaForm')[0].reset();  // Reset the form fields
                                    // Optionally, refresh the DataTable to reflect changes
                                    $('#viewPlantilla_table').DataTable().ajax.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: "Error!",
                                    text: response.message,
                                    icon: "error",
                                });
                            }
                        },
                        error: function () {
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
        var viewPlantillaPosListTable = $('#viewPlantillaPosList_table').DataTable({
            responsive: true,
            order: [[0, "asc"]],
            processing: true,
            dom: 'Bfrtip',
            paging: false,
            info: false,
            buttons: [], // No button in the DataTable now
            searching: false, // Disable the search bar
            ajax: {
                url: '../api/api_personnel.php?get_plantilla_list_details', // The API endpoint to fetch all plantilla list data
                dataSrc: function (json) {
                    return json.data; // Return the array of data directly
                },
                error: function (xhr, error, thrown) {
                    console.error('Error:', error);
                    console.error('Response:', xhr.responseText);
                    alert('Failed to load data');
                }
            },
            language: {
                emptyTable: "No data available" // Custom message when no data is available
            },
            columnDefs: [
                { responsivePriority: 1, targets: 0 }, // Plantilla Position ID
            ],
            columns: [
                { data: 'ppl_desc', title: 'Plantilla Position Name', className: 'align-middle' },
                { data: 'ppl_code', title: 'Plantilla Position Code', className: 'align-middle' },
                { data: 'ppl_rank', title: 'Plantilla Position Rank', className: 'align-middle' },
                {
                    data: null,
                    title: 'Action',
                    className: 'text-center',
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                            <button class="btn btn-info btn-sm editPlantillaList-btn" data-id="${row.ppl_id}">
                                <i class="fas fa-edit"></i>
                            </button>
                        `;
                    }
                }
            ]
        });
        // Bind the button outside the DataTable to open the modal
        $('#addPlantillaPosList-btn').on('click', function () {
            openAddPlantillaPosListModal(); // Function to open the modal
        });
        $('#addPlantillaPosListForm').submit(function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Information',
                text: "Are you sure you want to add this plantilla position?",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, save it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../api/api_personnel.php?add_plantilla_list',
                        type: 'POST',
                        processData: false,
                        contentType: false,
                        data: new FormData(this),
                        success: function (response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: "Success!",
                                    text: response.message,
                                    icon: "success",
                                }).then(() => {
                                    $('#addPlantillaPosListModal').modal('hide'); // Hide the modal
                                    $('#addPlantillaPosListForm')[0].reset();  // Reset the form fields
                                    // Optionally, refresh the DataTable to reflect changes
                                    $('#viewPlantillaPosList_table').DataTable().ajax.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: "Error!",
                                    text: response.message,
                                    icon: "error",
                                });
                            }
                        },
                        error: function () {
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
        $('#viewPlantillaPosList_table').on('click', '.editPlantillaList-btn', function () {
            var ppl_id = $(this).data('id'); // Get the Plantilla Position ID
            console.log('Fetching data for ppl_id:', ppl_id); // Debugging log
            $.ajax({
                url: '../api/api_personnel.php',
                data: { get_plantilla_list_detailss: 1, ppl_id: ppl_id },
                method: 'GET',
                dataType: 'json', // Ensures the response is treated as JSON
                success: function (response) {
                    console.log(response); // Debugging response
                    if (response.success && response.data.length > 0) {
                        var data = response.data[0]; // Access first object in array
                        // Populate form fields
                        $('#u_ppl_id').val(data.ppl_id || '');
                        $('#u_ppl_desc').val(data.ppl_desc || '');
                        $('#u_ppl_code').val(data.ppl_code || '');
                        $('#u_ppl_rank').val(data.ppl_rank || '');
                        $('#u_ppl_category').val(data.ppl_category || '');
                        // Show the modal
                        $('#updatePlantillaPosListModal').modal('show');
                    } else {
                        alert(response.message || 'No data found.');
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching plantilla details:", error);
                    alert('An error occurred while fetching plantilla details. Please try again.');
                }
            });
        });
        $('#updatePlantillaPosListForm').submit(function (e) {
            e.preventDefault(); // Prevent default form submission
            Swal.fire({
                title: 'Information',
                text: "Are you sure you want to update this?",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../api/api_personnel.php?update_plantilla_list', // Ensure correct API endpoint
                        type: 'POST',
                        data: $(this).serialize(), // Serialize form data
                        dataType: 'json',
                        success: function (response) {
                            console.log("Response:", response); // Debugging
                            if (response.status === 'success') { // Fix: Changed from response.success
                                Swal.fire({
                                    title: "Success!",
                                    text: response.message,
                                    icon: "success",
                                }).then(() => {
                                    $('#updatePlantillaPosListModal').modal('hide'); // Hide modal
                                    $('#updatePlantillaPosListForm')[0].reset(); // Reset form fields
                                    // Reload DataTable to reflect changes
                                    $('#viewPlantillaPosList_table').DataTable().ajax.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: "Error!",
                                    text: response.message,
                                    icon: "error",
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("AJAX Error:", error);
                            console.log("XHR Response:", xhr.responseText); // Debugging
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
        $(document).on('click', '.createAccount-btn', function () {
            let personnelId = $(this).data('id');
            Swal.fire({
                title: 'Create Account',
                text: 'Are you sure you want to create an account for this personnel?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Create',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../api/api_personnel.php?create_account=true',
                        type: 'POST',
                        data: { PersonnelId: personnelId },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                Swal.fire('Success', response.message, 'success');
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error', 'Failed to create account. Please try again.', 'error');
                        }
                    });
                }
            });
        });
        $.ajax({
            method: "GET",
            url: '../api/api_personnel.php?get_assignment_list',
            dataType: 'json'
        }).done(function (data) {
            if (Array.isArray(data) && data.length > 0) {
                $('#a_ass_desc').html('<option value="">--</option>');
                $.each(data, function (index, item) {
                    $('#a_ass_desc').append($('<option>', {
                        value: item.id,
                        text: item.anc_ass_list
                    }));
                });
            } else {
                console.warn('No subject codes found.');
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.error('AJAX request failed:', textStatus, errorThrown);
        });
        $.ajax({
            method: "GET",
            url: '../api/api_personnel.php?get_subject_code_list',
            dataType: 'json'
        }).done(function (data) {
            if (Array.isArray(data) && data.length > 0) {
                $('#a_subject_code, #u_subject_code').html('<option value="">--</option>');
                $.each(data, function (index, item) {
                    $('#a_subject_code, #u_subject_code').append($('<option>', {
                        value: item.subject_code,
                        text: item.subject_code
                    }));
                });
            } else {
                console.warn('No subject codes found.');
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
                $('#a_section, #u_section').html('<option value="">--</option>');
                $.each(data, function (index, item) {
                    $('#a_section, #u_section').append($('<option>', {
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
        $.ajax({
            method: "GET",
            url: '../api/api_personnel.php?get_subject2',
            dataType: 'json'
        }).done(function (data) {
            if (Array.isArray(data) && data.length > 0) {
                $('#a_subjectTaught, #u_subjectTaught').html('<option value="">--</option>');
                $.each(data, function (index, item) {
                    $('#a_subjectTaught, #u_subjectTaught').append($('<option>', {
                        value: item.subject_name,
                        text: item.subject_name
                    }));
                });
            } else {
                console.warn('No subject codes found.');
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.error('AJAX request failed:', textStatus, errorThrown);
        });
        $.ajax({
            method: "GET",
            url: '../api/api_personnel.php?get_ppl_list',
            dataType: 'json'
        }).done(function (data) {
            if (data && !data.error) {
                $.each(data, function (index, item) {
                    $('#pp_pp_desc, #u_pp_pp_desc').append($('<option>', {
                        value: item.ppl_desc,
                        text: item.ppl_desc
                    }));
                });
            } else {
                console.error('Error fetching data:', data.error);
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.error('AJAX request failed:', textStatus, errorThrown);
        });
        $.ajax({
            method: "GET",
            url: '../api/api_select_options.php?get_plapos_list',
            dataType: 'json'
        }).done(function (data) {
            if (Array.isArray(data) && data.length > 0) {
                $('#plaPos').html('<option value="">--</option>');
                $.each(data, function (index, item) {
                    $('#plaPos').append($('<option>', {
                        value: item.ppl_desc,
                        text: item.ppl_desc
                    }));
                });
            } else {
                console.warn('No school year data found.');
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.error('AJAX request failed:', textStatus, errorThrown);
        });
        // For "a_" prefixed inputs
        const fromInputA = document.getElementById("a_stFrom");
        const toInputA = document.getElementById("a_stTo");
        const totalMinInputA = document.getElementById("a_tatMin");
        const dayInputA = document.getElementById("a_stDay");
        function calculateTotalMinutesA() {
            let fromTime = fromInputA.value;
            let toTime = toInputA.value;
            let days = dayInputA.value ? dayInputA.value.split(", ") : [];
            if (fromTime && toTime) {
                let fromParts = fromTime.split(":");
                let toParts = toTime.split(":");
                let fromMinutes = parseInt(fromParts[0]) * 60 + parseInt(fromParts[1]);
                let toMinutes = parseInt(toParts[0]) * 60 + parseInt(toParts[1]);
                let totalMinutes = toMinutes - fromMinutes;
                if (totalMinutes < 0) totalMinutes += 1440; // Adjust if time crosses midnight
                totalMinutes *= days.length; // Multiply based on number of selected days
                totalMinInputA.value = totalMinutes;
            } else {
                totalMinInputA.value = "";
            }
        }
        fromInputA.addEventListener("input", calculateTotalMinutesA);
        toInputA.addEventListener("input", calculateTotalMinutesA);
        dayInputA.addEventListener("change", calculateTotalMinutesA);
        // For "u_" prefixed inputs
        const fromInputU = document.getElementById("u_stFrom");
        const toInputU = document.getElementById("u_stTo");
        const totalMinInputU = document.getElementById("u_tatMin");
        const dayInputU = document.getElementById("u_stDay");
        function calculateTotalMinutesU() {
            let fromTime = fromInputU.value;
            let toTime = toInputU.value;
            let days = dayInputU.value ? dayInputU.value.split(", ") : [];
            if (fromTime && toTime) {
                let fromParts = fromTime.split(":");
                let toParts = toTime.split(":");
                let fromMinutes = parseInt(fromParts[0]) * 60 + parseInt(fromParts[1]);
                let toMinutes = parseInt(toParts[0]) * 60 + parseInt(toParts[1]);
                let totalMinutes = toMinutes - fromMinutes;
                if (totalMinutes < 0) totalMinutes += 1440; // Adjust if time crosses midnight
                totalMinutes *= days.length; // Multiply based on number of selected days
                totalMinInputU.value = totalMinutes;
            } else {
                totalMinInputU.value = "";
            }
        }
        fromInputU.addEventListener("input", calculateTotalMinutesU);
        toInputU.addEventListener("input", calculateTotalMinutesU);
        dayInputU.addEventListener("change", calculateTotalMinutesU);
    });
    //modal sa adding ng anc ass list
    function openAddAncAssListModal() {
        $('#addAncAssListModal').modal('show');
    }
    //modal sa adding ng anc ass
    function openAddAncAssignmentModal(PersonnelId) {
        $('#aa_PersonnelId').val(PersonnelId);
        $('#addAncAssignmentModal').modal('show');
    }
    //modal sa adding ng anc ass
    function openAddPlantillaPosListModal(PersonnelId) {
        $('#addPlantillaPosListModal').modal('show');
    }
    function openAddPlantillaModal(PersonnelId) {
        $('#pp_PersonnelId').val(PersonnelId);
        $('#addPlantillaModal').modal('show');
    }
    //modal sa adding ng subject taught
    function openAddSubjectModal(PersonnelId) {
        $('#a_PersonnelId').val(PersonnelId);
        $('#addSubjectTaughtModal').modal('show');
    }
    //para sa pag show ng personnel content kapag clinick ang search button
    function toggleSection(event) {
        if (event) event.preventDefault();
        let personnelContent = document.getElementById("personnel_content");
        let icon = document.getElementById("toggleIcon");
        if (personnelContent.style.display === "none") {
            personnelContent.style.display = "block";
            icon.classList.remove("fa-chevron-down");
            icon.classList.add("fa-chevron-up");
        }
    }
    //pag na click ang button na chevron 
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
</script>
</body>
</html>