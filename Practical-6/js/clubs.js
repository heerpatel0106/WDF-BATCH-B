// ==========================================
// STUDENT DASHBOARD - NAME
// ==========================================

const studentName = document.getElementById("studentName");

if (studentName) {

    const savedUser = localStorage.getItem("campusHubUser");

    if (savedUser !== null) {

        const user = JSON.parse(savedUser);

        studentName.textContent = user.name.toUpperCase();
    }
}


// ==========================================
// JOIN CLUB
// ==========================================

function joinClub(clubName, button) {

    let joinedClubs =
        JSON.parse(localStorage.getItem("joinedClubs")) || [];

    if (joinedClubs.includes(clubName)) {
        return;
    }

    joinedClubs.push(clubName);

    localStorage.setItem(
        "joinedClubs",
        JSON.stringify(joinedClubs)
    );

    button.textContent = "Joined";
    button.disabled = true;

    alert("You have joined " + clubName + "!");
}


// ==========================================
// SHOW JOINED CLUBS ON EXPLORE PAGE
// ==========================================

const clubButtons = document.querySelectorAll(".join-btn");

if (clubButtons.length > 0) {

    const joinedClubs =
        JSON.parse(localStorage.getItem("joinedClubs")) || [];

    clubButtons.forEach(function (button) {

        const card = button.closest(".club-card");

        if (card) {

            const clubName =
                card.querySelector("h2").textContent;

            if (joinedClubs.includes(clubName)) {

                button.textContent = "Joined";
                button.disabled = true;
            }
        }
    });
}


// ==========================================
// DISPLAY MY CLUBS
// ==========================================

const myClubsContainer =
    document.getElementById("myClubsContainer");

if (myClubsContainer) {

    const joinedClubs =
        JSON.parse(localStorage.getItem("joinedClubs")) || [];

    if (joinedClubs.length === 0) {

        myClubsContainer.innerHTML =
            "<p>You have not joined any clubs yet.</p>";

    } else {

        joinedClubs.forEach(function (club) {

            myClubsContainer.innerHTML += `
                <article class="club-card">

                    <h2>${club}</h2>

                    <p>
                        You are currently a member of this club.
                    </p>

                </article>
            `;
        });
    }
}


// ==========================================
// FETCH CLUB JSON DATA
// ==========================================

fetch("../json/clubs.json")
    .then(response => response.json())
    .then(data => {
        console.log(data);
    });