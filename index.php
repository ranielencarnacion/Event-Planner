<?php
// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to the login page
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Event Planner</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style type="text/css">
    p, body, td, input, select, button { font-family: -apple-system,system-ui,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif; font-size: 14px; }
    body { padding: 0px; margin: 0px; background-color: #ffffff; }
    .space { margin: 10px 0px 10px 0px; }
    .header { background: #003267; background: linear-gradient(to right, #011329 0%,#00639e 44%,#011329 100%); padding:20px 10px; color: white; box-shadow: 0px 0px 10px 5px rgba(0,0,0,0.75); }

    .header a { color: white; }
    .header h1 a { text-decoration: none; color: white;}
    .header h1 { padding: 0px; margin: 0px; }
    .main { padding: 10px; margin-top: 10px; }
  </style>

  <style>
      .buttons {
          margin-bottom: 10px;
          /*margin-top: 20px;*/
          display: inline-flex;
      }

      .buttons button {
          background-color: #f3f3f3;
          color: #333333;
          border: 1px solid #cccccc;
          padding: .5rem 1rem;
          width: 80px;
          font-size: 14px;
          cursor: pointer;
          margin-right: 1px;
          transition: all 0.2s;
          box-sizing: border-box;
      }

      .buttons button.selected {
          background-color: #e5e5e5;
          border-color: #cccccc;
      }

      .buttons button:first-child {
          border-top-left-radius: 30px;
          border-bottom-left-radius: 30px;
      }

      .buttons button:last-child {
          border-top-right-radius: 30px;
          border-bottom-right-radius: 30px;
      }

      .buttons button:hover {
          background-color: #ffe794dd;
          border-color: #ffce18;
      }

      .buttons button:active {
          box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      }


      /* context menu icons */
      .icon:before {
          position: absolute;
          margin-left: 0px;
          margin-top: 3px;
          width: 14px;
          height: 14px;
          content: '';
      }

      .icon-blue:before { background-color: #3d85c6; }
      .icon-green:before { background-color: #6aa84f; }
      .icon-orange:before { background-color: #e69138; }
      .icon-red:before { background-color: #cc4125; }

      body .calendar_default_event_inner {
          border-radius: 10px;
      }

      body .month_default_event_inner {
          border-radius: 10px;
      }
      .logout-link {
    color: red !important; /* Forces the red color to override other styles */
    text-decoration: none;
    margin-right: 10px; /* Adds space between logout links */
}


.logout-link:hover {
    text-decoration: underline;
}


  </style>

  <!-- DayPilot library -->
  <script src="js/daypilot/daypilot-all.min.js"></script>

</head>
<body>
<div class="header">
  <h1><a>        <?php
        // Check if the user is logged in and display their name with the first letter capitalized
        if (isset($_SESSION['username'])) {
            echo ucfirst(htmlspecialchars($_SESSION['username'])) . "'s Event Planner";
        } else {
            echo "Event Planner"; // Default when not logged in
        }
        ?></a></h1>
  <a href="logout.php" class="logout-link">Logout</a>
  <a href="changepassword.php" class="logout-link">Change Password</a>


</div>

<div class="main">
  <div style="display:flex">
    <div style="">
      <div id="nav"></div>
    </div>
    <div style="flex-grow: 1; margin-left: 10px;">
      <div class="toolbar buttons">
        <button id="buttonDay">Day</button>
        <button id="buttonWeek">Week</button>
        <button id="buttonMonth">Month</button>
      </div>
      <div class="toolbar buttons">
      <button id="addEventButton" style="margin-left: 10px;">Add Event</button> <!-- Add Event button -->
      </div>
      <div id="dpDay"></div>
      <div id="dpWeek"></div>
      <div id="dpMonth"></div>
    </div>
  </div>
</div>

<script type="text/javascript">

  const nav = new DayPilot.Navigator("nav", {
    showMonths: 3,
    skipMonths: 3,
  });
  nav.init();

  const day = new DayPilot.Calendar("dpDay", {
    viewType: "Day"
  });
  configureCalendar(day);
  day.init();

  const week = new DayPilot.Calendar("dpWeek", {
    viewType: "Week"
  });
  configureCalendar(week);
  week.init();

  const month = new DayPilot.Month("dpMonth", {
    eventHeight: 32,
  });
  configureCalendar(month);
  month.init();

  function configureCalendar(calendar) {
  calendar.visible = false;
  calendar.contextMenu = new DayPilot.Menu({
    items: [
      {
        text: "Delete",
        onClick: async args => {
          var params = {
            id: args.source.id(),
          };
          await DayPilot.Http.post("calendar_delete.php", params);
          calendar.events.remove(params.id);
          console.log("Deleted");
        }
      },
      {
        text: "-"
      },
      {
        text: "Blue",
        icon: "icon icon-blue",
        color: "#3d85c6",
        onClick: args => { updateColor(args.source, args.item.color); }
      },
      {
        text: "Green",
        icon: "icon icon-green",
        color: "#6aa84f",
        onClick: args => { updateColor(args.source, args.item.color); }
      },
      {
        text: "Orange",
        icon: "icon icon-orange",
        color: "#e69138",
        onClick: args => { updateColor(args.source, args.item.color); }
      },
      {
        text: "Red",
        icon: "icon icon-red",
        color: "#cc4125",
        onClick: args => { updateColor(args.source, args.item.color); }
      }
    ]
  });

  calendar.autoArrange = true; // Automatically arrange overlapping events
calendar.eventHeight = 40; // Adjust height of events

  
calendar.onBeforeEventRender = args => {
      if (!args.data.backColor) {
        args.data.backColor = "#6aa84f";
      }
      args.data.backColor += "c0";
      args.data.borderColor = "darker";
      args.data.fontColor = "#fff";
      args.data.barHidden = true;

      args.data.areas = [
        {
          right: 4,
          top: 4,
          width: 24,
          height: 24,
          padding: 2,
          action: "ContextMenu",
          symbol: "/icons/daypilot.svg#hamburger-menu",
          backColor: args.data.backColor,
          fontColor: "#ffffff",
          style: "border-radius: 50%; border: 1px solid #ffffff"
        }
      ];
    };



  calendar.onEventMoved = async args => {
    const params = {
      id: args.e.id(),
      newStart: args.newStart,
      newEnd: args.newEnd
    };
    await DayPilot.Http.post("calendar_move.php", params);
    console.log("Moved.");
  };

  calendar.onEventResized = async args => {
    const params = {
      id: args.e.id(),
      newStart: args.newStart,
      newEnd: args.newEnd
    };
    await DayPilot.Http.post("calendar_move.php", params);
    console.log("Resized.");
  };

  calendar.onTimeRangeSelected = async args => {
    const form = [
      { name: "Name", id: "text", required: false },
      { name: "Start", id: "start", dateFormat: "MMMM d, yyyy h:mm tt" },
      { name: "End", id: "end", dateFormat: "MMMM d, yyyy h:mm tt" },
    ];

    const data = {
      start: args.start,
      end: args.end,
      text: "" // Default empty string for the event title
    };

    const active = switcher.active.control;

    const modal = await DayPilot.Modal.form(form, data);
    active.clearSelection();

    if (modal.canceled) {
      return;
    }

    const eventTitle = modal.result.text.trim() || "Event";

    const { data: result } = await DayPilot.Http.post("calendar_create.php", {
      ...modal.result,
      text: eventTitle,
    });

    active.events.add({
      start: modal.result.start,
      end: modal.result.end,
      id: result.id,
      text: eventTitle,
    });
  };

  calendar.onEventClick = async (args) => {
    const event = args.e.data;

    // Open the modal with pre-filled event data
    const form = [
        { name: "Event Name", id: "text", required: true, value: event.text },
        { name: "Start", id: "start", dateFormat: "MMMM d, yyyy h:mm tt", value: event.start },
        { name: "End", id: "end", dateFormat: "MMMM d, yyyy h:mm tt", value: event.end },
    ];

    const modal = await DayPilot.Modal.form(form, event);

    if (modal.canceled) {
        return; // Exit if the modal was canceled
    }

    // Prepare updated event data
    const updatedEvent = {
        id: event.id,
        text: modal.result.text.trim() || "Event", // Ensure text is not empty
        start: modal.result.start,
        end: modal.result.end,
    };

    try {
        // Update the event on the server
        const response = await fetch("calendar_update.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(updatedEvent),
        });

        const result = await response.json();

        if (result.success) {
            // Update the event in the calendar
            args.e.data.text = updatedEvent.text;
            args.e.data.start = updatedEvent.start;
            args.e.data.end = updatedEvent.end;

            const calendar = switcher.active.control;
            calendar.events.update(args.e);

            console.log("Event updated successfully.");
        } else {
            console.error("Failed to update event on server:", result.message);
        }
    } catch (error) {
        console.error("Error updating event:", error);
    }
};



}

  const switcher = new DayPilot.Switcher({
    triggers: [
      {id: "buttonDay", view: day },
      {id: "buttonWeek", view: week},
      {id: "buttonMonth", view: month}
    ],
    navigator: nav,
    selectedClass: "selected",
    onChanged: args => {
      switcher.events.load("calendar_events.php");
    },
  });

  switcher.select("buttonWeek");

  async function updateColor(e, color) {
    const params = {
      id: e.data.id,
      color: color
    };

    await DayPilot.Http.post("calendar_color.php", params);
    const calendar = switcher.active.control;
    e.data.backColor = color;
    calendar.events.update(e);
    console.log("Color updated");
  }

</script>
<script type="text/javascript">
let playedAlarms = new Set(); // Ensure we only trigger the alarm once for each event
let alarmSound = null; // Variable to handle the audio object

// Function to monitor event times and trigger alarm if it's time
function monitorEvents() {
  const now = new Date();
  const calendar = switcher.active.control; // Get the active calendar instance
  const events = calendar.events.list; // Get events currently loaded

  for (let event of events) {
    if (!playedAlarms.has(event.id)) {
      const eventStart = new Date(event.start);

      if (now >= eventStart && now < new Date(eventStart.getTime() + 1 * 60 * 1000)) { 
        // Trigger the alarm if the event's start time is within 1 minute range
        triggerAlarm(event.text);
        playedAlarms.add(event.id); // Mark this alarm as triggered
      }
    }
  }
}

// Function to show popup and loop alarm sound until "okay" is pressed
function triggerAlarm(eventTitle, eventEnd) {
  try {
    // Stop any previous sound instance safely
    if (alarmSound) {
      alarmSound.pause();
      alarmSound.currentTime = 0;
      alarmSound = null; // Clear the reference to avoid conflicts
    }

    // Initialize and loop alarm sound
    alarmSound = new Audio('alarm/alarm.wav');
    alarmSound.loop = true;
    alarmSound.play();

    // Format the end time for display
    const endTimeFormatted = new Date(eventEnd).toLocaleString(); // Ensure `eventEnd` is passed correctly
    const alertMessage = `Plan: ${eventTitle}`;

    // Show the alert popup
    DayPilot.Modal.alert(alertMessage).then(() => {
      // Stop and reset the sound on OK click
      if (alarmSound) {
        alarmSound.pause();
        alarmSound.currentTime = 0;
        alarmSound = null; // Clear the reference completely
      }
    });
  } catch (error) {
    console.error('Error triggering alarm:', error);
  }
}



setInterval(monitorEvents, 1 * 1000);

// Dynamically load events from your database using AJAX


// Call this to dynamically load events when the page initializes
loadEventsFromDatabase();

// Monitor the events every time you change views or update ranges
switcher.onChanged = function() {
  loadEventsFromDatabase();
};

window.onload = function () {
  async function loadEventsFromDatabase() {
    try {
      const response = await fetch('calendar_events.php');
      const events = await response.json();
      console.log(events); // Debugging: Check events loaded
    } catch (error) {
      console.error('Error loading events from database', error);
    }
  }

  loadEventsFromDatabase();
};
</script>
<script type="text/javascript">
  async function addEventManually() {
    const now = new Date();

    // Default start and end times in local time
    const localNow = now; // Local time (no adjustments here)
    const defaultEnd = new Date(localNow.getTime() + 60 * 60 * 1000); // 1 hour later in local time

    // Format times for the modal in local time
    const nowLocal = localNow.toISOString().slice(0, 19).replace("T", " ");
    const endLocal = defaultEnd.toISOString().slice(0, 19).replace("T", " ");

    const form = [
      { name: "Name", id: "text", required: false }, // Allow empty input
      { name: "Start", id: "start", dateFormat: "MMMM d, yyyy h:mm tt", value: nowLocal },
      { name: "End", id: "end", dateFormat: "MMMM d, yyyy h:mm tt", value: endLocal },
    ];

    const modal = await DayPilot.Modal.form(form);

    if (modal.canceled) {
      return;
    }

    const eventTitle = modal.result.text.trim() || "Event";

    // Convert modal input (local time) back to UTC for server storage
    const startUtc = new Date(modal.result.start + "Z").toISOString(); // Force interpretation as UTC
    const endUtc = new Date(modal.result.end + "Z").toISOString();

    // Send event to server
    const { data: result } = await DayPilot.Http.post("calendar_create.php", {
      ...modal.result,
      text: eventTitle,
      start: startUtc, // UTC time
      end: endUtc, // UTC time
    });

    // Add event to calendar UI
    const activeCalendar = switcher.active.control;

    activeCalendar.events.add({
      start: modal.result.start, // Local time for UI
      end: modal.result.end, // Local time for UI
      id: result.id,
      text: eventTitle,
    });
  }

  // Ensure events from the database are displayed in local time
  async function loadEventsFromDatabase() {
    try {
      const response = await fetch("calendar_events.php");
      const events = await response.json();

      // Convert server UTC times to local for display
      const localEvents = events.map(event => ({
        ...event,
        start: new Date(event.start).toISOString().slice(0, 19).replace("T", " "), // Convert to local time
        end: new Date(event.end).toISOString().slice(0, 19).replace("T", " "), // Convert to local time
      }));

      const calendar = switcher.active.control;
      calendar.events.load(localEvents);
    } catch (error) {
      console.error("Error loading events from database:", error);
    }
  }

  // Attach the manual event creation function to the button
  document.getElementById("addEventButton").addEventListener("click", addEventManually);

  window.onload = loadEventsFromDatabase;

  
</script>
<script type="text/javascript">
    // Function to update the event
    async function updateEvent(eventData) {
        try {
            const response = await fetch('calendar_update.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(eventData)
            });

            const data = await response.json();

            if (data.result === 'OK') {
                // Event updated successfully, update the event on the calendar view
                // Assuming you have a method in your calendar object to update events
                const updatedEvent = data.event;  // This assumes the server returns the updated event data

                // Find the event on the calendar and update it
                const event = calendar.getEvent(updatedEvent.id);
                event.data.start = updatedEvent.start;
                event.data.end = updatedEvent.end;
                event.data.text = updatedEvent.name;

                // Refresh the event on the calendar without a page reload
                event.update();

                alert(data.message); // Optionally show a success message
            } else {
                alert(data.message); // Show error message if update failed
            }
        } catch (error) {
            console.error('Error updating event:', error);
            alert('An error occurred while updating the event.');
        }
    }
    $(document).ready(function() {
    $('#calendar').fullCalendar({
        editable: true, // Allows drag-and-drop
        events: '/fetch_events.php', // Fetch events from the server
        eventDrop: function(event) {
            const eventData = {
                id: event.id,
                text: event.title,
                start: event.start.toISOString(),
                end: event.end.toISOString(),
            };
            updateEvent(eventData); // Call your updateEvent function
        },
        eventResize: function(event) {
            const eventData = {
                id: event.id,
                text: event.title,
                start: event.start.toISOString(),
                end: event.end.toISOString(),
            };
            updateEvent(eventData); // Call your updateEvent function
        }
    });
});

    // Example usage of the updateEvent function with some event data
    // You can call this function based on your event editing logic
    // updateEvent({ id: 1, text: 'New Event Name', start: '2024-12-15T10:00:00', end: '2024-12-15T11:00:00' });
</script>
</body>
</html>