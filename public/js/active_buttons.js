window.onload = function() {
    interestedButton.classList.add('active-interest'); // Add active class to the clicked button

};
// Get the buttons
const interestedButton = document.getElementById('show-contacts');
const archiveButton = document.getElementById('show-archive');
const discardButton = document.getElementById('show-discard');

// Function to remove active classes from all buttons
function clearActiveClasses() {
    interestedButton.classList.remove('active-interest');
    archiveButton.classList.remove('active-archive');
    discardButton.classList.remove('active-discard');
}

// Add click event listeners
interestedButton.addEventListener('click', () => {
    clearActiveClasses(); // Remove all active classes
    interestedButton.classList.add('active-interest'); // Add active class to the clicked button
});

archiveButton.addEventListener('click', () => {
    clearActiveClasses();
    archiveButton.classList.add('active-archive');
});

discardButton.addEventListener('click', () => {
    clearActiveClasses();
    discardButton.classList.add('active-discard');
});