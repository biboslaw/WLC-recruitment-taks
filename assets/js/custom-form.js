document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector(".custom-form");

    if (form) {
        // Pre-fill form fields if user data is available
        form.querySelector("[name='first_name']").value = cfp_user.first_name || '';
        form.querySelector("[name='last_name']").value = cfp_user.last_name || '';
        form.querySelector("[name='email']").value = cfp_user.email || '';

        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(form);
            formData.append("action", "submit_feedback");
            formData.append("security", cfp_user.security);

            // Sending entry after form submission
            fetch(cfp_user.ajax_url, {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    form.innerHTML = "<p>Thank you for sending us your feedback</p>";
                } else {
                    alert(data.data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        });
    }
});
