<?php
session_start();
include_once('functions.php');
session_timeout_chk();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Events Guide North</title>
    <link rel="stylesheet" type="text/css" href = "style.css?version=1">
</head>
<body>
<div id="body-wrapper">
    <?php
    echo generateHeader();
    echo generateMainNav("index.php");
    echo generateBanner();
    ?>
    <main id="with-sidebar">
        <div class="content">
            <div class="egn-intro">
                <h1 class="pageTitle">Welcome to Events Guide North!</h1>
                <p>I'm sooo lazy to write up a demo part here so there it goes the text from some random online source and fed it into chatgpt. I promise I didin't use any help of AI or any automation from the internet to structure, design and build my code. It's quite reflective that I did it myself given that the code are quite amateur, long, and sometimes unintentionally messy lol. Ok I should have wasted enough vertical space now. Not gonna lie I admire Mr. Johnathan and am inspired on making the notes fun and easy to read by adding random happy text. :)</p>
                <br>
                <p>Welcome to Events Guide North, your premier gateway to a vibrant world of entertainment across Northern England. At Events Guide North, we take pride in curating an extensive array of events, from cultural festivals to live performances, ensuring that you have access to the region's most exciting happenings. Our user-friendly platform simplifies the exploration and booking process, making it effortless for you to immerse yourself in the rich tapestry of experiences Northern England has to offer. Discover, engage, and create unforgettable memories with Events Guide North.</p>
                <br>

            </div>
        </div>
        <aside>
            <div id="offer">
                <h2>Special Offer!</h2>
                <h3 class="offer-title">Offers!</h3>
                <p class="offer-info"></p>
                <span class="offer-price"></span>
                <a class="offer-cta" href="bookEventsForm.php">Book now!</a>
            </div>

        </aside>
    </main>
    <?php
    echo generateFooter();
    ?>
</div>
<script>
    const offerTitle = document.querySelector("#offer .offer-title");
    const offerInfo = document.querySelector("#offer .offer-info");
    const offerPrice = document.querySelector("#offer .offer-price");
    const offerCTA = document.querySelector("#offer .offer-cta");
    window.addEventListener("load", () => {
        "use strict";
        updateOffer();
        let offerSlides = setInterval(updateOffer, 5000);
        const offerBox = document.getElementById("offer");
        // freeze the offer for readability when the mouse hovers over the offer box
        offerBox.addEventListener("mouseover", () => {
            clearInterval(offerSlides);
        })
        offerBox.addEventListener("mouseout", () => {
            offerSlides = setInterval(updateOffer, 5000);
        })
    })
</script>
<script src="functions.js?version=1"></script>
</body>
</html>