function toggleSort(downIconId, upIconId) {
    const sortDown = document.getElementById(downIconId);
    const sortUp = document.getElementById(upIconId);

    if (sortDown.style.display === "none") {
        sortDown.style.display = "inline";
        sortUp.style.display = "none";
    } else {
        sortDown.style.display = "none";
        sortUp.style.display = "inline";
    }
}

function sortTable(tableId, columnName, order) {
    let table = document.getElementById(tableId),
        rows,
        switching,
        i,
        x,
        y,
        shouldSwitch,
        columnIndex;

    // Determine column index based on the column name
    if (columnName === "name") columnIndex = 1;
    else if (columnName === "email") columnIndex = 2;
    else if (columnName === "role" || columnName === "country") columnIndex = 3;
    else if (columnName === "country" || columnName === "bu") columnIndex = 4;

    switching = true;

    // Loop until no switching is needed
    while (switching) {
        switching = false;
        rows = table.getElementsByTagName("tr");

        for (i = 1; i < rows.length - 1; i++) {
            shouldSwitch = false;
            x = rows[i].querySelectorAll("td")[columnIndex];
            y = rows[i + 1].querySelectorAll("td")[columnIndex];

            if (
                order === "asc" &&
                x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()
            ) {
                shouldSwitch = true;
                break;
            } else if (
                order === "desc" &&
                x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()
            ) {
                shouldSwitch = true;
                break;
            }
        }
        if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
        }
    }
    reassignRowNumbers(tableId);
}

function reassignRowNumbers(tableId) {
    const table = document.getElementById(tableId);
    const rows = table.getElementsByTagName("tr");

    for (let i = 1; i < rows.length; i++) {
        rows[i].querySelectorAll("td")[0].innerText = i; // Update "No #" column (index 0)
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
    if (columnName === "name") {
        columnIndex = 0; // Index for 'Agent' column
    } else if (columnName === "country") {
        columnIndex = 2; // Index for 'Country' column
    } else {
        return; // Exit if columnName is not handled
    }

    // Loop until no switching has been done
    while (switching) {
        switching = false;
        rows = table.tBodies[0].rows;

        for (i = 0; i < rows.length - 1; i++) {
            shouldSwitch = false;

            x = rows[i].cells[columnIndex].innerText.trim().toLowerCase();
            y = rows[i + 1].cells[columnIndex].innerText.trim().toLowerCase();

            if (order === "asc" && x > y) {
                shouldSwitch = true;
                break;
            } else if (order === "desc" && x < y) {
                shouldSwitch = true;
                break;
            }
        }
        if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
        }
    }
    // // Reassign "No #" column values
    // reassignRowNumbers();
}

// function reassignRowNumbers() {
//     const table = document.querySelector("#sales-agents-table");
//     const rows = table.tBodies[0].rows;

//     for (let i = 0; i < rows.length; i++) {
//         // Make sure we're updating only the "No #" column (second column, index 1)
//         let numberCell = rows[i].cells[1];

//         // Ensure that the second column (index 1) is updated with the row number, not the checkbox
//         if (numberCell) {
//             numberCell.innerText = i + 1; // Update the number starting from 1
//         }
//     }
// }
