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
                        Subjects
                    </h1>
                </div>
            </div>
            <div class="row" style="margin: 0;">
                <div class="col-sm-6" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        Browse and manage subject records of students.
                    </small>
                </div>
                <div class="col-sm-6 text-right" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        Academics / Subjects
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
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="subjectType"
                                            style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">Subject
                                            Type</label>
                                        <select class="form-control" id="subjectType" name="subjectType">
                                            <option value="">--</option>
                                            <option value="Specialized">Specialized</option>
                                            <option value="Core">Core</option>
                                            <option value="Applied">Applied</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-3" id="semesterDiv">
                                    <div class="form-group">
                                        <label for="semester"
                                            style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">Semester</label>
                                        <select class="form-control" id="semester" name="semester" disabled>
                                            <option value="">--</option>
                                            <option value="1">First Semester</option>
                                            <option value="2">Second Semester</option>
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
                                            data-toggle="modal" data-target="#addSubjectModal">
                                            <i class="fas fa-plus"></i> Add Subject
                                        </button>
                                        <button type="button" class="btn btn-info mr-2" id="btnArchive">
                                            <i class="fas fa-archive"></i> Archive
                                        </button>
                                        <button type="button" class="btn btn-info mr-2" id="btnReturn"
                                            style="display: none;">
                                            <i class="fas fa-reply"></i> Back
                                        </button>
                                        <button type="button" class="btn btn-info mr-2" id="copySubject"
                                            data-toggle="modal" data-target="#copySubjectModal">
                                            <i class="fas fa-copy"></i> Copy Subject
                                        </button>
                                        <button id="toggleButton" type="button" class="btn btn-primary mr-2"
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
                            style="text-transform: uppercase;  margin-bottom: 0;font-weight: normal; color:rgb(255, 255, 255); font-size: 12px; margin-left: 10px; display: block;">
                            Subject List</h1>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="subjects_table" class="table table-bordered table-striped table-hover"
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
                            Subject Archive List</h1>
                    </div>
                    <div class="card-body">
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
include_once('../partials/modals/modal_curriculum_setup.php');
include_once('../partials/footer.php');
?>
<script>
    $(document).ready(function () {
        var subjectsTable = $('#subjects_table').DataTable({
            responsive: true,
            order: [[0, "asc"]],
            processing: true,
            dom: 'Bfrtip',
            pageLength: 50,
            buttons: [],
            ajax: {
                url: '../api/api_curriculum.php',
                data: function (d) {
                    d.subjects_list = true; // Corrected to match PHP
                    d.schoolYear = $('#schoolYear').val();
                    d.gradeLevel = $('#gradeLevel').val();
                    d.semester = $('#semester').val();
                    d.subjectType = $('#subjectType').val();
                },
                dataSrc: ''
            },
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: 1 },
                { responsivePriority: 3, targets: 2 },
                { responsivePriority: 4, targets: 3 },
                { responsivePriority: 5, targets: 4 }
            ],
            columns: [
                { data: 'subject_code', title: 'Subject Code', className: 'align-middle' },
                { data: 'subject_name', title: 'Subject Name', className: 'align-middle' },
                { data: 'grade_level', title: 'Grade Level', className: 'align-middle' },
                { data: 'subjectType', title: 'Subject Type', className: 'align-middle' },
                {
                    data: null,
                    title: 'Action',
                    className: 'align-middle',
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                        <button class="btn btn-info btn-sm updateSubject-btn" data-id="${row.subject_id}" data-name="${row.subject_code}">
                            <i class="fas fa-edit"></i> 
                        </button>
                        <button class="btn btn-warning btn-sm archiveSubject-btn" data-id="${row.subject_id}">
                            <i class="fas fa-archive"></i> 
                        </button>
                    `;
                    }
                }
            ]
        });
        var archiveSubjectsTable = $('#archive_subjects_table').DataTable({
            responsive: true,
            order: [[0, "asc"]],
            processing: true,
            dom: 'Bfrtip',
            buttons: [],
            ajax: {
                url: '../api/api_curriculum.php',
                data: function (d) {
                    d.archive_subjects_list = true; // Corrected to match PHP
                    d.schoolYear = $('#schoolYear').val();
                    d.gradeLevel = $('#gradeLevel').val();
                    d.semester = $('#semester').val();
                    d.subjectType = $('#subjectType').val();
                },
                dataSrc: ''
            },
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: 1 },
                { responsivePriority: 3, targets: 2 },
                { responsivePriority: 4, targets: 3 },
                { responsivePriority: 5, targets: 4 }
            ],
            columns: [
                { data: 'subject_code', title: 'Subject Code', className: 'align-middle' },
                { data: 'subject_name', title: 'Subject Name', className: 'align-middle' },
                { data: 'grade_level', title: 'Grade Level', className: 'align-middle' },
                { data: 'subjectType', title: 'Subject Type', className: 'align-middle' },
                {
                    data: null,
                    title: 'Action',
                    className: 'align-middle',
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                    <button class="btn btn-info btn-sm unarchiveSubject-btn" data-id="${row.subject_id}">
                            <i class="fas fa-undo"></i> <!-- Unarchive icon -->
                        </button>
                        `;
                    }
                }
            ]
        });
        $('#addSubjectForm').on('submit', function (e) {
            e.preventDefault(); // Prevent the default form submission
            $.ajax({
                url: '../api/api_curriculum.php?add_subject',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json'
            }).done(function (response) {
                if (response.success) {
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success"
                    }).then(() => {
                        $('#addSubjectForm')[0].reset(); // Reset the form fields
                        $('#addSubjectModal').modal('hide'); // Hide the modal
                        $('#subjects_table').DataTable().ajax.reload(); // Refresh the DataTable
                    });
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: response.message,
                        icon: "error"
                    });
                }
            }).fail(function (xhr, status, error) {
                console.error('AJAX Error:', error);
                Swal.fire({
                    title: "Error!",
                    text: "An unexpected error occurred.",
                    icon: "error"
                });
            });
        });
        $('#subjects_table').on('click', '.updateSubject-btn', function () {
            var subject_id = $(this).data('id');
            var subject_code = $(this).data('name');
            $.ajax({
                url: '../api/api_curriculum.php?get_subject_data',
                type: 'GET',
                data: { subject_id: subject_id },
                dataType: 'json'
            }).then(function (response) {
                if (response.success) {
                    $('#u_subject_id').val(response.data.subject_id);
                    $('#u_curriculum').val(response.data.curriculum_id);
                    $('#u_subject_code').val(response.data.subject_code);
                    $('#u_subject_name').val(response.data.subject_name);
                    $('#u_subject_desc').val(response.data.subject_desc);
                    $('#u_subject_term').val(response.data.subject_term);
                    $('#u_grade_level').val(response.data.grade_level);
                    $('#u_strand').val(response.data.strand);
                    $('#u_weekly_hours').val(response.data.weekly_hours);
                    $('#u_subject_type').val(response.data.subjectType);
                    $('#u_subject_quarter').val(response.data.subject_semester);
                    $('#u_subject_order').val(response.data.subject_order);
                    // Check if nested_id has a value, then check the checkbox
                    if (response.data.nested_id) {
                        $('#u_checkbox').prop('checked', true);
                    } else {
                        $('#u_checkbox').prop('checked', false);
                    }
                    $('#updateSubjectModalLabel').html(`Update Subject / ${subject_code}`);
                    $('#updateSubjectModal').modal('show');
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
        $(document).on('submit', '#updateSubjectForm', function (e) {
            e.preventDefault();
            // Check if the checkbox is checked
            var isChecked = $('#u_checkbox').is(':checked') ? 1 : 0;
            // Create FormData object and append checkbox state
            var formData = new FormData(this);
            formData.append('u_checkbox', isChecked);
            console.log('Form data:', Object.fromEntries(formData)); // Debugging
            $.ajax({
                url: '../api/api_curriculum.php?update_subject',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json'
            }).then(function (response) {
                console.log('Response:', response); // Debugging
                if (response.success) {
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success",
                    }).then(() => {
                        $('#updateSubjectModal').modal('hide');
                        $('#subjects_table').DataTable().ajax.reload();
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
        $('#searchSection, #searchArchive').click(function (event) {
            event.preventDefault();
            subjectsTable.ajax.reload();
            archiveSubjectsTable.ajax.reload();
        });
        $('#subjects_table').on('click', '.archiveSubject-btn', function () {
            const subjectId = $(this).data('id'); // Get the subject_id from the button
            Swal.fire({
                title: 'Are you sure?',
                text: "This subject will go to subject archives!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, archive it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with archiving
                    $.ajax({
                        url: '../api/api_curriculum.php?archive_subject',
                        type: 'POST',
                        data: { subject_id: subjectId },
                        success: function (response) {
                            try {
                                const result = JSON.parse(response); // Parse the JSON response
                                if (result.success) {
                                    Swal.fire('Archived!', result.message, 'success');
                                    $('#archive_subjects_table').DataTable().ajax.reload();
                                    $('#subjects_table').DataTable().ajax.reload();
                                } else {
                                    Swal.fire('Error!', result.message, 'error');
                                }
                            } catch (e) {
                                // In case the response is not in JSON format
                                Swal.fire('Archived!', 'Subject moved successfully.', 'success');
                                $('#archive_subjects_table').DataTable().ajax.reload();
                                $('#subjects_table').DataTable().ajax.reload();
                            }
                        },
                        error: function (xhr, status, error) {
                            Swal.fire('Failed!', 'An error occurred while processing your request.', 'error');
                        }
                    });
                }
            });
        });
        $('#archive_subjects_table').on('click', '.unarchiveSubject-btn', function () {
            const subjectId = $(this).data('id'); // Get the subject_id from the button
            Swal.fire({
                title: 'Are you sure?',
                text: "This subject will be unarchived!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, restore it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with archiving
                    $.ajax({
                        url: '../api/api_curriculum.php?unarchive_subject',
                        type: 'POST',
                        data: { subject_id: subjectId },
                        success: function (response) {
                            try {
                                const result = JSON.parse(response); // Parse the JSON response
                                if (result.success) {
                                    Swal.fire('Restored!', result.message, 'success');
                                    $('#archive_subjects_table').DataTable().ajax.reload();
                                    $('#subjects_table').DataTable().ajax.reload();
                                } else {
                                    Swal.fire('Error!', result.message, 'error');
                                }
                            } catch (e) {
                                // In case the response is not in JSON format
                                Swal.fire('Unarchived!', 'Subject moved successfully.', 'success');
                                $('#archive_subjects_table').DataTable().ajax.reload();
                                $('#subjects_table').DataTable().ajax.reload();
                            }
                        },
                        error: function (xhr, status, error) {
                            Swal.fire('Failed!', 'An error occurred while processing your request.', 'error');
                        }
                    });
                }
            });
        });
        $('#copySectionForm').on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission
            // Collect only checked subject codes
            const selectedSubjects = [];
            $('input[name="selected_subjects[]"]:checked').each(function () {
                selectedSubjects.push($(this).val());
            });
            if (selectedSubjects.length === 0) {
                Swal.fire("Warning", "Please select at least one subject.", "warning");
                return;
            }
            // Serialize other form fields
            const formData = $(this).serializeArray();
            formData.push({ name: "selected_subjects_json", value: JSON.stringify(selectedSubjects) });
            $.ajax({
                url: '../api/api_curriculum.php?copy_subjects',
                type: 'POST',
                data: $.param(formData),
                dataType: 'json'
            }).done(function (response) {
                if (response.success) {
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success"
                    }).then(() => {
                        $('#copySubjectForm')[0].reset();
                        $('#copySubjectModal').modal('hide');
                        $('#subjects_table').DataTable().ajax.reload();
                    });
                } else {
                    Swal.fire("Error!", response.message, "error");
                }
            }).fail(function (xhr, status, error) {
                console.error('AJAX Error:', error);
                Swal.fire("Error!", "An unexpected error occurred.", "error");
            });
        });
        $('#copy_curriculum, #copy_sy_from').on('change', function () {
            const curriculumId = $('#copy_curriculum').val();
            const syFrom = $('#copy_sy_from').val();
            if (curriculumId && syFrom) {
                $.ajax({
                    url: '../api/api_curriculum.php',
                    type: 'POST',
                    data: {
                        fetch_subjects: true,
                        curriculum_id: curriculumId,
                        sy_from: syFrom
                    },
                    dataType: 'json',
                    success: function (response) {
                        const tbody = $('#copy_subject_table tbody');
                        tbody.empty();
                        if (response.length === 0) {
                            tbody.append('<tr><td colspan="3" class="text-center">No subjects found.</td></tr>');
                        } else {
                            response.forEach(subject => {
                                const row = `
              <tr>
                <td><input type="checkbox" name="selected_subjects[]" value="${subject.subject_code}"></td>
                <td>${subject.subject_code}</td>
                <td>${subject.subject_name}</td>
              </tr>
            `;
                                tbody.append(row);
                            });
                        }
                    },
                    error: function () {
                        Swal.fire("Error", "Failed to load subjects.", "error");
                    }
                });
            }
        });
        $('#selectAllSubjects').on('change', function () {
            $('input[name="selected_subjects[]"]').prop('checked', this.checked);
        });
        //FOR SELECT OPTIONS
        //CURRIULUM NAME
        $.ajax({
            method: "GET",
            url: '../api/api_select_options.php?get_curriculum_name',
            dataType: 'json'
        }).done(function (data) {
            if (Array.isArray(data) && data.length > 0) {
                $('#a_curriculum, #u_curriculum, #copy_curriculum').html('<option value="">--</option>');
                $.each(data, function (index, item) {
                    $('#a_curriculum, #u_curriculum, #copy_curriculum').append($('<option>', {
                        value: item.curriculum_id,
                        text: item.curriculum_desc
                    }));
                });
            } else {
                console.warn('No curriculum data found.');
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.error('AJAX request failed:', textStatus, errorThrown);
        });
        //TERM
        $.ajax({
            method: "GET",
            url: '../api/api_select_options.php?get_school_year_list',
            dataType: 'json'
        }).done(function (data) {
            if (Array.isArray(data) && data.length > 0) {
                // Clear the dropdowns without adding the default "--" optio
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
        //TERM
        $.ajax({
            method: "GET",
            url: '../api/api_select_options.php?get_school_year_list1',
            dataType: 'json'
        }).done(function (data) {
            if (Array.isArray(data) && data.length > 0) {
                // Clear the dropdowns without adding the default "--" optio
                $('#e_schoolyear, #a_curriculumterm, #copy_sy_from, #copy_sy_to, #u_subject_term').empty();
                $.each(data, function (index, item) {
                    $('#e_schoolyear, #a_curriculumterm, #copy_sy_from, #copy_sy_to, #u_subject_term').append($('<option>', {
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
    });
    //FILTER OPTIONS
    function toggleTermOptions() {
        let gradeLevel = document.getElementById("gradeLevel").value;
        let semesterSelect = document.getElementById("semester");
        if (parseInt(gradeLevel) <= 10) {
            semesterSelect.disabled = true; // Disable the select input
        } else {
            semesterSelect.disabled = false; // Enable the select input
        }
    }
    function toggleSubGrade() {
        let gradeLevel = document.getElementById("a_grade_level").value;
        let subSemAdd = document.getElementById("a_subject_quarter");
        let subStraAdd = document.getElementById("a_strand");
        if (parseInt(gradeLevel) <= 10) {
            subSemAdd.disabled = true; // Disable the select input
            subStraAdd.disabled = true; // Disable the select input
        } else {
            subSemAdd.disabled = false; // Enable the select input
            subStraAdd.disabled = false; // Disable the select input
        }
    }
    function toggleSubGradeEdit() {
        let gradeLevel = document.getElementById("u_grade_level").value;
        let subSemEdit = document.getElementById("u_subject_quarter");
        let strandEdit = document.getElementById("u_strand");
        if (parseInt(gradeLevel) <= 10) {
            subSemEdit.disabled = true; // Disable the select input
            strandEdit.disabled = true;
        } else {
            subSemEdit.disabled = false; // Enable the select input
            strandEdit.disabled = false;
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
    });
</script>
</body>

</html>