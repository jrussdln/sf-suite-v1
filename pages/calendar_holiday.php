<?php
require_once('page_header_sessions.php');
include('../includes/navbar.php');
?>
<div class="content-wrapper">
  <section class="content-header" style="margin: 0; padding: 10p  x; background-color: #f8f9fa;">
    <div class="container-fluid" style="margin: 3px; padding: 0;">
      <div class="row" style="margin: 0;">
        <div class="col-sm-6" style="padding: 0;">
          <h1 style="font-size: 1.5rem; margin: 1px;">Attendance</h1>
        </div>
      </div>
      <div class="row" style="margin: 0;">
        <div class="col-sm-6" style="padding: 0;">
          <h1 style="font-size: 1rem; margin: 1px; color: darkcyan">Records / Attendance / Holidays</h1>
        </div>
      </div>
    </div>
  </section>
  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-3">
          <div class="sticky-top mb-3">
            <div class="card" style="max-height: 200px; overflow-y: auto;">
              <div class="card-header">
                <h1
                  style="text-transform: uppercase; font-weight: normal; color:rgb(255, 255, 255); font-size: 12px; margin-left: 10px; display: block;">
                  Draggable Events</h1>
              </div>
              <div class="card-body">
                <!-- The events will be dynamically populated here -->
                <div id="external-events">
                  <!-- Events will be added here dynamically -->
                </div>
              </div>
            </div>
            <div class="card" style="max-height: 200px; overflow-y: auto;">
              <div class="card-header">
                <h1
                  style="text-transform: uppercase; font-weight: normal; color:rgb(255, 255, 255); font-size: 12px; margin-left: 10px; display: block;">
                  List of Events</h1>
              </div>
              <div class="card-body">
                <div id="external-events-list">
                </div>
              </div>
            </div>
            <!-- /.card -->
            <div class="card">
              <div class="card-header">
                <h1
                  style="text-transform: uppercase; font-weight: normal; color:rgb(255, 255, 255); font-size: 12px; margin-left: 10px; display: block;">
                  Add Event</h1>
              </div>
              <div class="card-body">
                <!-- /btn-group -->
                <div class="input-group">
                  <input id="new-event" type="text" class="form-control" placeholder="Event Title">
                  <div class="input-group-append">
                    <button id="add-new-event" type="button" class="btn btn-primary">Add</button>
                  </div>
                  <!-- /btn-group -->
                </div>
                <!-- /input-group -->
              </div>
            </div>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="card card-primary">
            <div class="card-body p-0">
              <!-- THE CALENDAR -->
              <div id="calendar"></div>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
<?php
include_once('../partials/modals/modal_user_setup.php');
include_once('../partials/footer.php');
?>
<script src="../plugins/jquery-ui/jquery-ui.min.js"></script>
<script src="../plugins/fullcalendar/main.min.js"></script>
<script src="../plugins/fullcalendar-daygrid/main.min.js"></script>
<script src="../plugins/fullcalendar-timegrid/main.min.js"></script>
<script src="../plugins/fullcalendar-interaction/main.min.js"></script>
<script src="../plugins/fullcalendar-bootstrap/main.min.js"></script>
<script>
  $(function () {
    initializeCalendarAndEvents(); // This will now only load data, not create calendar repeatedly
    initializeCalendar();
    function initializeCalendarAndEvents() {
      $.ajax({
        url: '../api/api_calendar.php?get_full_holidays_card',
        type: 'GET',
        beforeSend: function () {
          $('#external-events-list').html('<div>Loading holidays...</div>');
        },
        success: function (response) {
          var data;
          try {
            data = (typeof response === 'string') ? JSON.parse(response) : response;
            if (data.holidays && Array.isArray(data.holidays) && data.holidays.length > 0) {
              var eventContainer = $('#external-events-list');
              eventContainer.empty();
              data.holidays.forEach(function (holiday) {
                var event = $('<div/>')
                  .addClass('external-event bg-info position-relative')
                  .data('holiday_id', holiday.holiday_id)
                  .append($('<span/>').text(holiday.holiday_name));
                var deleteButton = $('<i/>')
                  .addClass('fas fa-trash delete-btn position-absolute')
                  .css({ 'top': '50%', 'right': '10px', 'cursor': 'pointer', 'transform': 'translateY(-50%)' })
                  .click(function () {
                    $.ajax({
                      url: '../api/api_calendar.php?delete_full_holidays_cards',
                      type: 'POST',
                      data: { holiday_id: holiday.holiday_id },
                      dataType: 'json',
                      success: function (response) {
                        if (response.success) {
                          initializeCalendarAndEvents();
                          initializeCalendar();
                        } else {
                          alert('Error: ' + response.message);
                        }
                      }
                    });
                  });
                event.append(deleteButton);
                eventContainer.append(event);
              });
            } else {
              $('#external-events-list').html('<div>No holidays available.</div>');
            }
          } catch (e) {
            console.error('Error parsing response:', e);
            alert('Failed to load holiday events.');
          }
        },
        error: function () {
          alert('Failed to load holiday events.');
        }
      });
      $.ajax({
        url: '../api/api_calendar.php?get_holidays',
        type: 'GET',
        success: function (response) {
          var data;
          try {
            data = (typeof response === 'string') ? JSON.parse(response) : response;
            if (data.holidays) {
              var eventContainer = $('#external-events');
              eventContainer.empty();
              data.holidays.forEach(function (holiday) {
                var event = $('<div/>')
                  .addClass('external-event bg-success')
                  .text(holiday.holiday_name)
                  .data('holiday_id', holiday.holiday_id);
                eventContainer.append(event);
              });
              ini_events($('#external-events div.external-event'));
            }
          } catch (e) {
            console.error('Error parsing JSON:', e);
            alert('Failed to load holiday events.');
          }
        },
        error: function () {
          alert('Failed to load holiday events.');
        }
      });
    }
    function ini_events(ele) {
      ele.each(function () {
        var eventObject = { title: $.trim($(this).text()) };
        $(this).data('eventObject', eventObject);
        $(this).draggable({
          zIndex: 1070,
          revert: true,
          revertDuration: 0
        });
      });
    }
    // Initialize calendar ONCE only
    var Calendar = FullCalendar.Calendar;
    var Draggable = FullCalendarInteraction.Draggable;
    var containerEl = document.getElementById('external-events');
    new Draggable(containerEl, {
      itemSelector: '.external-event',
      eventData: function (eventEl) {
        return {
          title: eventEl.innerText,
          backgroundColor: window.getComputedStyle(eventEl, null).getPropertyValue('background-color'),
          borderColor: window.getComputedStyle(eventEl, null).getPropertyValue('background-color'),
          textColor: window.getComputedStyle(eventEl, null).getPropertyValue('color')
        };
      }
    });
    var calendar;
    function initializeCalendar() {
      var calendarEl = document.getElementById('calendar');
      if (calendar) {
        calendar.destroy(); // Destroys the previous instance before creating a new one
      }
      calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: ['bootstrap', 'interaction', 'dayGrid', 'timeGrid'],
        header: {
          left: 'prev,next today',
          center: 'title',
          right: 'backButton dayGridMonth'
        },
        customButtons: {
          backButton: {
            text: 'Back',
            click: function () {
              window.history.back();
            }
          }
        },
        events: function (info, successCallback, failureCallback) {
          $.ajax({
            url: '../api/api_calendar.php?get_full_holidays',
            method: 'GET',
            success: function (data) {
              var events = data.map(function (event) {
                return {
                  title: event.holiday_name,
                  start: event.holiday_date,
                  backgroundColor: '#f56954',
                  borderColor: '#f56954'
                };
              });
              successCallback(events);
            },
            error: failureCallback
          });
        },
        editable: true,
        droppable: true,
        drop: function (info) {
          var holidayId = $(info.draggedEl).data('holiday_id');
          $(info.draggedEl).remove();
          $.ajax({
            url: '../api/api_calendar.php',
            type: 'POST',
            data: {
              holiday_id: holidayId,
              holiday_date: info.dateStr
            },
            success: function (response) {
              console.log('Raw response:', response); // 👈 Add this
              initializeCalendarAndEvents();
              initializeCalendar();
              var data;
              try {
                data = JSON.parse(response);
              } catch (e) {
                return;
              }
              if (data.success) {
                alert('Event date updated successfully!');
              } else {
                alert('Error updating event: ' + data.message);
              }
            },
            error: function () {
              alert('Failed to update event date.');
            }
          });
        }
      });
      calendar.render();
    }
    // Add new event
    $('#add-new-event').click(function (e) {
      e.preventDefault();
      var val = $('#new-event').val();
      if (val.length === 0) return;
      $.ajax({
        url: '../api/api_calendar.php?add_event',
        type: 'POST',
        data: {
          holiday_name: val
        },
        success: function (response) {
          try {
            var data = JSON.parse(response);
            if (!data.success) {
              initializeCalendarAndEvents();
            }
          } catch (e) {
            initializeCalendarAndEvents();
          }
        },
        error: function () {
          initializeCalendarAndEvents();
          calendar.render();
        }
      });
      initializeCalendarAndEvents();
      $('#new-event').val('');
    });
  });
</script>
</body>
</html>