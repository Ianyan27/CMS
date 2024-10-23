const showBuBtn = document.getElementById('show-bu');
const showCountryBtn = document.getElementById('show-country');

const buContainer = document.getElementById('bu');
const countryContainer = document.getElementById('countries');

// Function to hide all tables
function hideAllTables() {
    buContainer.style.display = 'none';
    countryContainer.style.display = 'none';
}

// Show Contacts Table (default)
showBuBtn.addEventListener('click', function() {
    hideAllTables();
    buContainer.style.display = 'block';
});

// Show Archive Table
showCountryBtn.addEventListener('click', function() {
    hideAllTables();
    countryContainer.style.display = 'block';
});
