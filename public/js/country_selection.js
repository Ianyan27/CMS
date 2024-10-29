document.addEventListener("DOMContentLoaded", function() {
    const countryDropdown = document.getElementById('countryDropdown');
    const selectedCountriesContainer = document.getElementById('selectedCountries');
    const selectedCountries = new Map(); // Stores selected countries by id

    // Event listener for dropdown selection
    countryDropdown.addEventListener('change', function() {
        const selectedCountryId = countryDropdown.value;
        const selectedCountryName = countryDropdown.options[countryDropdown.selectedIndex].text;

        console.log("Selected country ID:", selectedCountryId);
        console.log("Selected country name:", selectedCountryName);

        // Only add if the country is not already selected
        if (selectedCountryId && !selectedCountries.has(selectedCountryId)) {
            selectedCountries.set(selectedCountryId, selectedCountryName);
            console.log(`Added country: ${selectedCountryName} (ID: ${selectedCountryId})`);
            displaySelectedCountries();
        } else {
            console.log("Country is already selected or invalid selection.");
        }

        // Reset dropdown to default state after selection
        countryDropdown.value = '';
    });

    // Function to display selected countries as chips
    function displaySelectedCountries() {
        // Clear the container
        selectedCountriesContainer.innerHTML = '';

        console.log("Displaying selected countries...");

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
            console.log(`Created chip for country: ${name} (ID: ${id})`);
        });

        // Add event listeners for each remove button
        document.querySelectorAll('.remove-btn').forEach(button => {
            button.addEventListener('click', function() {
                const countryId = this.getAttribute('data-id');
                selectedCountries.delete(countryId);
                console.log(`Removed country: ${countryId}`);
                displaySelectedCountries();
            });
        });

        // Log current selection status
        console.log("Current selected countries:", Array.from(selectedCountries.entries()));
    }
});
