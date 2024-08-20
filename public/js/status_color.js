
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