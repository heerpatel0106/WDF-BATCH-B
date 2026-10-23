// ==========================================
// DASHBOARD COUNTS
// ==========================================

const myClubsCount =
    document.getElementById("myClubsCount");

const myEventsCount =
    document.getElementById("myEventsCount");

const myCommunitiesCount =
    document.getElementById("myCommunitiesCount");


if (myClubsCount) {

    const joinedClubs =
        JSON.parse(localStorage.getItem("joinedClubs")) || [];

    myClubsCount.textContent = joinedClubs.length;
}


if (myEventsCount) {

    const registeredEvents =
        JSON.parse(localStorage.getItem("registeredEvents")) || [];

    myEventsCount.textContent = registeredEvents.length;
}


if (myCommunitiesCount) {

    const joinedCommunities =
        JSON.parse(localStorage.getItem("joinedCommunities")) || [];

    myCommunitiesCount.textContent = joinedCommunities.length;
}