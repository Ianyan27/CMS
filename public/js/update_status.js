document.getElementById('statusSwitch').addEventListener('change', function() {
    const isChecked = this.checked;
    const statusText = document.querySelector('.owner-status');
    const ownerPid = this.getAttribute('data-owner-pid');
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

    // Log status and owner PID for debugging
    console.log('Changing status to:', isChecked ? 'active' : 'inactive');
    console.log('Owner PID:', ownerPid);

    // Send AJAX request to update status on the server
    fetch(`/buh/update-status-owner/${ownerPid}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken // Include CSRF token from meta tag
        },
        body: JSON.stringify({
            status: isChecked ? 'active' : 'inactive'
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        // Optionally handle success feedback here
    })
    .catch(error => {
        console.error('Error updating status:', error);
        // Optionally handle error feedback here
    });
});
