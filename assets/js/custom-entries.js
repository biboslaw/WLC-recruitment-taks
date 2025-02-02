document.addEventListener("DOMContentLoaded", function () {
    const entriesContainer = document.querySelector(".custom-entries");

    if (entriesContainer) {
        fetchEntries(1); // Load first page on page load

        // Fetch entries depending on the page number
        function fetchEntries(page) {
            fetch(cfp_ajax.ajax_url, {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({
                    action: "get_entries",
                    page: page,
                    security: cfp_ajax.security
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderEntries(data.data.entries, data.data.total_pages, page);
                } else {
                    entriesContainer.innerHTML = `<p>${data.data.message}</p>`;
                }
            })
            .catch(error => console.error("Error:", error));
        }

        // Render entries with pagination (if needed)
        function renderEntries(entries, totalPages, currentPage) {
            if (entries.length === 0) {
                entriesContainer.innerHTML = "<p>No entries found.</p>";
                return;
            }

            let html = "<ul>";
            entries.forEach(entry => {
                html += `<li data-id="${entry.id}">${entry.first_name} ${entry.last_name} - ${entry.subject}</li>`;
            });
            html += "</ul>";

            // Pagination
            if (totalPages > 1) {
                html += '<div class="pagination">';
                for (let i = 1; i <= totalPages; i++) {
                    const activeClass = i === currentPage ? "active" : "";
                    html += `<button class="page-btn ${activeClass}" data-page="${i}">${i}</button>`;
                }
                html += '</div>';
            }

            entriesContainer.innerHTML = html;

            // Adding event listener to handle swiching pages
            document.querySelectorAll(".page-btn").forEach(btn => {
                btn.addEventListener("click", (e) => {
                    fetchEntries(parseInt(e.target.dataset.page));
                });
            });

            // Adding event lintener to display entry details
            document.querySelectorAll(".custom-entries li").forEach(item => {
                item.addEventListener("click", function () {
                    fetchEntryDetails(this.dataset.id);
                });
            });
        }

        // Fetching entry details
        function fetchEntryDetails(id) {
            fetch(cfp_ajax.ajax_url, {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({
                    action: "get_entry_details",
                    id: id,
                    security: cfp_ajax.security
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const entry = data.data;
                    let detailsHtml = `<p><strong>First Name:</strong> ${entry.first_name}</p>
                                       <p><strong>Last Name:</strong> ${entry.last_name}</p>
                                       <p><strong>Email:</strong> ${entry.email}</p>
                                       <p><strong>Subject:</strong> ${entry.subject}</p>
                                       <p><strong>Message:</strong> ${entry.message}</p>`;
                    entriesContainer.insertAdjacentHTML("beforeend", `<div class="entry-details">${detailsHtml}</div>`);
                } else {
                    alert(data.data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        }
    }
});
