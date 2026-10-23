// ==========================================
// REGISTER EVENT
// ==========================================

function registerEvent(eventName, button) {

    let registeredEvents =
        JSON.parse(localStorage.getItem("registeredEvents")) || [];

    if (registeredEvents.includes(eventName)) {
        return;
    }

    registeredEvents.push(eventName);

    localStorage.setItem(
        "registeredEvents",
        JSON.stringify(registeredEvents)
    );

    button.textContent = "Registered";
    button.disabled = true;

    alert("You have registered for " + eventName + "!");
}


// ==========================================
// SHOW REGISTERED EVENTS ON EXPLORE PAGE
// ==========================================

const eventButtons =
    document.querySelectorAll(".event-btn");

if (eventButtons.length > 0) {

    const registeredEvents =
        JSON.parse(localStorage.getItem("registeredEvents")) || [];

    eventButtons.forEach(function (button) {

        const card = button.closest(".event-card");

        if (card) {

            const eventName =
                card.querySelector("h2").textContent;

            if (registeredEvents.includes(eventName)) {

                button.textContent = "Registered";
                button.disabled = true;
            }
        }
    });
}


// ==========================================
// DISPLAY MY EVENTS
// ==========================================

const myEventsContainer =
    document.getElementById("myEventsContainer");

if (myEventsContainer) {

    const registeredEvents =
        JSON.parse(localStorage.getItem("registeredEvents")) || [];

    if (registeredEvents.length === 0) {

        myEventsContainer.innerHTML =
            "<p>You have not registered for any events yet.</p>";

    } else {

        registeredEvents.forEach(function (event) {

            myEventsContainer.innerHTML += `
                <article class="event-card">

                    <h2>${event}</h2>

                    <p>
                        You are registered for this event.
                    </p>

                </article>
            `;
        });
    }
}