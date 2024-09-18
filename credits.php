<?php
session_start();
include_once('functions.php');
session_timeout_chk();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Credits - EGN</title>
    <link rel="stylesheet" type="text/css" href = "style.css?version=1">
</head>
<body>
<div id="body-wrapper">
    <?php
    echo generateHeader();
    echo generateMainNav("credits.php");
    echo generateBanner();
    ?>
    <main>
        <div class="content credits-wrapper">
            <h1 class="pageTitle">Credits</h1>
            <h2>Student Information</h2>
            <p>Name: Khant Thura</p>
            <p>Student ID: 22048419</p>
            <p>Email: khant.thura@northumbria.ac.uk</p>
            <br>
            <h2>References</h2>
            <ul>
                <li>How to pause interval on mouseover and resume when the mouse is no longer over (no date) Stack Overflow. Available at: <a href="https://stackoverflow.com/questions/58802229/how-to-pause-interval-on-mouseover-and-resume-when-the-mouse-is-no-longer-over" target="_blank">https://stackoverflow.com/questions/58802229/how-to-pause-interval-on-mouseover-and-resume-when-the-mouse-is-no-longer-over</a>(Accessed: 11 January 2024).</li>
                <li>Get into Newcastle (no date) Available at: <a href="https://www.getintonewcastle.co.uk/images/media/newsletter.png" target="_blank">https://www.getintonewcastle.co.uk/images/media/newsletter.png
                </a>(Accessed: 10 January 2024).</li>
                <li>What is the exact difference between currentTarget property and target property in JavaScript? (no date) Stack Overflow. Available at: <a href="https://stackoverflow.com/questions/10086427/what-is-the-exact-difference-between-currenttarget-property-and-target-property" target="_blank">https://stackoverflow.com/questions/10086427/what-is-the-exact-difference-between-currenttarget-property-and-target-property
                    </a>(Accessed: 5 January 2024).</li>
                <li>Replace HTML checkbox with images (no date) Replace HTML checkbox with images | Lulu’s blog. Available at:<a href="https://lucidar.me/en/web-dev/replace-html-checkbox-with-images/" target="_blank">https://lucidar.me/en/web-dev/replace-html-checkbox-with-images/
                    </a>(Accessed: 10 January 2024).</li>
                <li>Checkbox Vector Image (no date) Vector Stock .Available at:<a href="https://www.vectorstock.com/royalty-free-vector/check-uncheck-concept-checkbox-set-with-blank-vector-29007092" target="_blank">https://www.vectorstock.com/royalty-free-vector/check-uncheck-concept-checkbox-set-with-blank-vector-29007092
                    </a>(Accessed: 10 January 2024).</li>
                <li>A guide to accessible form validation (2023) Smashing Magazine. Available at: <a href="https://www.smashingmagazine.com/2023/02/guide-accessible-form-validation/#accessibility-in-forms" target="_blank">https://www.smashingmagazine.com/2023/02/guide-accessible-form-validation/#accessibility-in-forms
                    </a>(Accessed: 6 January 2024). </li>
                <li>Main website logo is created using <a href="https://canva.com" target="_blank">Canva</a>.</li>
            </ul>
            <p>Note: I edited the DDL of EGN_events so that the eventDescription column can accept 512 characters instead of 256 characters since the original description of event ID 9 was exceeding the varchar limit at the first place.</p>
        </div>
    </main>
    <?php
    echo generateFooter();
    ?>
</div>
</body>
</html>