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
                        Grade Records
                    </h1>
                </div>
            </div>
            <div class="row" style="margin: 0;">
                <div class="col-sm-6" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        Browse and manage grade records of students.
                    </small>
                </div>
                <div class="col-sm-6 text-right" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        Records / Grade Records
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
                                        <label for="schoolYear"
                                            style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">School
                                            Year</label>
                                        <select class="form-control" id="schoolYear" name="schoolYear" disabled>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-4">
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
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="studentSection"
                                            style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">Section</label>
                                        <select class="form-control" id="studentSection" name="studentSection">
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
                                    <div class="d-flex">
                                        <?php
                                        if ($_SESSION['access_level'] == 'SA' || $_SESSION['access_level'] == 'SIC') {
                                            ?>
                                            <button type="button" class="btn btn-success mr-2" id="gradeRecCon"
                                                data-toggle="modal" data-target="#gradeRecordControlModal">
                                                <i class="fas fa-cogs">&nbsp; Grade Record Control</i>
                                            </button>
                                            <button type="button" class="btn btn-success mr-2" id="gradeTransmutation"
                                                data-toggle="modal" data-target="#viewTransmutationModal">
                                                <i class="fas fa-eye">&nbsp; Transmutation</i>
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
    <section class="content" id="section_content" style="display: none;">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h1
                            style="text-transform: uppercase; margin-bottom: 0; font-weight: normal; color:rgb(255, 255, 255); font-size: 12px; margin-left: 10px; display: block;">
                            Section List</h1>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="section_table" class="table table-bordered table-striped table-hover"
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
include_once('../partials/modals/modal_grades_setup.php');
include_once('../partials/modals/modal_section_setup.php');
include_once('../partials/footer.php');
?>
<script>
    $(document).ready(function () {
        var sectionTable = $('#section_table').DataTable({
            responsive: true,
            order: [[0, "asc"]],
            processing: true,
            serverSide: false,
            dom: 'Bfrtip',
            buttons: [],
            searching: false,
            pageLength: 50,
            ajax: {
                url: '../api/api_section.php',  // You can leave this as it is
                type: 'GET',  // Change from POST to GET
                data: function (d) {
                    // Send the parameters using GET method
                    d.section_list = true;  // Add this to identify the section list request
                    d.schoolYear = $('#schoolYear').val();
                    d.gradeLevel = $('#gradeLevel').val();
                    d.studentSection = $('#studentSection').val();
                },
                dataSrc: 'data'  // Make sure 'data' matches the key returned in the server response    
            },
            columns: [
                { data: 'SectionName', title: 'Section Name', className: 'align-middle' },
                { data: 'GradeLevel', title: 'Grade Level', className: 'align-middle' },
                { data: 'SchoolYear', title: 'School Year', className: 'align-middle' },
                { data: 'Facility', title: 'Facility', className: 'align-middle' },
                {
                    data: 'StudentCount',
                    title: 'Students',
                    className: 'align-middle',
                    render: function (count, type, row) {
                        return `${count} Enrolled Student(s)`; // Display the student count
                    }
                },
                { data: 'ClassAdviser', title: 'Class Adviser', className: 'align-middle' },
                {
                    data: null,
                    title: 'Action',
                    className: 'text-center',
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                            <button class="btn btn-secondary btn-sm viewSection-btn" 
                            data-id="${row.SectionId}" 
                            data-name="${row.SectionName}"
                            data-grade="${row.GradeLevel}"> 
                            <i class="fas fa-eye"></i>
                        </button>
                        `;
                    }
                }
            ]
        });
        $('#section_table').on('click', '.viewSection-btn', function () {
            var SectionId = $(this).data('id');
            var SectionName = $(this).data('name');
            var GradeLevel = $(this).data('grade');
            var SchoolYear;
            var SectionStrand;
            $.ajax({
                url: '../api/api_section.php?get_section_data',
                type: 'GET',
                data: { SectionId: SectionId },
                dataType: 'json',
                beforeSend: function () {
                    Swal.fire({
                        title: "Loading...",
                        text: "Fetching class data...",
                        icon: "info",
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });
                }
            })
                .done(function (response) {
                    Swal.close();
                    if (response.success && response.data) {
                        // Populate fields with section data
                        $('#display_SectionId_g').val(response.data.SectionId);
                        $('#display_sectionname_g').val(response.data.SectionName);
                        $('#display_gradelevel_g').val(response.data.GradeLevel);
                        $('#display_schoolyear_g').val(response.data.SchoolYear);
                        $('#display_facility_g').val(response.data.Facility);
                        $('#display_strand_g').val(response.data.SectionStrand);
                        SectionStrand = response.data.SectionStrand;
                        SchoolYear = response.data.SchoolYear;
                        // Show the modal for viewing section details
                        $('#viewSectionGradeModal').modal('show');
                        $('#viewSectionModalLabel').html(`Section Details / ${SectionName}`);
                        // Sync grades directly after fetching section data
                        $.ajax({
                            url: '../api/api_grade.php?sync_grade',
                            type: 'GET',
                            data: {
                                SectionName: SectionName,
                                SectionId: SectionId,
                                GradeLevel: GradeLevel,
                                SchoolYear: SchoolYear,
                                SectionStrand: SectionStrand
                            },
                            success: function (syncResponse) {
                                // No reminders or messages, just run the sync
                                $('#section_info_table_grade').DataTable().ajax.reload();
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                console.error('AJAX Error:', textStatus, errorThrown);
                            }
                        });
                    } else {
                    }
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                });
        });
        $('#searchSection').click(function (event) {
            event.preventDefault();
            sectionTable.ajax.reload();
        });
        $('#section_table').on('click', '.viewSection-btn', function () {
            SectionId = $(this).attr('data-id');
            sectionInfoTableGrade.ajax.reload();
            $('#viewSectionGradeModal').modal('show');
        });
        var SectionId;
        var sectionInfoTableGrade = $('#section_info_table_grade').DataTable({
            responsive: true,
            order: [[0, "asc"]],
            processing: true,
            dom: 'Bfrtip',
            pageLength: 50,
            buttons: [],
            ajax: {
                url: '../api/api_grade.php?student_in_section',
                data: function (d) {
                    d.SectionId = SectionId;
                },
                dataSrc: 'data',
                error: function (xhr, error, thrown) {
                    console.error('Error:', error);
                    console.error('Response:', xhr.responseText);
                    alert('Failed to load data');
                }
            },
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: 1 }
            ],
            columns: [
                { data: 'lrn', title: 'LRN', className: 'align-middle' },
                { data: 'name', title: 'Full Name', className: 'align-middle' },
                {
                    data: null,
                    title: 'Action',
                    className: 'text-center',
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                        <button class="btn btn-secondary btn-sm viewGrade-btn" data-lrn="${row.lrn}" data-year="${row.school_year}" data-level="${row.grade_level}" data-name="${row.name}" data-level="${row.grade_level}" data-section="${row.section}">
                            <i class="fas fa-eye"></i>
                        </button>
                    `;
                    }
                }
            ]
        });
        var lrn, name, school_year, grade_level, section; // Define globally
        // Handle click event for viewing grades
        $('#section_info_table_grade').on('click', '.viewGrade-btn', function () {
            lrn = $(this).data('lrn');
            school_year = $(this).data('year');
            grade_level = $(this).data('level'); // Corrected here
            full_name = $(this).data('name');
            section = $(this).data('section');
            $(this).prop('disabled', true); // Disable button to prevent multiple clicks
            if (grade_level >= 7 && grade_level <= 10) {
                // Junior High School (JHS) - Show JHS moda
                $('#gradeInfoModalLabel').text(`Section Details / ${section} / Grades / ${full_name}`);
                $('#gradeInfoModal').modal('show');
            } else {
                // Senior High School (SHS) - Show SHS modal
                $('#gradeShsInfoModalLabel').text(`Section Details / ${section} / Grades / ${full_name}`);
                $('#gradeInfoShsModal').modal('show');
            }
        });
        // Reload DataTable only after the modal is fully shown
        $('#gradeInfoModal').on('shown.bs.modal', function () {
            gradeInfoTable.ajax.reload(null, false);
            $('.viewGrade-btn').prop('disabled', false);
        });
        $('#gradeInfoShsModal').on('shown.bs.modal', function () {
            gradeInfoShsTable.ajax.reload(null, false);
            $('.viewGrade-btn').prop('disabled', false);
        });
        var identifier = '<?php echo htmlspecialchars($_SESSION['Identifier']); ?>';
        // Initialize DataTable for JHS
        var gradeInfoTable = $('#grade_info_table').DataTable({
            responsive: true,
            order: [[0, "asc"]],
            processing: true,
            dom: 'Bfrtip',
            searching: false,
            pageLength: 50,
            buttons: [],
            ajax: {
                <?php
                if ($_SESSION['access_level'] == 'SA' || $_SESSION['access_level'] == 'SIC') {
                    ?>
                                            url: '../api/api_grade.php',
                    type: "GET",
                    data: function (d) {
                        return {
                            student_grade_data: true,
                            lrn: lrn,
                            school_year: school_year,
                            grade_level: grade_level
                        };
                    },
                    <?php
                }
                ?>
<?php
if ($_SESSION['access_level'] == 'TEACHER') {
    ?>
                                                url: '../api/api_grade.php',
                    type: "GET",
                    data: function (d) {
                        return {
                            student_grade_data_teacher: true,
                            lrn: lrn,
                            school_year: school_year,
                            grade_level: grade_level,
                            identifier: identifier
                        };
                    },
                    <?php
}
?>
        dataSrc: function (json) {
                    if (!json.success) {
                        alert(json.message);
                        return [];
                    }
                    return json.data;
                },
                error: function (xhr, error, thrown) {
                    console.error('Error:', error);
                    console.error('Response:', xhr.responseText);
                    alert('Failed to load grade data. Please try again later.');
                }
            },
            columns: [
                { data: 'subject_name', title: 'Subject', className: 'align-middle' },
                { data: 'fstq_grade_tr', title: '1st Quarter', className: 'align-middle' },
                { data: 'scndq_grade_tr', title: '2nd Quarter', className: 'align-middle' },
                { data: 'trdq_grade_tr', title: '3rd Quarter', className: 'align-middle' },
                { data: 'fthq_grade_tr', title: '4th Quarter', className: 'align-middle' },
                {
                    data: null,
                    title: 'Action',
                    className: 'text-center',
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                    <button class="btn btn-secondary btn-sm studentGradeData-btn" data-id="${row.sgj_id}" data-name="${row.subject_name}"> 
                        <i class="fas fa-edit"></i>
                    </button>
                `;
                    }
                }
            ]
        });
        var identifier = '<?php echo htmlspecialchars($_SESSION['Identifier']); ?>';
        // Initialize DataTable for SHS
        var gradeInfoShsTable = $('#grade_info_shs_table').DataTable({
            responsive: true,
            order: [[0, "asc"]],
            processing: true,
            dom: 'Bfrtip',
            pageLength: 50,
            searching: false,
            buttons: [],
            ajax: {
                <?php
                if ($_SESSION['access_level'] == 'SA' || $_SESSION['access_level'] == 'SIC') {
                    ?>
                                            url: '../api/api_grade.php',
                    type: "GET",
                    data: function (d) {
                        return {
                            student_grade_data_shs: true,
                            lrn: lrn,
                            school_year: school_year,
                            semester: $('#semester').val() // Get selected semester
                        };
                    },
                    <?php
                }
                ?>
<?php
if ($_SESSION['access_level'] == 'TEACHER') {
    ?>
                                            url: '../api/api_grade.php',
                    type: "GET",
                    data: function (d) {
                        return {
                            student_grade_data_shs_teacher: true,
                            lrn: lrn,
                            school_year: school_year,
                            semester: $('#semester').val(), // Get selected semester
                            identifier: identifier
                        };
                    },
                    <?php
}
?>
        dataSrc: function (json) {
                    if (!json.success) {
                        alert(json.message);
                        return [];
                    }
                    return json.data;
                },
                error: function (xhr, error, thrown) {
                    console.error('Error:', error);
                    console.error('Response:', xhr.responseText);
                }
            },
            columns: [
                { data: 'subject_name', title: 'Subject', className: 'align-middle' },
                { data: 'fsts_grade_tr', title: '1st Quarter', className: 'align-middle' },
                { data: 'scnds_grade_tr', title: '2nd Quarter', className: 'align-middle' },
                {
                    data: null,
                    title: 'Action',
                    className: 'text-center',
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                    <button class="btn btn-secondary btn-sm studentGradeDataShs-btn" data-id="${row.sg_id}" data-name="${row.subject_name}"> 
                        <i class="fas fa-edit"></i>
                    </button>
                `;
                    }
                }
            ]
        });
        // Event Listener: Update table when semester changes
        $('#semester').on('change', function () {
            gradeInfoShsTable.ajax.reload(); // Reload DataTable with new semester filter
        });
        $('#grade_info_table').on('click', '.studentGradeData-btn', function () {
            var gradeId = $(this).data('id'); // Get the grade ID from the button's data attribute
            var subject_name = $(this).data('name');
            $('#editStudentGradeModalLabel').text(`Section Details / ${section} / Grades / ${subject_name} / ${full_name}`);
            $.ajax({
                url: '../api/api_grade.php?get_grade_details', // Adjust the URL as needed
                method: 'GET',
                data: { id: gradeId },
                success: function (response) {
                    if (response.success) {
                        // Process the response and populate the modal fields
                        console.log("Grade Details:", response.data);
                        $('#u_sg_lrn').val(response.data.lrn);
                        $('#u_sg_id').val(response.data.sgj_id); // Populate subject name
                        $('#u_sg_fstq_grade').val(response.data.fstq_grade); // Populate 1st Quarter grade
                        $('#u_sg_scndq_grade').val(response.data.scndq_grade); // Populate 2nd Quarter grade
                        $('#u_sg_trdq_grade').val(response.data.trdq_grade); // Populate 3rd Quarter grade
                        $('#u_sg_fthq_grade').val(response.data.fthq_grade); // Populate 4th Quarter grade
                        $('#u_sg_fstq_grade_tr').val(response.data.fstq_grade_tr); // Populate 1st Quarter grade
                        $('#u_sg_scndq_grade_tr').val(response.data.scndq_grade_tr); // Populate 2nd Quarter grade
                        $('#u_sg_trdq_grade_tr').val(response.data.trdq_grade_tr); // Populate 3rd Quarter grade
                        $('#u_sg_fthq_grade_tr').val(response.data.fthq_grade_tr); // Populate 4th Quarter grade
                        // Show the edit modal
                        $('#editStudentGradeModal').modal('show');
                    } else {
                        alert(response.message);
                    }
                },
                error: function (xhr, error, thrown) {
                    console.error('Error fetching grade details:', error);
                    alert('Failed to load grade details. Please try again later.');
                }
            });
        });
        $('#updateNestedBtn').on('click', function () {
            var gradeId = $('#u_sg_id').val(); // Get the gradeId from the modal
            var lrn = $('#u_sg_lrn').val();
            $.ajax({
                url: '../api/api_grade.php?update_nested_grade',
                method: 'GET',
                data: {
                    gradeId: gradeId,
                    lrn: lrn
                },
                success: function (response) {
                    console.log(response); // Debugging the response from PHP
                    if (response.success) {
                        console.log(response.averages); // Ensure averages are being received correctly
                        // Now re-fetch the grade details to refresh the modal data
                        $.ajax({
                            url: '../api/api_grade.php?get_grade_details',
                            method: 'GET',
                            data: { id: gradeId },
                            success: function (response) {
                                if (response.success) {
                                    console.log("Grade Details (Refreshed):", response.data);
                                    $('#u_sg_lrn').val(response.data.lrn);
                                    $('#u_sg_id').val(response.data.sgj_id);
                                    $('#u_sg_fstq_grade').val(response.data.fstq_grade);
                                    $('#u_sg_scndq_grade').val(response.data.scndq_grade);
                                    $('#u_sg_trdq_grade').val(response.data.trdq_grade);
                                    $('#u_sg_fthq_grade').val(response.data.fthq_grade);
                                    $('#u_sg_fstq_grade_tr').val(response.data.fstq_grade_tr);
                                    $('#u_sg_scndq_grade_tr').val(response.data.scndq_grade_tr);
                                    $('#u_sg_trdq_grade_tr').val(response.data.trdq_grade_tr);
                                    $('#u_sg_fthq_grade_tr').val(response.data.fthq_grade_tr);
                                } else {
                                    alert(response.message);
                                }
                            },
                            error: function (xhr, error, thrown) {
                                console.error('Error re-fetching grade details:', error);
                                alert('Failed to refresh grade details.');
                            }
                        });
                    } else {
                        console.error('Failed to update grade averages:', response.message);
                    }
                },
                error: function (xhr, error, thrown) {
                    console.error('Error fetching grade averages:', error);
                }
            });
        });
        // Event listener for the studentGradeData-btn
        $('#grade_info_shs_table').on('click', '.studentGradeDataShs-btn', function () {
            var gradeId = $(this).data('id'); // Get the grade ID from the button's data attribute
            var subject_name = $(this).data('name');
            console.log("Grade ID:", gradeId); // Debugging
            $('#editStudentGradeShsModalLabel').text(`Section Details / ${section} / Grades / ${subject_name} / ${full_name}`);
            // Fetch more details about the grade
            $.ajax({
                url: '../api/api_grade.php?get_grade_details_shs', // Adjust the URL as needed
                method: 'GET',
                data: { id: gradeId },
                success: function (response) {
                    if (response.success) {
                        // Process the response and populate the modal fields
                        console.log("Grade Details:", response.data);
                        $('#s_sg_id').val(response.data.sg_id); // Populate subject name
                        $('#s_sg_fsts_grade').val(response.data.fsts_grade); // Populate 1st Quarter grade
                        $('#s_sg_scnds_grade').val(response.data.scnds_grade); // Populate 2nd Quarter grade
                        $('#s_sg_fsts_grade_tr').val(response.data.fsts_grade_tr); // Populate 1st Quarter grade
                        $('#s_sg_scnds_grade_tr').val(response.data.scnds_grade_tr); // Populate 2nd Quarter grade
                        // Show the edit modal
                        $('#editStudentGradeShsModal').modal('show');
                    } else {
                        alert(response.message);
                    }
                },
                error: function (xhr, error, thrown) {
                    console.error('Error fetching grade details:', error);
                    alert('Failed to load grade details. Please try again later.');
                }
            });
        });
        // Handle form submission
        $('#editStudentGradeForm').on('submit', function (event) {
            event.preventDefault(); // Prevent the default form submission
            // Gather form data
            var formData = $(this).serialize(); // Serialize the form data
            // Send the updated grade data to the server
            $.ajax({
                url: '../api/api_grade.php', // Adjust the URL as needed
                method: 'POST',
                data: formData,
                success: function (response) {
                    if (response.success) {
                        $('#editStudentGradeModal').modal('hide'); // Hide the modal
                        $('#grade_info_table').DataTable().ajax.reload();
                    } else {
                        console.error('Error updating grades:', response.message);
                    }
                },
                error: function (xhr, error, thrown) {
                    console.error('Error updating grades:', error);
                }
            });
        });
        // Handle form submission for editing SHS student grades
        $('#editStudentGradeShsForm').on('submit', function (event) {
            event.preventDefault(); // Prevent the default form submission
            // Gather form data
            var formData = $(this).serialize(); // Serialize the form data
            // Send the updated grade data to the server
            $.ajax({
                url: '../api/api_grade.php?update_shs', // Adjust the URL as needed
                method: 'POST',
                data: formData,
                success: function (response) {
                    if (response.success) {
                        $('#editStudentGradeShsModal').modal('hide'); // Hide the modal
                        $('#grade_info_shs_table').DataTable().ajax.reload();
                    } else {
                        console.error('Error updating SHS grades:', response.message);
                    }
                },
                error: function (xhr, error, thrown) {
                    console.error('Error updating SHS grades:', error);
                }
            });
        });
        // When the button is clicked to open the modal
        $('#gradeRecCon').on('click', function () {
            $.ajax({
                url: '../api/api_grade.php',
                type: 'GET',
                data: { get_grade_control: true }, // Send parameter to fetch grade control status
                success: function (response) {
                    if (response.success) {
                        // Junior High School - Update checkboxes based on response data
                        $('#firstGrading').prop('checked', response.data['11'] === 1);  // Assuming 11 is Q1
                        $('#secondGrading').prop('checked', response.data['12'] === 1); // Assuming 12 is Q2
                        $('#thirdGrading').prop('checked', response.data['13'] === 1);  // Assuming 13 is Q3
                        $('#fourthGrading').prop('checked', response.data['14'] === 1); // Assuming 14 is Q4
                        // Senior High School - Update checkboxes based on response data
                        $('#firstSemester').prop('checked', response.data['21'] === 1);  // Assuming 21 is S1
                        $('#secondSemester').prop('checked', response.data['22'] === 1); // Assuming 22 is S2
                    } else {
                        console.log('Error fetching grade control status: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching grade control status: ' + error);
                }
            });
        });
        // Function to handle checkbox changes
        $('input[type="checkbox"]').change(function () {
            // Map the checkbox id to its corresponding c_desc values
            let cb_values = {
                'firstGrading': 11,
                'secondGrading': 12,
                'thirdGrading': 13,
                'fourthGrading': 14,
                'firstSemester': 21,
                'secondSemester': 22
            };
            let cb_id = $(this).attr('id'); // Get the id of the checkbox
            let c_desc = cb_values[cb_id];  // Get the corresponding c_desc value
            let c_status = $(this).prop('checked') ? 1 : 0; // 1 if checked, 0 if unchecked
            // Send the data to the server using AJAX
            $.ajax({
                url: '../api/api_grade.php?update_grade_control', // API endpoint to update the grade control status
                type: 'POST',
                data: {
                    c_desc: c_desc,
                    c_status: c_status
                },
                success: function (response) {
                    if (response.success) {
                        console.log('Status updated successfully for ' + c_desc);
                        $('#grade_info_shs_table').DataTable().ajax.reload();
                        $('#grade_info_jhs_table').DataTable().ajax.reload();
                        refreshControl();
                    } else {
                        console.log('Error updating status: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error updating status: ' + error);
                }
            });
        });
        refreshControl();
        function refreshControl() {
            $.ajax({
                url: '../api/api_grade.php', // Your API endpoint
                type: 'GET',
                data: { get_grade_control_status: true }, // Assuming you handle the GET request for grade control status
                success: function (response) {
                    if (response.success) {
                        // Check the status for each of the fields and set readonly and placeholder accordingly
                        if (response.data['11'] === 0) {
                            $('#u_sg_fstq_grade').prop('readonly', true).attr('placeholder', 'Not Available');
                        } else {
                            $('#u_sg_fstq_grade').prop('readonly', false).attr('placeholder', '');
                        }
                        if (response.data['12'] === 0) {
                            $('#u_sg_scndq_grade').prop('readonly', true).attr('placeholder', 'Not Available');
                        } else {
                            $('#u_sg_scndq_grade').prop('readonly', false).attr('placeholder', '');
                        }
                        if (response.data['13'] === 0) {
                            $('#u_sg_trdq_grade').prop('readonly', true).attr('placeholder', 'Not Available');
                        } else {
                            $('#u_sg_trdq_grade').prop('readonly', false).attr('placeholder', '');
                        }
                        if (response.data['14'] === 0) {
                            $('#u_sg_fthq_grade').prop('readonly', true).attr('placeholder', 'Not Available');
                        } else {
                            $('#u_sg_fthq_grade').prop('readonly', false).attr('placeholder', '');
                        }
                        if (response.data['21'] === 0) {
                            $('#s_sg_fsts_grade').prop('readonly', true).attr('placeholder', 'Not Available');
                        } else {
                            $('#s_sg_fsts_grade').prop('readonly', false).attr('placeholder', '');
                        }
                        if (response.data['22'] === 0) {
                            $('#s_sg_scnds_grade').prop('readonly', true).attr('placeholder', 'Not Available');
                        } else {
                            $('#s_sg_scnds_grade').prop('readonly', false).attr('placeholder', '');
                        }
                    } else {
                        console.error('Error fetching grade control status');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching grade control status: ' + error);
                }
            });
        }
        var transmutationTable = $('#transmutation_info_table').DataTable({
            responsive: true,
            order: [[0, "asc"]],
            pageLength: 50,
            processing: true,
            dom: 'Bfrtip',
            buttons: [],
            searching: false,
            ajax: {
                url: '../api/api_grade.php',
                type: 'GET',
                data: function (d) {
                    d.get_transmutation_data = true;
                },
                dataSrc: 'data'
            },
            columns: [
                { data: 'min_grade', title: 'Min Grade', className: 'align-middle' },
                { data: 'transmuted_grade', title: 'Transmuted Grade', className: 'align-middle' },
                {
                    data: null,
                    title: 'Action',
                    className: 'text-center',
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                    <button class="btn btn-secondary btn-sm viewTransmutation-btn" data-id="${row.transmutation_id}"
                    data-mingrade="${row.min_grade}" 
                    data-transmutedgrade="${row.transmuted_grade}">
                        <i class="fas fa-eye"></i>
                    </button>
                `;
                    }
                }
            ]
        });
        $(document).on('click', '.viewTransmutation-btn', function () {
            // Get data attributes from the clicked button
            var minGrade = $(this).data('mingrade'); // Fix
            var transmutedGrade = $(this).data('transmutedgrade'); // Fix
            var transmutationId = $(this).data('id'); // Fix
            // Populate the modal form fields
            $('#min_grade').val(minGrade);
            $('#trans_grade').val(transmutedGrade);
            $('#trans_id').val(transmutationId);
            // Show the modal
            $('#updateTransmutationModal').modal('show');
        });
        $(document).on('click', '#updateTransmutationBtn', function () {
            // Get form values
            var minGrade = $('#min_grade').val();
            var transmutedGrade = $('#trans_grade').val();
            var transmutationId = $('#trans_id').val();
            // Validate input
            if (minGrade === "" || transmutedGrade === "") {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Fields',
                    text: 'Please fill in all fields.'
                });
                return;
            }
            if (minGrade > 100 || transmutedGrade > 100) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Grade',
                    text: 'Grades must not be greater than 100.'
                });
                return;
            }
            $.ajax({
                url: '../api/api_grade.php',
                type: 'POST',
                data: {
                    update_transmutation: true,
                    transmutationId: transmutationId,
                    minGrade: minGrade,
                    transmutedGrade: transmutedGrade
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('#updateTransmutationModal').modal('hide');
                        $('#transmutation_info_table').DataTable().ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Update Failed',
                            text: response.message
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Request Error',
                        text: 'An error occurred while updating the transmutation data.'
                    });
                }
            });
        });
        <?php
        if ($_SESSION['access_level'] == 'SA' || $_SESSION['access_level'] == 'SIC') {
            ?>
            //TERM
            $.ajax({
                method: "GET",
                url: '../api/api_select_options.php?get_school_year_list',
                dataType: 'json'
            }).done(function (data) {
                if (Array.isArray(data) && data.length > 0) {
                    // Clear the dropdowns without adding the default "--" option
                    $('#schoolYear, #e_schoolyear, #a_curriculumterm').empty();
                    $.each(data, function (index, item) {
                        $('#schoolYear, #e_schoolyear, #a_curriculumterm').append($('<option>', {
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
            <?php
        }
        ?>
        <?php
        if ($_SESSION['access_level'] == 'TEACHER') {
            ?>
            $.ajax({
                method: "GET",
                url: '../api/api_select_options.php?get_section_list_teach', // corrected param
                dataType: 'json'
            }).done(function (data) {
                if (Array.isArray(data) && data.length > 0) {
                    $('#schoolYear, #e_schoolyear, #a_curriculumterm').empty();
                    $.each(data, function (index, item) {
                        $('#schoolYear, #e_schoolyear, #a_curriculumterm').append($('<option>', {
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
            <?php
        }
        ?>
        <?php
        if ($_SESSION['access_level'] == 'SA' || $_SESSION['access_level'] == 'SIC') {
            ?>
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
            <?php
        }
        ?>
        <?php
        if ($_SESSION['access_level'] == 'TEACHER') {
            ?>
            var identifier = '<?php echo htmlspecialchars($_SESSION['Identifier']); ?>';
            $.ajax({
                method: "GET",
                url: '../api/api_select_options.php?get_section_list_subt=1&identifier=' + encodeURIComponent(identifier),
                dataType: 'json'
            }).done(function (data) {
                if (Array.isArray(data) && data.length > 0) {
                    $('#studentSection, #as_section, #es_section');
                    $.each(data, function (index, item) {
                        $('#studentSection, #as_section, #es_section').append($('<option>', {
                            value: item.SectionName,
                            text: item.SectionName
                        }));
                    });
                } else {
                    console.warn('No section data found.');
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX request failed:', textStatus, errorThrown);
            });
            <?php
        }
        ?>
    });
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
</script>
</body>

</html>