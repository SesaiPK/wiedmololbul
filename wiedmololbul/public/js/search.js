document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.querySelector('input[placeholder="Search..."]');
    const searchForm = document.createElement("form");

    searchForm.method = "GET";
    searchForm.action = "/homepage";

    // Umieść input w formularzu
    searchInput.parentNode.replaceChild(searchForm, searchInput);
    searchForm.appendChild(searchInput);

    searchInput.addEventListener("keypress", function (event) {
        if (event.key === "Enter") {
            event.preventDefault(); // Zapobiega domyślnemu odświeżeniu strony
            searchForm.submit(); // Wysyła formularz z frazą do Symfony
        }
    });
});
