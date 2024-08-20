document.getElementById('search-input').addEventListener('input', function() {
    let input = this.value.toLowerCase();
    let table = document.querySelector('.table');
    let rows = table.querySelectorAll('tbody tr');

    rows.forEach(row => {
        let emailCell = row.querySelectorAll('td')[2]; // Assuming email is in the 3th column
        let email = emailCell.textContent.toLowerCase();

        if (email.includes(input)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});