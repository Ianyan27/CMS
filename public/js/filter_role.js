function toggleFilter() {
    const filterContainer = document.getElementById('filterContainer');
    filterContainer.style.display = (filterContainer.style.display === 'none' 
    || filterContainer.style.display === '') ? 'block' : 'none';
}
// Apply the filter to the table
function applyFilter() {
    const checkboxes = document.querySelectorAll('#filterContainer input[type="checkbox"]');
    const rows = document.querySelectorAll('#user-table tbody tr');
    let selectedFilters = [];
    // Gather selected filters
    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            selectedFilters.push(checkbox.value);
            console.log(checkbox.value);
        }
    });
    // Show/hide rows based on filter
    rows.forEach(row => {
        const role = row.getAttribute('data-role');
        console.log('Row Role: ' + role);
    
        if (selectedFilters.length === 0 || selectedFilters.includes(role)) {
            row.style.display = ''; // Show row
        } else {
            row.style.display = 'none'; // Hide row
        }
    });
}

function toggleFilterRole(){
    const filterContainer = document.getElementById('filterStatusContainer');
    filterContainer.style.display = (filterContainer.style.display === 'none' 
    || filterContainer.style.display === '') ? 'block' : 'none';
}
function applyRoleFilter() {
    const checkboxes = document.querySelectorAll('#filterStatusContainer input[type="checkbox"]');
    const rows = document.querySelectorAll('#user-table tbody tr');
    let selectedFilters = [];
    // Gather selected filters
    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            selectedFilters.push(checkbox.value);
            console.log(checkbox.value);
            
        }
    });
    // Show/hide rows based on filter
    rows.forEach(row => {
        const role = row.getAttribute('data-role');
        console.log('Row role:', role); // Debug
        if (selectedFilters.length === 0 || selectedFilters.includes(role)) {
            row.style.display = ''; // Show row
        } else {
            row.style.display = 'none'; // Hide row
        }
    });
}

function toggleInfoCollapse() {
    var infoCollapse = document.getElementById('infoCollapse');
    if (infoCollapse.style.display === 'none' || infoCollapse.style.display === '') {
        infoCollapse.style.display = 'block';
    } else {
        infoCollapse.style.display = 'none';
    }
}

