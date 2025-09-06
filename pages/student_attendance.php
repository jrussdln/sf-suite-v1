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
                        Attendance
                    </h1>
                </div>
            </div>
            <div class="row" style="margin: 0;">
                <div class="col-sm-6" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        Track and manage attendance records of students.
                    </small>
                </div>
                <div class="col-sm-6 text-right" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        Records / Attendance
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
                                            <!-- New View Holiday Button -->
                                            <button id="viewHolidayBtn" type="button" class="btn btn-success mr-2"
                                                onclick="window.location.href='calendar_holiday.php';">
                                                <i class="fas fa-calendar-alt"></i> Holiday
                                            </button>
                                            <?php
                                        }
                                        ?>
                                        <!-- Existing Toggle Button -->
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
include_once('../partials/modals/modal_section_setup.php');
include_once('../partials/footer.php');
?>
<script>
    $(document).ready(function () {
        var selectedSectionName;
        var selectedId;
        var sectionTable = $('#section_table').DataTable({
            responsive: true,
            order: [[0, "asc"]],
            searching: false,
            pageLength: 50,
            lengthChange: false,
            ajax: {
                url: '../api/api_section.php?section_list',
                type: 'GET',
                data: function (d) {
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
                        <button class="btn btn-secondary btn-sm viewAttendance-btn" data-id="${row.SectionId}" data-name="${row.SectionName}">
                        <i class="fas fa-pen"></i>
                        </button>
                        <button class="btn btn-primary btn-sm saveAttendance-btn" data-id="${row.SectionId}" data-name="${row.SectionName}">
                            <i class="fas fa-save"></i> 
                        </button>
                    `;
                    }
                }
            ]
        });
        //search button 
        $('#searchSection').click(function (event) {
            event.preventDefault();
            sectionTable.ajax.reload();
        });
        // Save attendance button behavior
        $(document).on('click', '.saveAttendance-btn', function () {
            const sectionId = $(this).data('id');
            const sectionName = $(this).data('name');
            const currentDate = new Date();
            const currentDay = currentDate.getDate();
            const currentMonth = currentDate.getMonth() + 1; // January is 0
            const currentYear = currentDate.getFullYear();
            // Check if it's within the first 5 days of the month
            if (currentDay >= 1 && currentDay <= 5) {
                Swal.fire({
                    title: `Save Attendance Report?`,
                    text: `You are within the first 5 days of ${currentMonth}/${currentYear}. Do you want to save the attendance report for the previous month?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, save it!',
                    cancelButtonText: 'No, cancel!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '../api/api_attendance.php?sync_attendance',
                            method: 'GET',
                            data: {
                                SectionName: sectionName,
                                SectionId: sectionId
                            },
                            success: function (response) {
                                console.log("Attendance saved successfully:", response);
                                Swal.fire(
                                    'Saved!',
                                    'Attendance report saved successfully.',
                                    'success'
                                );
                            },
                            error: function (xhr, status, error) {
                                console.error("Error saving attendance:", error);
                                Swal.fire(
                                    'Error!',
                                    'An error occurred while saving attendance.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            } else {
                Swal.fire({
                    title: 'Saving Not Available',
                    text: 'Attendance saving is only available during the first 5 days of the month.',
                    icon: 'info',
                    confirmButtonText: 'OK'
                });
            }
        });
        //pagkuha ng section data
        $('#section_table').on('click', '.viewAttendance-btn', function () {
            var SectionId = $(this).data('id');
            var SectionName = $(this).data('name');
            // Fetch class data first
            $.ajax({
                url: '../api/api_section.php?get_section_data',
                type: 'GET',
                data: { SectionId: SectionId },
                dataType: 'json',
                beforeSend: function () {
                    Swal.fire({
                        title: "Loading...",
                        text: "Fetching class data and syncing attendance...",
                        icon: "info",
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });
                }
            }).then(function (response) {
                if (response.success) {
                    // Fill in the fields
                    $('#display_SectionId').val(response.data.SectionId);
                    $('#display_sectionname').val(response.data.SectionName);
                    $('#display_gradelevel').val(response.data.GradeLevel);
                    $('#display_schoolyear').val(response.data.SchoolYear);
                    $('#display_facility').val(response.data.Facility);
                    $('#display_semester').val(response.data.SectionSemester);
                    $.ajax({
                        url: '../api/api_attendance.php?sync_attendance',
                        type: 'GET',
                        dataType: 'json', // ✅ add this line
                        data: {
                            SectionName: SectionName,
                            SectionId: SectionId
                        },
                        success: function (res) {
                            Swal.close();
                            if (res.success) {
                                $('#viewSectionModal').modal('show');
                                $('#viewSectionModalLabel').html(`Section Details / ${SectionName}`);
                                $('#section_info_table').DataTable().ajax.reload();
                            } else {
                                console.error('Sync Error:', res.message || 'Failed to sync attendance data.');
                                Swal.fire("Error!", res.message || "Failed to sync attendance.", "error");
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            Swal.close();
                            console.error('AJAX Error:', textStatus, errorThrown);
                            Swal.fire("Error!", "Failed to sync attendance.", "error");
                        }
                    });
                } else {
                    Swal.fire("Error!", response.message || "Failed to fetch class data.", "error");
                }
            }).catch(function (error) {
                console.error('Error:', error);
                Swal.fire("Error!", "An unexpected error occurred while fetching class data.", "error");
            });
        });
        //para lumabas ang modal ng section
        $('#section_table').on('click', '.viewAttendance-btn', function () {
            selectedId = $(this).data('id');
            var SectionName = $(this).data('name');
            $('#viewSectionModal').modal('show');
            $('#viewSectionModalLabel').html(`Section Details / ${SectionName}`);
            $('#section_info_table').DataTable().ajax.reload();
        });
        // Fetch holidays
        function fetchHolidays() {
            return $.ajax({
                url: '../api/api_attendance.php',
                type: 'GET',
                data: { get_holidays: true },
                dataType: 'json'
            });
        }
        const today = new Date();
        const currentDay = today.getDate();
        const currentMonth = today.getMonth(); // 0-indexed
        const currentYear = today.getFullYear();
        // Calculate total days in current month
        const totalDaysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        // Function to split total days into 4 ranges (as equal as possible)
        function getMonthWeeks(totalDays) {
            const weekSize = Math.floor(totalDays / 4);
            const remainder = totalDays % 4;
            let weeks = [];
            let start = 1;
            for (let i = 0; i < 4; i++) {
                let end = start + weekSize - 1;
                if (i < remainder) {
                    end += 1; // distribute remainder days
                }
                if (end > totalDays) end = totalDays;
                weeks.push({ startDay: start, endDay: end });
                start = end + 1;
            }
            return weeks;
        }
        // Get the 4 dynamic week ranges for current month
        const monthWeeks = getMonthWeeks(totalDaysInMonth);
        // Global selectedWeekIndex
        let selectedWeekIndex = 0; // default to first week (index 0)
        // Fetch holidays and initialize the table
        fetchHolidays().done(function (holidays) {
            const holidayDates = holidays.map(holiday => new Date(holiday.holiday_date).setHours(0, 0, 0, 0));
            // Function to build dynamic day columns based on selected week range
            function buildDynamicDayColumns() {
                const { startDay, endDay } = monthWeeks[selectedWeekIndex];
                return Array.from({ length: endDay - startDay + 1 }, (_, index) => {
                    const day = startDay + index;
                    const date = new Date(currentYear, currentMonth, day);
                    const dayOfWeek = date.getDay();
                    const normalizedDate = new Date(date.setHours(0, 0, 0, 0)).getTime();
                    const isEditable = day <= currentDay;
                    const isHoliday = holidayDates.includes(normalizedDate);
                    const isDisabled = (dayOfWeek === 0 || dayOfWeek === 6 || isHoliday);
                    return {
                        data: `Day${day}`,
                        title: `Day ${day}`,
                        className: 'align-middle',
                        render: (data, type, row) => {
                            const checkedAttr = (!isDisabled && data !== 'X') ? 'checked' : '';
                            const disabledAttr = (isEditable && !isDisabled) ? '' : 'disabled';
                            return `
                    <label class="custom-x-checkbox">
                        <input type="checkbox"
                            class="attendance-checkbox"
                            data-lrn="${row.lrn}"
                            data-id="${row.section_id}"
                            data-day="Day${day}"
                            ${checkedAttr}
                            ${disabledAttr}>
                        <span></span>
                    </label>
                `;
                        },
                        createdCell: (td) => {
                            if (isHoliday) {
                                $(td).css({ 'background-color': 'rgb(59, 228, 146)', 'color': 'black' });
                                $(td).attr('title', 'Holiday');
                            } else if (isDisabled) {
                                $(td).css({ 'background-color': 'red', 'color': 'white' });
                            }
                        }
                    };
                });
            }
            // Initialize with columns for the first week by default
            let dynamicDayColumns = buildDynamicDayColumns();
            // DataTable instance
            let sectionInfoTable = $('#section_info_table').DataTable({
                responsive: true,
                order: [[0, "asc"]],
                processing: true,
                serverSide: false,
                searching: true,
                pageLength: 50,
                dom: "<'row mb-2'<'col-md-6'f><'col-md-6 text-right'<'dropdown ms-auto'B>>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-md-5'i><'col-md-7'p>>",
                buttons: [{
                    extend: 'collection',
                    text: 'Select Week',
                    className: 'btn btn-primary dropdown-toggle',
                    autoClose: true,
                    buttons: monthWeeks.map((range, i) => ({
                        text: `${range.startDay} - ${range.endDay}`,
                        className: 'dropdown-item',
                        attr: {
                            style: 'color: #333; padding: 8px 16px; font-weight: normal; background-color: white;'
                        },
                        action: function () {
                            selectedWeekIndex = i;
                            updateTableForWeek();
                        }
                    }))
                }]
                ,
                ajax: {
                    url: '../api/api_attendance.php?get_attendance_by_section',
                    type: 'GET',
                    data: function (d) {
                        d.selectedId = selectedId;
                        const { startDay, endDay } = monthWeeks[selectedWeekIndex];
                        d.startDate = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(startDay).padStart(2, '0')}`;
                        d.endDate = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(endDay).padStart(2, '0')}`;
                    },
                    dataSrc: function (json) {
                        if (!json.success) {
                            // Silent fail: log to console only
                            console.warn('No data returned:', json.message || 'No data available for the selected section.');
                            return [];
                        }
                        return json.data;
                    },
                    error: function (xhr, error, thrown) {
                        console.error('Error:', error);
                        console.error('Response:', xhr.responseText);
                    }
                },
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 1 },
                ],
                columns: [
                    { data: 'lrn', title: 'LRN', className: 'align-left' },
                    { data: 'name', title: 'Full Name', className: 'align-left' },
                    ...dynamicDayColumns
                ]
            });
            // Function to update columns and reload table on week change
            function updateTableForWeek() {
                // Remove old DataTable instance if exists
                if ($.fn.DataTable.isDataTable('#section_info_table')) {
                    sectionInfoTable.clear();
                    sectionInfoTable.destroy();
                }
                // Clear the table header and body to remove old columns completely
                $('#section_info_table thead').empty();
                $('#section_info_table tbody').empty();
                dynamicDayColumns = buildDynamicDayColumns();
                // Recreate DataTable
                sectionInfoTable = $('#section_info_table').DataTable({
                    responsive: true,
                    order: [[0, "asc"]],
                    processing: true,
                    serverSide: false,
                    searching: true,
                    pageLength: 50,
                    dom: "<'row mb-2'<'col-md-6'f><'col-md-6 text-right'<'dropdown ms-auto'B>>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-md-5'i><'col-md-7'p>>",
                    buttons: [{
                        extend: 'collection',
                        text: 'Select Week',
                        className: 'btn btn-primary dropdown-toggle',
                        autoClose: true,
                        buttons: monthWeeks.map((range, i) => ({
                            text: `${range.startDay} - ${range.endDay}`,
                            className: 'dropdown-item',
                            attr: {
                                style: 'color: #333; padding: 8px 16px; font-weight: normal;background-color: white;'
                            },
                            action: function () {
                                selectedWeekIndex = i;
                                updateTableForWeek();
                            }
                        }))
                    }]
                    ,
                    ajax: {
                        url: '../api/api_attendance.php?get_attendance_by_section',
                        type: 'GET',
                        data: function (d) {
                            d.selectedId = selectedId;
                            const { startDay, endDay } = monthWeeks[selectedWeekIndex];
                            d.startDate = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(startDay).padStart(2, '0')}`;
                            d.endDate = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(endDay).padStart(2, '0')}`;
                        },
                        dataSrc: function (json) {
                            if (!json.success) {
                                console.warn('No data available for the selected section:', json.message || 'Unknown reason');
                                return [];
                            }
                            return json.data;
                        },
                        error: function (xhr, error, thrown) {
                            console.error('Error:', error);
                            console.error('Response:', xhr.responseText);
                        }
                    },
                    columnDefs: [
                        { responsivePriority: 1, targets: 0 },
                        { responsivePriority: 2, targets: 1 },
                    ],
                    columns: [
                        { data: 'lrn', title: 'LRN', className: 'align-left' },
                        { data: 'name', title: 'Full Name', className: 'align-left' },
                        ...dynamicDayColumns
                    ]
                });
            }
        });
        //para sa checkboxes
        $('#section_info_table').on('change', '.attendance-checkbox', function () {
            const lrn = $(this).data('lrn');
            const sectionId = $(this).data('id');
            const day = $(this).data('day');
            const isChecked = $(this).is(':checked');
            const value = isChecked ? '' : 'X';
            if (!lrn || !sectionId || !day) {
                return; // silently fail if essential data is missing
            }
            $.ajax({
                url: '../api/api_attendance.php?edit_attendance',
                type: 'POST',
                data: {
                    lrn: lrn,
                    sectionId: sectionId,
                    day: day,
                    value: value,
                    schoolYear: $('#display_schoolyear').val()
                },
                success: function (response) {
                    try {
                        const res = typeof response === 'string' ? JSON.parse(response.trim()) : response;
                        if (!res.success) {
                            // silently ignore failure
                        }
                    } catch (error) {
                        // silently ignore JSON parsing errors
                    }
                },
                error: function () {
                    // silently ignore AJAX errors
                }
            });
        });
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
                url: '../api/api_select_options.php?get_section_list_teacher=1&identifier=' + encodeURIComponent(identifier),
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
    $(document).ready(function () {
        const currentDate = new Date();
        const currentDay = currentDate.getDate();
        const currentMonth = currentDate.getMonth() + 1;
        const currentYear = currentDate.getFullYear();
        // Trigger reminder only on the first 5 days of the month
        if (currentDay >= 1 && currentDay <= 5) {
            Swal.fire({
                title: 'Reminder',
                text: `Don't forget to save the attendance report for ${currentMonth - 1 <= 0 ? 12 : currentMonth - 1}/${currentMonth - 1 <= 0 ? currentYear - 1 : currentYear}.`,
                icon: 'info',
                confirmButtonText: 'Got it!'
            });
        }
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