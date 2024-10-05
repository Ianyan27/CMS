const showActivitiesBtn = document.getElementById('show-activities');
const showDeletedActivitiesBtn = document.getElementById('show-deleted-activities');
const addActivityBtn = document.getElementById('addActivityBtn');

const activityContainer = document.getElementById('activity-table');
const deletedActivityContainer = document.getElementById('deleted-activity-table');

// Function to hide all tables
function hideAllTables() {
    activityContainer.style.display = 'none';
    deletedActivityContainer.style.display = 'none';
}

// Function to deactivate both buttons
function deactivateAllButtons() {
    showActivitiesBtn.classList.remove('active-interest');
    showDeletedActivitiesBtn.classList.remove('active-archive');
}

// Show Activities Table and set the first button as active (default)
showActivitiesBtn.addEventListener('click', function() {
    hideAllTables();
    activityContainer.style.display = 'block';
    addActivityBtn.style.display = 'block';

    // Set active button
    deactivateAllButtons();
    showActivitiesBtn.classList.add('active-interest');
});

// Show Deleted Activities Table and set the second button as active
showDeletedActivitiesBtn.addEventListener('click', function() {
    hideAllTables();
    deletedActivityContainer.style.display = 'block';
    addActivityBtn.style.display = 'none';

    // Set active button
    deactivateAllButtons();
    showDeletedActivitiesBtn.classList.add('active-archive');
});

// Initialize by showing the activities table and setting the first button as active
hideAllTables();
activityContainer.style.display = 'block'; // Default table
showActivitiesBtn.classList.add('active-interest'); // Default active button
