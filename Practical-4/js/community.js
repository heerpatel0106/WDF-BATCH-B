// ==========================================
// JOIN COMMUNITY
// ==========================================

function joinCommunity(communityName, button) {

    let joinedCommunities =
        JSON.parse(localStorage.getItem("joinedCommunities")) || [];

    if (joinedCommunities.includes(communityName)) {
        return;
    }

    joinedCommunities.push(communityName);

    localStorage.setItem(
        "joinedCommunities",
        JSON.stringify(joinedCommunities)
    );

    button.textContent = "Joined";
    button.disabled = true;

    alert("You have joined " + communityName + "!");
}


// ==========================================
// SHOW JOINED COMMUNITIES ON EXPLORE PAGE
// ==========================================

const communityButtons =
    document.querySelectorAll(".community-card .join-btn");

if (communityButtons.length > 0) {

    const joinedCommunities =
        JSON.parse(localStorage.getItem("joinedCommunities")) || [];

    communityButtons.forEach(function (button) {

        const card = button.closest(".community-card");

        if (card) {

            const communityName =
                card.querySelector("h2").textContent;

            if (joinedCommunities.includes(communityName)) {

                button.textContent = "Joined";
                button.disabled = true;
            }
        }
    });
}


// ==========================================
// DISPLAY MY COMMUNITIES
// ==========================================

const myCommunitiesContainer =
    document.getElementById("myCommunitiesContainer");

if (myCommunitiesContainer) {

    const joinedCommunities =
        JSON.parse(localStorage.getItem("joinedCommunities")) || [];

    if (joinedCommunities.length === 0) {

        myCommunitiesContainer.innerHTML =
            "<p>You have not joined any communities yet.</p>";

    } else {

        joinedCommunities.forEach(function (community) {

            myCommunitiesContainer.innerHTML += `
                <article class="community-card">

                    <h2>${community}</h2>

                    <p>
                        You are a member of this community.
                    </p>

                </article>
            `;
        });
    }
}