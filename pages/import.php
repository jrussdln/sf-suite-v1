<?php
session_start();
require_once('../includes/initialize.php');
include_once(PARTIALS_PATH . 'header.php');
date_default_timezone_set('Asia/Manila');
$access_level = $_SESSION['access_level'];
if (!isset($_SESSION['access_level'])) {
    header("location: ../index.php");
} else {
    $access_level = $_SESSION['access_level'];
}
?>
<!-- /.navbar -->
<?php
include('../includes/navbar.php');
?>
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Subjects</h3>
                        <div class="card-tools d-flex align-items-center">
                            <div>
                                <!-- Register Here Button -->
                                <button type="button" class="btn btn-tool text-left mr-2" data-toggle="modal"
                                    data-target="#addSubjectModal">
                                    <h6><i class="fas fa-plus"></i>&nbsp;Add Subject</h6>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <section class="content">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="subjects_table" class="table table-bordered table-striped table-hover"
                                style="width: 100%;">
                                <thead class="bg-info" height="40">
                                </thead>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </div>
</div>
</section>
</div>
<?php
include_once('../partials/footer.php');
?>
<script>
    $(document).ready(function () {
        var subjectsTable = $('#subjects_table').DataTable({
            responsive: true,
            order: [[0, "asc"]],
            processing: true,
            dom: 'Bfrtip',
            buttons: [],
            ajax: {
                url: '../api/api_curriculum.php?subjects_list', // The API URL
                dataSrc: 'data',
                error: function (xhr, error, thrown) {
                    console.error('Error:', error);
                    console.error('Response:', xhr.responseText);
                    alert('Failed to load data');
                }
            },
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: 1 },
                { responsivePriority: 3, targets: 2 }
            ],
            columns: [
                { data: 'subject_code', title: 'LRN', className: 'align-middle' },
                { data: 'subject_name', title: 'Subject Name', className: 'align-middle' },
                { data: 'grade_level', title: 'Grade Level', className: 'align-middle' },
                { data: 'weekly_hours', title: 'Weekly Hours', className: 'align-middle' },
                { data: 'core_subject', title: 'Core Subject', className: 'align-middle' },
                { data: 'specialized_subject', title: 'Specialized Subject', className: 'align-middle' },
                { data: 'created_at', title: 'Created', className: 'align-middle' },
                {
                    data: null,
                    title: 'Action',
                    className: 'text-center',
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                        <button class="btn btn-info btn-sm viewCurriculum-btn" data-id="${row.curriculum_id}">View</button>
                        <button class="btn btn-danger btn-sm deleteCurriculum-btn" data-id="${row.curriculum_id}">Delete</button>
                    `;
                    }
                }
            ]
        });
    });
</script>