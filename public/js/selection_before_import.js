const buCountryMap = {
    "SG Retail": ["Singapore"],
    "HED": ["Malaysia", "Myanmar", "Indonesia", "Philippines", "Vietnam", "Cambodia", "Laos", "Thailand", "India", "Sri Lanka", "Others"],
    "Alliance": ["Malaysia", "Myanmar", "Indonesia", "Philippines", "Vietnam", "Cambodia", "Laos", "Thailand", "India", "Sri Lanka", "Others"],
    "Enterprise International": ["Malaysia", "Indonesia", "Philippines"],
    "Enterprise Singapore": ["Singapore"],
    "Talent Management": ["Malaysia", "Singapore", "Myanmar", "Indonesia", "Philippines", "Vietnam", "Cambodia", "Laos", "Thailand", "India", "Sri Lanka", "Others"]
};

// BUH mapping for each BU and country
const buhMap = {
    "SG Retail": {
        "Singapore": "Max"
    },
    "HED": {
        "Malaysia": "She Nee",
        "Myanmar": "Shine",
        "Indonesia": "Tissa",
        "Philippines": "Abbigail",
        "Vietnam": "Dung",
        "Cambodia": "Metta",
        "Laos": "Metta",
        "Thailand": "Metta",
        "India": "Metta",
        "Sri Lanka": "Rizvi",
        "Others": "Metta"
    },
    "Alliance": {
        "Malaysia": "Elise Tan",
        "Myanmar": "Shine",
        "Indonesia": "Indra",
        "Philippines": "Hysie",
        "Vietnam": "Dung",
        "Cambodia": "Tep",
        "Laos": "Tep",
        "Thailand": "Tep",
        "India": "Metta",
        "Sri Lanka": "Rizvi",
        "Others": "Metta"
    },
    "Enterprise International": {
        "Malaysia": "Christopher",
        "Indonesia": "Christopher",
        "Philippines": "Christopher"
    },
    "Enterprise Singapore": {
        "Singapore": "Caesar"
    },
    "Talent Management": {
        "Malaysia": "Parvin",
        "Singapore": "Parvin",
        "Myanmar": "Parvin",
        "Indonesia": "Parvin",
        "Philippines": "Parvin",
        "Vietnam": "Parvin",
        "Cambodia": "Parvin",
        "Laos": "Parvin",
        "Thailand": "Parvin",
        "India": "Parvin",
        "Sri Lanka": "Parvin",
        "Others": "Parvin"
    }
};

// Array to hold selected countries
let selectedCountries = [];

// Update Country Checkboxes based on selected BU
function updateCountryCheckboxes() {
    const buDropdown = document.getElementById('buDropdown');
    const selectedBU = buDropdown.value;
    const countryCheckboxesDiv = document.getElementById('countryCheckboxes');
    countryCheckboxesDiv.innerHTML = ''; // Clear previous checkboxes

    // Clear selected countries when BU changes
    selectedCountries = [];
    updateSelectedCountriesList(); // Clear the displayed selected countries
    updateSelectedValues(); // Update the displayed values

    if (selectedBU && buCountryMap[selectedBU]) {
        const countries = buCountryMap[selectedBU];
        countries.forEach(country => {
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.value = country;
            checkbox.id = `country_${country}`;
            checkbox.onchange = function () {
                if (checkbox.checked) {
                    addCountry(country);
                } else {
                    removeCountry(country);
                }
            };

            const label = document.createElement('label');
            label.htmlFor = `country_${country}`;
            label.textContent = country;
            countryCheckboxesDiv.classList.add('checkbox-container');

            // Append checkbox and label
            const checkboxWrapper = document.createElement('div');
            checkboxWrapper.classList.add('checkbox-item');
            checkboxWrapper.appendChild(checkbox);
            checkboxWrapper.appendChild(label);
            countryCheckboxesDiv.appendChild(checkboxWrapper);
        });
    }

    // Reset BUH dropdown
    document.getElementById('buhDropdown').innerHTML = '<option value="">Select BUH</option>';
}

// Function to add selected country
function addCountry(country) {
    if (!selectedCountries.includes(country)) {
        selectedCountries.push(country);
        updateSelectedCountriesList();
        updateBUHDropdown();
        updateSelectedValues();
    }
}

// Function to remove selected country
function removeCountry(country) {
    selectedCountries = selectedCountries.filter(c => c !== country);
    updateSelectedCountriesList();
    updateBUHDropdown();
    updateSelectedValues();
}

// Update the list of selected countries with remove buttons
function updateSelectedCountriesList() {
    const selectedCountriesList = document.getElementById('selectedCountriesList');
    selectedCountriesList.innerHTML = ''; // Clear previous list

    selectedCountries.forEach(country => {
        const countryDiv = document.createElement('div');
        countryDiv.classList.add('countries');
        countryDiv.classList.add('border-educ');
        const countryLabel = document.createElement('span');
        countryLabel.textContent = country;
        const removeButton = document.createElement('button');
        removeButton.textContent = '✖';
        removeButton.classList.add('remove-button');
        removeButton.onclick = function () {
            removeCountry(country);
            document.getElementById(`country_${country}`).checked = false; // Uncheck the checkbox
        };

        countryDiv.appendChild(countryLabel);
        countryDiv.appendChild(removeButton);
        selectedCountriesList.appendChild(countryDiv);
    });
}

// Update BUH Dropdown based on selected BU and selected countries
function updateBUHDropdown() {
    const buDropdown = document.getElementById('buDropdown');
    const selectedBU = buDropdown.value;

    const buhDropdown = document.getElementById('buhDropdown');
    buhDropdown.innerHTML = '<option value="">Select BUH</option>'; // Clear BUH dropdown

    if (selectedBU && selectedCountries.length > 0) {
        selectedCountries.forEach(country => {
            if (buhMap[selectedBU] && buhMap[selectedBU][country]) {
                const buh = buhMap[selectedBU][country];
                const option = document.createElement('option');
                option.value = buh;
                option.text = buh;
                buhDropdown.appendChild(option);
            }
        });
    }
}

// Update selected values displayed on the right side
function updateSelectedValues() {
    const selectedCountriesText = selectedCountries.length > 0 ? selectedCountries.join(', ') : "None";

    // Get all checked BUH checkboxes
    const checkedBUHs = Array.from(document.querySelectorAll('#buhCheckboxes input[type="checkbox"]:checked'))
        .map(checkbox => checkbox.nextElementSibling.textContent); // Get the label text

    const selectedBUH = checkedBUHs.length > 0 ? checkedBUHs.join(', ') : "None";

    document.getElementById('selectedBUH').innerText = selectedBUH;
    document.getElementById('selectedCountry').innerText = selectedCountriesText;
}
// Existing arrays and maps remain the same
// ...

// Update selected values displayed on the right side
function updateSelectedValues() {
    const selectedBU = document.getElementById('buDropdown').value || "None";
    const selectedCountriesText = selectedCountries.length > 0 ? selectedCountries.join(', ') : "None";
    const selectedBUH = document.getElementById('buhDropdown').value || "None";

    document.getElementById('selectedBU').innerText = selectedBU;
    document.getElementById('selectedCountry').innerText = selectedCountriesText;
    document.getElementById('selectedBUH').innerText = selectedBUH;
}
