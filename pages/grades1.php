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
        // SECTION TABLE INITIALIZATION
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
                url: '../api/api_section.php',
                type: 'GET',
                data: function (d) {
                    d.section_list = true;
                    d.schoolYear = $('#schoolYear').val();
                    d.gradeLevel = $('#gradeLevel').val();
                    d.studentSection = $('#studentSection').val();
                },
                dataSrc: 'data'
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
                    render: function (count) {
                        return `${count} Enrolled Student(s)`;
                    }
                },
                { data: 'ClassAdviser', title: 'Class Adviser', className: 'align-middle' },
                {
                    data: null,
                    title: 'Action',
                    className: 'text-center',
                    orderable: false,
                    render: function (row) {
                        return `
                        <button class="btn btn-secondary btn-sm viewSection-btn" 
                            data-id="${row.SectionId}" 
                            data-name="${row.SectionName}"
                            data-grade="${row.GradeLevel}">
                            <i class="fas fa-eye"></i>
                        </button>`;
                    }
                }
            ]
        });
        $('#searchSection').click(function (e) {
            e.preventDefault();
            sectionTable.ajax.reload();
        });
        let SectionId;
        let sectionInfoTableGrade;
        let firstSetIndexes = [];
        let secondSetIndexes = [];
        $('#section_table').on('click', '.viewSection-btn', function () {
            SectionId = $(this).data('id');
            const SectionName = $(this).data('name');
            const GradeLevel = $(this).data('grade');
            $.ajax({
                url: '../api/api_section.php?get_section_data',
                type: 'GET',
                data: { SectionId },
                dataType: 'json',
                beforeSend: function () {
                    Swal.fire({
                        title: "Loading...",
                        text: "Fetching class data...",
                        icon: "info",
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });
                },
                success: function (response) {
                    Swal.close();
                    if (response.success && response.data) {
                        const data = response.data;
                        // Display section info
                        $('#display_SectionId_g').val(data.SectionId);
                        $('#display_sectionname_g').val(data.SectionName);
                        $('#display_gradelevel_g').val(data.GradeLevel);
                        $('#display_schoolyear_g').val(data.SchoolYear);
                        $('#display_facility_g').val(data.Facility);
                        $('#display_strand_g').val(data.SectionStrand);
                        const SchoolYear = data.SchoolYear;
                        $('#viewSectionGradeModal').modal('show');
                        $('#viewSectionModalLabel').text(`Section Details / ${SectionName}`);
                        // Get Subjects
                        $.ajax({
                            url: '../api/api_grade.php?get_subjects',
                            type: 'GET',
                            data: {
                                grade_level: GradeLevel,
                                school_year: SchoolYear
                            },
                            dataType: 'json',
                            // Inside the success function of the subjects AJAX request
                            // Inside the success function of the subjects AJAX request
                            success: function (subjectResponse) {
                                if (subjectResponse.success) {
                                    const dynamicColumns = [
                                        { data: 'lrn', title: 'LRN', className: 'align-middle editable' },
                                        { data: 'name', title: 'Full Name', className: 'align-middle editable' }
                                    ];
                                    firstSetIndexes = [];
                                    secondSetIndexes = [];
                                    const totalSubjects = subjectResponse.subjects.length;
                                    const midPoint = Math.ceil(totalSubjects / 2);
                                    subjectResponse.subjects.forEach((subject, index) => {
                                        // Add column for subject_code with title as subject_id
                                        dynamicColumns.push({
                                            data: subject.subject_id, // This should match the subject_id in the returned data
                                            title: subject.subject_code, // Set title to subject_id
                                            className: 'align-middle editable',
                                            visible: index < midPoint // Show first set only
                                        });
                                        if (index < midPoint) {
                                            firstSetIndexes.push(dynamicColumns.length - 1); // index for subject_code column
                                        } else {
                                            secondSetIndexes.push(dynamicColumns.length - 1);
                                        }
                                    });
                                    // Destroy old DataTable if exists
                                    if ($.fn.DataTable.isDataTable('#section_info_table_grade')) {
                                        $('#section_info_table_grade').DataTable().clear().destroy();
                                    }
                                    sectionInfoTableGrade = $('#section_info_table_grade').DataTable({
                                        responsive: true,
                                        order: [[0, "asc"]],
                                        processing: true,
                                        serverSide: false,
                                        dom: 'Bfrtip',
                                        buttons: [],
                                        searching: false,
                                        pageLength: 50,
                                        ajax: {
                                            url: '../api/api_grade.php?student_in_section',
                                            type: 'GET',
                                            data: {
                                                SectionId: SectionId,
                                                schoolYear: SchoolYear // Pass the school year
                                            },
                                            dataSrc: 'data',
                                            error: function (xhr) {
                                                console.error('Load error:', xhr.responseText);
                                                alert('Failed to load student data.');
                                            }
                                        },
                                        columns: dynamicColumns
                                    });
                                } else {
                                    Swal.fire("Error", "No subjects found.", "error");
                                }
                            },
                            error: function () {
                                Swal.fire("Error", "Unable to fetch subjects.", "error");
                            }
                        });
                    }
                },
                error: function () {
                    Swal.fire("Error", "Failed to load section details.", "error");
                }
            });
        });
        // INLINE CELL EDITING
        $('#section_info_table_grade tbody').on('click', 'td.editable', function () {
            const cell = sectionInfoTableGrade.cell(this);
            const originalValue = cell.data();
            if ($(this).find('input').length > 0) return;
            const $input = $('<input>', {
                type: 'text',
                class: 'form-control form-control-sm',
                value: originalValue
            }).appendTo($(this).empty()).focus();
            $input.on('blur keypress', function (e) {
                if (e.type === 'blur' || e.which === 13) {
                    const newValue = $input.val();
                    cell.data(newValue).draw();
                    console.log(`Updated: "${originalValue}" → "${newValue}"`);
                }
            });
        });
        // Reload table on grading select change
        $('#grading_select').on('change', function () {
            if (sectionInfoTableGrade) {
                sectionInfoTableGrade.ajax.reload();
            }
        });
        // Switch between Grade Set 1 and 2
        $('#grade_set').off('change').on('change', function () {
            const selectedSet = $(this).val();
            if (!sectionInfoTableGrade) return;
            if (selectedSet === "1") {
                secondSetIndexes.forEach(i => sectionInfoTableGrade.column(i).visible(false));
                firstSetIndexes.forEach(i => sectionInfoTableGrade.column(i).visible(true));
            } else {
                firstSetIndexes.forEach(i => sectionInfoTableGrade.column(i).visible(false));
                secondSetIndexes.forEach(i => sectionInfoTableGrade.column(i).visible(true));
            }
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