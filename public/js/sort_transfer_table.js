function toggleSort(downIconId, upIconId) {
    const sortDown = document.getElementById(downIconId);
    const sortUp = document.getElementById(upIconId);

    if (sortDown.style.display === 'none') {
        sortDown.style.display = 'inline';
        sortUp.style.display = 'none';
    } else {
        sortDown.style.display = 'none';
        sortUp.style.display = 'inline';
    }
}
function sortTable(columnName, order) {
    let table, rows, switching, i, x, y, shouldSwitch;
    table = document.querySelector(".table");
    switching = true;
    
    // Loop until no switching has been done
    while (switching) {
        switching = false;
        rows = table.tBodies[0].rows; // Ensure we're only working with tbody rows
        
        // Loop through all table rows except the first (headers)
        for (i = 0; i < (rows.length - 1); i++) {
            shouldSwitch = false;
            
            // Determine the column index based on columnName
            let columnIndex;
            switch (columnName) {
                case 'name':
                    columnIndex = 2; // Index for the 'Name' column
                    break;
                case 'email':
                    columnIndex = 3; // Index for the 'Email' column
                    break;
                case 'country':
                    columnIndex = 5; // Index for the 'Country' column
                    break;
                default:
                    columnIndex = -1; // Invalid column
                    break;
            }

            // Compare the two elements in the column to see if they should switch
            x = rows[i].querySelectorAll("td")[columnIndex];
            y = rows[i + 1].querySelectorAll("td")[columnIndex];
            
            if (order === 'asc' && x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                shouldSwitch = true;
                break;
            } else if (order === 'desc' && x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                shouldSwitch = true;
                break;
            }
        }
        if (shouldSwitch) {
            // If a switch has been marked, make the switch and mark the switch as done
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
        }
    }
    reassignRowNumbersTableContainer();
}

function reassignRowNumbersTableContainer() {
    const table = document.querySelector(".table");
    const rows = table.tBodies[0].rows; // Only get rows in the tbody

    // Loop through all rows, starting from index 0
    for (let i = 0; i < rows.length; i++) {
        // Ensure we're updating the "No #" column (index 1), not the checkbox (index 0)
        rows[i].querySelectorAll("td")[1].innerText = i + 1; // Assign row number starting from 1
    }
}