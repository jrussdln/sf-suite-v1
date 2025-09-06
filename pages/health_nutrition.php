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
                        Health and Nutrition
                    </h1>
                </div>
            </div>
            <div class="row" style="margin: 0;">
                <div class="col-sm-6" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        Browse and manage health records of students.
                    </small>
                </div>
                <div class="col-sm-6 text-right" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        Records / Health and Nutrition
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
                                <div class="col-6">
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
                            </div>
                            <div class="row">
                                <div class="col-12 d-flex justify-content-between">
                                    <div>
                                        <button type="submit" class="btn btn-success mr-2" id="searchSection"
                                            onclick="toggleSection()">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                    <button id="toggleButton" type="button" class="btn btn-primary"
                                        onclick="toggleSection()">
                                        <i id="toggleIcon" class="fas fa-chevron-down"></i>
                                    </button>
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
                            style="text-transform: uppercase;margin-bottom: 0; font-weight: normal; color:rgb(255, 255, 255); font-size: 12px; margin-left: 10px; display: block;">
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
include_once('../partials/modals/modal_section_setup.php');
include_once('../partials/modals/modal_user_setup.php');
include_once('../partials/modals/modal_health_nutrition.php');
include_once('../partials/footer.php');
?>
<script>
    $(document).ready(function () {
        let selectedSectionName;
        let selectedId;
        var sectionId; // Declare sectionId
        //section table
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
                    d.semester = $('#semester').val();
                },
                dataSrc: 'data'  // Make sure 'data' matches the key returned in the server response    
            },
            columns: [
                { data: 'SectionName', title: 'Section Name', className: 'align-middle' },
                { data: 'GradeLevel', title: 'Grade Level', className: 'align-middle' },
                { data: 'SchoolYear', title: 'School Year', className: 'align-middle' },
                { data: 'Facility', title: 'Facility', className: 'align-middle' },
                {
                    data: 'StudentCount',  // Use the preloaded StudentCount from server
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
                         <button class="btn btn-secondary btn-sm viewHnr-btn" data-id="${row.SectionId}" data-name="${row.SectionName}">
                            <i class="fas fa-eye"></i>
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
        $('#section_table').on('click', '.viewHnr-btn', function () {
            var SectionId = $(this).data('id');
            var SectionName = $(this).data('name');
            // Fetching section data
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
                    if (response.success) {
                        // Populate form fields with section data
                        $('#display_SectionId_hnr').val(response.data.SectionId);
                        $('#display_sectionname_hnr').val(response.data.SectionName);
                        $('#display_gradelevel_hnr').val(response.data.GradeLevel);
                        $('#display_schoolyear_hnr').val(response.data.SchoolYear);
                        $('#display_facility_hnr').val(response.data.Facility);
                        $('#display_semester_hnr').val(response.data.SectionSemester);
                        // Sync health and nutrition records with a new AJAX request
                        $.ajax({
                            url: '../api/api_school_forms.php?sync_hnr',
                            type: 'GET',
                            dataType: 'json', // Ensure the response is JSON
                            data: {
                                SectionName: SectionName,
                                SectionId: SectionId
                            },
                            success: function (res) {
                                if (res.success) {
                                    // Reload DataTable after syncing
                                    $('#section_info_table_hnr').DataTable().ajax.reload();
                                } else {
                                    console.error('Sync Error:', res.message || 'Failed to sync health and nutrition data.');
                                }
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                console.error('AJAX Error:', textStatus, errorThrown);
                                console.error("Raw response:", jqXHR.responseText); // For debugging
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
        //para sa view section info modal
        // Event handler for viewing health and nutrition records
        $('#section_table').on('click', '.viewHnr-btn', function () {
            sectionId = $(this).data('id');
            console.log('Selected Section:', sectionId); // Corrected variable name
            $('#viewSectionHnrModal').modal('show');
            sectionInfoTable.ajax.reload(); // Reload the DataTable
        });
        var sectionInfoTable = $('#section_info_table_hnr').DataTable({
            responsive: true,
            order: [[0, "asc"]],
            processing: true,
            serverSide: false,
            dom: 'Bfrtip',
            buttons: [],
            pageLength: 50,
            ajax: {
                url: '../api/api_school_forms.php?get_hnr_by_section',
                type: 'GET',
                data: function (d) {
                    d.sectionId = sectionId;
                },
                dataSrc: function (json) {
                    if (!sectionId) {
                        // Don't show any alert or error if sectionId is empty
                        return [];
                    }
                    if (!json.success) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: json.message || 'No data available.'
                        });
                        return [];
                    }
                    return json.data;
                }
            },
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: 1 },
                { responsivePriority: 3, targets: 2 },
                { responsivePriority: 4, targets: 3 },
                { responsivePriority: 5, targets: 4 },
                { responsivePriority: 6, targets: 5 }
            ],
            columns: [
                { data: 'lrn', title: 'LRN', className: 'align-middle' },
                { data: 'name', title: 'Full Name', className: 'align-middle' },
                { data: 'age', title: 'Age', className: 'align-middle' },
                { data: 'weight', title: 'Weight', className: 'align-middle' },
                { data: 'height', title: 'Height', className: 'align-middle' },
                { data: 'nutritional_status', title: 'Nutritional Status', className: 'align-middle' },
                { data: 'remarks', title: 'Remarks', className: 'align-middle' },
                {
                    data: null,
                    title: 'Actions',
                    className: 'align-middle',
                    render: function (data, type, row) {
                        return `
                    <button type="button" class="btn btn-sm btn-primary editHnr-btn" data-id="${row.health_nutrition_id}" data-toggle="modal" data-target="#editHealthInfoModal">
                        <i class="fas fa-edit"></i>
                    </button>
                `;
                    }
                }
            ]
        });
        //pag na click ang edit button
        $('#section_info_table_hnr').on('click', '.editHnr-btn', function () {
            const health_nutrition_id = $(this).data('id'); // Get the health nutrition ID from the button's data attribute
            // Fetch health nutrition data based on ID
            $.ajax({
                url: '../api/api_school_forms.php?get_hnrr', // Ensure this URL is correct
                type: 'GET',
                data: { health_nutrition_id: health_nutrition_id },
                dataType: 'json'
            }).then(function (response) {
                if (response.success) {
                    // Dynamically populate the modal fields
                    $('#hnr_health_nutrition_id').val(response.data.health_nutrition_id);
                    $('#hnr_birthdate').val(response.data.birthdate);
                    $('#hnr_age').val(response.data.age);
                    $('#hnr_weight').val(response.data.weight);
                    $('#hnr_height').val(response.data.height);
                    $('#hnr_heightSquared').val(response.data.height_squared);
                    $('#hnr_bmi').val(response.data.bmi);
                    $('#hnr_nutritionalStatus').val(response.data.nutritional_status);
                    $('#hnr_heightForAge').val(response.data.height_for_age);
                    $('#hnr_remarks').val(response.data.remarks);
                    // Show the edit health info modal
                    $('#editHealthInfoModal').modal('show');
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
        //submission ng edit button
        $('#editHealthInfoForm').submit(function (e) {
            e.preventDefault(); // Prevent the default form submission
            // Get form data
            const formData = $(this).serialize(); // Serialize the form data
            // Send form data via AJAX
            $.ajax({
                url: '../api/api_school_forms.php?update_hnr', // Ensure this URL is correct for your server-side endpoint
                type: 'POST', // Use POST to submit form data
                data: formData,
                dataType: 'json', // Expect JSON response
                success: function (response) {
                    if (response.success) {
                        // Handle success
                        Swal.fire({
                            title: "Success!",
                            text: "Health info updated successfully.",
                            icon: "success",
                        }).then(function () {
                            $('#editHealthInfoModal').modal('hide'); // Close the modal$('#updateLmForm')[0].reset(); // Reset form fields
                            $('#section_info_table_hnr').DataTable().ajax.reload(); // Refresh DataTable
                        });
                    } else {
                        // Handle error from the server
                        Swal.fire({
                            title: "Error!",
                            text: response.message, // Display error message from the server
                            icon: "error",
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error); // Log any unexpected errors
                    Swal.fire({
                        title: "Error!",
                        text: "An unexpected error occurred. Please try again.",
                        icon: "error",
                    });
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
    function calculateAge() {
        const birthdateInput = document.getElementById("hnr_birthdate").value;
        const ageInput = document.getElementById("hnr_age");
        if (birthdateInput) {
            const birthDate = new Date(birthdateInput);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            ageInput.value = age;
        } else {
            ageInput.value = "";
        }
    }
    function calculateBMI() {
        const weight = parseFloat(document.getElementById("hnr_weight").value);
        const height = parseFloat(document.getElementById("hnr_height").value);
        const heightSquaredInput = document.getElementById("hnr_heightSquared");
        const bmiInput = document.getElementById("hnr_bmi");
        const nutritionalStatusInput = document.getElementById("hnr_nutritionalStatus");
        if (!isNaN(height) && height > 0) {
            const heightSquared = (height * height).toFixed(2);
            heightSquaredInput.value = heightSquared;
            if (!isNaN(weight) && weight > 0) {
                const bmi = (weight / heightSquared).toFixed(2);
                bmiInput.value = bmi; // Set BMI field
                let status = "";
                if (bmi < 16) {
                    status = "Severely Wasted";
                } else if (bmi >= 16 && bmi < 18.5) {
                    status = "Wasted";
                } else if (bmi >= 18.5 && bmi < 25) {
                    status = "Normal";
                } else if (bmi >= 25 && bmi < 30) {
                    status = "Overweight";
                } else if (bmi >= 30) {
                    status = "Obese";
                }
                nutritionalStatusInput.value = status;
            } else {
                bmiInput.value = "";
                nutritionalStatusInput.value = "";
            }
        } else {
            heightSquaredInput.value = "";
            bmiInput.value = "";
            nutritionalStatusInput.value = "";
        }
    }
    function determineHeightForAge() {
        const birthdateInput = document.getElementById("hnr_birthdate").value;
        const heightInput = parseFloat(document.getElementById("hnr_height").value);
        const heightForAgeSelect = document.getElementById("hnr_heightForAge");
        if (!birthdateInput || isNaN(heightInput) || heightInput <= 0) {
            heightForAgeSelect.value = "";
            return;
        }
        const birthDate = new Date(birthdateInput);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        const heightStandards = {
            2: { severelyStunted: 75, stunted: 85, normal: 95 },
            5: { severelyStunted: 90, stunted: 100, normal: 110 },
            10: { severelyStunted: 115, stunted: 125, normal: 140 },
            15: { severelyStunted: 130, stunted: 145, normal: 165 }
        };
        let category = "Normal";
        for (const ageKey in heightStandards) {
            if (age <= ageKey) {
                const standard = heightStandards[ageKey];
                if (heightInput < standard.severelyStunted) {
                    category = "Severely Stunted";
                } else if (heightInput < standard.stunted) {
                    category = "Stunted";
                } else if (heightInput > standard.normal) {
                    category = "Tall";
                }
                break;
            }
        }
        heightForAgeSelect.value = category;
    }
    document.getElementById("hnr_height").addEventListener("input", function () {
        calculateBMI();
        determineHeightForAge();
    });
    document.getElementById("hnr_weight").addEventListener("input", calculateBMI);
    document.getElementById("hnr_birthdate").addEventListener("change", function () {
        calculateAge();
        determineHeightForAge();
    });
</script>