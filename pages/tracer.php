<?php
require_once('page_header_sessions.php');
include('../includes/navbar.php');
?>
<div class="content-wrapper">

  <!-- Header Section -->
  <section class="content-header" style="margin: 0; padding: 8px 10px; background-color: #f8f9fa;">
    <div class="container-fluid" style="padding: 0;">
      <div class="row align-items-center" style="margin: 0;">
        <div class="col-sm-6" style="padding: 0;">
          <h1 style="font-size: 1.2rem; margin: 2px 0; color: #343a40;">Alumni Tracer</h1>
        </div>
      </div>
      <div class="row" style="margin: 0;">
        <div class="col-sm-6" style="padding: 0;">
          <small style="font-size: 0.85rem; color: #6c757d;">
            Send surveys to alumni to collect post-grad insights.
          </small>
        </div>
        <div class="col-sm-6 text-right" style="padding: 0;">
          <small style="font-size: 0.85rem; color: #6c757d;">
            Alumni Tracer /
          </small>
        </div>
      </div>
    </div>
  </section>

  <!-- Main Content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">

        <!-- Left Column: Notification Form -->
        <div class="col-lg-6 col-md-12">
          <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
              <h1 style="text-transform: uppercase; margin-bottom: 0; font-weight: normal; font-size: 12px; margin-left: 10px;">Send Notification</h1>
            </div>
            <div class="card-body">
              <form id="notificationForm">
                <div class="form-group">
                  <label for="notificationTitle" class="form-label">Title</label>
                  <select id="notificationTitle" class="form-control" disabled>
                    <option value="Alumni Notice">Alumni Notice</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="notificationSubject" class="form-label">Subject</label>
                  <input type="text" id="notificationSubject" class="form-control" placeholder="Enter notification subject" required>
                </div>
                <div class="form-group">
                  <label for="notificationMessage" class="form-label">Additional Message/Links</label>
                  <textarea id="notificationMessage" class="form-control" placeholder="Enter notification message" rows="3"></textarea>
                </div>
                <div class="form-group">
                  <label for="notificationRecipient" class="form-label">Select Alumni Batch</label>
                  <select id="notificationRecipient" class="form-control" required></select>
                </div>
                <div class="d-flex justify-content-end">
                  <button type="submit" class="btn btn-primary">Send</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <!-- Right Column: History & Messages -->
        <div class="col-lg-6 col-md-12">

          <!-- History Card -->
          <div class="card shadow-sm mb-4" id="historyCard">
            <div class="card-header bg-secondary text-white">
              <h1 class="text-uppercase mb-0" style="font-size: 12px; margin-left: 10px;">History</h1>
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
              <ul id="announcementHistory" class="list-group">
                <li class="list-group-item">No announcements sent yet.</li>
              </ul>
            </div>
            <div class="card-footer text-end">
              <button class="btn btn-primary" id="viewQuestionsBtn">View Questions</button>
              <button class="btn btn-primary" id="viewMessagesBtn">View Messages</button>
            </div>
          </div>

          <!-- Messages Card -->
          <div class="card shadow-sm mb-4" id="allMessagesCard" style="display: none;">
            <div class="card-header bg-info text-white">
              <h1 class="text-uppercase mb-0" style="font-size: 12px; margin-left: 10px;">All Announcements</h1>
            </div>
            <div class="px-3 pt-2">
              <div class="row">
                <div class="col-md-6">
                  <label for="filterRecipient" class="form-label">Filter by Recipient</label>
                  <select id="filterRecipient" class="form-control"></select>
                </div>
                <div class="col-md-6">
                  <label for="filterDate" class="form-label">Filter by Date</label>
                  <input type="date" id="filterDate" class="form-control">
                </div>
              </div>
            </div>
            <div class="card-body" id="allMessagesContainer" style="max-height: 400px; overflow-y: auto;">
              <ul id="allMessagesList" class="list-group">
                <li class="list-group-item">No messages available.</li>
              </ul>
            </div>
            <div class="card-footer text-end">
              <button class="btn btn-secondary" id="backToHistoryBtn">Back to History</button>
            </div>
          </div>

          <!-- Questions Card -->
          <div class="card shadow-sm mb-4" id="allQuestionsCard" style="display: none;">
            <div class="card-header bg-info text-white">
              <h1 class="text-uppercase mb-0" style="font-size: 12px; margin-left: 10px;">All Questions</h1>
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
              <table id="questionsTable" class="table table-striped table-bordered" style="width: 100%;">
                <thead>
                  <tr>
                    <th>Question Description</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- Data loaded via AJAX -->
                </tbody>
              </table>
            </div>
            <div class="card-footer text-end">
              <button class="btn btn-secondary" id="backToHistoryFromQuestionsBtn">Back to History</button>
              <button class="btn btn-primary me-2" id="addQuestBtn" data-toggle="modal" data-target="#addQuestionModal">Add Question</button>
              
            </div>
          </div>





        </div>

      </div>
    </div>
  </section>
</div>

<?php
include_once('../partials/modals/modal_questions.php');
include_once('../partials/modals/modal_user_setup.php');
include_once('../partials/footer.php'); ?>
<script>
$(document).ready(function () {

    // --- INITIAL LOAD ---
    fetchAnnouncements();
    allMessagesList();
    loadSchoolYearOptions();

    // --- FORM SUBMISSION: Send Notification ---
    $("#notificationForm").submit(function(e) {
        e.preventDefault();

        const formData = {
            title: $("#notificationTitle").val(),
            subject: $("#notificationSubject").val(),
            message: $("#notificationMessage").val(),
            school_year: $("#notificationRecipient").val()
        };

        Swal.fire({
            title: 'Sending notification...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            type: "POST",
            url: "send_email_tracer.php",
            data: formData,
            dataType: "json",
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: response.message,
                        confirmButtonText: "OK"
                    });

                    let listItem = `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${formData.title}</strong> - ${formData.subject}<br>
                                ${formData.message}<br>
                                <small>Sent to: ${formData.school_year}</small>
                            </div>
                            <button class="btn btn-danger btn-sm delete-announcement">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </li>`;

                    let historyList = $("#announcementHistory");

                    if (historyList.children().first().text() === "No announcements sent yet.") {
                        historyList.html("");
                    }

                    historyList.prepend(listItem);
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: response.message,
                        confirmButtonText: "OK"
                    });
                }
                $("#notificationForm")[0].reset();
            },
            error: function(xhr) {
                Swal.close();
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "An error occurred: " + xhr.responseText,
                    confirmButtonText: "OK"
                });
            }
        });
    });

    // --- FETCH ANNOUNCEMENTS (Tracer History) ---
    function fetchAnnouncements() {
        $.ajax({
            type: "GET",
            url: "../api/api_announcement.php?get_tracer_history",
            dataType: "json",
            success: function(response) {
                let historyList = $("#announcementHistory");
                historyList.empty();

                if (response.success) {
                    if (response.data.length === 0) {
                        historyList.append('<li class="list-group-item">No announcements sent yet.</li>');
                    } else {
                        response.data.forEach(function(announcement) {
                            let maxLength = 100;
                            let message = announcement.message;
                            let shortMessage = message.length > maxLength ? message.substring(0, maxLength) + '...' : message;

                            let listItem = `
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>${announcement.title}</strong> - ${announcement.subject}<br>
                                        <small>Sent to: ${announcement.recipient} on ${new Date(announcement.created_at).toLocaleString()}</small>
                                    </div>
                                    <button class="btn btn-danger btn-sm delete-announcement" data-id="${announcement.id}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </li>`;
                            historyList.append(listItem);
                        });
                    }
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: response.message,
                        confirmButtonText: "OK"
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "An error occurred while fetching announcements: " + xhr.responseText,
                    confirmButtonText: "OK"
                });
            }
        });
    }

    // --- FETCH ALL MESSAGES WITH FILTERS ---
    function allMessagesList() {
        let recipientFilter = $("#filterRecipient").val();
        let dateFilter = $("#filterDate").val();

        $.ajax({
            type: "GET",
            url: "../api/api_announcement.php",
            data: { 
                all_tracer_announcements: true, 
                recipient: recipientFilter, 
                date: dateFilter 
            },
            dataType: "json",
            success: function(response) {
                let historyList = $("#allMessagesList");
                historyList.empty();

                if (response.success && response.data.length > 0) {
                    response.data.forEach(function(announcement) {
                        let maxLength = 100;
                        let message = announcement.message;
                        let shortMessage = message.length > maxLength ? message.substring(0, maxLength) + '...' : message;

                        let formattedDate = new Date(announcement.created_at).toLocaleString('en-US', {
                            year: 'numeric', 
                            month: 'short', 
                            day: 'numeric', 
                            hour: '2-digit', 
                            minute: '2-digit', 
                            hour12: true
                        });

                        let listItem = `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${announcement.title}</strong> - ${announcement.subject}<br>
                                    <small>Sent to: ${announcement.recipient} on ${formattedDate}</small>
                                </div>
                                <div>
                                    <button class="btn btn-info btn-sm view-announcement" data-id="${announcement.id}" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm delete-announcement" data-id="${announcement.id}" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </li>`;
                        historyList.append(listItem);
                    });
                } else {
                    historyList.append('<li class="list-group-item">No announcements found.</li>');
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "An error occurred while fetching announcements: " + xhr.responseText,
                    confirmButtonText: "OK"
                });
            }
        });
    }

    // --- VIEW SINGLE ANNOUNCEMENT DETAILS ---
    $(document).on("click", ".view-announcement", function () {
        let announcementId = $(this).data("id");

        $.ajax({
            type: "GET",
            url: "../api/api_announcement.php",
            data: { announcement_id: announcementId },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    let announcement = response.data;

                    $("#allMessagesContainer, #backToHistoryBtn").hide();
                    $("#fullMessageView").remove();

                    let fullMessageDiv = `
                        <div id="fullMessageView" class="p-3">
                            <h5>${announcement.title}</h5>
                            <p><strong>Subject:</strong> ${announcement.subject}</p>
                            <p>${announcement.message}</p>
                            <p><small>Sent to: ${announcement.recipient} on ${new Date(announcement.created_at).toLocaleString()}</small></p>
                            <button class="btn btn-secondary" id="backToMessages">Back</button>
                        </div>`;

                    $("#allMessagesCard").append(fullMessageDiv);
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: response.message,
                        confirmButtonText: "OK"
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Failed to load message details.",
                    confirmButtonText: "OK"
                });
            }
        });
    });

    // --- BACK TO ALL MESSAGES FROM DETAIL VIEW ---
    $(document).on("click", "#backToMessages", function () {
        $("#fullMessageView").remove();
        $("#allMessagesContainer, #backToHistoryBtn").show();
    });

    // --- FILTER CHANGE HANDLERS ---
    $("#filterRecipient, #filterDate").on("change", function() {
        allMessagesList();
    });

    // --- DELETE ANNOUNCEMENT HANDLER ---
    $("#announcementHistory, #allMessagesList").on("click", ".delete-announcement", function() {
        const btn = $(this);
        const li = btn.closest("li");
        const announcementId = btn.data("id");

        Swal.fire({
            title: 'Are you sure?',
            text: "Do you really want to delete this announcement?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Deleting announcement...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    type: "POST",
                    url: "../api/api_announcement.php",
                    data: {
                        action: 'delete_announcement',
                        id: announcementId
                    },
                    dataType: "json",
                    success: function(response) {
                        Swal.close();
                        if (response.success) {
                            li.remove();
                            fetchAnnouncements();
                            allMessagesList();
                            Swal.fire({
                                icon: "success",
                                title: "Deleted!",
                                text: "Announcement deleted successfully.",
                                confirmButtonText: "OK"
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: response.message || "Failed to delete announcement.",
                                confirmButtonText: "OK"
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "An error occurred while deleting the announcement: " + xhr.responseText,
                            confirmButtonText: "OK"
                        });
                    }
                });
            }
        });
    });

    // --- LOAD SCHOOL YEAR OPTIONS FOR SELECTS ---
    function loadSchoolYearOptions() {
        $.ajax({
            method: "GET",
            url: '../api/api_personnel.php?get_sy_list',
            dataType: 'json'
        }).done(function(data) {
            if (data && !data.error) {
                $('#notificationRecipient, #filterRecipient').html('<option value="">--</option>');
                $.each(data, function(index, item) {
                    $('#notificationRecipient, #filterRecipient').append(
                        $('<option>', { value: item.sy_term, text: item.sy_term })
                    );
                });
            } else {
                console.error('Error fetching data:', data.error);
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error('AJAX request failed:', textStatus, errorThrown);
        });
    }
// Show Questions, hide others
document.getElementById('viewQuestionsBtn').addEventListener('click', function() {
    document.getElementById('historyCard').style.display = 'none';
    document.getElementById('allMessagesCard').style.display = 'none';
    document.getElementById('allQuestionsCard').style.display = 'block';
});

// Show Messages, hide others
document.getElementById('viewMessagesBtn').addEventListener('click', function() {
    document.getElementById('historyCard').style.display = 'none';
    document.getElementById('allQuestionsCard').style.display = 'none';
    document.getElementById('allMessagesCard').style.display = 'block';
});

// Back to History button inside allMessagesCard
document.getElementById('backToHistoryBtn').addEventListener('click', function() {
    document.getElementById('allMessagesCard').style.display = 'none';
    document.getElementById('allQuestionsCard').style.display = 'none';
    document.getElementById('historyCard').style.display = 'block';
});
document.getElementById('backToHistoryFromQuestionsBtn').addEventListener('click', function() {
    document.getElementById('allQuestionsCard').style.display = 'none';
    document.getElementById('historyCard').style.display = 'block';
});


});
$(document).ready(function () {
  // Initialize the questions DataTable
  var questionsTable = $('#questionsTable').DataTable({
    responsive: true,
    order: [[0, 'asc']],
    pageLength: 10,
    processing: true,
    dom: 'Bfrtip',
    buttons: [],
    ajax: {
      url: '../api/api_announcement.php',
      data: function (d) {
        d.get_questions = true; // tell backend to send questions data
      },
      dataSrc: function (json) {
        if (!json.success) {
          Swal.fire('Error', json.message || 'Failed to load questions', 'error');
          return [];
        }
        return json.data;
      }
    },
    columns: [
      { data: 'question_desc', title: 'Question Description', className: 'align-middle' },
      {
        data: null,
        title: 'Action',
        orderable: false,
        searchable: false,
        className: 'text-center align-middle',
        render: function (data, type, row) {
          return `<button class="btn btn-sm btn-primary edit-question" data-question_id="${row.question_id}" title="Edit">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button class="btn btn-sm btn-info view-choices-btn" data-question_id="${row.question_id}" title="View Choices" style="margin-right:5px;">
                    <i class="fas fa-eye"></i>
                  </button>`;
        }
      }
    ]
  });
  
$('#addQuestionForm').submit(function(e) {
  e.preventDefault();

  $.ajax({
    type: 'POST',
    url: $(this).attr('action'),
    data: $(this).serialize(),
    dataType: 'json',
    success: function(response) {
      if (response.success) {
        Swal.fire('Success', response.message, 'success');
        $('#addQuestionModal').modal('hide');
        $('#questionsTable').DataTable().ajax.reload(); // Refresh table data
        $('#addQuestionForm')[0].reset();
      } else {
        Swal.fire('Error', response.message, 'error');
      }
    },
    error: function() {
      Swal.fire('Error', 'Something went wrong', 'error');
    }
  });
});

$('#questionsTable tbody').on('click', 'button.edit-question', function () {
  var questionId = $(this).data('question_id');

  var $row = $(this).closest('tr');
  // Use the DataTable API to get row data instead of DOM traversal:
  var data = questionsTable.row($row).data();

  // Populate the modal inputs
  $('#edit_question_id').val(questionId);
  $('#edit_question_desc').val(data.question_desc);

  // Show the modal
  $('#editQuestionModal').modal('show');
});
   $('#editQuestionForm').submit(function(e) {
  e.preventDefault();

  $.ajax({
    type: 'POST',
    url: '../api/api_announcement.php', // or the correct path
    data: $(this).serialize() + '&action=update_question', // add action flag
    dataType: 'json',
    success: function(response) {
      if (response.success) {
        Swal.fire('Success', response.message, 'success');
        $('#editQuestionModal').modal('hide');
        $('#questionsTable').DataTable().ajax.reload(); // Refresh table data
      } else {
        Swal.fire('Error', response.message, 'error');
      }
    },
    error: function() {
      Swal.fire('Error', 'Something went wrong', 'error');
    }
  });
});

// Initialize choicesTable variable here for later initialization
  var choicesTable;
// Handler for the View button click
  $('#questionsTable tbody').on('click', 'button.view-choices-btn', function () {
    var questionId = $(this).data('question_id');

    // Set the question ID in the modal
    $('#modal_question_id').val(questionId); // Set the hidden input for question ID

    // Show modal
    $('#choicesModal').modal('show');

    // If choicesTable is already initialized, destroy before reinitializing with new ajax params
    if ($.fn.DataTable.isDataTable('#choicesTable')) {
      choicesTable.destroy();
    }

    // Initialize choicesTable for this question_id
    choicesTable = $('#choicesTable').DataTable({
      responsive: true,
      processing: true,
      searching: false,
      paging: true,
      pageLength: 5,
      ajax: {
        url: '../api/api_announcement.php',
        type: 'GET',
        data: { get_choices: true, question_id: questionId },
        dataSrc: function (json) {
          if (!json.success) {
            Swal.fire('Error', json.message || 'Failed to load choices', 'error');
            return [];
          }
          return json.data;
        }
      },
      columns: [
        { data: 'choices_content', title: 'Choices', className: 'align-middle' },
        {
          data: null,
          title: 'Action',
          orderable: false,
          searchable: false,
          className: 'text-center align-middle',
          render: function(data, type, row) {
            return `
              <button class="btn btn-sm btn-danger delete-choice-btn" data-choices_id="${row.choices_id}" title="Delete">
                <i class="fas fa-trash-alt"></i>
              </button>
            `;
          }
        }
      ]
    });
  });
  // Delete choice button click handler
$('#choicesTable tbody').on('click', 'button.delete-choice-btn', function () {
  var choiceId = $(this).data('choices_id');

  Swal.fire({
    title: 'Are you sure?',
    text: "This choice will be deleted permanently!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: '../api/api_announcement.php',
        type: 'POST',
        dataType: 'json',
        data: {
          action: 'delete_choice',
          choice_id: choiceId
        },
        success: function(response) {
          if (response.success) {
            Swal.fire('Deleted!', response.message, 'success');
            // Reload choices table to reflect deletion
            $('#choicesTable').DataTable().ajax.reload(null, false);
          } else {
            Swal.fire('Error', response.message || 'Failed to delete choice.', 'error');
          }
        },
        error: function() {
          Swal.fire('Error', 'Failed to communicate with server.', 'error');
        }
      });
    }
  });
});
// Handler for the Add Choice button click in the addChoicesModal
 // Add Choice button click handler
  $('#submitChoiceBtn').click(function (e) {
    e.preventDefault();

    const choiceContent = $('#choices_content').val().trim();
    const questionId = $('#modal_question_id').val(); // Get the question ID

    // Validate inputs
    if (!questionId) {
      Swal.fire('Error', 'Question ID is required.', 'error');
      return;
    }

    if (!choiceContent) {
      Swal.fire('Error', 'Choice content is required.', 'error');
      $('#choices_content').focus();
      return;
    }

    // AJAX request to add choice
    $.ajax({
      type: 'POST',
      url: '../api/api_announcement.php',
      data: {
        action: 'add_choice',
        question_id: questionId,
        choices_content: choiceContent
      },
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          Swal.fire('Success', response.message, 'success');
          $('#addChoicesModal').modal('hide');
          $('#choicesTable').DataTable().ajax.reload(null, false); // Refresh choices table
          $('#choices_content').val(''); // Clear input
        } else {
          Swal.fire('Error', response.message, 'error');
        }
      },
      error: function () {
        Swal.fire('Error', 'Something went wrong', 'error');
      }
    });
  });
});

</script>

</body>
</html>