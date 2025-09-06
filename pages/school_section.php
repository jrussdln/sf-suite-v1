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
                        Sections
                    </h1>
                </div>
            </div>
            <div class="row" style="margin: 0;">
                <div class="col-sm-6" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        Browse and manage sections of students.
                    </small>
                </div>
                <div class="col-sm-6 text-right" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        Academics / Sections
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
                                <?php
                                if ($_SESSION['access_level'] == 'SA' || $_SESSION['access_level'] == 'SIC') {
                                    ?>
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
                                    <?php
                                }
                                ?>
                                <?php
                                if ($_SESSION['access_level'] == 'TEACHER') {
                                    ?>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="schoolYear"
                                                style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">School
                                                Year</label>
                                            <select class="form-control" id="schoolYear" name="schoolYear" disabled>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6" id="semesterDiv">
                                        <div class="form-group">
                                            <label for="studentSection"
                                                style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">Section</label>
                                            <select class="form-control" id="studentSection" name="studentSection">
                                            </select>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
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
                                            data-target="#addSectionModal">
                                            <i class="fas fa-plus"></i> Add Section
                                        </button>
                                        <button type="button" class="btn btn-info mr-2" id="copySection"
                                            data-toggle="modal" data-target="#copySectionModal">
                                            <i class="fas fa-copy"></i> Copy Section
                                        </button>
                                        <button type="button" class="btn btn-info mr-2" data-toggle="modal"
                                            data-target="#uploadFileModal">
                                            <i class="fas fa-upload"></i> Import
                                        </button>
                                        <button type="button" class="btn btn-info mr-2" id="downloadFileTemplate">
                                            <i class="fas fa-download"></i> Template
                                        </button>
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
                            style="text-transform: uppercase;  margin-bottom: 0; font-weight: normal; color:rgb(255, 255, 255); font-size: 12px; margin-left: 10px; display: block;">
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
include_once('../partials/modals/modal_import.php');
include_once('../partials/modals/modal_section_setup.php');
include_once('../partials/footer.php');
?>
<script>
    $(document).ready(function () {
        var sectionTable = $('#section_table').DataTable({
            responsive: true,
            order: [[0, "asc"]],
            searching: false, // Disable the search bar
            lengthChange: false, // Disable the "Show entries" dropdown
            pageLength: 50,
            ajax: {
                url: '../api/api_section.php',  // You can leave this as it is
                type: 'GET',  // Change from POST to GET
                data: function (d) {
                    // Send the parameters using GET method
                    d.section_list = true;  // Add this to identify the section list request
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
                        <button class="btn btn-primary btn-sm editSection-btn" data-id="${row.SectionId}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-secondary btn-sm viewSection-btn" data-id="${row.SectionId}" data-name="${row.SectionName}">
                            <i class="fas fa-eye"></i>
                        </button>
                    `;
                    }
                }
            ]
        });
        $('#searchSection').click(function (event) {
            event.preventDefault(); // Prevent form submission
            sectionTable.ajax.reload();
        });
        $('#addSectionForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: '../api/api_section.php?add_section',
                type: 'post',
                data: $(this).serialize(),
                dataType: 'json'
            }).then(function (response) {
                if (response.success) {
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success",
                    }).then(() => {
                        $('#addSectionModal').modal('hide');
                        $('#addSectionForm')[0].reset();
                        $('#section_table').DataTable().ajax.reload();
                        if ($('#section_content').css('display') === 'none') {
                            $('#section_content').show(); // Make the section content visible
                            $('#toggleIcon').removeClass('fa-chevron-down').addClass('fa-chevron-up'); // Change toggle icon
                        }
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
        $('#section_table').on('click', '.editSection-btn', function () {
            var SectionId = $(this).data('id');
            $.ajax({
                url: '../api/api_section.php?get_section_data', // Update this URL to fetch user data
                type: 'GET',
                data: { SectionId: SectionId },
                dataType: 'json'
            }).then(function (response) {
                if (response.success) {
                    // Dynamically populate the modal fields
                    $('#e_SectionId').val(response.data.SectionId);
                    $('#e_sectionname').val(response.data.SectionName);
                    $('#e_gradelevel').val(response.data.GradeLevel);
                    $('#e_schoolyear').val(response.data.SchoolYear);
                    $('#e_facility').val(response.data.Facility);
                    $('#e_sectionstrand').val(response.data.SectionStrand);
                    $('#updateSectionModal').modal('show');
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
        $('#updateSectionForm').on('submit', function (e) {
            e.preventDefault();  // Prevent the default form submission
            console.log('Form data:', $(this).serialize()); // Log the serialized form data
            $.ajax({
                url: '../api/api_section.php?edit_section',
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
                        $('#updateSectionModal').modal('hide');
                        $('#section_table').DataTable().ajax.reload();
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
        $('#section_table').on('click', '.viewSection-btn', function () {
            var SectionId = $(this).data('id');
            // Fetch user data based on LRN
            $.ajax({
                url: '../api/api_section.php?get_section_data', // Update this URL to fetch user data
                type: 'GET',
                data: { SectionId: SectionId },
                dataType: 'json'
            }).then(function (response) {
                if (response.success) {
                    // Dynamically populate the modal display elements
                    $('#display_SectionId').val(response.data.SectionId); // Hidden input
                    $('#display_sectionname').val(response.data.SectionName);
                    $('#display_gradelevel').val(response.data.GradeLevel);
                    $('#display_schoolyear').val(response.data.SchoolYear);
                    $('#display_facility').val(response.data.Facility);
                    $('#display_sectionstrand').val(response.data.SectionStrand);
                    $('#viewSectionModal').modal('show');
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
        $('#section_table').on('click', '.viewSection-btn', function () {
            SectionId = $(this).attr('data-id');
            SectionName = $(this).attr('data-name');
            // Update the modal title with the section name
            $('#viewSectionModalLabel').html(`Section Details / ${SectionName}`);
            sectionInfoTable.ajax.reload();
        });
        $('#copy_sy_from').on('change', function () {
            const syFrom = $('#copy_sy_from').val();
            if (syFrom) {
                $.ajax({
                    url: '../api/api_section.php',
                    type: 'POST',
                    data: {
                        fetch_section: true,
                        sy_from: syFrom
                    },
                    dataType: 'json',
                    success: function (response) {
                        const tbody = $('#copy_section_table tbody');
                        tbody.empty();
                        if (response.length === 0) {
                            tbody.append('<tr><td colspan="3" class="text-center">No section found.</td></tr>');
                        } else {
                            response.forEach(section => {
                                const row = `
              <tr>
                <td><input type="checkbox" name="selected_section[]" value="${section.SectionName}"></td>
                <td>${section.SectionName}</td>
                <td>${section.GradeLevel}</td>
                <td>${section.Facility}</td>
              </tr>
            `;
                                tbody.append(row);
                            });
                        }
                    },
                    error: function () {
                        Swal.fire("Error", "Failed to load sections.", "error");
                    }
                });
            }
        });
        $('#selectAllSubjects').on('change', function () {
            $('input[name="selected_section[]"]').prop('checked', this.checked);
        });
        $('#copySectionForm').on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission
            // Collect only checked section IDs
            const selectedSection = [];
            $('input[name="selected_section[]"]:checked').each(function () {
                selectedSection.push($(this).val());
            });
            if (selectedSection.length === 0) {
                Swal.fire("Warning", "Please select at least one section.", "warning");
                return;
            }
            // Serialize other form fields
            const formData = $(this).serializeArray();
            formData.push({ name: "selected_section_json", value: JSON.stringify(selectedSection) });
            $.ajax({
                url: '../api/api_section.php?copy_section', // Ensure your PHP expects this
                type: 'POST',
                data: $.param(formData),
                dataType: 'json'
            }).done(function (response) {
                if (response.success) {
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success"
                    }).then(() => {
                        $('#copySectionForm')[0].reset();
                        $('#copySectionModal').modal('hide');
                        $('#section_table').DataTable().ajax.reload();
                    });
                } else {
                    Swal.fire("Error!", response.message, "error");
                }
            }).fail(function (xhr, status, error) {
                console.error('AJAX Error:', error);
                Swal.fire("Error!", "An unexpected error occurred.", "error");
            });
        });
        var SectionId;
        var sectionInfoTable = $('#section_info_table').DataTable({
            responsive: true,
            order: [[0, "asc"]],
            pageLength: 50,
            processing: true,
            dom: 'Bfrtip',
            buttons: [],
            ajax: {
                url: '../api/api_section.php?student_in_section', // The API URL to get students
                data: function (d) {
                    d.SectionId = SectionId;  // ClassName will be set when the button is clicked
                },
                dataSrc: 'data',  // Ensure the response has the 'data' key to return the student data
                error: function (xhr, error, thrown) {
                    console.error('Error:', error);
                    console.error('Response:', xhr.responseText);
                    alert('Failed to load data');
                }
            },
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: 1 },
                { responsivePriority: 3, targets: 2 },
                { responsivePriority: 4, targets: 3 }
            ],
            columns: [
                { data: 'lrn', title: 'LRN', className: 'align-middle' },
                { data: 'name', title: 'Full Name', className: 'align-middle' },
                { data: 'grade_level', title: 'Grade Level', className: 'align-middle' },
                { data: 'section', title: 'Section', className: 'align-middle' }
            ]
        });
        //TERM
        $.ajax({
            method: "GET",
            url: '../api/api_select_options.php?get_section_strand',
            dataType: 'json'
        }).done(function (data) {
            if (Array.isArray(data) && data.length > 0) {
                $('#e_sectionstrand, #a_sectionstrand').html('<option value="">--</option>');
                $.each(data, function (index, item) {
                    $('#e_sectionstrand, #a_sectionstrand').append($('<option>', {
                        value: item.strand_track,
                        text: item.strand_track
                    }));
                });
            } else {
                console.warn('No data found.');
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.error('AJAX request failed:', textStatus, errorThrown);
        });
        //TERM
        $.ajax({
            method: "GET",
            url: '../api/api_select_options.php?get_school_year_list1',
            dataType: 'json'
        }).done(function (data) {
            if (Array.isArray(data) && data.length > 0) {
                // Clear the dropdowns without adding the default "--" optio
                $('#copy_sy_from, #copy_sy_to').empty();
                $.each(data, function (index, item) {
                    $('#copy_sy_from, #copy_sy_to').append($('<option>', {
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
        $('#downloadFileTemplate').on('click', function () {
            // Ask for confirmation before proceeding
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to download the importing template?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, download it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show SweetAlert's default loading spinner
                    const loadingSwal = Swal.fire({
                        title: 'Exporting...',
                        text: 'Please wait while we export your data.',
                        icon: 'info',  // This will show the default loading spinner
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();  // This is the built-in SweetAlert spinner
                        }
                    });
                    // Perform AJAX request to export data
                    $.ajax({
                        url: '../includes/functions/func_export_template.php',
                        type: 'GET',
                        data: {},  // Send any required data here (if needed)
                        xhrFields: {
                            responseType: 'blob' // Expect a binary response (file download)
                        },
                        success: function (response, status, xhr) {
                            // Create a link element to download the file
                            var blob = new Blob([response], { type: xhr.getResponseHeader('Content-Type') });
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob);
                            link.download = xhr.getResponseHeader('Content-Disposition').split('filename=')[1].replace(/"/g, '');
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                            // Show success message after download
                            Swal.fire({
                                icon: 'success',
                                title: 'Export Successful',
                                text: 'Your data has been exported successfully.'
                            });
                        },
                        error: function () {
                            // Handle error if AJAX fails
                            Swal.fire({
                                icon: 'error',
                                title: 'Export Failed',
                                text: 'There was an error exporting your data.'
                            });
                        },
                        complete: function () {
                            // Close the loading Swal after the AJAX request is complete
                            loadingSwal.close();
                        }
                    });
                }
            });
        });
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
    //INPUT FIELD NG IMPORT MODAL
    document.querySelector(".custom-file-input").addEventListener("change", function (event) {
        let fileName = event.target.files[0] ? event.target.files[0].name : "Choose file...";
        event.target.nextElementSibling.innerText = fileName;
    });
</script>
</body>

</html>