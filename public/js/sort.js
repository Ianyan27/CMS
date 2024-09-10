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
            rows = table.rows;
            
            // Loop through all table rows except the first (headers)
            for (i = 1; i < (rows.length - 1); i++) {
                shouldSwitch = false;
                
                // Determine the column index based on columnName
                let columnIndex;
                if (columnName === 'name') {
                    columnIndex = 1; // Index for the 'Name' column
                }else if(columnName === 'email'){
                    columnIndex = 2; // Indes for the 'Email' column
                }else if(columnName === 'role' || columnName === 'country') {
                    columnIndex = 3; // Index for the 'Role' column
                } else if(columnName === 'country' || columnName === 'bu'){
                    columnIndex = 4;
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

    function reassignRowNumbersTableContainer(){
        const table = document.querySelector(".table");
        const rows = table.rows;

        for (let i = 1; i < rows.length; i++) {
            rows[i].querySelectorAll("td")[0].innerText = i; // Reassign "No #" column (index 1)
        }
    }
    

/*
*
*   Sorting for Sales Agent Page
* 
*/
    function sortByColumn(columnName, order) {
        let table, rows, switching, i, x, y, shouldSwitch;
        table = document.querySelector("#sales-agents-table");
        switching = true;

        // Define column indices based on columnName
        let columnIndex;
        if (columnName === 'agent') {
            columnIndex = 2; // Index for 'Agent' column
        } else if (columnName === 'country') {
            columnIndex = 3; // Index for 'Country' column
        } else {
            return; // Exit if columnName is not handled
        }

        // Loop until no switching has been done
        while (switching) {
            switching = false;
            rows = table.tBodies[0].rows;

            for (i = 0; i < (rows.length - 1); i++) {
                shouldSwitch = false;

                x = rows[i].cells[columnIndex].innerText.trim().toLowerCase();
                y = rows[i + 1].cells[columnIndex].innerText.trim().toLowerCase();

                if (order === 'asc' && x > y) {
                    shouldSwitch = true;
                    break;
                } else if (order === 'desc' && x < y) {
                    shouldSwitch = true;
                    break;
                }
            }
            if (shouldSwitch) {
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
            }
        }
        // Reassign "No #" column values
        reassignRowNumbers();
    }

    function reassignRowNumbers() {
        const table = document.querySelector("#sales-agents-table");
        const rows = table.tBodies[0].rows;

        for (let i = 0; i < rows.length; i++) {
            rows[i].cells[1].innerText = i + 1; // Reassign "No #" column (index 1)
        }
    }
