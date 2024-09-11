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

function toggleInfoCollapse() {
    var infoCollapse = document.getElementById('infoCollapse');
    var infoButton = document.getElementById('infoButton');

    // Toggle visibility of the collapse element
    if (infoCollapse.style.display === "none" || infoCollapse.style.display === "") {
        infoCollapse.style.display = "block";
        infoButton.classList.add('active-interest');  // Add active class when opened
    } else {
        infoCollapse.style.display = "none";
        infoButton.classList.remove('active-interest');  // Remove active class when closed
    }
}

