<?php
session_start();
include_once('functions.php');
session_timeout_chk();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Authenticating...</title>
    <link rel="stylesheet" type="text/css" href = "style.css?version=1">
</head>
<body>
<div id="body-wrapper">
    <?php
    echo generateHeader("hide-log-in");
    echo generateMainNav();
    ?>
    <main>
        <div class="content">
            <h1 class="pageTitle">Administration Login</h1>
            <?php
            // check login status and perform redirections
            if (!check_login() && empty($_POST)){
                header("Location: login.php");
            } else if (!check_login() && !empty($_POST)) {
                list($input, $errors) = validate_login();
                if ($errors) {
                    echo show_errors($errors);
                } else {
                    // redirecting to called page if user was coming from another page to improve continuity
                    if (isset($_GET['from'])){
                        header("Location:".$_GET['from']);
                    } else {
                        header ("Location: index.php");
                    }
                }
            } else {
                session_unset();
                session_destroy();
                echo "<p>Successfully logged out! Redirecting you back to homepage...</p>\n<p>If not redirected automatically, <a href='index.php'>click here</a></p>\n";
                header ("Refresh:3; URL=index.php");
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