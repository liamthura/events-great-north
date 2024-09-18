<?php
session_start();
include_once('functions.php');
session_timeout_chk();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - EGN</title>
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
            if (check_login()) {
                echo "<p>You're already logged in as ".get_session("firstname")."!</p>\n";
            } else {
                echo getLoginForm();
            }
            ?>
        </div>

    </main>
    <?php
    echo generateFooter();
    ?>
</div>
<script>
window.addEventListener("load", () => {
    document.getElementById("loginForm").addEventListener("submit", (event) => {
        let errors = 0;
        if (event.target.username.value.trim() === "") {
            document.getElementById("usernameWarning").textContent = "Username cannot be empty!";
            errors += 1;
        } else {
            document.getElementById("usernameWarning").textContent = "";
        }
        if (event.target.password.value === "") {
            document.getElementById("passwordWarning").textContent = "Password cannot be empty!";
            errors += 1;
        } else {
            document.getElementById("passwordWarning").textContent = "";
        }
        if (errors !== 0) {
            event.preventDefault();
        }
    })
})
</script>
</body>
</html>