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
                        Announcements
                    </h1>
                </div>
            </div>
            <div class="row" style="margin: 0;">
                <div class="col-sm-6" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        Create and send announcements for student and teachers.
                    </small>
                </div>
                <div class="col-sm-6 text-right" style="padding: 0;">
                    <small style="font-size: 0.85rem; color: #6c757d;">
                        Announcements /
                    </small>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Notification Form Section (Left Column) -->
            <div class="col-lg-6 col-md-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h1 style="text-transform: uppercase; margin-bottom: 0; font-weight: normal; color:rgb(255, 255, 255); font-size: 12px; margin-left: 10px; display: block;">Send Notification</h1>
                    </div>
                    <div class="card-body">
                        <form id="notificationForm">
                            <div class="form-group">
                                <label for="notificationTitle" style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">Title</label>
                                <select id="notificationTitle" class="form-control" required>
                                    <option value="">-</option>
                                    <option value="Reminder">Alert</option>
                                    <option value="Event/Activity Notice">Event/Activity Notice</option>
                                    <option value="Reminders">Reminders</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="notificationSubject" style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">Subject</label>
                                <input type="text" id="notificationSubject" class="form-control" placeholder="Enter notification subject" required>
                            </div>
                            <div class="form-group">
                                <label for="notificationMessage" style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">Message</label>
                                <textarea id="notificationMessage" class="form-control" placeholder="Enter notification message" rows="3" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="notificationRecipient" style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">Send To</label>
                                <select id="notificationRecipient" class="form-control" required></select>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Send Notification</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- History & Messages Section (Right Column) -->
            <div class="col-lg-6 col-md-12">
                <!-- Announcement History Card -->
                <div class="card shadow-sm mb-4" id="historyCard">
                    <div class="card-header bg-secondary text-white">
                        <h1 style="text-transform: uppercase; margin-bottom: 0; font-weight: normal; color:rgb(255, 255, 255); font-size: 12px; margin-left: 10px; display: block;">History</h1>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        <ul id="announcementHistory" class="list-group">
                            <!-- Announcements will be dynamically inserted here -->
                            <li class="list-group-item" >No announcements sent yet.</li>
                        </ul>
                    </div>
                    <div class="card-footer text-end">
                        <button class="btn btn-primary" id="viewMessagesBtn">View Messages</button>
                    </div>
                </div>
                <!-- All Messages Card -->
                <div class="card shadow-sm mb-4" id="allMessagesCard" style="display: none;">
                    <div class="card-header bg-info text-white">
                        <h1 style="text-transform: uppercase; margin-bottom: 0; font-weight: normal; color:rgb(255, 255, 255); font-size: 12px; margin-left: 10px; display: block;">All Announcements</h1>
                    </div>
                    <!-- Filters Section -->
                    <div class="px-3 pt-2">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="filterRecipient" class="form-label" style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">Filter by Recipient</label>
                                <select id="filterRecipient" class="form-control">
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="filterDate" class="form-label" style="text-transform: uppercase; font-weight: normal; color: #4a4a4a; font-size: 12px; margin-bottom: 0; display: block;">Filter by Date</label>
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
            </div>
        </div>
    </div>
</section>
</div>
<?php 
include_once('../partials/modals/modal_user_setup.php');
include_once('../partials/footer.php'); ?>
<script>
$(document).ready(function() {
    // Fetch announcements on page load
    fetchAnnouncements();
    allMessagesList();
    // Handle the send notification form submission
    $("#notificationForm").submit(function(e) {
        e.preventDefault();
        var formData = {
            title: $("#notificationTitle").val(),
            subject: $("#notificationSubject").val(),
            message: $("#notificationMessage").val(),
            recipient: $("#notificationRecipient").val()
        };
        // Show loading alert while processing the request
        Swal.fire({
            title: 'Sending notification...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        $.ajax({
            type: "POST",
            url: "send_email.php",
            data: formData,
            dataType: "json",
            success: function(response) {
                Swal.close(); // Close the loading alert
                if (response.success) {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: response.message,
                        confirmButtonText: "OK"
                    });
                    let listItem = `<li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${formData.title}</strong> - ${formData.subject}<br>
                            ${formData.message}<br>
                            <small>Sent to: ${formData.recipient}</small>
                        </div>
                        <button class="btn btn-danger btn-sm delete-announcement">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </li>`;
                    let historyList = $("#announcementHistory");
                    // Clear placeholder if exists
                    if (historyList.children().first().text() === "No announcements sent yet.") {
                        historyList.html("");
                    }
                    historyList.prepend(listItem);
                    $("#notificationForm")[0].reset();
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: response.message,
                        confirmButtonText: "OK"
                    });
                }
            fetchAnnouncements();
            allMessagesList();
                // Reset form
            },
            error: function(xhr, status, error) {
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
    // Function to fetch announcements from your API
    function fetchAnnouncements() {
    $.ajax({
        type: "GET",
        url: "../api/api_announcement.php?get_announcement_history", // Ensure this URL is correct
        dataType: "json",
        success: function(response) {
            let historyList = $("#announcementHistory");
            historyList.empty(); // Clear existing items
            if (response.success) {
                if (response.data.length === 0) {
                    historyList.append('<li class="list-group-item">No announcements sent yet.</li>');
                } else {
                    response.data.forEach(function(announcement) {
                        // Truncate long messages
                        let maxLength = 100; // Adjust this number as needed
                        let message = announcement.message;
                        let shortMessage = message.length > maxLength ? message.substring(0, maxLength) + '...' : message;
                        let listItem = `<li class="list-group-item d-flex justify-content-between align-items-center">
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
        error: function(xhr, status, error) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "An error occurred while fetching announcements: " + xhr.responseText,
                confirmButtonText: "OK"
            });
        }
    });
}
// Function to fetch and filter announcements
function allMessagesList() {
    let recipientFilter = $("#filterRecipient").val();
    let dateFilter = $("#filterDate").val();
    $.ajax({
        type: "GET",
        url: "../api/api_announcement.php",
        data: { 
            get_announcements: true, 
            recipient: recipientFilter, 
            date: dateFilter 
        }, // Pass filters as query parameters
        dataType: "json",
        success: function(response) {
            let historyList = $("#allMessagesList");
            historyList.empty(); // Clear existing items
            if (response.success) {
                if (response.data.length === 0) {
                    historyList.append('<li class="list-group-item">No announcements found.</li>');
                } else {
                    response.data.forEach(function(announcement) {
                        let maxLength = 100;
                        let message = announcement.message;
                        let shortMessage = message.length > maxLength ? message.substring(0, maxLength) + '...' : message;
                        let createdDate = new Date(announcement.created_at);
                        let formattedDate = createdDate.toLocaleString('en-US', {
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
        error: function(xhr, status, error) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "An error occurred while fetching announcements: " + xhr.responseText,
                confirmButtonText: "OK"
            });
        }
    });
}
     $(document).on("click", ".view-announcement", function () {
        let announcementId = $(this).data("id");
        $.ajax({
            type: "GET",
            url: "../api/api_announcement.php",
            data: { announcement_id: announcementId }, // Pass the announcement ID
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    let announcement = response.data;
                    // Hide the allMessagesList and clear it
                    $("#allMessagesContainer, #backToHistoryBtn").hide();
                    // Remove any existing fullMessageView
                    $("#fullMessageView").remove();
                    // Create a new div for the full message inside allMessagesCard
                    let fullMessageDiv = `
                        <div id="fullMessageView" class="p-3">
                            <h5>${announcement.title}</h5>
                            <p><strong>Subject:</strong> ${announcement.subject}</p>
                            <p>${announcement.message}</p>
                            <p><small>Sent to: ${announcement.recipient} on 
                            ${new Date(announcement.created_at).toLocaleString()}</small></p>
                            <button class="btn btn-secondary" id="backToMessages">Back</button>
                        </div>
                    `;
                    // Append full message inside the card (inside allMessagesCard)
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
   $(document).on("click", "#backToMessages", function () {
    $("#fullMessageView").remove(); // Remove the full message view
    $("#allMessagesContainer, #backToHistoryBtn").show(); // Show the messages list and back button
});
// Event Listeners for Filters
$("#filterRecipient, #filterDate").on("change", function() {
    allMessagesList(); // Fetch announcements whenever filters change
});
   // Delegated event listener for dynamically created delete buttons
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
                type: "POST", // Use POST instead of DELETE for better compatibility
                url: "../api/api_announcement.php",
                data: {
                    action: 'delete_announcement',  // Use a clear action key
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
                error: function(xhr, status, error) {
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
    $.ajax({
    method: "GET",
    url: '../api/api_select_options.php?get_section_list',
    dataType: 'json'
}).done(function(data) {
    // Check if the response is a non-empty array
    if (Array.isArray(data) && data.length > 0) {
        // Define the options to append to the dropdown
        const defaultOptions = [
            { value: "", text: "--" },
            { value: "students", text: "All Students Only" },
            { value: "teachers", text: "All Teachers Only" },
            { value: "all", text: "All Students & Teachers" }
        ];
        // Append default options first
        const $dropdown = $('#notificationRecipient, #filterRecipient');
        $dropdown.empty(); // Clear existing options
        defaultOptions.forEach(option => {
            $dropdown.append(new Option(option.text, option.value));
        });
        // Append the dynamic section options
        $.each(data, function(index, item) {
            $dropdown.append(new Option(item.SectionName, item.SectionName));
        });
    } else {
        console.warn('No school year data found.');
        // Optional: Display a message to the user if no data is found
        $('#notificationRecipient').html('<option value="">No sections available</option>');
    }
}).fail(function(jqXHR, textStatus, errorThrown) {
    console.error('AJAX request failed:', textStatus, errorThrown);
    // Optional: Show a user-friendly error message if the request fails
    $('#notificationRecipient').html('<option value="">Error loading sections</option>');
});
document.getElementById('viewMessagesBtn').addEventListener('click', function() {
    // Hide History Card
    document.getElementById('historyCard').style.display = 'none';
    // Show All Messages Card
    document.getElementById('allMessagesCard').style.display = 'block';
});
document.getElementById('backToHistoryBtn').addEventListener('click', function() {
    // Show History Card
    document.getElementById('historyCard').style.display = 'block';
    // Hide All Messages Card
    document.getElementById('allMessagesCard').style.display = 'none';
});
});
</script>
</body>
</html>
