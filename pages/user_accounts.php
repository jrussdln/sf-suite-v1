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
                        User Administration
                    </h1>
                </div>
            </div>
            <div class="row" style="margin: 0;">
                <div class="col-sm-6" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        Browse and manage user account records of students and teachers.
                    </small>
                </div>
                <div class="col-sm-6 text-right" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        User Administration /
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
                                        <label for="userStatus"
                                            style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">User
                                            State</label>
                                        <select class="form-control" id="userStatus" name="userStatus">
                                            <option value="" disabled selected>--</option>
                                            <option value="ACTIVE">ACTIVE</option>
                                            <option value="BLOCKED">BLOCKED</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="userRole"
                                            style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">Role</label>
                                        <select class="form-control" id="userRole" name="userRole">
                                            <option value="">--</option>
                                            <option value="TEACHER">TEACHER</option>
                                            <option value="SA">SCHOOL ADMINISTRATOR</option>
                                            <option value="STUDENT">STUDENT</option>
                                            <option value="SIC">SCHOOL ICT COORDINATOR</option>
                                            <option value="LMP">LEARNING MATERIAL PERSONNEL</option>
                                            <option value="HNP">HEALTH AND NUTRITION PERSONNEL</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="userStatus1"
                                            style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">Status</label>
                                        <select class="form-control" id="userStatus1" name="userStatus1">
                                            <option value="" disabled selected>--</option>
                                            <option value="Active">ACTIVE NOW</option>
                                            <option value="Inactive">INACTIVE</option>
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
                                            data-target="#addUserModal">
                                            <i class="fas fa-plus"></i>Add user
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
                            style="text-transform: uppercase; margin-bottom: 0; font-weight: normal; color:rgb(255, 255, 255); font-size: 12px; margin-left: 10px; display: block;">
                            User Accounts</h1>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="users_table" class="table table-bordered table-striped table-hover"
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
include_once('../partials/footer.php');
?>
<script>
    $(document).ready(function () {
        var usersTable = $('#users_table').DataTable({
            responsive: true,
            order: [[0, "asc"]],
            processing: true,
            dom: 'Bfrtip',
            pageLength: 50,
            buttons: [],
            ajax: {
                url: '../api/api_users.php',  // Ensure the path is correct
                data: function (d) {
                    d.users_table = true;  // Pass the 'users_table' flag
                    d.userRole = $('#userRole').val();  // Pass selected role
                    d.userStatus = $('#userStatus').val();  // Pass selected status
                    d.userStatus1 = $('#userStatus1').val();
                },
                dataSrc: 'data'  // Use 'data' to access the array inside the response
            },
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: 3 }
            ],
            columns: [
                { data: 'Identifier', title: 'Identifier', className: 'text-center align-middle' },
                {
                    data: null,
                    title: 'Student Name',
                    className: 'align-middle text-start',
                    render: function (data, type, row) {
                        // Check if any part of the name is null or undefined, and replace with "No Name"
                        let fullName = `${row.UserLName || 'No Name'}`;
                        if (row.UserFName) fullName += ` ${row.UserFName}`;
                        if (row.UserMName) fullName += ` ${row.UserMName}`;
                        if (row.UserEName) fullName += ` ${row.UserEName}`;
                        let statusText = "Active now";
                        let statusClass = "bg-success font-weight-lighter";
                        if (row.user_status !== "Active" && row.updated_at) {
                            let updatedTime = new Date(row.updated_at);
                            let currentTime = new Date();
                            let diffInSeconds = Math.floor((currentTime - updatedTime) / 1000);
                            let timeAgo = "";
                            if (diffInSeconds < 60) {
                                timeAgo = `${diffInSeconds} sec ago`;
                            } else if (diffInSeconds < 3600) {
                                timeAgo = `${Math.floor(diffInSeconds / 60)} min ago`;
                            } else if (diffInSeconds < 86400) {
                                timeAgo = `${Math.floor(diffInSeconds / 3600)} hr${Math.floor(diffInSeconds / 3600) > 1 ? 's' : ''} ago`;
                            } else if (diffInSeconds < 604800) {
                                timeAgo = `${Math.floor(diffInSeconds / 86400)} day${Math.floor(diffInSeconds / 86400) > 1 ? 's' : ''} ago`;
                            } else if (diffInSeconds < 2592000) {
                                timeAgo = `${Math.floor(diffInSeconds / 604800)} week${Math.floor(diffInSeconds / 604800) > 1 ? 's' : ''} ago`;
                            } else if (diffInSeconds < 31536000) {
                                timeAgo = `${Math.floor(diffInSeconds / 2592000)} month${Math.floor(diffInSeconds / 2592000) > 1 ? 's' : ''} ago`;
                            } else {
                                timeAgo = `${Math.floor(diffInSeconds / 31536000)} year${Math.floor(diffInSeconds / 31536000) > 1 ? 's' : ''} ago`;
                            }
                            statusText = `Active ${timeAgo}`;
                            statusClass = "bg-primary font-weight-lighter";
                        }
                        let statusBadge = `<span class="badge ${statusClass} ms-2">${statusText}</span>`;
                        return `<div class="d-inline-block text-start">${fullName} ${statusBadge}</div>`;
                    }
                },
                { data: 'Gender', title: 'Gender', className: 'align-middle' },
                { data: 'Role', title: 'Role', className: 'align-middle' },
                { data: 'email', title: 'Email Address', className: 'align-middle' },
                { data: 'username', title: 'Username', className: 'align-middle' },
                {
                    data: null,
                    title: 'Action',
                    className: 'text-center',
                    orderable: false,
                    render: function (data, type, row) {
                        let editButton = `
                    <button class="tablebtn btn-success btn-sm userProfile-btn" data-id="${row.Identifier}">
                        <i class="fas fa-edit"></i>
                    </button>`;
                        let blockButton = `
                    <button class="tablebtn btn-warning btn-sm userBlock-btn" data-id="${row.Identifier}">
                        <i class="fas fa-lock"></i>
                    </button>`;
                        let unblockButton = `
                    <button class="btn btn-danger btn-sm userUnblock-btn" data-id="${row.Identifier}">
                        <i class="fas fa-unlock"></i>
                    </button>`;
                        // Check if user_status_access is null or empty
                        if (!row.user_account_access) {
                            return editButton + blockButton; // Show only Edit & Block
                        } else {
                            return editButton + unblockButton; // Show Edit & Unblock
                        }
                    }
                }
            ],
            initComplete: function () {
                // Add custom styles or tooltips after table initialization
                $('.userProfile-btn').tooltip({ title: 'Edit Profile', placement: 'top' });
                $('.userBlock-btn').tooltip({ title: 'Block User', placement: 'top' });
                $('.userUnblock-btn').tooltip({ title: 'Unblock User', placement: 'top' });
            }
        });
        $('#addUserForm').submit(function (e) {
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
                    let formData = new FormData(this);
                    $.ajax({
                        url: '../api/api_users.php?add_users',  // Ensure this URL matches the actual endpoint on your server
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
                                    $('#addUserModal').modal('hide');
                                    $('#users_table').DataTable().ajax.reload();  // Adjust table ID if needed
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
        $('#users_table').on('click', '.userProfile-btn', function () {
            var identifier = $(this).data('id');
            // Fetch user data based on LRN
            $.ajax({
                url: '../api/api_users.php?get_user_data',
                type: 'GET',
                data: { identifier: identifier },
                dataType: 'json'
            }).then(function (response) {
                if (response.success) {
                    // Dynamically populate the modal fields
                    $('#uap_identifier').val(response.data.Identifier);
                    $('#uap_user_email').val(response.data.email);
                    $('#uap_user_fname').val(response.data.UserFName);
                    $('#uap_user_mname').val(response.data.UserMName);
                    $('#uap_user_lname').val(response.data.UserLName);
                    $('#uap_user_ename').val(response.data.UserEName);
                    $('#uap_gender').val(response.data.Gender);
                    $('#uap_birthdate').val(response.data.BirthDate);
                    $('#uap_role').val(response.data.Role);
                    $('#uap_username').val(response.data.username);
                    // Show the profile modal
                    $('#userAccProfileModal').modal('show');
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
        $('#userAccProfileForm').on('submit', function (e) {
            e.preventDefault();  // Prevent the default form submission
            $.ajax({
                url: '../api/api_users.php?edit_user',  // Update this URL as necessary
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
                        $('#userAccProfileModal').modal('hide');  // Hide the modal
                        // Optionally, refresh the page or update the UI
                        $('#users_table').DataTable().ajax.reload();
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
        $('#users_table').on('click', '.userDelete-btn', function () {
            const identifier = $(this).data('id'); // Get the LRN from the button's data attribute
            // Show SweetAlert confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, unregister it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send DELETE request using AJAX
                    $.ajax({
                        url: `../api/api_users.php?delete_user=true&identifier=${identifier}`, // Pass the LRN as a query parameter
                        type: 'DELETE', // HTTP method
                        success: function (response) {
                            if (response.success) {
                                Swal.fire(
                                    'Removed!',
                                    'The user account removed successfully.',
                                    'success'
                                );
                                // Optionally, refresh the DataTable to reflect changes
                                $('#users_table').DataTable().ajax.reload();
                            } else {
                                Swal.fire(
                                    'Error!',
                                    `Failed to remove user: ${response.message}`,
                                    'error'
                                );
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Error:', error);
                            Swal.fire(
                                'Error!',
                                'An error occurred while unregistering the user.',
                                'error'
                            );
                        },
                    });
                }
            });
        });
        $('#searchSection').click(function (event) {
            event.preventDefault();
            usersTable.ajax.reload();
        });
        $(document).ready(function () {
            $(document).on('click', '.userBlock-btn', function () {
                let userId = $(this).data('id');
                Swal.fire({
                    title: 'Block User',
                    text: "Are you sure you want to block this user?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, block it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '../api/api_users.php',
                            type: 'POST',
                            data: { action: 'block_user', userId: userId },
                            dataType: 'json', // Ensure JSON response parsing
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire('Blocked!', 'The user has been blocked.', 'success');
                                    $('#users_table').DataTable().ajax.reload(); // Refresh table
                                } else {
                                    Swal.fire('Error!', response.message || 'Failed to block user.', 'error');
                                }
                            },
                            error: function (xhr, status, error) {
                                console.error(xhr.responseText); // Debugging
                                Swal.fire('Error!', 'Something went wrong.', 'error');
                            }
                        });
                    }
                });
            });
        });
        $(document).on('click', '.userUnblock-btn', function () {
            let userId = $(this).data('id');
            Swal.fire({
                title: 'Unblock User',
                text: "Are you sure you want to unblock this user?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, unblock it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../api/api_users.php',
                        type: 'POST',
                        data: { action: 'unblock_user', userId: userId },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                Swal.fire('Unblocked!', 'The user has been unblocked.', 'success');
                                $('#users_table').DataTable().ajax.reload();
                            } else {
                                Swal.fire('Error!', response.message || 'Failed to unblock user.', 'error');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                        }
                    });
                }
            });
        });
    });
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