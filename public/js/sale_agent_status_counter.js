document.addEventListener('DOMContentLoaded', () => {
    const statusSwitches = document.querySelectorAll('.statusSwitch');

    // Initialize counts
    let activeCount = 0;
    let inactiveCount = 0;

    statusSwitches.forEach(switchElement => {
        if (switchElement.checked) {
            activeCount++;
        } else {
            inactiveCount++;
        }
    });

    // Function to update displayed counts
    function updateDisplayedCounts() {
        document.querySelector('#hubspotContactsSection .font-educ').textContent = activeCount;
        document.querySelector('#engagingContactsSection .font-educ').textContent = inactiveCount;
    }

    // Update displayed counts initially
    updateDisplayedCounts();

    // Add event listeners to each switch
    statusSwitches.forEach(switchElement => {
        switchElement.addEventListener('change', function() {
            if (this.checked) {
                // Switching from inactive to active
                activeCount++;
                inactiveCount--;
            } else {
                // Switching from active to inactive
                activeCount--;
                inactiveCount++;
            }

            // Update the displayed counts
            updateDisplayedCounts();
        });
    });
});
