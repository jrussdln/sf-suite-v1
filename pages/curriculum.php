<?php
require_once('page_header_sessions.php');
include('../includes/navbar.php');
?>
<!-- Curriculum Setup -->
<div class="content-wrapper">
    <section class="content-header" style="margin: 0; padding: 8px 10px; background-color: #f8f9fa;">
        <div class="container-fluid" style="padding: 0;">
            <div class="row align-items-center" style="margin: 0;">
                <div class="col-sm-6" style="padding: 0;">
                    <h1 style="font-size: 1.2rem; margin: 2px 0; color: #343a40;">
                        Academic Structure
                    </h1>
                </div>
            </div>
            <div class="row" style="margin: 0;">
                <div class="col-sm-6" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        Browse and manage active academic year, curriculum, and strand/track offerings.
                    </small>
                </div>
                <div class="col-sm-6 text-right" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        Academic Structure /
                    </small>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"
                            style="text-transform: uppercase; font-weight: normal; color:rgb(255, 255, 255); font-size: 12px; margin-left: 10px; display: block;">
                            Academic Year</h3>
                        <div class="card-tools d-flex align-items-center">
                            <button type="button" class="btn btn-tool text-left" data-toggle="modal"
                                data-target="#addSchoolYearModal">
                                <h6><i class="fas fa-plus"></i></h6>
                            </button>
                        </div>
                    </div>
                    <section class="content">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="school_year_table" class="table table-bordered table-striped table-hover"
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
    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"
                            style="text-transform: uppercase; font-weight: normal; color:rgb(255, 255, 255); font-size: 12px; margin-left: 10px; display: block;">
                            Curriculum</h3>
                        <div class="card-tools d-flex align-items-center">
                            <button type="button" class="btn btn-tool text-left" data-toggle="modal"
                                data-target="#addCurriculumModal">
                                <h6><i class="fas fa-plus"></i></h6>
                            </button>
                        </div>
                    </div>
                    <section class="content">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="curriculum_table" class="table table-bordered table-striped table-hover"
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
    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"
                            style="text-transform: uppercase; font-weight: normal; color:rgb(255, 255, 255); font-size: 12px; margin-left: 10px; display: block;">
                            Strand/Track</h3>
                        <div class="card-tools d-flex align-items-center">
                            <button type="button" class="btn btn-tool text-left" data-toggle="modal"
                                data-target="#addStrandTrackModal">
                                <h6><i class="fas fa-plus"></i></h6>
                            </button>
                        </div>
                    </div>
                    <section class="content">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="strand_track_table" class="table table-bordered table-striped table-hover"
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
include_once('../partials/modals/modal_curriculum_setup.php');
include_once('../partials/modals/modal_user_setup.php');
include_once('../partials/footer.php');
?>
<script>
    $(document).ready(function () {
        var schoolYearTable = $('#school_year_table').DataTable({
            responsive: true,
            order: [[0, "asc"]],
            processing: true,
            dom: 'Bfrtip',
            buttons: [],
            searching: false, // Disable the search bar
            paging: false,
            info: false,
            ajax: {
                url: '../api/api_curriculum.php?school_year_list', // The API URL
                dataSrc: 'data',
                error: function (xhr, error, thrown) {
                    console.error('Error:', error);
                    console.error('Response:', xhr.responseText);
                    alert('Failed to load data');
                }
            },
            columns: [
                { data: 'sy_term', title: 'Term', className: 'align-middle' },
                {
                    data: 'sy_status',
                    title: 'In-Use',
                    className: 'align-middle',
                    orderable: false,
                    render: function (data, type, row) {
                        const isChecked = data === 'Active' ? 'checked' : '';
                        return `
                    <label class="radio-container">
                        <input type="radio" name="inUse" class="custom-radio" data-id="${row.sy_id}" ${isChecked}>
                        <span class="checkmark"></span>
                    </label>
                `;
                    }
                }
            ],
        });
        $('#school_year_table').on('change', 'input[name="inUse"]', function () {
            var selectedSyId = $(this).data('id');
            Swal.fire({ 
                icon: 'warning',
                title: 'Are you sure?',
                text: 'Do you really want to switch the school year?',
                showCancelButton: true,
                confirmButtonText: 'Yes, switch it!',
                cancelButtonText: 'No, cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../api/api_curriculum.php?update_sy_status',
                        type: 'POST',
                        data: {
                            sy_id: selectedSyId,
                            update_sy_status: true
                        },
                        success: function (response) {
                            if (response.success) {
                                // Brief success dialog with auto-close
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                // Reload the page after a short delay
                                setTimeout(() => {
                                    location.reload();
                                }, 1600);
                            } else {
                                // Show error with OK button
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: response.message
                                });
                            }
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to update status.'
                            });
                        }
                    });
                } else {
                    $('#school_year_table').DataTable().ajax.reload(); // Refresh DataTable
                }
            });
        });
        $('#addSchoolYearForm').on('submit', function (e) {
            e.preventDefault();
            const syTerm = $('#a_sy_term').val().trim();
            if (syTerm.length !== 9) {
                return Swal.fire("Invalid Input!", "School Year must be exactly 9 characters (e.g., 2023-2024).", "warning");
            }
            $.post({
                url: '../api/api_curriculum.php?add_school_year',
                data: $(this).serialize(),
                dataType: 'json',
                success(response) {
                    const icon = response.success ? "success" : "error";
                    Swal.fire(response.success ? "Success!" : "Error!", response.message, icon)
                        .then(() => {
                            if (response.success) {
                                $('#addSchoolYearForm')[0].reset();
                                $('#addSchoolYearModal').modal('hide');
                                location.reload();
                            }
                        });
                },
                error(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    Swal.fire("Duplicate Entry!", "School Year already exists!", "error");
                }
            });
        });

        var curriculumTable = $('#curriculum_table').DataTable({
            responsive: true,
            order: [[0, "asc"]],
            processing: true,
            dom: 'Bfrtip',
            buttons: [],
            paging: false,
            info: false,
            searching: false, // Disable the search bar
            ajax: {
                url: '../api/api_curriculum.php?curriculum_list', // The API URL
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
                { data: 'curriculum_desc', title: 'Description', className: 'align-middle' },
                {
                    data: 'curriculum_status',
                    title: 'In-Use',
                    className: 'align-middle',
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                            <input type="checkbox" class="custom-checkbox" data-id="${row.curriculum_id}" ${data === 'Active' ? 'checked' : ''}>
                        `;
                    }
                },
                {
                    data: null,
                    title: 'Action',
                    className: 'align-middle',
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                            <button class="btn btn-warning btn-sm updateCurriculum-btn" data-id="${row.curriculum_id}">
                                <i class="fas fa-edit"></i> 
                            </button>
                        `;
                    }
                }
            ]
        });
        $('#curriculum_table').on('change', 'input.custom-checkbox', function () {
            let curriculumId = $(this).data('id'); // Get the clicked curriculum ID
            let isChecked = $(this).prop('checked'); // Check if it's selected
            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: isChecked ? 'Do you want to activate this curriculum?' : 'Do you want to deactivate this curriculum?',
                showCancelButton: true,
                confirmButtonText: 'Yes, update!',
                cancelButtonText: 'No, cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../api/api_curriculum.php?update_curriculum_status',
                        type: 'POST',
                        data: {
                            curriculum_id: curriculumId,
                            status: isChecked ? 'Active' : 'Inactive' // Toggle status based on checkbox
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.message
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: response.message
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to update curriculum status.'
                            });
                        }
                    });
                } else {
                    // Restore the previous state if canceled
                    $(this).prop('checked', !isChecked);
                }
            });
        });
        $('#addCurriculumForm').on('submit', function (e) {
            e.preventDefault();  // Prevent the default form submission
            $.ajax({
                url: '../api/api_curriculum.php?add_curriculum',  // Update this URL as necessary
                type: 'post',
                data: $(this).serialize(),  // Serialize the form data
                dataType: 'json'  // Expect JSON response
            }).then(function (response) {
                if (response.success) {
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success",
                    }).then(() => {
                        $('#addCurriculumModal').modal('hide');  // Hide the modal
                        // Optionally, refresh the page or update the UI
                        $('#curriculum_table').DataTable().ajax.reload();
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
        $('#curriculum_table').on('click', '.updateCurriculum-btn', function () {
            var curriculum_id = $(this).data('id');
            $.ajax({
                url: '../api/api_curriculum.php?get_curriculum_data',
                type: 'GET',
                data: { curriculum_id: curriculum_id },
                dataType: 'json'
            }).then(function (response) {
                console.log(response); // Debugging: Log full response
                if (response.success) {
                    var data = response.data;
                    if (!data || !data.curriculum) {
                        Swal.fire({
                            title: "Error!",
                            text: "Curriculum data is missing.",
                            icon: "error",
                        });
                        return;
                    }
                    // Populate curriculum fields
                    $('#curriculum_id').val(data.curriculum.curriculum_id);
                    $('#u_curriculum_desc').val(data.curriculum.curriculum_desc);
                    // Populate subjects dynamically
                    $('#subjects-grade-7').text(data.subjects[7] || 'No subjects available');
                    $('#subjects-grade-8').text(data.subjects[8] || 'No subjects available');
                    $('#subjects-grade-9').text(data.subjects[9] || 'No subjects available');
                    $('#subjects-grade-10').text(data.subjects[10] || 'No subjects available');
                    $('#subjects-grade-11').text(data.subjects[11] || 'No subjects available');
                    $('#subjects-grade-12').text(data.subjects[12] || 'No subjects available');
                    // Show modal
                    $('#updateCurriculumModal').modal('show');
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: response.message || "Unexpected response format.",
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
        
        var strandTrackTable = $('#strand_track_table').DataTable({
            responsive: true,
            order: [[0, "asc"]],
            processing: true,
            dom: 'Bfrtip',
            buttons: [],
            searching: false, // Disable the search bar
            paging: false,
            info: false,
            ajax: {
                url: '../api/api_curriculum.php?strand_track_list', // The API URL
                dataSrc: 'data',
                error: function (xhr, error, thrown) {
                    console.error('Error:', error);
                    console.error('Response:', xhr.responseText);
                    alert('Failed to load data');
                }
            },
            columns: [
                { data: 'strand_track', title: 'Strand/Track', className: 'align-middle' },
                { data: 'description', title: 'Description', className: 'align-middle' },
                {
                    data: 'strand_track_status',
                    title: 'In-Use',
                    className: 'align-middle',
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                        <input type="checkbox" class="custom-checkbox" data-id="${row.id}" ${data === 'Active' ? 'checked' : ''}>
                    `;
                    }
                },
                {
                    data: null,
                    title: 'Action',
                    className: 'align-middle',
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                        <button class="btn btn-warning btn-sm updateStrandTrack-btn" data-id="${row.id}">
                            <i class="fas fa-edit"></i> 
                        </button>
                    `;
                    }
                }
            ],
        });
        $('#addStrandTrackForm').on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission
            // Proceed with AJAX submission if validation passes
            $.ajax({
                url: '../api/api_curriculum.php?add_strand_track',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            title: "Success!",
                            text: response.message,
                            icon: "success"
                        }).then(() => {
                            $('#addStrandTrackForm')[0].reset();
                            $('#addStrandTrackModal').modal('hide');
                            location.reload(); // Refresh UI
                        });
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: response.message,
                            icon: "error"
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', error);
                    Swal.fire({
                        title: "Duplicate Entry!",
                        text: "School Year already exists!",
                        icon: "error"
                    });
                }
            });
        });
        $('#strand_track_table').on('click', '.updateStrandTrack-btn', function () {
            var id = $(this).data('id');
            $.ajax({
                url: '../api/api_curriculum.php?get_strand_track_data',
                type: 'GET',
                data: { id: id },
                dataType: 'json'
            }).then(function (response) {
                console.log(response); // Debugging: Log full response
                if (response.success) {
                    var data = response.data;
                    $('#ust_id').val(data.id);
                    $('#ust_strand_track').val(data.strand_track);
                    $('#ust_description').val(data.description);
                    // Show modal
                    $('#editStrandTrackModal').modal('show');
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: response.message || "Unexpected response format.",
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
        $('#editStrandTrackForm').on('submit', function (e) {
            e.preventDefault();  // Prevent default form submission
            console.log('Form data:', $(this).serialize()); // Debugging: Log form data
            $.ajax({
                url: '../api/api_curriculum.php?edit_strand_track',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json'
            }).done(function (response) {
                console.log('Response:', response); // Debugging: Log AJAX response
                if (response.success) {
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success",
                    }).then(() => {
                        $('#editStrandTrackModal').modal('hide');
                        $('#strand_track_table').DataTable().ajax.reload();
                    });
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: response.message || "An unexpected error occurred.",
                        icon: "error",
                    });
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX Error:', textStatus, errorThrown);
                Swal.fire({
                    title: "Error!",
                    text: "Failed to connect to the server.",
                    icon: "error",
                });
            });
        });
        $('#strand_track_table').on('change', 'input.custom-checkbox', function () {
            let id = $(this).data('id'); // Get the clicked curriculum ID
            let isChecked = $(this).prop('checked'); // Check if it's selected
            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: isChecked ? 'Do you want to activate this strand/track?' : 'Do you want to deactivate this strand/track?',
                showCancelButton: true,
                confirmButtonText: 'Yes, update!',
                cancelButtonText: 'No, cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../api/api_curriculum.php?update_strand_track',
                        type: 'POST',
                        data: {
                            id: id,
                            status: isChecked ? 'Active' : 'Inactive' // Toggle status based on checkbox
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.message
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: response.message
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to update strand/track status.'
                            });
                        }
                    });
                } else {
                    // Restore the previous state if canceled
                    $(this).prop('checked', !isChecked);
                }
            });
        });
        
        $('#updateCurriculumForm').on('submit', function (e) {
            e.preventDefault();  // Prevent the default form submission
            console.log('Form data:', $(this).serialize()); // Log the serialized form data
            $.ajax({
                url: '../api/api_curriculum.php?edit_curriculum',
                type: 'post',
                data: $(this).serialize(),
                dataType: 'json'
            }).then(function (response) {
                console.log('Response:', response); // Log the response
                if (response.success) {
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success",
                    }).then(() => {
                        $('#updateCurriculumModal').modal('hide');
                        $('#curriculum_table').DataTable().ajax.reload();
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
    });
</script>
</body>
</html>