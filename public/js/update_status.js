document.getElementById('statusSwitch').addEventListener('change', function() {
    const isChecked = this.checked;
    const statusText = document.querySelector('.owner-status');
    const ownerPid = this.getAttribute('data-owner-pid');
    const userType = this.getAttribute('data-user-type'); // Attribute indicating if it's admin or buh
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Update status text
    if (isChecked) {
        statusText.textContent = 'Status: Active';
        statusText.classList.remove('inactive-text');
        statusText.classList.add('status-text');
    } else {
        statusText.textContent = 'Status: Inactive';
        statusText.classList.remove('status-text');
        statusText.classList.add('inactive-text');
    }

    // Determine the correct endpoint based on user type
    const endpoint = userType == 'Admin' 
        ? `/admin/update-status-sale-agent/${ownerPid}` 
        : `/buh/update-status-sale-agent/${ownerPid}`;

    // Log status, owner PID, and endpoint for debugging
    console.log('Changing status to:', isChecked ? 'active' : 'inactive');
    console.log('Owner PID:', ownerPid);
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
