document.addEventListener("DOMContentLoaded", function() {
    const countryDropdown = document.getElementById('countryDropdown');
    const selectedCountriesContainer = document.getElementById('selectedCountries');
    const selectedCountries = new Map(); // Stores selected countries by id

    // Event listener for dropdown selection
    countryDropdown.addEventListener('change', function() {
        const selectedCountryId = countryDropdown.value;
        const selectedCountryName = countryDropdown.options[countryDropdown.selectedIndex].text;

        // Only add if the country is not already selected
        if (selectedCountryId && !selectedCountries.has(selectedCountryId)) {
            selectedCountries.set(selectedCountryId, selectedCountryName);
            displaySelectedCountries();
        }

        // Reset dropdown to default state after selection
        countryDropdown.value = '';
    });

    // Function to display selected countries as chips
    function displaySelectedCountries() {
        // Clear the container
        selectedCountriesContainer.innerHTML = '';

        // Loop through selected countries and create chips
        selectedCountries.forEach((name, id) => {
            const chip = document.createElement('div');
            chip.classList.add('country-chip');
            chip.innerHTML = `
            ${name} 
            <button type="button" class="remove-btn" data-id="${id}">×</button>
            <input type="hidden" name="country[]" value="${id}">
        `;
            selectedCountriesContainer.appendChild(chip);
        });

        // Add event listeners for each remove button
        document.querySelectorAll('.remove-btn').forEach(button => {
            button.addEventListener('click', function() {
                const countryId = this.getAttribute('data-id');
                selectedCountries.delete(countryId);
                displaySelectedCountries();
            });
        });
    }
});