<?php
require_once('page_header_sessions.php');
include('../includes/navbar.php');
?>
<div class="wrapper">
    <?php
    if ($_SESSION['access_level'] == 'TEACHER' || $_SESSION['access_level'] == 'SA' || $_SESSION['access_level'] == 'SIC' || $_SESSION['access_level'] == 'HNP' || $_SESSION['access_level'] == 'LMP') {
        ?>
        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="container-fluid py-2">
                <div class="row">
                    <div class="col-12">
                        <h1 style="font-size: 1.2rem; margin: 2px 0; color: #343a40;">Dashboard</h1>
                        <p class="mb-2"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-3 col-sm-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-sm mb-0">Active Users</p>
                                        <h4 class="mb-0 user-number" id="total-user">--</h4>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-users opacity-10 text-xl"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-end">
                                <p class="mb-0 text-sm text-success" id="user-outoff-change">
                                    <span class="font-weight-bolder">N/A</span>
                                </p>
                                </p>
                                <div class="d-flex align-items-center"
                                    style="font-size: 0.75rem; font-weight: 300; color: #6c757d;">
                                    <i class="fas fa-clock me-1" style="font-size: 0.7rem;"></i>
                                    <span id="user-update-time">N/A</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-sm mb-0">Enrolled Students</p>
                                        <h4 class="mb-0 enrollment-number" id="total-enrolled">--</h4>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-graduate opacity-10 text-xl"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-end">
                                <p class="mb-0 text-sm text-success" id="enrollment-percentage-change">
                                    <span class="font-weight-bolder">N/A</span>
                                </p>
                                <div class="d-flex align-items-center"
                                    style="font-size: 0.75rem; font-weight: 300; color: #6c757d;">
                                    <i class="fas fa-clock me-1" style="font-size: 0.7rem;"></i>
                                    <span id="enrollment-update-time">N/A</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-sm mb-0">Personnel</p>
                                        <h4 class="mb-0 personnel-number" id="total-personnel">--</h4>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-tie opacity-10 text-xl"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-end">
                                <div class="d-flex align-items-center"
                                    style="font-size: 0.75rem; font-weight: 300; color: #6c757d;">
                                    <i class="fas fa-clock me-1" style="font-size: 0.7rem;"></i>
                                    <span id="personnel-update-time">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Card for Subjects -->
                    <div class="col-xl-3 col-sm-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-sm mb-0">Subjects</p>
                                        <h4 class="mb-0 subject-number" id="total-subjects">--</h4>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-book opacity-10 text-xl"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-end">
                                <div class="d-flex align-items-center"
                                    style="font-size: 0.75rem; font-weight: 300; color: #6c757d;">
                                    <i class="fas fa-clock me-1" style="font-size: 0.7rem;"></i>
                                    <span id="subjects-update-time">N/A</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-5 col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-0">Daily Attendance Rate (Today)</h6>
                                <p class="text-sm">JHS - SHS</p>
                                <div class="chart">
                                    <canvas id="chart-bars" height="170"></canvas>
                                </div>
                                <hr>
                                <div class="d-flex">
                                    <i class="fas fa-clock text-sm my-auto me-1"></i>
                                    <p class="mb-0 text-sm update-time">Just updated</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Card for Enrolled  -->
                    <div class="col-lg-7 col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-0">Enrolled Students</h6>
                                <p class="text-sm">JHS - SHS</p>
                                <div class="chart">
                                    <canvas id="chart-line-tasks" height="170"></canvas>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between align-items-end">
                                    <p class="mb-0 text-sm text-success" id="enrollment-percentage-change1">
                                        <span class="font-weight-bolder">N/A</span>
                                    </p>
                                    <div class="d-flex align-items-center"
                                        style="font-size: 0.75rem; font-weight: 300; color: #6c757d;">
                                        <i class="fas fa-clock me-1" style="font-size: 0.7rem;"></i>
                                        <span id="enrollment-update-time1">N/A</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row d-flex align-items-stretch">
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-0">Learning Progress and Achievement</h6>
                                <p class="text-sm">JHS - SHS</p>
                                <div class="chart">
                                    <canvas id="chart-line-tasks1" height="170"></canvas>
                                </div>
                                <hr>
                                <div class="d-flex">
                                    <i class="fas fa-clock text-sm my-auto me-1"></i>
                                    <p class="mb-0 text-sm update-time">Just updated</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- First Card -->
                    <div class="col-lg-6 col-md-6 mb-4 d-flex">
                        <div class="card flex-fill">
                            <div class="card-body d-flex flex-column">
                                <!-- Header with dropdown -->
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <h6 class="mb-0">Academic Achiever (by Section)</h6>
                                        <p class="text-sm mb-0">JHS - SHS</p>
                                    </div>
                                    <div>
                                        <select class="form-select form-select-sm" id="sectionDboard" name="sectionDboard">
                                        </select>
                                    </div>
                                </div>
                                <!-- Container for top achievers -->
                                <div id="top-achievers" class="flex-grow-1" style="min-height: 170px; overflow-y: auto;">
                                </div>
                                <hr>
                                <!-- Update time display -->
                                <div class="d-flex mt-auto">
                                    <i class="fas fa-clock text-sm my-auto me-1"></i>
                                    <p class="mb-0 text-sm update-time ">Just updated</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Additional Row for Results -->
                <div class="row d-flex align-items-stretch">
                    <!-- First Card -->
                    <div class="col-lg-8 col-md-6 mb-4 d-flex">
                        <div class="card flex-fill">
                            <div class="card-body d-flex flex-column p-3">
                                <!-- Header with dropdown -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="mb-1">Alumni Responses</h6>
                                        <p class="text-sm text-muted mb-0">Responses</p>
                                    </div>
                                    <div>
                                        <select class="form-select form-select-sm" id="questionDboard"
                                            name="questionDboard">
                                            <!-- Options will be populated dynamically -->
                                        </select>
                                    </div>
                                </div>
                                <!-- Chart container -->
                                <div class="chart mb-3">
                                    <canvas id="chart-bars-question" height="170"></canvas>
                                </div>
                                <hr class="my-2">
                                <!-- Update time display -->
                                <div class="d-flex align-items-center mt-auto">
                                    <i class="fas fa-clock text-sm me-2"></i>
                                    <p class="mb-0 text-sm text-muted update-time">Just updated</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Second Card -->
                    <div class="col-lg-4 col-md-6 mb-4 d-flex">
                        <div class="card flex-fill">
                            <div class="card-body d-flex flex-column p-3">
                                <!-- Header -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="mb-1">Recent Responses (<span id="response-count">0</span>)</h6>
                                        <p class="text-sm text-muted mb-0">Tracer Survey - All Batches</p>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <!-- Response Log List -->
                                <div id="response-log-list" class="flex-grow-1"
                                    style="max-height: 220px; overflow-y: auto;">
                                    <!-- Dynamic content here -->
                                </div>
                                <hr class="my-2">
                                <!-- Update Time -->
                                <div class="d-flex align-items-center mt-auto">
                                    <i class="fas fa-clock text-sm me-2"></i>
                                    <p class="mb-0 text-sm text-muted update-time">Just updated</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content-wrapper -->
    <?php
    }
    ?>
<?php
if ($_SESSION['access_level'] == 'STUDENT') {
    ?>
    <div class="content-wrapper py-5">
        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">
                <div class="d-flex align-items-center justify-content-center vh-100">
                    <div class="card shadow-lg border-0 text-center">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Student Analytics</h5>
                            <p class="card-text text-muted">
                                View detailed insights and school information by clicking the button below.
                            </p>
                            <a href="./student_analytics.php" class="btn btn-primary btn-s w-100 mt-2">
                                View Analytics
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>
</div>
<?php
include_once('../partials/modals/modal_user_setup.php');
include_once('../partials/footer.php'); ?>
<script>
    $(document).ready(function () {
        fetchSubjectCount();
        fetchEnrolledCount();
        fetchUserCount();
        fetchPersonnelCount();
        fetchSections();
        loadResponseLogs();
        // Call the function to fetch enrollment counts when the document is ready
        fetchEnrollmentCounts();
        let jhsChart, shsChart;
        let sectionNames = [];
        let attendancePercentages = [];
        let attendanceCounts = []; // Store attendance count
        let xCounts = []; // Store X count
        const updateElements = document.querySelectorAll(".update-time");
        const now = new Date();
        const day = String(now.getDate()).padStart(2, '0');
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const year = now.getFullYear();
        const formattedDate = `${day}/${month}/${year}`;
        updateElements.forEach(el => {
            el.textContent = `${formattedDate}`;
            el.classList.add("text-muted");
            el.style.fontWeight = "normal"; // Ensures it's not bold
        });
        function loadResponseLogs() {
            $.ajax({
                url: '../api/api_announcement.php?get_responses',
                method: 'GET',
                dataType: 'json'
            }).done(function (data) {
                const container = $('#response-log-list');
                const countSpan = $('#response-count');
                container.empty();
                if (Array.isArray(data) && data.length > 0) {
                    countSpan.text(data.length);
                    data.forEach(item => {
                        const fullName = item.full_name && item.full_name.trim() !== '' ? item.full_name : 'No name';
                        const row = `
                    <div class="mb-2">
                        <p class="mb-0 fw-semibold">${fullName}</p>
                        <p class="mb-0 text-muted small">${item.submitted_at}</p>
                    </div>
                `;
                        container.append(row);
                    });
                } else {
                    countSpan.text(0);
                    container.html('<p class="text-muted small">No recent responses.</p>');
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Failed to fetch response log:', textStatus, errorThrown);
            });
        }
        //enrolled stud
        function fetchSections() {
            $.ajax({
                method: "GET",
                url: "../api/api_main_dashboard.php?get_section_list",
                dataType: "json",
                success: function (response) {
                    if (response.error) {
                        console.error("Error:", response.error);
                        return;
                    }
                    if (Array.isArray(response) && response.length > 0) {
                        sectionNames = response.map(section => section.SectionName);
                        attendancePercentages = new Array(sectionNames.length).fill(0); // Initialize percentages with zeros
                        attendanceCounts = new Array(sectionNames.length).fill(0); // Initialize attendance count with zeros
                        xCounts = new Array(sectionNames.length).fill(0); // Initialize X count with zeros
                        console.log("Sections Loaded:", sectionNames);
                        fetchAttendanceCounts(); // Fetch attendance after sections are loaded
                        fetchEnrollmentCounts();
                    } else {
                        console.error("Invalid API response:", response);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error("Error fetching sections:", textStatus, errorThrown, jqXHR.responseText);
                }
            });
        }
        function fetchEnrollmentCounts() {
            $.ajax({
                method: "GET",
                url: "../api/api_main_dashboard.php?get_enrolled_counts",
                dataType: "json",
                success: function (response) {
                    console.log("Raw Enrollment API Response:", response); // Debugging
                    if (!Array.isArray(response) || response.length === 0) {
                        console.error("Invalid API response:", response);
                        return;
                    }
                    let enrollmentMap = {};
                    response.forEach(section => {
                        let sectionName = (section.SectionName || "Unknown Section").trim().toLowerCase();
                        enrollmentMap[sectionName] = parseInt(section.enrollment_count, 10) || 0;
                    });
                    console.log("Enrollment Map:", enrollmentMap);
                    // Update enrollment count array using case-insensitive matching
                    enrolledCounts = sectionNames.map(name => enrollmentMap[name.toLowerCase()] || 0);
                    console.log("Final Enrollment Counts:", enrolledCounts);
                    drawLineChart(sectionNames, enrolledCounts);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error("Error fetching enrollment counts:", textStatus, errorThrown, jqXHR.responseText);
                }
            });
        }
        //daily att rate
        function fetchAttendanceCounts() {
            $.ajax({
                method: "GET",
                url: "../api/api_main_dashboard.php?get_section_attendance",
                dataType: "json",
                success: function (response) {
                    console.log("Raw Attendance API Response:", response); // Debugging
                    if (!Array.isArray(response) || response.length === 0) {
                        console.error("Invalid API response:", response);
                        return;
                    }
                    // Initialize attendance data arrays
                    attendanceCounts = [];
                    xCounts = [];
                    attendancePercentages = [];
                    // Process the response to extract attendance data by year level
                    response.forEach(yearLevelData => {
                        const yearLevel = yearLevelData.GradeLevel; // Assuming the response has 'GradeLevel'
                        const attendanceCount = parseInt(yearLevelData.attendance_count, 10) || 0;
                        const xCount = parseInt(yearLevelData.x_count, 10) || 0;
                        const totalCount = attendanceCount + xCount;
                        // Calculate attendance percentage
                        const attendancePercentage = (totalCount > 0) ? Math.round((attendanceCount / totalCount) * 100 * 100) / 100 : 0; // Fixed rounding
                        // Store the data in the arrays
                        attendanceCounts[yearLevel - 7] = attendanceCount; // Adjust index for year levels 7-12
                        xCounts[yearLevel - 7] = xCount; // Adjust index for year levels 7-12
                        attendancePercentages[yearLevel - 7] = attendancePercentage; // Adjust index for year levels 7-12
                    });
                    console.log("Final Attendance Counts:", attendanceCounts);
                    console.log("Final X Counts:", xCounts);
                    console.log("Final Attendance Percentages:", attendancePercentages);
                    drawChart();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error("Error fetching attendance counts:", textStatus, errorThrown, jqXHR.responseText);
                }
            });
        }
        function drawChart() {
            var ctx = document.getElementById("chart-bars").getContext("2d");
            if (window.myChart) {
                window.myChart.destroy();
            }
            const presentData = attendancePercentages;
            const absentData = attendancePercentages.map((p) => 100 - p);
            window.myChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: ['7', '8', '9', '10', '11', '12'], // Year levels as labels
                    datasets: [
                        {
                            label: "Present",
                            borderWidth: 0,
                            borderRadius: 4,
                            borderSkipped: false,
                            backgroundColor: "#43A047", // Green
                            data: presentData,
                            barThickness: "flex",
                            stack: "stack1"
                        },
                        {
                            label: "Absent",
                            borderWidth: 0,
                            borderRadius: 4,
                            borderSkipped: false,
                            backgroundColor: "#E53935", // Red
                            data: absentData,
                            barThickness: "flex",
                            stack: "stack1"
                        }
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true },
                        tooltip: {
                            callbacks: {
                                label: function (tooltipItem) {
                                    const index = tooltipItem.dataIndex;
                                    const present = attendanceCounts[index] || 0;
                                    const absent = xCounts[index] || 0;
                                    const percent = attendancePercentages[index] || 0;
                                    // Only show this on the first dataset (Present), hide on the second (Absent)
                                    if (tooltipItem.datasetIndex === 0) {
                                        return `Present: ${present} | Absent: ${absent} | Percentage: ${percent.toFixed(2)}%`;
                                    } else {
                                        return ''; // Hide the red (Absent) dataset tooltip
                                    }
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: "index"
                    },
                    scales: {
                        y: {
                            stacked: true,
                            max: 100,
                            beginAtZero: true,
                            grid: {
                                drawBorder: false,
                                display: true,
                                borderDash: [5, 5],
                                color: "#e5e5e5"
                            },
                            ticks: {
                                padding: 10,
                                font: { size: 14, lineHeight: 2 },
                                color: "#737373"
                            }
                        },
                        x: {
                            stacked: true,
                            grid: { drawBorder: false, display: false },
                            ticks: {
                                display: true, // Show the year levels
                                color: "#737373",
                                padding: 1,
                                font: { size: 10, lineHeight: 1 }
                            }
                        }
                    }
                }
            });
        }
        function drawBarChart(labels, counts) {
            const ctx = document.getElementById("chart-bars-question").getContext("2d");
            if (window.barChartQuestion) {
                window.barChartQuestion.destroy(); // clean up
            }
            window.barChartQuestion = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Responses',
                        data: counts,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
        //
        function fetchEnrolledCount() {
            $.ajax({
                url: '../api/api_main_dashboard.php?count_enrolled',
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    if (data.error) {
                        console.error('API error:', data.error);
                        $('#total-enrolled').text('Error');
                        $('#enrollment-percentage-change, #enrollment-percentage-change1')
                            .text('N/A')
                            .removeClass('text-success text-danger text-muted');
                    } else {
                        $('#total-enrolled').text(data.total_enrolled || '0');
                        // Handle percentage change display
                        if (data.percentage_change !== undefined && data.change_direction) {
                            const percentageChange = parseFloat(data.percentage_change).toFixed(2);
                            let changeText = `${percentageChange}% ${data.change_direction} from last S.Y.`;
                            let changeClass = 'text-success';
                            if (data.change_direction === 'decreased') {
                                changeClass = 'text-danger';
                            } else if (data.change_direction === 'no change') {
                                changeClass = 'text-muted';
                            }
                            $('#enrollment-percentage-change, #enrollment-percentage-change1')
                                .text(changeText)
                                .removeClass('text-success text-danger text-muted')
                                .addClass(changeClass);
                        } else {
                            $('#enrollment-percentage-change, #enrollment-percentage-change1')
                                .text('N/A')
                                .removeClass('text-success text-danger text-muted');
                        }
                        $('#enrollment-update-time, #enrollment-update-time1').text(new Date().toLocaleDateString());
                    }
                },
                error: function (jqXHR) {
                    console.error('Server Error:', jqXHR.responseText);
                    $('#total-enrolled').text('Error');
                    $('#enrollment-percentage-change')
                        .text('N/A')
                        .removeClass('text-success text-danger text-muted');
                    $('#enrollment-update-time, #enrollment-update-time1').text('N/A');
                }
            });
        }
        function fetchUserCount() {
            $.ajax({
                url: '../api/api_main_dashboard.php?count_user',
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    if (data.error) {
                        console.error('API error:', data.error);
                        $('#total-user').text('Error loading data');
                        $('#user-outoff-change').html('<span class="font-weight-bolder">N/A</span>');
                    } else {
                        $('#total-user').text(data.total_active || 'N/A');
                        $('#user-outoff-change').html(
                            `<span class="font-weight-bolder">${data.total_active}</span> out of ${data.total_users}`
                        );
                        $('#user-update-time').text(new Date().toLocaleDateString());
                    }
                },
                error: function (jqXHR) {
                    console.error('Server Error:', jqXHR.responseText);
                    $('#total-user').text('Error loading data');
                    $('#user-outoff-change').html('<span class="font-weight-bolder">N/A</span>');
                    $('#user-update-time').text('N/A');
                }
            });
        }
        function fetchPersonnelCount() {
            $.ajax({
                url: '../api/api_main_dashboard.php?count_personnel',
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    if (data.total_personnel !== undefined) {
                        $('#total-personnel').text(data.total_personnel);
                        $('#personnel-update-time').text(new Date().toLocaleDateString());
                    } else {
                        console.error('Unexpected API response:', data);
                        $('#total-personnel').text('N/A');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('Server Error:', textStatus, errorThrown, jqXHR.responseText);
                    $('#total-personnel').text('Error loading data');
                    $('#personnel-update-time').text('N/A');
                }
            });
        }
        function fetchSubjectCount() {
            $.ajax({
                url: '../api/api_main_dashboard.php?count_subjects',
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    if (data.total_subjects !== undefined) {
                        $('#total-subjects').text(data.total_subjects);
                        $('#subjects-update-time').text(new Date().toLocaleDateString());
                    } else {
                        console.error('Unexpected API response:', data);
                        $('#total-subjects').text('N/A');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('Server Error:', textStatus, errorThrown, jqXHR.responseText);
                    $('#total-subjects').text('Error loading data');
                    $('#subjects-update-time').text('N/A');
                }
            });
        }
        $.ajax({
            method: "GET",
            url: '../api/api_select_options.php?get_section_list',
            dataType: 'json'
        }).done(function (data) {
            $('#sectionDboard').empty(); // Clear existing options
            // Add default option
            $('#sectionDboard').append($('<option>', {
                value: '',
                text: 'Select Section',
                disabled: true,
                selected: true
            }));
            if (Array.isArray(data) && data.length > 0) {
                $.each(data, function (index, item) {
                    $('#sectionDboard').append($('<option>', {
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
        $('#questionDboard').on('change', function () {
            const questionId = $(this).val();
            if (!questionId) return; // Prevent empty call
            $.ajax({
                url: '../api/api_announcement.php',
                method: 'GET',
                data: {
                    get_question_summary: true,
                    question_id: questionId
                },
                dataType: 'json'
            }).done(function (data) {
                if (Array.isArray(data)) {
                    const labels = data.map(item => item.label);
                    const counts = data.map(item => item.count);
                    drawBarChart(labels, counts);
                } else {
                    console.error('Unexpected response format:', data);
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Failed to fetch question summary:', textStatus, errorThrown);
            });
        });
        $.ajax({
            method: "GET",
            url: '../api/api_select_options.php?get_question_list',
            dataType: 'json'
        }).done(function (data) {
            if (Array.isArray(data) && data.length > 0) {
                // Removed default empty option
                $('#questionDboard').empty(); // Clear existing options
                $.each(data, function (index, item) {
                    $('#questionDboard').append($('<option>', {
                        value: item.question_id,
                        text: item.question_desc
                    }));
                });
            } else {
                console.warn('No section data found.');
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.error('AJAX request failed:', textStatus, errorThrown);
        });
        $('#sectionDboard').on('change', function () {
            const selectedSection = $(this).val();
            if (!selectedSection) return;
            $.ajax({
                method: "GET",
                url: '../api/api_main_dashboard.php',
                data: {
                    get_top_achievers: true,
                    section: selectedSection
                },
                dataType: 'json'
            }).done(function (data) {
                if (Array.isArray(data) && data.length > 0) {
                    let achieversHTML = '<ol class="list-group list-group-numbered">';
                    data.forEach(function (student) {
                        let honorText = '';
                        let medalColor = '';
                        const avg = parseFloat(student.general_average);
                        if (avg >= 90 && avg <= 94) {
                            honorText = 'With Honor';
                            medalColor = 'text-orange'; // Custom bronze
                        } else if (avg >= 95 && avg <= 97) {
                            honorText = 'With High Honor';
                            medalColor = 'text-silver'; // Custom silver
                        } else if (avg >= 98 && avg <= 100) {
                            honorText = 'With Highest Honor';
                            medalColor = 'text-gold'; // Custom gold
                        }
                        achieversHTML += `<li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong><i class="fas fa-award me-1 ${medalColor}"></i></strong> ${student.name}
                        ${honorText ? `<small class="text-muted ms-1">(${honorText})</small>` : ''}
                    </div>
                    <span class="badge bg-success rounded-pill">${student.general_average}</span>
                </li>`;
                    });
                    achieversHTML += '</ol>';
                    $('#top-achievers').html(achieversHTML);
                } else {
                    $('#top-achievers').html('<p>No achievers found.</p>');
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Error fetching achievers:', textStatus, errorThrown);
            });
        });
        fetchEnrollmentData();
        function fetchEnrollmentData() {
            $.ajax({
                method: "GET",
                url: "../api/api_main_dashboard.php?get_enrollment_by_year",
                dataType: "json",
                success: function (response) {
                    if (!Array.isArray(response) || response.length === 0) {
                        console.error("No enrollment data found");
                        return;
                    }
                    // Process data for line chart
                    const schoolYears = response.map(item => item.term);
                    const studentCounts = response.map(item => item.student_count);
                    renderEnrollmentLineChart(schoolYears, studentCounts);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error("Error fetching enrollment data:", textStatus, errorThrown);
                }
            });
        }
        function renderEnrollmentLineChart(labels, data) {
            const ctx = document.getElementById('chart-line-tasks').getContext('2d');
            // Destroy previous chart instance if exists
            if (window.enrollmentChart) {
                window.enrollmentChart.destroy();
            }
            window.enrollmentChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Enrollment',
                        data: data,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: false,
                            text: 'Student Enrollment by School Year',
                            font: {
                                size: 16
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return `${context.dataset.label}: ${context.raw.toLocaleString()}`;
                                }
                            }
                        },
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Students'
                            },
                            ticks: {
                                callback: function (value) {
                                    return value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }
        fetchYearLevelAchievementData();
        function fetchYearLevelAchievementData() {
            $.ajax({
                method: "GET",
                url: "../api/api_main_dashboard.php?get_yearlevel_achievement",
                dataType: "json",
                success: function (response) {
                    console.log("API Response:", response); // DEBUG
                    const defaultYears = [7, 8, 9, 10, 11, 12];
                    const promotedCounts = Array(defaultYears.length).fill(0);
                    const conditionalCounts = Array(defaultYears.length).fill(0);
                    const retainedCounts = Array(defaultYears.length).fill(0);
                    if (Array.isArray(response)) {
                        response.forEach(item => {
                            const year = parseInt(item.grade_level);
                            const index = defaultYears.indexOf(year);
                            const action = item.action_taken?.toUpperCase?.() || "";
                            if (index !== -1) {
                                const count = parseInt(item.total_count) || 0;
                                switch (action) {
                                    case 'PROMOTED':
                                        promotedCounts[index] = count;
                                        break;
                                    case 'CONDITIONAL':
                                        conditionalCounts[index] = count;
                                        break;
                                    case 'RETAINED':
                                        retainedCounts[index] = count;
                                        break;
                                }
                            }
                        });
                    }
                    console.log("Promoted:", promotedCounts);
                    console.log("Conditional:", conditionalCounts);
                    console.log("Retained:", retainedCounts);
                    renderAchievementBarChart(defaultYears, promotedCounts, conditionalCounts, retainedCounts);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error("AJAX Error:", textStatus, errorThrown);
                    const years = [7, 8, 9, 10, 11, 12];
                    const zeros = Array(years.length).fill(0);
                    renderAchievementBarChart(years, zeros, zeros, zeros);
                }
            });
        }
        function renderAchievementBarChart(labels, promoted, conditional, retained) {
            const ctx = document.getElementById('chart-line-tasks1');
            if (!ctx) {
                console.error("Canvas element with ID 'chart-line-tasks1' not found!");
                return;
            }
            const chartCtx = ctx.getContext('2d');
            if (window.achievementChart) {
                window.achievementChart.destroy();
            }
            // Combine all data arrays and find the max value
            const allCounts = promoted.concat(conditional, retained);
            const maxValue = Math.max(...allCounts);
            const yMax = Math.ceil(maxValue * 2); // Double the highest count
            window.achievementChart = new Chart(chartCtx, {
                type: 'line',
                data: {
                    labels: labels.map(year => `${year}`),
                    datasets: [
                        {
                            label: 'Promoted',
                            data: promoted,
                            borderColor: '#4CAF50',
                            backgroundColor: '#4CAF50',
                            fill: false,
                            tension: 0.3,
                            pointRadius: 4
                        },
                        {
                            label: 'Conditional',
                            data: conditional,
                            borderColor: '#FFC107',
                            backgroundColor: '#FFC107',
                            fill: false,
                            tension: 0.3,
                            pointRadius: 4
                        },
                        {
                            label: 'Retained',
                            data: retained,
                            borderColor: '#F44336',
                            backgroundColor: '#F44336',
                            fill: false,
                            tension: 0.3,
                            pointRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: false,
                            text: 'Student Achievement by Year Level',
                            font: { size: 16, weight: 'bold' }
                        },
                        tooltip: {
                            callbacks: {
                                label: context => `${context.dataset.label}: ${context.raw}`
                            }
                        },
                        legend: {
                            position: 'top',
                            labels: {
                                boxWidth: 12,
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: yMax, // Dynamic maximum value
                            title: {
                                display: true,
                                text: 'Number of Students',
                                font: { weight: 'bold' }
                            },
                            ticks: {
                                stepSize: 10, // Optional: adjust based on data
                                precision: 0
                            },
                            grid: {
                                display: true,
                                color: 'rgba(0,0,0,0.05)'
                            }
                        },
                        x: {
                            title: {
                                display: false,
                                text: 'Year Level',
                                font: { weight: 'bold' }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
</body>

</html>