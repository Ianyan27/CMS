function toggleFilter() {
    const filterContainer = document.getElementById('filterContainer');
    filterContainer.style.display = (filterContainer.style.display === 'none' 
    || filterContainer.style.display === '') ? 'block' : 'none';
}
// Apply the filter to the table
function applyFilter() {
    const checkboxes = document.querySelectorAll('#filterContainer input[type="checkbox"]');
    const rows = document.querySelectorAll('#contacts-table tbody tr');
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
        const status = row.getAttribute('data-status');
        console.log('Row Status: ' + status);
    
        if (selectedFilters.length === 0 || selectedFilters.includes(status)) {
            row.style.display = ''; // Show row
        } else {
            row.style.display = 'none'; // Hide row
        }
    });
}

function toggleFilterStatus(){
    const filterContainer = document.getElementById('filterStatusContainer');
    filterContainer.style.display = (filterContainer.style.display === 'none' 
    || filterContainer.style.display === '') ? 'block' : 'none';
}
function applyStatusFilter() {
    const checkboxes = document.querySelectorAll('#filterStatusContainer input[type="checkbox"]');
    const rows = document.querySelectorAll('#sales-agents-table tbody tr');
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
        const status = row.getAttribute('data-status');
        console.log('Row Status:', status); // Debug
        if (selectedFilters.length === 0 || selectedFilters.includes(status)) {
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

