
// Global variable to hold selected countries
let selectedCountries = [];

// Function to update countries and BUH based on selected BU
function updateCountryCheckboxes() {
    const buDropdown = document.getElementById('buDropdown');
    const selectedBU = buDropdown.value;
    console.log("Selected BU:", selectedBU);

    // Update the selected BU display
    document.getElementById('selectedBU').textContent = selectedBU || 'None';

    // Clear previous checkboxes
    const countryCheckboxes = document.getElementById('countryCheckboxes');
    countryCheckboxes.innerHTML = '';

    // Reset selected country and BUH display
    document.getElementById('selectedCountry').textContent = 'None';
    document.getElementById('selectedBUH').textContent = 'None';

    // Clear BUH dropdown
    const buhDropdown = document.getElementById('buhDropdown');
    buhDropdown.innerHTML = '<option value="" selected disabled>Select BUH</option>'; // Reset options

    // If no BU is selected, clear the BUH display
    if (!selectedBU) {
        console.log("No BU selected. Exiting updateCountryCheckboxes.");
        return; // Do not fetch data if no BU is selected
    }

    // Fetch the countries and BUH from the server
    console.log("Fetching BU data for:", selectedBU);
    fetch(`{{ route('get.bu.data') }}?business_unit=${selectedBU}`)
        .then(response => {
            console.log("Response received from server:", response);
            return response.json();
        })
        .then(data => {
            // Update country checkboxes
            console.log("Data received from server:", data);
            data.countries.forEach(country => {
                const checkbox = document.createElement('div');
                checkbox.classList.add('form-check');
                checkbox.innerHTML = `
                            <input class="form-check-input" type="checkbox" value="${country}" id="${country}" onchange="updateSelectedCountries()">
                            <label class="form-check-label font-educ" for="${country}">${country}</label>
                        `;
                countryCheckboxes.appendChild(checkbox);
            });

            // Store the BUH data by country
            window.buhDataByCountry = data.buh; // Store BUH data globally
            console.log("BUH data by country stored:", window.buhDataByCountry);
        })
        .catch(error => console.error('Error fetching BU data:', error));
}

function updateSelectedCountries() {
    const checkboxes = document.querySelectorAll('#countryCheckboxes input[type="checkbox"]');
    selectedCountries = Array.from(checkboxes)
        .filter(checkbox => checkbox.checked)
        .map(checkbox => checkbox.value);

    console.log("Selected countries updated:", selectedCountries);

    // Update the selected countries display
    document.getElementById('selectedCountry').textContent = selectedCountries.length > 0 ? selectedCountries.join(
        ', ') : 'None';

    // Update the BUH dropdown based on selected countries
    updateBuhDropdown(selectedCountries);

    // Update the selected countries list
    updateSelectedCountriesList();
}

function updateBuhDropdown(selectedCountries) {
    const buhDropdown = document.getElementById('buhDropdown');
    buhDropdown.innerHTML = '<option value="" selected disabled>Select BUH</option>'; // Reset options

    if (selectedCountries.length === 0) {
        console.log("No countries selected for BUH dropdown.");
        document.getElementById('selectedBUH').textContent = 'None';
        return;
    }

    // Collect BUH values based on selected countries
    const buhValues = selectedCountries.flatMap(country => window.buhDataByCountry[country] || []);

    // Populate BUH dropdown with unique BUH values
    const uniqueBuhValues = [...new Set(buhValues)];

    console.log("Unique BUH values collected:", uniqueBuhValues);

    uniqueBuhValues.forEach(buh => {
        const option = document.createElement('option');
        option.value = buh;
        option.textContent = buh;
        buhDropdown.appendChild(option);
    });

    // **Do not update selectedBUH here!**
}

// Add an event listener for the BUH dropdown to update the selectedBUH display when a BUH is selected
document.getElementById('buhDropdown').addEventListener('change', function () {
    const selectedBuh = this.value; // Get the selected BUH
    document.getElementById('selectedBUH').textContent = selectedBuh || 'None'; // Update the display
    console.log("Selected BUH updated to:", selectedBuh);
});


function updateSelectedCountriesList() {
    const selectedCountriesList = document.getElementById('selectedCountriesList');
    selectedCountriesList.innerHTML = ''; // Clear previous list

    selectedCountries.forEach(country => {
        const countryDiv = document.createElement('div');
        countryDiv.classList.add('countries', 'border-educ');

        const countryLabel = document.createElement('span');
        countryLabel.textContent = country;

        const removeButton = document.createElement('button');
        removeButton.textContent = '✖'; // Remove button
        removeButton.classList.add('remove-button');
        removeButton.onclick = function () {
            console.log("Removing country:", country);
            removeCountry(country);
        };

        countryDiv.appendChild(countryLabel);
        countryDiv.appendChild(removeButton);
        selectedCountriesList.appendChild(countryDiv);
    });
}

function removeCountry(country) {
    console.log("Country to remove:", country);
    // Remove country from the selectedCountries array
    selectedCountries = selectedCountries.filter(c => c !== country);

    // Uncheck the checkbox for the removed country
    document.getElementById(country).checked = false;

    // Update the selected countries list display
    updateSelectedCountriesList();

    // Update the displayed selected countries text
    document.getElementById('selectedCountry').textContent = selectedCountries.length > 0 ? selectedCountries.join(
        ', ') : 'None';

    // If no countries are selected, reset the BUH dropdown
    if (selectedCountries.length === 0) {
        const buhDropdown = document.getElementById('buhDropdown');
        buhDropdown.innerHTML = '<option value="" selected disabled>Select BUH</option>'; // Reset options
        document.getElementById('selectedBUH').textContent = 'None';
        console.log("No countries selected. BUH dropdown reset.");
    }
}

// Function to reset selections when changing the BU
function resetSelections() {
    console.log("Resetting selections...");
    selectedCountries = []; // Clear the selected countries
    document.getElementById('selectedCountry').textContent = 'None'; // Update display
    updateSelectedCountriesList(); // Clear the displayed list

    // Clear BUH dropdown and display
    const buhDropdown = document.getElementById('buhDropdown');
    buhDropdown.innerHTML = '<option value="" selected disabled>Select BUH</option>'; // Reset options
    document.getElementById('selectedBUH').textContent = 'None'; // Reset display
}

// Call resetSelections function whenever the BU dropdown changes
document.getElementById('buDropdown').addEventListener('change', resetSelections);