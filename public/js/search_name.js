document.getElementById('search-name').addEventListener('input', function() {
    var input = this.value.toLowerCase();
    var rows = document.querySelectorAll('#sales-agents-table tbody tr');

    rows.forEach(function(row) {
        var nameCell = row.querySelector('td:nth-child(2)'); // Target the first column (Name)
        var nameText = nameCell.textContent || nameCell.innerText;

        if (nameText.toLowerCase().includes(input)) {
            row.style.display = ''; // Show the row if it matches the search input
        } else {
            row.style.display = 'none'; // Hide the row if it doesn't match
        }
    });
});
