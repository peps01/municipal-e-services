const dropdownToggle = document.querySelector(".dropdown-toggle");
const dropdown = document.querySelector(".dropdown");

dropdownToggle.addEventListener("click", function(e) {
    e.preventDefault();
    dropdown.classList.toggle("active");
});
const profileUpload = document.getElementById("profile-upload");
const profilePic = document.getElementById("profile-pic");

profileUpload.addEventListener("change", function() {

    const file = this.files[0];

    if (file) {
        const reader = new FileReader();

        reader.addEventListener("load", function() {
            profilePic.src = this.result;
        });

        reader.readAsDataURL(file);
    }
});

// AJAX page loading for dashboard content
document.addEventListener("DOMContentLoaded", function() {

    const pageContent = document.getElementById("page-content");

    // Select all links: either _content.php or ajax-link class
    const links = document.querySelectorAll(".menu a, .ajax-link");

    function loadPage(url) {

        pageContent.classList.add("fade-out");

        setTimeout(() => {

            fetch(url)
                .then(res => {
                    if (!res.ok) throw new Error("Failed");
                    return res.text();
                })
                .then(data => {

                    pageContent.innerHTML = data;

                    pageContent.classList.remove("fade-out");
                    pageContent.classList.add("fade-in");

                    setTimeout(() => {
                        pageContent.classList.remove("fade-in");
                    }, 300);

                })
                .catch(() => {
                    pageContent.innerHTML = "<p>Error loading page</p>";
                });

        }, 150);

    }

    links.forEach(link => {

        link.addEventListener("click", function(e) {

            const url = this.getAttribute("href");

            // Only trigger AJAX for links ending with _content.php OR having ajax-link class
            if (!url.includes("_content.php") && !this.classList.contains("ajax-link")) return;

            e.preventDefault();
            loadPage(url);

        });

    });

});

// profile updater
document.addEventListener("submit", function(e) {

    if (e.target.classList.contains("profile-form")) {

        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        fetch("profile_content.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.text())
            .then(data => {

                const pageContent = document.getElementById("page-content");
                pageContent.innerHTML = data;

                const alert = pageContent.querySelector(".alert");

                if (alert) {
                    setTimeout(() => {
                        alert.style.opacity = "0";
                        alert.style.transition = "opacity 0.5s";

                        setTimeout(() => {
                            alert.remove();
                        }, 500);

                    }, 2000);
                }

            })
            .catch(err => console.log(err));

    }

});