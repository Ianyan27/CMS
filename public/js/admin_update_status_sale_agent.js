// Attach event listener to a parent element (e.g., the container holding the checkboxes)
document.querySelector('.owner-contacts-container').addEventListener('change', function(event) {
    // Check if the event is triggered by a checkbox with the class 'statusSwitch'
    if (event.target.classList.contains('statusSwitch')) {
        const checkbox = event.target;
        const isChecked = checkbox.checked;
        const statusText = checkbox.nextElementSibling.querySelector('.status-text'); // Find the status text inside the slider
        const id = checkbox.getAttribute('data-saleagent-pid');
        const userType = checkbox.getAttribute('data-user-type');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Update status text and background color dynamically
        if (isChecked) {
            statusText.textContent = 'Active';
        } else {
            statusText.textContent = 'Inactive';
        }

        // Determine the correct endpoint based on user type
        const endpoint = userType == 'Admin' 
            ? `/admin/update-status-sale-agent/${id}` 
            : `/buh/update-status-sale-agent/${id}`;

        // Log status, owner PID, and endpoint for debugging
        console.log('Changing status to:', isChecked ? 'active' : 'inactive');
        console.log('Sale Agent PID:', id);
        console.log('Role:', userType);
        console.log('Endpoint:', endpoint);

        // Send AJAX request to update status on the server
        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken // Include CSRF token from meta tag
            },
            body: JSON.stringify({
                status: isChecked ? 'active' : 'inactive'
            })
        })
        .then(response => response.json())
        .then(data => {
            // Check the response and show modal if needed
            if (data.message) {
                $('#successModal .modal-body').text(data.message);
                $('#successModal').modal('show');
            } else {
                console.error('Unexpected response:', data);
            }
        })
        .catch(error => {
            console.error('Error updating status:', error);
            $('#errorModal .modal-body').text('An error occurred while updating status.');
            $('#errorModal').modal('show');
        });
    }
});

document.querySelectorAll('.statusSwitch').forEach(switchElement => {
    switchElement.addEventListener('change', function() {
        const row = this.closest('tr');  // Get the current row
        const newStatus = this.checked ? 'active' : 'inactive';  // Determine the new status
        row.setAttribute('data-status', newStatus);  // Update the row's data-status attribute

        // Optionally update the status text displayed in the UI
        const statusText = row.querySelector('.status-text');
        if (statusText) {
            statusText.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
        }
    });
});
