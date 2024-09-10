function updateStatusOwner(ownerPid) {
    // Construct URL directly
    let url = `/buh/update-status-owner/${ownerPid}`;
    
    // Create a form dynamically
    let form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    
    // Add CSRF token
    let csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(csrfToken);
    
    // Submit the form
    document.body.appendChild(form);
    form.submit();
}
