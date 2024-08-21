document.addEventListener('DOMContentLoaded', function() {
    const statusInput = document.getElementById('status');
    const statusValue = statusInput.value;
    switch (statusValue) {
        case 'HubSpot':
            statusInput.style.backgroundColor = '#FFE8E2';
            statusInput.style.color = '#FF5C35';
            break;
        case 'Discard':
            statusInput.style.backgroundColor = '#FF7F86';
            statusInput.style.color = '#BD000C';
            break;
        case 'In Progress':
            statusInput.style.backgroundColor = '#FFF3CD';
            statusInput.style.color = '#FF8300';
            break;
        case 'New':
            statusInput.style.backgroundColor = '#CCE5FF';
            statusInput.style.color = '#318FFC';
            break;
        case 'Archive':
            statusInput.style.backgroundColor = '#E2E3E5';
            statusInput.style.color = '#303030';
            break;
        default:
            statusInput.style.backgroundColor = '#FFFFFF';
    }
});
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('contact-status');

    function updateStatusStyles() {
        const statusValue = statusSelect.value;
        switch (statusValue) {
            case 'HubSpot':
                statusSelect.style.backgroundColor = '#FFE8E2';
                statusSelect.style.color = '#FF5C35';
                break;
            case 'Discard':
                statusSelect.style.backgroundColor = '#FF7F86';
                statusSelect.style.color = '#BD000C';
                break;
            case 'InProgress':
                statusSelect.style.backgroundColor = '#FFF3CD';
                statusSelect.style.color = '#FF8300';
                break;
            case 'New':
                statusSelect.style.backgroundColor = '#CCE5FF';
                statusSelect.style.color = '#318FFC';
                break;
            case 'Archive':
                statusSelect.style.backgroundColor = '#E2E3E5';
                statusSelect.style.color = '#303030';
                break;
            default:
                statusSelect.style.backgroundColor = '#FFFFFF';
                statusSelect.style.color = '#000000';
        }
    }

    // Update styles on page load
    updateStatusStyles();

    // Update styles when the selected option changes
    statusSelect.addEventListener('change', updateStatusStyles);
});
