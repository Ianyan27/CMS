// Select all checkboxes with the class 'statusSwitch'
document.querySelectorAll('.statusSwitch').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        const isChecked = this.checked;
        const statusText = this.nextElementSibling.querySelector('.status-text'); // Find the status text inside the slider
        const id = this.getAttribute('data-saleagent-pid');
        const userType = this.getAttribute('data-user-type');
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
    });
});
