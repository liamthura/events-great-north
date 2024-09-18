<?php
session_start();
include_once('functions.php');
session_timeout_chk();
if (!check_login()) {
    // auto redirect to login page if not logged in
    header("Location: login.php?from=editProcess.php");
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Processing</title>
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
            <h1 class="pageTitle">Update Process</h1>
            <?php
            list($input, $errors) = validate_updates();
            if ($errors) {
                echo show_errors($errors);
            } else {
                echo "<p>Successfully updated! Go back to <a href='admin.php'>Manage Events</a></p>\n";
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
