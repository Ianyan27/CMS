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
        if (selectedFilters.length === 0 || selectedFilters.includes(status)) {
            row.style.display = ''; // Show row
        } else {
            row.style.display = 'none'; // Hide row
        }
    });
}