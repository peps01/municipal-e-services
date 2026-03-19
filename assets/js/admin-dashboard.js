// Select all dropdown toggles
const dropdownToggles = document.querySelectorAll(".dropdown-toggle");

// Open/close dropdown on click
dropdownToggles.forEach(toggle => {
    toggle.addEventListener("click", function(e) {
        e.preventDefault();
        const parent = this.parentElement;

        // Close other dropdowns first
        document.querySelectorAll(".dropdown").forEach(d => {
            if (d !== parent) d.classList.remove("active");
        });

        // Toggle current dropdown
        parent.classList.toggle("active");
    });
});

// Close dropdown when clicking outside
document.addEventListener("click", function(e) {
    const isDropdown = e.target.closest(".dropdown");
    if (!isDropdown) {
        document.querySelectorAll(".dropdown").forEach(d => d.classList.remove("active"));
    }
});