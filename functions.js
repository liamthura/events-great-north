"use strict"
// function to check if TnC is checked on editEventsForm page
function checkTnC () {
    if (bookingForm.termsChkbx.checked) {
        bookingForm.submit.disabled = false;
        document.getElementById("termsText").style.color = "#212121";
        document.getElementById("termsText").style.fontWeight = "normal";
    } else {
        bookingForm.submit.disabled = true;
        document.getElementById("termsText").style.color = "#FF0000";
        document.getElementById("termsText").style.fontWeight = "bold";
    }
}

// function to calculate total of event ticket options
function calculateTotal () {
    let totalPrice = 0;
    let selectedEvents = 0;

    const events = bookingForm.querySelectorAll("#bookEvents .item");
    const deliveryOptions = bookingForm.querySelectorAll(".delivery-options-container input[name=deliveryType][type=radio][data-price]");

    for (const event of events) {
        const eventSelection = event.querySelector("input[type=checkbox][data-price]");

        if (eventSelection.checked) {
            totalPrice += parseFloat(eventSelection.dataset.price);
            selectedEvents += 1;
        }
    }
    // remembering how many events was selected
    totalSelectedEvents = selectedEvents;

    for (const deliveryType of deliveryOptions) {
        if (deliveryType.checked && selectedEvents !== 0) {
            totalPrice += parseFloat(deliveryType.dataset.price);
        }
    }
    // fixes output to 2 digits decimals
    bookingForm.total.value = totalPrice.toFixed(2);
}

// function to dyanamically check booking for on bookEventsForm page
function checkBookingForm (event) {
    let errors = 0;
    if (totalSelectedEvents === 0) {
        document.getElementById("eventWarning").textContent = "Please select at least one event.";
        errors += 1;
    } else {
        document.getElementById("eventWarning").textContent = "";
    }

    if (event.currentTarget.forename.value === "" || event.currentTarget.surname.value === "") {
        document.getElementById("namesWarning").textContent = "Please provide both forename and surname.";
        errors += 1;
    } else {
        document.getElementById("namesWarning").textContent = "";
    }

    if (!event.currentTarget.termsChkbx.checked) {
        errors += 1;
    }

    if (errors !== 0) {
        event.preventDefault();
    }
}

// function to fetch special offers json data provided by getOffers.php
function updateOffer () {
    const URL = "getOffers.php";
    fetch(URL)
        .then(
            function (response) {
                // console.log(response);
                return response.json();
            }
        )
        .then(
            function (offer) {
                // console.log(offer);
                offerTitle.innerText = offer.eventTitle;
                offerInfo.innerText = offer.catDesc + " Event";
                offerPrice.innerText = "£" + offer.eventPrice;
                offerCTA.hidden = false;
            }
        )
        .catch(
            // custom error messages for front end
            function (error) {
                console.log("Something went wrong!", error);
                offerTitle.textContent = "Oops!";
                offerInfo.textContent = "We can't seem to get you any offers at the moment.";
                offerPrice.textContent = "";
                offerCTA.hidden = true;
            }
        )
}