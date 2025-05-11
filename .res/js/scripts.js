document.addEventListener("DOMContentLoaded", function () {
    initSearch();
    initSort();
});

function initSearch() {
    document.getElementById("searchInput").addEventListener("keyup", function () {
        var input = removeAccents(this.value.toLowerCase());
        var rows = document.querySelectorAll(".table-main tr");

        rows.forEach(function (row, index) {
            if (index === 0) return; // Saltar la fila de encabezados

            var text = removeAccents(row.textContent.toLowerCase());
            row.style.display = text.includes(input) ? "" : "none";
        });
    });
}

function removeAccents(str) {
    return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
}

function initSort() {
    const headers = document.querySelectorAll(".table-main th[data-sortable='true']");
    headers.forEach((header, index) => {
        header.style.cursor = "pointer";
        header.addEventListener("click", function () {
            sortTableByColumn(index, header.getAttribute("data-type"));
        });
    });
}

function sortTableByColumn(columnIndex, type) {
    const table = document.querySelector(".table-main");
    const rows = Array.from(table.querySelectorAll("tr:nth-child(n+2)")); // omite el encabezado
    let ascending = table.getAttribute("data-sort-dir") !== "asc";

    rows.sort((a, b) => {
        const cellA = a.children[columnIndex].textContent.trim();
        const cellB = b.children[columnIndex].textContent.trim();

        let valA = type === "number" ? parseFloat(cellA) : cellA.toLowerCase();
        let valB = type === "number" ? parseFloat(cellB) : cellB.toLowerCase();

        valA = removeAccents(valA);
        valB = removeAccents(valB);

        if (valA < valB) return ascending ? -1 : 1;
        if (valA > valB) return ascending ? 1 : -1;
        return 0;
    });

    // reinsertar filas ordenadas
    const tbody = table.querySelector("tbody") || table;
    rows.forEach(row => tbody.appendChild(row));

    table.setAttribute("data-sort-dir", ascending ? "asc" : "desc");
}

function back() {   
    if (document.referrer) {
        window.location = document.referrer;
    }
}