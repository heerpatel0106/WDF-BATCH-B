// ==========================================
// LOGIN
// ==========================================

function loginUser() {

    const role = document.getElementById("role").value;
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;

    if (role === "" || email === "" || password === "") {
        alert("Please fill in all the required fields.");
        return;
    }

    const savedUser = localStorage.getItem("campusHubUser");

    if (savedUser === null) {
        alert("No account found. Please sign up first.");
        return;
    }

    const user = JSON.parse(savedUser);

    if (
        email === user.email &&
        password === user.password &&
        role === user.role
    ) {

        // Store login status
        localStorage.setItem("isLoggedIn", "true");

        alert("Login successful!");

        if (role === "student") {
            window.location.href = "student-dashboard.html";
        }

        else if (role === "faculty") {
            window.location.href = "faculty-dashboard.html";
        }

        else if (role === "coordinator") {
            window.location.href = "coordinator-dashboard.html";
        }

    } else {

        alert("Invalid email, password, or role.");
    }
}


// ==========================================
// RESET LOGIN FORM
// ==========================================

function resetForm() {

    document.getElementById("role").value = "";
    document.getElementById("email").value = "";
    document.getElementById("password").value = "";
}


// ==========================================
// RESET SIGNUP FORM
// ==========================================

function resetSignupForm() {

    document.getElementById("name").value = "";
    document.getElementById("role").value = "";
    document.getElementById("email").value = "";
    document.getElementById("password").value = "";
}