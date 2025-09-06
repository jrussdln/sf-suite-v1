<footer class="main-footer">
    <div class="float-right d-none d-sm-block">
    </div>
    <strong>&copy; <?php echo date("Y"); ?> </strong> SF-Suite: CCC Development Team
</footer>
<!-- ./wrapper -->
</body>
<?php
include_once('core_javascript.php');
?>
<script>
    $(document).ready(function () {
        // Toggle icon for manageRecordsMenu
        $('#manageRecordsMenu').on('show.bs.collapse', function () {
            $('#sidebarToggleIcon2').removeClass('fa-angle-down').addClass('fa-angle-left');
        });
        $('#manageRecordsMenu').on('hide.bs.collapse', function () {
            $('#sidebarToggleIcon2').removeClass('fa-angle-left').addClass('fa-angle-down');
        });
        // Toggle icon for academicSetupMenu
        $('#academicSetupMenu').on('show.bs.collapse', function () {
            $('#sidebarToggleIcon1').removeClass('fa-angle-down').addClass('fa-angle-left');
        });
        $('#academicSetupMenu').on('hide.bs.collapse', function () {
            $('#sidebarToggleIcon1').removeClass('fa-angle-left').addClass('fa-angle-down');
        });
    });
</script>
<script type="text/javascript">
    // Logout action
    $('[data-action="user_logout"]').on('click', function () {
        Swal.fire({
            title: 'Logout?',
            text: "Select 'Sign out' below if you are ready to end your current session.",
            icon: 'info',  // Change 'type' to 'icon' for SweetAlert2
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sign out',
            cancelButtonText: 'Cancel'  // Optional, for more clarity
        }).then((result) => {
            if (result.isConfirmed) { // SweetAlert2 uses `isConfirmed` instead of `value`
                window.location.href = `../partials/logout.php`;
            }
        });
    });
</script>
<script>
    let inactivityTime = function () {
        let time;
        let timeoutLimit = 300000; // 5 minutes
        function resetTimer() {
            clearTimeout(time);
            time = setTimeout(() => {
                window.location.href = '../partials/logout.php';
            }, timeoutLimit);
        }
        // Events that reset the timer
        window.onload = resetTimer;
        document.onmousemove = resetTimer;
        document.onkeydown = resetTimer;
        document.ontouchstart = resetTimer;
        document.onclick = resetTimer;
    };
    inactivityTime();
</script>

<script>
    $(document).ready(function () {
        // Handle the click event for the user profile button
        $(document).on('click', '.userProfile2-btn', function (e) {
            e.preventDefault();
            const identifier = $(this).data('id');
            if (identifier !== 'No Account') {
                $.ajax({
                    url: '../api/api_users.php?get_user_profile',
                    type: 'GET',
                    data: { identifier: identifier },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            $('#a_identifier').val(response.data.Identifier);
                            $('#a_user_email').val(response.data.email);
                            $('#a_user_fname').val(response.data.UserFName);
                            $('#a_user_mname').val(response.data.UserMName);
                            $('#a_user_lname').val(response.data.UserLName);
                            $('#a_user_ename').val(response.data.UserEName);
                            $('#a_gender').val(response.data.Gender);
                            $('#a_birthdate').val(response.data.BirthDate);
                            $('#a_role').val(response.data.Role);
                            $('#a_username').val(response.data.username);
                            $('#a_password').val(response.data.password);
                        } else {
                            alert(response.message || 'Failed to fetch user details.');
                        }
                    },
                    error: function () {
                        alert('An error occurred while fetching user details.');
                    }
                });
            } else {
                alert('No valid user account linked.');
            }
            $('#userProfileModal').modal('show');
        });
        $('#userProfileForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: '../api/api_users.php?edit_user_profile',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            title: "Success!",
                            text: response.message,
                            icon: "success",
                        }).then(() => {
                            Swal.fire({
                                title: "Do you want to refresh your account?",
                                text: "Refreshing your account will log you out.",
                                icon: "question",
                                showCancelButton: true,
                                confirmButtonText: "Yes, refresh",
                                cancelButtonText: "No, stay",
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = '../index.php';
                                }
                            });
                        });
                    } else {
                        Swal.fire({ title: "Error!", text: response.message, icon: "error" });
                    }
                },
                error: function () {
                    Swal.fire({ title: "Error!", text: "An unexpected error occurred.", icon: "error" });
                }
            });
        });
        $(document).on('click', '.resetPassword-btn', function (e) {
            e.preventDefault();
            const identifier = $(this).data('id');
            if (identifier !== 'No Account') {
                $.ajax({
                    url: '../api/api_users.php?get_username',
                    type: 'GET',
                    data: { identifier: identifier },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            $('#reset_username').val(response.data.username);
                        } else {
                            alert(response.message || 'Failed to fetch user details.');
                        }
                    },
                    error: function () {
                        alert('An error occurred while fetching user details.');
                    }
                });
            } else {
                alert('No valid user account linked.');
            }
            $('#resetPasswordModal').modal('show');
        });
        $('#resetPasswordForm').on('submit', function (e) {
            e.preventDefault();
            const newPassword = $('#new_password').val().trim();
            const retypePassword = $('#retype_password').val().trim();
            $('#new_password, #retype_password').removeClass('is-invalid');
            if (newPassword !== retypePassword) {
                Swal.fire({ title: "Error!", text: "Passwords do not match.", icon: "error" });
                $('#new_password, #retype_password').addClass('is-invalid');
                return;
            }
            $.ajax({
                url: '../api/api_users.php?action=edit_user_password',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        Swal.fire({ title: "Success!", text: response.message, icon: "success" })
                            .then(() => {
                                $('#resetPasswordModal, #userProfileModal').modal('hide');
                                $('#resetPasswordForm')[0].reset();
                            });
                    } else {
                        Swal.fire({ title: "Error!", text: response.message, icon: "error" });
                    }
                },
                error: function () {
                    Swal.fire({ title: "Error!", text: "An unexpected error occurred.", icon: "error" });
                }
            });
        });
        $('#schoolInfoSection').on('click', function () {
            $.ajax({
                url: '../api/api_curriculum.php?get_school_info', // Call the new endpoint
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    if (data) {
                        // Populate the modal fields with the fetched data
                        $('#id').val(data.id);
                        $('#school_id').val(data.school_id);
                        $('#school_name').val(data.school_name);
                        $('#region').val(data.region);
                        $('#division').val(data.division);
                        $('#district').val(data.district);
                        $('#bosy_date').val(data.bosy_date);
                        $('#eosy_date').val(data.eosy_date);
                        $('#school_head').val(data.school_head);
                        $('#school_curriculum').val(data.school_curriculum);
                    } else {
                        alert('No data found');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        });
        $('#updateSchoolInfoForm').on('submit', function (event) {
            event.preventDefault(); // Prevent the default form submission
            // Serialize the form data
            var formData = $(this).serialize();
            // Send the data to the API
            $.ajax({
                url: '../api/api_curriculum.php', // Your API endpoint
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        // Use SweetAlert for success notification
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'School information updated successfully!',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Close the modal
                            $('#updateSchoolInfoModal').modal('hide');
                            // Optionally, refresh the data on the page or perform other actions
                        });
                    } else {
                        // Use SweetAlert for error notification
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to update school information: ' + response.message,
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error updating data:', error);
                    // Use SweetAlert for error notification
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred while updating the school information.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
        $.ajax({
            method: "GET",
            url: '../api/api_select_options.php?get_school_curriculum',
            dataType: 'json'
        }).done(function (data) {
            if (Array.isArray(data) && data.length > 0) {
                $('#school_curriculum').html('<option value="">--</option>');
                $.each(data, function (index, item) {
                    $('#school_curriculum').append($('<option>', {
                        value: item.curriculum_desc,
                        text: item.curriculum_desc
                    }));
                });
            } else {
                console.warn('No school year data found.');
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.error('AJAX request failed:', textStatus, errorThrown);
        });
        $('#refreshButton').on('click', function (e) {
            e.preventDefault();
            Swal.fire({
                title: "Do you want to refresh your account?",
                text: "Refreshing your account will log you out.",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Yes, refresh",
                cancelButtonText: "No, stay",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = './../index.php';
                } else {
                    Swal.fire({ title: "Stay Logged In", text: "You can continue using your account.", icon: "info" });
                }
            });
        });
        let isFetching = false;
        function fetchActiveSchoolYear() {
            if (isFetching) return;
            isFetching = true;
            $.ajax({
                url: '../api/api_curriculum.php?active_school_year=true',
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                    $('#schoolYearText, #schoolYearText1').text(response.active_school_year || 'No active school year found.');
                },
                error: function () {
                    $('#schoolYearText,#schoolYearText1').text('Error fetching school year.');
                },
                complete: function () {
                    isFetching = false;
                }
            });
        }
        fetchActiveSchoolYear();
        setInterval(fetchActiveSchoolYear, 30000);
    });
</script>