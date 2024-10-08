<!-- This script contains code to build a web page with a form. It includes a PHP code to dynamically generate the html to display one checkbox for each of the events currently held in the EGN_events database table within the form. The user can select one or more events that they are interested in booking by clicking the checkboxes. Use the browser to look at the structure of the html generated by the php code.

You MUST NOT in any way change the php code provided OR the code within the FORM tags.

You MAY add Javascript for task 4.
You MAY modify the page to add your own navigation menu and link to your own stylesheet using additional html or php. However, please note that styling will not be marked for this assignment. -->
<?php
session_start();
include_once('functions.php');
session_timeout_chk();
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Book Events - EGN</title>
    <link rel="stylesheet" type="text/css" href = "style.css?version=1">
</head>
<body>
<div id="body-wrapper">
    <?php
    echo generateHeader();
    echo generateMainNav("bookEventsForm.php");
    echo generateBanner();
    ?>

    <main>
        <div class="content">
            <h1 class="pageTitle">Book Events</h1>
            <form id="bookingForm" action="javascript:alert('form submitted');" method="get">
                <section id="bookEvents">
                    <h2>Select events</h2>
                    <p>Book your favourite events and grab your tickets before they're gone! To select an event, simply click on the box on the right side of the event you wish to select. Select as many as you want!</p>
                    <?php
                    try {
                        // include the file with the function for the database connection
                        require_once('functions.php');
                        // get database connection
                        $dbConn = getConnection();
                        $sqlEvents = 'SELECT eventID, eventTitle, eventStartDate, eventEndDate, catDesc, venueName, eventPrice FROM EGN_events e INNER JOIN EGN_categories c ON e.catID = c.catID INNER JOIN EGN_venues v ON e.venueID = v.venueID ORDER BY eventTitle';

                        // execute the query
                        $rsEvents = $dbConn->query($sqlEvents);

                        while ($event = $rsEvents->fetchObject()) {
                            $eventTitle = $event->eventTitle;
                            echo "\t<div class='item'>
				<div class='event-title'><span class='eventTitle'>".filter_var($eventTitle, FILTER_SANITIZE_SPECIAL_CHARS)."</span></div>
				<div class='event-date'><span class='eventStartDate'>Event starts: {$event->eventStartDate}</span>
            	<span class='eventEndDate'>Event ends: {$event->eventEndDate}</span></div> 
	            <div class='event-meta'><span class='catDesc'>Category: {$event->catDesc}</span>
	         	<span class='venueName'>Venue: {$event->venueName}</span>
	            <span class='eventPrice'>Ticket: £{$event->eventPrice}</span></div> 
	            <label class='chosen'><input type='checkbox' name='event[]' value='{$event->eventID}' data-price='{$event->eventPrice}'><img class='checked-icon' src='checked.jpg' alt='checked'><img class='unchecked-icon' src='unchecked.jpeg' alt='unchecked'></label>
	      		</div>\n";
                        }
                    }
                    catch (Exception $e) {
                        echo "Problem occurred:" . $e->getMessage();
                    }
                    ?>
                </section>
                <section id="collection">
                    <h2>Collection method</h2>
                    <p>Please select whether you want your chosen event ticket(s) to be delivered to your home address (a charge applies for this) or whether you want to collect them yourself.</p>
                    <div class="delivery-options-container">
                        <label><input type="radio" name="deliveryType" value="home" data-price="7.99" checked>
                        Home address - &pound;7.99</label>&nbsp; | &nbsp;
                        <label><input type="radio" id="deliveryType" name="deliveryType" value="ticketOffice" data-price="0"> Collect from ticket office - no charge</label>
                    </div>
                </section>
                <section id="checkCost">
                    <h2>Total cost</h2>
                    <label for="total">Total</label> <input type="text" id="total" name="total" size="10" readonly>
                </section>
                <section id="placeBooking">
                    <h2>Place booking</h2>
                    <h3>Your details</h3>
                    <div id="custDetails" class="custDetails">
                        <label for="forename">Forename</label> <input type="text" id="forename" name="forename" aria-required="true" accesskey="f">
                        <label for="surname">Surname</label> <input type="text" id="surname" name="surname" aria-required="true" accesskey="s">
                        <span id="namesWarning" class="warning"></span>
                    </div>
                    <label style="color: #FF0000; font-weight: bold;" id='termsText'><input type="checkbox" name="termsChkbx" aria-required="true">I have read and agree to the terms and conditions
                    </label>
                    <p><span id="eventWarning" class="warning"></span></p>
                    <p><input type="submit" name="submit" value="Book now!" disabled></p>
                </section>
            </form>
        </div>
    </main>
    <?php
    echo generateFooter();
    ?>
</div>


<!-- Here you need to add Javascript or a link to a script (.js file) to process the form as required for task 4 of the assignment -->
<script>
    let totalSelectedEvents = 0;
    window.addEventListener("load", () =>{
        "use strict"
        const bookingForm = document.getElementById("bookingForm");
        bookingForm.addEventListener("change", calculateTotal);
        bookingForm.addEventListener("submit", checkBookingForm);
        bookingForm.termsChkbx.addEventListener("click", checkTnC);

    })
</script>
<script src="functions.js?version=1"></script>
</body>
</html>