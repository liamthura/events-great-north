<?php
session_start();
include_once ('functions.php');
session_timeout_chk();
if (!check_login()) {
    // auto redirect to login page if not logged in
    header("Location: login.php?from=admin.php");
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Management Panel - EGN</title>
    <link rel="stylesheet" type="text/css" href = "style.css?version=1">
</head>
<body>
<div id="body-wrapper">
    <?php
    echo generateHeader();
    echo generateMainNav("admin");
    ?>
    <main>
        <div class="content">
            <h1 class="pageTitle">Manage Events</h1>
            <p>This is an event management panel. As an administrator you can access, manage and edit events. To edit an event, simply click on the event you would like to make changes to.</p>
            <div class="table-wrapper">
                <?php
                try {
                    $dbConn = getConnection();
                    $eventsListSql = "select eventID, eventTitle, catDesc, venueName, location, eventStartDate, eventEndDate, eventPrice from EGN_events e join EGN_categories c on e.catID = c.catID join EGN_venues v on e.venueID = v.venueID order by eventTitle";
                    $eventsListQuery = $dbConn->query($eventsListSql);
                } catch (Exception $e) {
                    echo "<p class='warning'>Connection error:".$e->getMessage()."</p>\n";
                    log_error($e);
                }
                if (!empty($eventsListQuery)) {
                    echo "<table id='eventTable'>\n";
                    echo "\t<thead>\n";
                    echo "\t\t<tr>\n";
                    echo "\t\t\t<th>Event Name</th>\n";
                    echo "\t\t\t<th>Category</th>\n";
                    echo "\t\t\t<th>Venue</th>\n";
                    echo "\t\t\t<th>Location</th>\n";
                    echo "\t\t\t<th>Start Date</th>\n";
                    echo "\t\t\t<th>End Date</th>\n";
                    echo "\t\t\t<th>Pricing</th>\n";
                    echo "\t\t</tr>\n";
                    echo "\t</thead>\n";
                    echo "\t<tbody>\n";
                    while ($event = $eventsListQuery->fetchObject()){
                        // sanitising output
                        echo "\t\t<tr data-href='editEvent.php?eventID=$event->eventID'>\n";
                        echo "\t\t\t<td>".htmlspecialchars($event->eventTitle,ENT_QUOTES)."</td>\n";
                        echo "\t\t\t<td>".htmlspecialchars($event->catDesc,ENT_QUOTES)."</td>\n";
                        echo "\t\t\t<td>".htmlspecialchars($event->venueName,ENT_QUOTES)."</td>\n";
                        echo "\t\t\t<td>".htmlspecialchars($event->location,ENT_QUOTES)."</td>\n";
                        echo "\t\t\t<td>".htmlspecialchars($event->eventStartDate,ENT_QUOTES)."</td>\n";
                        echo "\t\t\t<td>".htmlspecialchars($event->eventEndDate,ENT_QUOTES)."</td>\n";
                        echo "\t\t\t<td>".htmlspecialchars($event->eventPrice,ENT_QUOTES)."</td>\n";
                        echo "\t\t</tr>\n";
                    }
                    echo "\t</tbody>\n";
                    echo "</table>\n";
                } else {
                    echo "<p>No data to show.</p>\n";
                }
                ?>
            </div>
        </div>
    </main>
    <?php
    echo generateFooter();
    ?>
</div>
<script>
    const eventRows = document.querySelectorAll("#eventTable tbody tr");
    for (const event of eventRows) {
        //attaching the edit link using data-href so that the whole table row is clickable
        event.addEventListener("click", ()=>{
            window.open(event.dataset.href, "_self");
        });
    }
</script>
</body>
</html>