document.getElementById('select-all').addEventListener('click', function(event) {
    const checkboxes = document.querySelectorAll('.contact-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = event.target.checked;
    });
});
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.contact-checkbox');
    const selectedCount = document.getElementById('selectedCount');
    
    // Function to update the count
    function updateCount() {
        const checkedCount = document.querySelectorAll('.contact-checkbox:checked').length;
        selectedCount.textContent = checkedCount;
    }

    // Attach the updateCount function to each checkbox
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateCount);
    });

    // Also update the count if the "Select All" checkbox is clicked
    document.getElementById('select-all').addEventListener('change', updateCount);
});