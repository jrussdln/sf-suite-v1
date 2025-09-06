<?php
session_start();
require_once('../includes/initialize.php');
require_once('../includes/db_config.php');
date_default_timezone_set('Asia/Manila');
include_once(PARTIALS_PATH . 'header.php');
include('../includes/navbar.php');
// Allowe access levels for manual search
$allowedAccessLevels = ['SA', 'SIC', 'LMP', 'HNP', 'TEACHER'];
$userAccessLevel = $_SESSION['access_level'] ?? '';
?>
<div class="content-wrapper">
  <section class="content-header" style="margin: 0; padding: 8px 10px; background-color: #f8f9fa;">
    <div class="container-fluid" style="padding: 0;">
      <div class="row align-items-center" style="margin: 0;">
        <div class="col-sm-6" style="padding: 0;">
          <h1 style="font-size: 1.2rem; margin: 2px 0; color: #343a40;">
            Student Analytics
          </h1>
        </div>
      </div>
      <div class="row" style="margin: 0;">
        <div class="col-sm-6" style="padding: 0;">
          <small style="font-size: 0.85rem; color: #6c757d;">
            View and analyze student's basic information, academic performance, and summarized insights.
          </small>
        </div>
        <div class="col-sm-6 text-right" style="padding: 0;">
          <small style="font-size: 0.85rem; color: #6c757d;">
            Student Analytics /
          </small>
        </div>
      </div>
    </div>
  </section>
  <?php if (in_array($userAccessLevel, $allowedAccessLevels)) { ?>
    <!-- Search Form Section for allowed access levels -->
    <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card card-primary" style="background: -webkit-linear-gradient(to right, #281E5D, #1565C0, #b92b27); 
                          background: linear-gradient(to right, #281E5D, #1565C0); 
                          color: #ffffff;">
            <div class="card-body">
              <form id="searchForm" method="POST" action="">
                <div class="row">
                  <div class="col-2">
                    <div class="form-group">
                      <button type="submit" class="btn btn-success btn-block">
                        Search <i class="fas fa-search"></i>
                      </button>
                    </div>
                  </div>
                  <div class="col-10">
                    <div class="form-group">
                      <input type="text" class="form-control" id="lrn" name="lrn"
                        placeholder="Enter Learner Reference Number">
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  <?php } ?>
  <!-- Main Content Section -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <!-- Left Column -->
        <div class="col-md-3">
          <div class="card card-primary card-outline">
            <div class="card-body box-profile">
              <div class="text-center">
                <img class="profile-user-img img-fluid img-circle" src="../dist/img/profile_icon.png" alt="">
              </div>
              <h3 class="profile-name text-center"
                style="font-size: 1rem; font-weight: lighter; margin-bottom:0;margin-top:5px;"></h3>
              <p class="text-muted text-center">Student</p>
              <ul class="list-group list-group-unbordered mb-3">
                <li class="list-group-item">
                  <b>Proficiency</b> <a class="float-right" id="gradeAverageDisplay">0%</a>
                </li>
                <li class="list-group-item">
                  <b>Passed Subjects</b> <a class="float-right" id="passedSubjectsDisplay">0</a>
                </li>
                <li class="list-group-item">
                  <b>Failed Subjects</b> <a class="float-right" id="failedSubjectsDisplay">0</a>
                </li>
              </ul>
              <a href="javascript:void(0);" class="btn btn-primary btn-block" id="showReportCard"><b>View Grade</b></a>
            </div>
          </div>
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">About Me</h3>
            </div>
            <div class="card-body">
              <strong><i class="fas fa-book mr-1"></i> Section(Current)</strong>
              <p class="text-muted" id="sectionDisplay" style="font-size: .900rem; font-weight: light;"></p>
              <hr>
              <strong><i class="fas fa-map-marker-alt mr-1"></i> Location</strong>
              <p class="text-muted" id="locationDisplay" style="font-size: .900rem; font-weight: light;"></p>
              <hr>
            </div>
          </div>
        </div>
        <!-- Right Column -->
        <div class="col-md-9">
          <div class="card">
            <div class="card-header p-2">
              <ul class="nav nav-pills">
                <li class="nav-item">
                  <a class="nav-link" href="#timeline" data-toggle="tab">Timeline</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link active" href="#accountInformation" data-toggle="tab">Account Information</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#reportCard" data-toggle="tab" id="reportCardTab">View Grade</a>
                </li>
              </ul>
            </div>
            <div class="card-body" style="overflow: auto;">
              <!-- Single Tab Content Container -->
              <div class="tab-content">
                <!-- Timeline Tab -->
                <div class="tab-pane" id="timeline">
                  <div class="timeline timeline-inverse" id="timelineContainer"></div>
                </div>
                <!-- Account Information Tab -->
                <div class="tab-pane active" id="accountInformation">
                  <form class="form-horizontal">
                    <div class="form-group row">
                      <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputName" value="" disabled>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                      <div class="col-sm-10">
                        <input type="email" class="form-control" id="inputEmail" value="" disabled>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputName2" class="col-sm-2 col-form-label">Username</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputName2" value="" disabled>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputExperience" class="col-sm-2 col-form-label">LRN</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputExperience" value="" disabled>
                      </div>
                    </div>
                  </form>
                </div>
                <!-- Report Card Tab -->
                <div class="tab-pane" id="reportCard">
                  <h4>Summary of Grade</h4>
                  <div id="reportCardContent">
                    <!-- Dynamic tables will be inserted here -->
                  </div>
                </div>
              </div>
              <!-- End of Tab Content Container -->
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
include_once('../partials/footer.php');
?>
<script>
  $(document).ready(function () {
    // Function to fetch analytics data via AJAX
    function fetchAnalytics(lrn) {
      $.ajax({
        url: '../api/api_analytics.php',
        type: 'POST',
        data: { lrn: lrn },
        dataType: 'json',
        success: function (timelineData) {
          console.log("Timeline data:", timelineData);
          buildTimeline(timelineData);
          buildReportCard(timelineData);
        },
        error: function (xhr, status, error) {
          console.error("Error fetching timeline data:", error);
          // Show SweetAlert for error
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'An error occurred while fetching timeline data.',
          });
        }
      });
      // AJAX call to get account information based on LRN
      $.ajax({
        url: '../api/api_analytics2.php?get_user_info',
        type: 'POST',
        data: { lrn: lrn },
        dataType: 'json',
        success: function (userData) {
          console.log("User data:", userData);
          if (userData) {
            var fullName = (userData.UserLName || '') + ' ' +
              (userData.UserFName || '') + ' ' +
              (userData.UserMName || '') + ' ' +
              (userData.UserEName || '');
            $('#inputName').val(fullName.trim());
            $('#inputEmail').val(userData.email || '');
            $('#inputName2').val(userData.username || '');
            $('#inputExperience').val(userData.lrn || '');
            $('.profile-name').text(fullName);
            $('#sectionDisplay').text(userData.section || 'N/A');
            $('#locationDisplay').text(userData.location || 'N/A');
          } else {
            // Show SweetAlert for no user data
            Swal.fire({
              icon: 'warning',
              title: 'No Data Found',
              text: 'No user data found for the provided LRN.',
            });
          }
        },
        error: function (xhr, status, error) {
          console.error("Error fetching user data:", error);
          // Show SweetAlert for error
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'An error occurred while fetching user data.',
          });
        }
      });
    }
    <?php if (in_array($userAccessLevel, $allowedAccessLevels)) { ?>
      $('#searchForm').on('submit', function (e) {
        e.preventDefault();
        var lrn = $('#lrn').val().trim();
        if (lrn === '') {
          // Use SweetAlert instead of the default alert
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please enter a Learner Reference No.',
          });
          return;
        }
        fetchAnalytics(lrn);
      });
    <?php } else { ?>
      // For others, automatically use the session's identifier
      var lrn = "<?php echo $_SESSION['Identifier']; ?>";
      fetchAnalytics(lrn);
    <?php } ?>
    // Show Report Card tab when button is clicked
    $('#showReportCard').on('click', function () {
      $('#reportCardTab').tab('show');
    });
    // (Include your buildReportCard() and buildTimeline() functions here)
    function buildReportCard(timelineData) {
      var $reportCardContent = $('#reportCardContent');
      $reportCardContent.empty();
      $.each(timelineData, function (index, item) {
        var $schoolYearContainer = $('<div>')
          .addClass('school-year-container')
          .css({
            'margin-bottom': '20px',
            'padding': '10px',
            'border': '1px solid #ddd',
            'border-radius': '4px',
            'background-color': '#f8f9fa'
          });
        $schoolYearContainer.append($('<h5>').text("School Year " + item.school_year));
        if (item.subjects && item.subjects.length > 0) {
          var $table = $('<table>').addClass('table table-bordered');
          var $thead = $('<thead>');
          var $headerRow = $('<tr>');
          var firstSubject = item.subjects[0];
          if (firstSubject.grades && firstSubject.grades.fthq !== undefined) {
            $headerRow
              .append($('<th style="text-align: center;">').text('Subject Code'))
              .append($('<th style="text-align: center;">').text('Subject'))
              .append($('<th>').text('4th Qtr'))
              .append($('<th>').text('3rd Qtr'))
              .append($('<th>').text('2nd Qtr'))
              .append($('<th>').text('1st Qtr'))
              .append($('<th>').text('Average'))
              .append($('<th>').text('Remarks'));
          } else if (firstSubject.grades && firstSubject.grades.scnds !== undefined) {
            $headerRow
              .append($('<th style="text-align: center;">').text('Subject Code'))
              .append($('<th style="text-align: center;">').text('Subject'))
              .append($('<th>').text('2nd Sem'))
              .append($('<th>').text('1st Sem'))
              .append($('<th>').text('Average'))
              .append($('<th>').text('Remarks'));
          } else {
            $headerRow
              .append($('<th style="text-align: center;">').text('Subject Code'))
              .append($('<th style="text-align: center;">').text('Subject'))
              .append($('<th>').text('Grade'))
              .append($('<th>').text('Remarks'));
          }
          $thead.append($headerRow);
          $table.append($thead);
          var $tbody = $('<tbody>');
          $.each(item.subjects, function (i, subject) {
            var gradeVal = parseFloat(subject.average);
            var remark = (!isNaN(gradeVal) && gradeVal > 0)
              ? (gradeVal >= 75 ? 'Passed' : 'Failed')
              : 'Not Available';
            if (subject.grades) {
              if (subject.grades.fthq !== undefined) {
                var $row = $('<tr>')
                  .append($('<td>').text(subject.subject_code).css('text-align', 'center'))
                  .append($('<td>').text(subject.subject_name).css('text-align', 'right'))
                  .append($('<td>').text(subject.grades.fthq))
                  .append($('<td>').text(subject.grades.trdq))
                  .append($('<td>').text(subject.grades.scndq))
                  .append($('<td>').text(subject.grades.fstq))
                  .append($('<td>').text(subject.average))
                  .append($('<td>').text(remark));
                $tbody.append($row);
              } else if (subject.grades.scnds !== undefined) {
                var $row = $('<tr>')
                  .append($('<td>').text(subject.subject_code).css('text-align', 'center'))
                  .append($('<td>').text(subject.subject_name).css('text-align', 'right'))
                  .append($('<td>').text(subject.grades.scnds))
                  .append($('<td>').text(subject.grades.fsts))
                  .append($('<td>').text(subject.average))
                  .append($('<td>').text(remark));
                $tbody.append($row);
              } else {
                var $row = $('<tr>')
                  .append($('<td>').text(subject.subject_code).css('text-align', 'center'))
                  .append($('<td>').text(subject.subject_name).css('text-align', 'right'))
                  .append($('<td>').text(subject.average))
                  .append($('<td>').text(remark));
                $tbody.append($row);
              }
            } else {
              var $row = $('<tr>')
                .append($('<td>').text(subject.subject_code).css('text-align', 'center'))
                .append($('<td>').text(subject.subject_name).css('text-align', 'right'))
                .append($('<td>').text(subject.average))
                .append($('<td>').text(remark));
              $tbody.append($row);
            }
          });
          $table.append($tbody);
          var $tfoot = $('<tfoot>');
          var $gwaRow;
          if (firstSubject.grades && firstSubject.grades.fthq !== undefined) {
            $gwaRow = $('<tr>')
              .append($('<td colspan="7">').html('<strong>GWA</strong>'))
              .append($('<td>').text(item.general_average));
          } else if (firstSubject.grades && firstSubject.grades.scnds !== undefined) {
            $gwaRow = $('<tr>')
              .append($('<td colspan="5">').html('<strong>GWA</strong>'))
              .append($('<td>').text(item.general_average));
          } else {
            $gwaRow = $('<tr>')
              .append($('<td colspan="3">').html('<strong>GWA</strong>'))
              .append($('<td>').text(item.general_average));
          }
          $tfoot.append($gwaRow);
          $table.append($tfoot);
          $schoolYearContainer.append($table);
        } else {
          $schoolYearContainer.append($('<p>').text("No subjects data available."));
        }
        $reportCardContent.append($schoolYearContainer);
      });
    }
    function buildTimeline(data) {
      var timelineContainer = $('#timelineContainer');
      timelineContainer.empty();
      var passedCount = 0;
      var failedCount = 0;
      if (data.length > 0) {
        var total = 0;
        var count = 0;
        data.forEach(function (item) {
          var avg = parseFloat(item.general_average);
          if (isNaN(avg)) { avg = 0; }
          total += avg;
          count++;
        });
        if (count > 0) {
          var overallPercentage = total / count;
          $('#gradeAverageDisplay').text(overallPercentage.toFixed(2) + '%');
        }
        data.forEach(function (item) {
          if (item.subjects && Array.isArray(item.subjects)) {
            item.subjects.forEach(function (subject) {
              var rawAvg = subject.average;
              if (typeof rawAvg === "string") { rawAvg = rawAvg.replace(',', '.'); }
              var subjectAvg = parseFloat(rawAvg);
              if (isNaN(subjectAvg) || subjectAvg < 75) { failedCount++; }
              else { passedCount++; }
            });
          }
        });
        $('#passedSubjectsDisplay').text(passedCount);
        $('#failedSubjectsDisplay').text(failedCount);
        data.forEach(function (item) {
          var schoolYearLabel = '<div class="time-label"><span class="bg-success">School Year ' + item.school_year + '</span></div>';
          var healthItem = item.health ?
            '<div><i class="fas fa-heartbeat bg-danger"></i><div class="timeline-item"><span class="time"><i class="far fa-clock"></i> ' + item.school_year + '</span><h3 class="timeline-header"><a href="#">Health Check</a> conducted</h3><div class="timeline-body">Height: ' + item.health.height + ' cm | Weight: ' + item.health.weight + ' kg | BMI: ' + item.health.bmi + '</div></div></div>' :
            '<div><i class="fas fa-heartbeat bg-secondary"></i><div class="timeline-item"><h3 class="timeline-header">No Health Data Available</h3></div></div>';
          var learningMaterialsItem = '<div><i class="fas fa-book bg-primary"></i><div class="timeline-item"><span class="time"><i class="far fa-clock"></i> ' + item.school_year + '</span><h3 class="timeline-header"><a href="#">Learning Materials</a> distributed</h3><div class="timeline-body">' + (item.learning_materials || 'No Learning Materials Available') + '</div></div></div>';
          
          var generalAverageItem = '<div><i class="fas fa-chart-line bg-info"></i><div class="timeline-item"><span class="time"><i class="far fa-clock"></i> ' + item.school_year + '</span><h3 class="timeline-header"><a href="#">General Average</a> for the Year</h3><div class="timeline-body">' + item.general_average + '</div></div></div>';
          timelineContainer.append(schoolYearLabel + healthItem + learningMaterialsItem + generalAverageItem);
        });
      } else {
        timelineContainer.append('<div><i class="far fa-clock bg-gray"></i><div class="timeline-item"><h3 class="timeline-header">No Records Found</h3></div></div>');
      }
      timelineContainer.append('<div><i class="far fa-clock bg-gray"></i></div>');
    }
  });
</script>
</body>

</html>