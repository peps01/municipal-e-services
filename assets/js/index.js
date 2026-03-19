// login form toggle
const loginLink = document.querySelector(".login-link");
const loginForm = document.querySelector(".login-form");

loginLink.addEventListener("click", function(e) {
    e.preventDefault();

    if (loginForm.style.display === "block") {
        loginForm.style.display = "none";
        return;
    }

    const rect = loginLink.getBoundingClientRect();

    loginForm.style.display = "block";

    // For fixed position, use rect values directly
    loginForm.style.top = rect.bottom + 10 + "px"; // 10px gap below the button
    loginForm.style.left = rect.right - loginForm.offsetWidth + "px";
});

// Click outside to close login
document.addEventListener("click", function(e) {
    if (!loginForm.contains(e.target) && !loginLink.contains(e.target)) {
        loginForm.style.display = "none";
    }
});

// Register popup toggle
const registerLink = document.querySelector(".register-popup-link");
const registerPopup = document.getElementById("registerPopup");
const registerForm = document.getElementById("registerForm");
const registerClose = document.getElementById("registerClose");

// Toggle register popup
registerLink.addEventListener("click", function(e) {
    e.preventDefault();
    if (loginForm) loginForm.style.display = "none"; // Close login form if exists
    registerPopup.style.display = "flex"; // Show register popup
});

// Close popup when clicking the X button and reset form
registerClose.addEventListener("click", () => {
    registerPopup.style.display = "none";
    registerForm.reset(); // reset only when clicking X
});

// Close popup when clicking outside the form (overlay only)
// preserves typed data
document.addEventListener("click", function(e) {
    if (registerPopup.style.display === "flex" && e.target === registerPopup) {
        registerPopup.style.display = "none";
        // do NOT reset form here — keeps inputs intact
    }
});

// Register form submission
registerForm.addEventListener("submit", function(e) {
    e.preventDefault();

    const formData = new FormData(registerForm);

    fetch("backend/register.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.text())
        .then(data => {
            showRegisterMessage(data);

            if (data.toLowerCase().includes("success")) {
                registerForm.reset();
                registerPopup.style.display = "none";
            }
        });
});

// Show styled messages
function showRegisterMessage(message) {
    const msg = document.getElementById("registerMsg");
    msg.innerText = message;
    msg.style.display = "block";

    if (message.toLowerCase().includes("success")) {
        msg.className = "register-message success";
    } else {
        msg.className = "register-message error";
    }

    setTimeout(() => {
        msg.style.display = "none";
    }, 3000);
}
// Scroll Services Function
function scrollServices(direction) {

    const container = document.getElementById("serviceContainer");

    const cardWidth = container.querySelector(".service-card").offsetWidth + 20;

    container.scrollLeft += direction * cardWidth * 3;

}

const params = new URLSearchParams(window.location.search);
const error = params.get("error");
const msgDiv = document.getElementById('errorMessage');

if (error) {
    msgDiv.textContent = decodeURIComponent(error);
    msgDiv.style.display = 'block';

    setTimeout(() => {
        msgDiv.style.display = 'none';
    }, 4000);
}