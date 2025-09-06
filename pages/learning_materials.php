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
                        Learning Materials
                    </h1>
                </div>
            </div>
            <div class="row" style="margin: 0;">
                <div class="col-sm-6" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        Browse and manage learning material records of students.
                    </small>
                </div>
                <div class="col-sm-6 text-right" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        Records / Learning Materials
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
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="schoolYear"
                                            style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">School
                                            Year</label>
                                        <select class="form-control" id="schoolYear" name="schoolYear" disabled>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
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
                                            <button type="button" class="btn btn-danger mr-2" id="enableDistribution">
                                                <i class="fas fa-toggle-off"></i>
                                                D/R
                                            </button>
                                            <button type="button" class="btn btn-success mr-2" id="disableDistribution"
                                                style="display: none;">
                                                <i class="fas fa-toggle-on"></i>
                                                D/R
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
include_once('../partials/modals/modal_learning_material_setup.php');
include_once('../partials/modals/modal_section_setup.php');
include_once('../partials/footer.php');
?>
<script>
    $(document).ready(function () {
        var selectedId; // Declare selectedId at a higher scope
        // Initialize the section table
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
                    d.section_list = true; // Identify the section list request
                    d.schoolYear = $('#schoolYear').val();
                    d.gradeLevel = $('#gradeLevel').val();
                    d.semester = $('#semester').val();
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
                    render: function (data, type, row) {
                        return `
                        <button class="btn btn-secondary btn-sm viewLm-btn" data-id="${row.SectionId}" data-name="${row.SectionName}">
                            <i class="fas fa-eye"></i>
                        </button>
                    `;
                    }
                }
            ]
        });
        // Search button functionality
        $('#searchSection').click(function (event) {
            event.preventDefault();
            sectionTable.ajax.reload();
        });
        // Event handler for viewing learning materials by section
        $('#section_table').on('click', '.viewLm-btn', function () {
            selectedId = $(this).data('id');
            var SectionName = $(this).data('name');
            // Fetch section data
            $.ajax({
                url: '../api/api_section.php?get_section_data',
                type: 'GET',
                data: { SectionId: selectedId },
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
                    if (response.success) {
                        // Populate form fields
                        $('#display_SectionId_lm').val(response.data.SectionId);
                        $('#display_sectionname_lm').val(response.data.SectionName);
                        $('#display_gradelevel_lm').val(response.data.GradeLevel);
                        $('#display_schoolyear_lm').val(response.data.SchoolYear);
                        $('#display_facility_lm').val(response.data.Facility);
                        $('#display_semester_lm').val(response.data.SectionSemester);
                        // Show the modal for viewing learning materials
                        $('#viewSectionLmModal').modal('show');
                        // Sync learning materials
                        $.ajax({
                            url: '../api/api_learning_material.php?sync_lm',
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                SectionName: SectionName,
                                SectionId: selectedId
                            },
                            success: function (res) {
                                if (res.success) {
                                    $('#section_info_table_lm').DataTable().ajax.reload();
                                } else {
                                    console.error('Sync Error:', res.message || 'Failed to sync learning material data.');
                                }
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                console.error('AJAX Error:', textStatus, errorThrown);
                                console.error("Raw response:", jqXHR.responseText);
                            }
                        });
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: response.message || "Failed to fetch class data.",
                            icon: "error",
                        });
                    }
                })
                .catch(function (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        title: "Error!",
                        text: "An unexpected error occurred while fetching class data.",
                        icon: "error",
                    });
                });
        });
        // Initialize the section info table
        var sectionInfoTable = $('#section_info_table_lm').DataTable({
            responsive: true,
            order: [[0, "asc"]],
            processing: true,
            serverSide: false,
            searching: true,
            pageLength: 50,
            dom: 'frtip',
            ajax: {
                url: '../api/api_learning_material.php?get_lm_by_section',
                type: 'GET',
                data: function (d) {
                    d.sectionId = selectedId; // Use the selected section ID
                },
                dataSrc: function (json) {
                    if (!json.success) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: json.message || 'No data available for the selected section.',
                        });
                        return []; // Return an empty array to prevent table rendering
                    }
                    return json.data; // Populate the table with the data
                },
                error: function (xhr, error, thrown) {
                    console.error('Error:', error);
                }
            },
            columns: [
                {
                    data: null,
                    title: '<input type="checkbox" id="selectAll">', // Header checkbox
                    className: 'text-center align-middle', // Centers content in the cell
                    orderable: false,
                    render: function (data, type, row) {
                        return `<div class="d-flex justify-content-center align-items-center h-100">
                    <input type="checkbox" class="row-checkbox m-0" value="${row.lrn},${row.learning_material_id}">
                </div>`;
                    }
                },
                { data: 'lrn', title: 'LRN', className: 'align-middle' },
                { data: 'name', title: 'Full Name', className: 'align-middle' },
                { data: 'section', title: 'Section', className: 'align-middle' },
                {
                    data: null,
                    title: 'Descriptions',
                    className: 'align-middle',
                    render: function (data, type, row) {
                        const descriptions = [];
                        for (let i = 1; i <= 9; i++) {
                            const desc = row[`Desc${i}`];
                            const returned = row[`Returned${i}`];
                            if (desc) {
                                // Use HTML to style the status
                                const status = returned ? '<span style="color: green;">✓</span>' : '<span style="color: red;">X</span>';
                                descriptions.push(`${desc} (${status})`);
                            }
                        }
                        return descriptions.length > 0 ? descriptions.join(', ') : 'No descriptions available'; // Return combined descriptions or a default message
                    }
                },
                {
                    data: null,
                    title: 'Action',
                    className: 'text-center',
                    orderable: false,
                    render: function (data, type, row) {
                        return `<button class="btn btn-primary btn-sm editReturnLm-btn" data-id="${row.learning_material_id}">
                    <i class="fas fa-edit"></i>
                </button>`;
                    }
                }
            ]
        });
        $('#issueButton').on('click', function () {
            var selectedIds = [];
            $('input.row-checkbox:checked').each(function () {
                var values = $(this).val().split(','); // Split if using combined value
                selectedIds.push(values[1]); // Push learning_material_id (second value)
            });
            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Selection',
                    text: 'Please select at least one learning material.',
                });
                return; // Exit if no checkbox is selected
            }
            // Fetch the learning materials for the selected IDs
            $.ajax({
                url: '../api/api_learning_material.php',
                type: 'GET',
                data: {
                    action: 'get_learning_materials_by_ids', // New action to get materials by IDs
                    ids: selectedIds // Pass the selected IDs
                },
                success: function (response) {
                    if (response.success) {
                        // Assuming response.data is an array of learning materials
                        if (response.data.length > 0) {
                            // For simplicity, let's just take the first item
                            var learningMaterial = response.data[0];
                            // Populate the modal fields with the fetched data
                            $('#learning_material_id').val(learningMaterial.learning_material_id);
                            $('#Desc1').val(learningMaterial.Desc1);
                            $('#Desc2').val(learningMaterial.Desc2);
                            $('#Desc3').val(learningMaterial.Desc3);
                            $('#Desc4').val(learningMaterial.Desc4);
                            $('#Desc5').val(learningMaterial.Desc5);
                            $('#Desc6').val(learningMaterial.Desc6);
                            $('#Desc7').val(learningMaterial.Desc7);
                            $('#Desc8').val(learningMaterial.Desc8);
                            $('#Desc9').val(learningMaterial.Desc9);
                            // Show the modal
                            $('#updateLmModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'info',
                                title: 'No Data',
                                text: 'No learning materials found for the selected IDs.',
                            });
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to fetch learning materials.',
                        });
                    }
                },
                error: function (xhr, error, thrown) {
                    console.error('Error:', xhr); // Log the entire xhr object for debugging
                    console.error('Error Type:', error); // Log the error type
                    console.error('Thrown:', thrown); // Log the thrown error
                    // Display a more informative error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: `Failed to load data. Status: ${xhr.status} - ${xhr.statusText}. Response: ${xhr.responseText || 'No response'}. Please try again later.`,
                    });
                }
            });
        });
        //on change ng select all
        $('#section_info_table_lm').on('change', '#selectAll', function () {
            const isChecked = $(this).is(':checked');
            $('.row-checkbox').prop('checked', isChecked);
        });
        //save changes button
        $('#saveChangesBtnn').click(function () {
            var selectedIds = [];
            $('input.row-checkbox:checked').each(function () {
                var values = $(this).val().split(','); // Split if using combined value
                selectedIds.push(values[1]); // Push learning_material_id (second value)
            });
            // Check if any learning material IDs are selected
            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Selection',
                    text: 'Please select at least one learning material.',
                });
                return; // Exit if no checkbox is selected
            }
            // Serialize form data
            var formData = $('#updateLmForm').serialize();
            // Add the selected IDs to the form data
            formData += '&learning_material_ids=' + selectedIds.join(','); // Append selected IDs to form data
            // Make AJAX request to update learning materials
            $.ajax({
                url: '../api/api_learning_material.php?update_learning_material=1', // Add query param
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    console.log("Response:", response); // Log the response for debugging
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                        });
                        $('#updateLmModal').modal('hide'); // Close modal
                        $('#updateLmForm')[0].reset(); // Reset form fields
                        $('#section_info_table_lm').DataTable().ajax.reload(); // Refresh DataTable
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to update learning material.',
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('XHR Response:', xhr.responseText);
                    let errorMessage = 'An error occurred. Please try again later.';
                    // If there's a specific error message in the response, use that
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 500) {
                        errorMessage = 'Internal Server Error. Please check the API.';
                    } else if (xhr.status === 404) {
                        errorMessage = 'API endpoint not found. Check the URL.';
                    } else if (xhr.status === 403) {
                        errorMessage = 'Access denied. You do not have permission.';
                    } else if (xhr.status === 400) {
                        errorMessage = 'Bad request. Please check your input data.';
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error ' + xhr.status,
                        text: errorMessage,
                    });
                }
            });
        });
        //return llearning material click
        $('#section_info_table_lm').on('click', '.editReturnLm-btn', function (event) {
            event.preventDefault(); // Prevent default form submission
            const learningMaterialId = $(this).data('id');
            $.ajax({
                url: '../api/api_learning_material.php?get_lm_details',
                type: 'GET',
                data: { id: learningMaterialId }, // Ensure this matches the PHP parameter
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        // Clear checkbox states before populating
                        for (let i = 1; i <= 9; i++) {
                            $(`#r_check${i}`).prop('checked', false); // Uncheck all checkboxes
                        }
                        // Populate the form fields with response data
                        for (let i = 1; i <= 9; i++) {
                            $(`#r_Desc${i}`).val(response.data[`Desc${i}`]);
                            const returnedValue = response.data[`Returned${i}`];
                            console.log(`Returned Value for Desc${i}:`, returnedValue);
                            const isTimestamp = returnedValue && !isNaN(Date.parse(returnedValue));
                            if (isTimestamp) {
                                $(`#r_check${i}`).prop('checked', true); // Check checkbox if it's a valid timestamp
                            } else {
                                $(`#r_check${i}`).prop('checked', false); // Uncheck if no valid timestamp
                            }
                        }
                        $('#learning_material_id').val(learningMaterialId); // Set hidden input
                        $('#editReturnLmModal').modal('show');
                        // Call the function to toggle checkboxes
                        toggleCheckboxes();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message || 'Failed to fetch learning material details.',
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred while fetching learning material details.',
                    });
                }
            });
        });
        //edit return learning material submission
        $('#editReturnLmForm').on('submit', function (event) {
            event.preventDefault();
            const learningMaterialId = $('#learning_material_id').val();
            const formData = { learning_material_id: learningMaterialId };
            for (let i = 1; i <= 9; i++) {
                formData[`Desc${i}`] = $(`#r_Desc${i}`).val();
                formData[`Returned${i}`] = $(`#r_check${i}`).is(':checked') ? new Date().toISOString() : null;
            }
            $.ajax({
                url: '../api/api_learning_material.php?update_return',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Learning material details have been successfully updated.',
                        }).then(() => {
                            $('#editReturnLmModal').modal('hide'); // Close the modal
                            $('#section_info_table_lm').DataTable().ajax.reload(); // Refresh DataTable
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message || 'Failed to update learning material details.',
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred while updating learning material details.',
                    });
                }
            });
        });
        //checkboxes input
        $('#editReturnLmForm input[type="text"]').on('input', function () {
            toggleCheckboxes();
        });
        //checkboxes on change
        $('#editReturnLmForm input[type="checkbox"]').on('change', function () {
            const index = $(this).attr('id').replace('r_check', ''); // Get the index from the checkbox ID
            const descInput = $(`#r_Desc${index}`);
            if (!$(this).is(':checked')) {
                $(`#Returned${index}`).val(null);
            }
        });
        $.ajax({
            url: '../api/api_learning_material.php?checkStatus', // Endpoint to check status
            type: 'GET', // Use GET method to fetch data
            success: function (response) {
                // Assuming response contains { c_status: 0 or 1 }
                if (response.c_status === 0) {
                    $('#enableDistribution').hide(); // Hide enable button
                    $('#disableDistribution').show(); // Show disable button
                } else {
                    $('#disableDistribution').hide(); // Hide disable button
                    $('#enableDistribution').show(); // Show enable button
                }
            },
            error: function (xhr, status, error) {
                console.error('Error fetching status:', error);
            }
        });
        // Toggle for Distribution
        $('#enableDistribution').click(function () {
            $(this).hide(); // Hide the enable button
            $('#disableDistribution').show(); // Show the disable button
            // Make AJAX call to update the database
            $.ajax({
                url: '../api/api_learning_material.php?disableDistribution',
                type: 'POST', // Use POST method to send data
                data: { c_id: 1, c_status: 0 }, // Data to be sent to the server
                success: function (response) {
                    console.log('Distribution disabled successfully:', response);
                },
                error: function (xhr, status, error) {
                    console.error('Error disabling distribution:', error);
                }
            });
        });
        $('#disableDistribution').click(function () {
            $(this).hide(); // Hide the disable button
            $('#enableDistribution').show(); // Show the enable button
            // Make AJAX call to update the database
            $.ajax({
                url: '../api/api_learning_material.php?enableDistribution',
                type: 'POST', // Use POST method to send data
                data: { c_id: 1, c_status: 1 }, // Data to be sent to the server
                success: function (response) {
                    console.log('Distribution enabled successfully:', response);
                },
                error: function (xhr, status, error) {
                    console.error('Error enabling distribution:', error);
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
    });
    function toggleTermOptions() {
        let gradeLevel = document.getElementById("gradeLevel").value;
        let semesterSelect = document.getElementById("semester");
        if (parseInt(gradeLevel) <= 10) {
            semesterSelect.disabled = true; // Disable the select input
        } else {
            semesterSelect.disabled = false; // Enable the select input
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
    function toggleCheckboxes() {
        for (let i = 1; i <= 9; i++) {
            const descInput = $(`#r_Desc${i}`);
            const checkbox = $(`#r_check${i}`);
            // Disable checkbox if the corresponding description input is empty
            if (descInput.val().trim() === '') {
                checkbox.prop('disabled', true); // Disable the checkbox
                checkbox.prop('checked', false); // Uncheck the checkbox when disabled
            } else {
                checkbox.prop('disabled', false); // Enable the checkbox
            }
        }
    }
</script>
</body>
</html>