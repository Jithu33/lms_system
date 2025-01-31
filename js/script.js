 // script.js
// Add any JavaScript functionality here if needed

// Example: Form validation for signup and login pages
document.addEventListener("DOMContentLoaded", function () {
    const forms = document.querySelectorAll("form");

    forms.forEach((form) => {
        form.addEventListener("submit", function (event) {
            let valid = true;

            // Check if all required fields are filled
            form.querySelectorAll("input[required], select[required]").forEach((input) => {
                if (!input.value.trim()) {
                    valid = false;
                    input.classList.add("error");
                } else {
                    input.classList.remove("error");
                }
            });

            if (!valid) {
                event.preventDefault();
                alert("Please fill out all required fields.");
            }
        });
    });
});
