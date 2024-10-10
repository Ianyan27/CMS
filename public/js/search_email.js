document.getElementById('search-input').addEventListener('input', function() {
    let input = this.value.toLowerCase();

    // Get all tables to apply the search
    let tables = document.querySelectorAll('.table-container table');

    tables.forEach(table => {
        let rows = table.querySelectorAll('tbody tr');

        rows.forEach(row => {
            let usernameCell = row.querySelectorAll('td')[1]; // Assuming email is in the 3rd column
            let username = usernameCell ? usernameCell.textContent.toLowerCase() : '';

            let emailCell = row.querySelectorAll('td')[2]; // Assuming email is in the 3rd column
            let email = emailCell ? emailCell.textContent.toLowerCase() : '';

            let buhNameCell = row.querySelectorAll('td')[3]; // Assuming email is in the 3rd column
            let buhName = buhNameCell ? buhNameCell.textContent.toLowerCase() : '';

            let buhEmailCell = row.querySelectorAll('td')[4]; // Assuming email is in the 3rd column
            let buhEmail = buhEmailCell ? buhEmailCell.textContent.toLowerCase() : '';

            if (username.includes(input) || email.includes(input) ||buhEmail.includes(input) || buhName.includes(input)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
