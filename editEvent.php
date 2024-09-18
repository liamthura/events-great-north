<?php
session_start();
include_once('functions.php');
session_timeout_chk();
if (!check_login()) {
    // auto redirect to login page if not logged in
    header("Location: login.php?from=editEvent.php");
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit an Event</title>
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
            <h1 class="pageTitle">Edit an Event</h1>
            <?php
            // ensure an event ID is passed (user experience)
            if (isset($_GET) && !empty($_GET['eventID'])) {
                $eventID = trim($_GET['eventID']);
                echo getEventEditForm($eventID);
            } else {
                echo "<p>You have not selected any event to edit, please go to \"<a href='admin.php'>Manage Events</a>\" and choose an event to edit.</p>";
            }
            ?>
        </div>
    </main>
    <?php
    echo generateFooter();
    ?>
</div>
</body>
</html>
