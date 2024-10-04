@section('title', 'Sale Admin Page')

@extends('layouts.app')

@section('content')

    <div class="container-max-height">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="headings">Import CSV</h5>
        </div>
        <div class="sale-admin-container row my-3 px-3">
            <div class="col-12 my-2 px-3">
                <div class="row" style="min-height: 160px;">
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-12">
                                <label class="font-educ d-block" for="buDropdown">Select BU</label>
                                <select id="buDropdown" class="w-75 platforms search-bar" name="business_unit"
                                    onchange="updateCountryDropdown(); handleBUChange()">
                                    <option value="">Select BU</option>
                                    @foreach ($businessUnit as $bu)
                                        <option value="{{ $bu->name }}">{{ $bu->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 d-none" id="country-container">
                                <label class="font-educ d-block" for="countryDropdown">Select Country</label>
                                <select id="countryDropdown" name="countryCheckboxes" class="w-75 platforms search-bar"
                                    name="country" onchange="updateSelectedCountryAndBuh(); handleCountryChange()">
                                    <option value="">Select Country</option>
                                </select>
                            </div>
                            <div class="d-none">
                                <div class="col-lg-12 d-none" id="buh-container">
                                    <label for="buhDropdown">Select BUH:</label>
                                    <select id="buhDropdown" class="w-100 platforms search-bar" name="buh">
                                        <option value="" selected disabled>Select BUH</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="summary col-lg-6 border-educ rounded" style="padding: 0 1.25rem;">
                        <div class="text-center font-educ mt-2 h4">
                            Summary
                        </div>
                        <div class="summary my-2">
                            <div class="my-2">
                                <p class="d-inline">Business Unit:</p> <span class="text-left" id="selectedBU">None</span>
                            </div>
                            <div class="my-2">
                                <p class="d-inline">Country: </p><span class="text-left" id="selectedCountry">None</span>
                            </div>
                            <div class="my-2">
                                <p class="d-inline">Business Unit Head:</p><span class="text-left"
                                    id="selectedBUH">None</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row my-3 d-none border-educ rounded" id="import-container">
                    <div class="col-lg-6">
                        <div id="platform-container">
                            <div class="alert alert-danger d-none" id="platformValidationMsg" role="alert"
                                style="font-size: medium">
                                Please Select Platform *
                            </div>
                            <label class="font-educ" for="platform">Select Platform</label>
                            <select id="platform"
                                class="platforms search-bar d-flex align-items-center justify-content-start w-100 m-0"
                                name="platform">
                                <option value="" selected disabled>Select Platform</option>
                                <option value="linkedin">LinkedIn</option>
                                <option value="apollo">Apollo</option>
                                <option value="raw">Raw</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6 d-flex align-items-center justify-content-end">
                        <div id="raw-btn-container">
                            <button class="btn hover-action" onclick="window.location.href='{{ route('get-csv') }}'">
                                Get CSV Format
                            </button>
                        </div>
                    </div>
                    <div class="col-12 text-center my-3">
                        <div class="card-body justify-content-center align-items-center drop-zone" id="dropZone">
                            <div class="mx-5">
                                <h5 class="mb-4 font-educ">Drag and drop your files</h5>
                                <p class="mb-4" title="The uploaded file must be a file of type: csv">File formats
                                    we support
                                    <i class="fas fa-info-circle"></i>
                                </p>
                            </div>
                            @csrf
                            <div>
                                <input accept=".csv" type="file" name="csv_file" required id="fileInput" class="d-none"
                                    disabled>
                            </div>
                            <div>
                                <label for="fileInput" id="fileInputLabel" class="btn hover-action mt-4 disabled">
                                    Filter first before importing
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 d-none my-3" id="file-card">
                        <div class="card-body card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <img src="../images/csv.png" alt="" style="height:4rem;">
                                    <p id="file-name" class="text-muted d-none"></p>
                                </div>
                                <div class="w-50">
                                    <div class="progress" id="progressContainer" role="progressbar" aria-valuenow="0"
                                        aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-educ" id="progressBar" style="width: 0%;"></div>
                                    </div>
                                    <p id="progress-message" class="text-muted d-none mt-2"></p>
                                    <p id="error-message" class="text-danger d-none mt-2"></p>
                                </div>
                                <div>
                                    <input type="submit" id="submitBtn" class="btn hover-action"
                                        style="margin-left: auto">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Event handler for when the country dropdown is changed
            $('#countryDropdown').on('change', function() {
                const selectedCountry = $(this).val();
                $('#buhDropdown')
                updateSelectedCountryAndBuh(selectedCountry);
            });

        });
        // Function to update countries and BUH based on selected BU
        function updateCountryDropdown() {
            const buDropdown = document.getElementById('buDropdown');
            const selectedBU = buDropdown.value;
            console.log("Selected BU:", selectedBU);

            // Update the selected BU display
            document.getElementById('selectedBU').textContent = selectedBU || 'None';

            // Clear previous country options
            const countryDropdown = document.getElementById('countryDropdown');
            countryDropdown.innerHTML = '<option value="" selected disabled>Select Country</option>'; // Reset options

            // Reset selected country and BUH display
            document.getElementById('selectedCountry').textContent = 'None';
            document.getElementById('selectedBUH').textContent = 'None';

            // Clear BUH dropdown
            const buhDropdown = document.getElementById('buhDropdown');
            buhDropdown.innerHTML = '<option value="" selected disabled>Select BUH</option>'; // Reset options

            // If no BU is selected, clear the BUH display
            if (!selectedBU) {
                console.log("No BU selected. Exiting updateCountryDropdown.");
                return; // Do not fetch data if no BU is selected
            }

            // Fetch the countries and BUH from the server
            console.log("Fetching BU data for:", selectedBU);
            fetch(`{{ route('get.bu.data') }}`, {
                    method: 'POST', // Use POST method
                    headers: {
                        'Content-Type': 'application/json', // Specify the content type
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for Laravel
                    },
                    body: JSON.stringify({
                        business_unit: selectedBU
                    }) // Send the selected BU in the request body
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json(); // Parse response as JSON
                })
                .then(data => {
                    // Log the complete data received from the server to inspect its structure
                    console.log("Complete data received from server:", data);
                    console.log("buh: ", data.buh[0]);
                    const buhValue = data.buh[0];

                    // $.each(buhData, function(index, value) {
                    //     $('#buhDropdown').append(`<option value="${value.id}">${value.name}</option>`);
                    // });
                    // Update country dropdown
                    data.countries.forEach(country => {
                        const option = document.createElement('option');
                        option.value = country;
                        option.textContent = country;
                        countryDropdown.appendChild(option);
                    });

                    // Store the BUH data by country
                    window.buhDataByCountry = data.buh[0]; // Assuming 'buh' is a key in your JSON response
                    console.log("BUH data by country stored:", window.buhDataByCountry);
                })
                .catch(error => console.error('Error fetching BU data:', error));
        }

        // Function to update selected country and automatically select the BUH
        // Function to update selected country and automatically select the BUH
        function updateSelectedCountryAndBuh() {
            const countryDropdown = document.getElementById('countryDropdown');
            const selectedCountry = countryDropdown.value;

            console.log("Selected country:", selectedCountry);

            // Update the selected country display
            document.getElementById('selectedCountry').textContent = selectedCountry || 'None';

            // Update the BUH dropdown based on the selected country
            const buhDropdown = document.getElementById('buhDropdown');
            buhDropdown.innerHTML = '<option value="" selected disabled>Select BUH</option>'; // Reset options

            if (!selectedCountry) {
                console.log("No country selected for BUH.");
                document.getElementById('selectedBUH').textContent = 'None';
                return;
            }

            // Log the BUH data to inspect it
            console.log("BUH data by country:", buhDropdown);

            // Get BUH for the selected country
            const buhValue = window.buhDataByCountry;

            // Check if buhValue exists and is not an array (since it's a string in your case)
            if (typeof buhValue === 'string') {
                // Create a single option for the BUH dropdown
                const option = document.createElement('option');
                option.value = buhValue;
                option.textContent = buhValue;
                buhDropdown.appendChild(option);

                // Automatically select the first (and only) BUH
                buhDropdown.value = buhValue;
                document.getElementById('selectedBUH').textContent = buhValue; // Update the BUH display
                console.log("Automatically selected BUH:", buhValue);
            } else {
                console.error("BUH data is not available or not valid for selected country:", selectedCountry);
                document.getElementById('selectedBUH').textContent = 'None';
            }
        }

        function handleBUChange() {
            const buDropdown = document.getElementById('buDropdown');
            const selectedBU = buDropdown.value;
            console.log(selectedBU);

            // Show the country dropdown if a BU is selected
            const countryContainer = document.getElementById('country-container');
            if (selectedBU) {
                countryContainer.classList.remove('d-none');
            } else {
                countryContainer.classList.add('d-none');
                hideAll(); // Reset everything if no BU is selected
            }
        }

        // Function to handle country change and show BUH dropdown and CSV import section
        function handleCountryChange() {
            const countryDropdown = document.getElementById('countryDropdown');
            const selectedCountry = countryDropdown.value;

            // Show the BUH dropdown and CSV import if a country is selected
            const buhContainer = document.getElementById('buh-container');
            const importContainer = document.getElementById('import-container');
            if (selectedCountry) {
                buhContainer.classList.remove('d-none');
                importContainer.classList.remove('d-none');
            } else {
                buhContainer.classList.add('d-none');
                importContainer.classList.add('d-none');
            }
        }

        // Function to reset all hidden containers
        function hideAll() {
            document.getElementById('country-container').classList.add('d-none');
            document.getElementById('buh-container').classList.add('d-none');
            document.getElementById('import-container').classList.add('d-none');
        }

        // Initially hide everything
        document.addEventListener('DOMContentLoaded', function() {
            hideAll();
        });

        //-----------declaring----------------//
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const uploadForm = document.getElementById('uploadForm');
        const submitBtn = document.getElementById('submitBtn');
        const fileName = document.getElementById('file-name');
        const card = document.getElementById('file-card');
        const progressContainer = document.getElementById('progressContainer');
        const progressBar = document.getElementById('progressBar');
        const progressMessage = document.getElementById('progress-message');
        const errorMessage = document.getElementById('error-message');
        const radioContainer = document.getElementById('radio-container');

        //----------Drag and drop--------------//
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length) {
                fileInput.files = files;
                handleFiles();
            }
        });

        //-----------form field validation----------//
        fileInput.addEventListener('change', handleFiles);
        document.getElementById('platform').addEventListener('change', handlePlatformChange);

        function handleFiles() {
            const fileNameText = fileInput.files[0].name;
            fileName.textContent = fileNameText;
            fileName.classList.remove('d-none');
            card.classList.remove('d-none');
            errorMessage.classList.add('d-none');
            progressContainer.classList.add('d-none');
            progressMessage.classList.add('d-none');

            checkIfReadyToSubmit();
        }

        function handlePlatformChange() {

            checkIfReadyToSubmit();
        }

        function checkIfReadyToSubmit() {
            const platformSelect = document.getElementById('platform');
            const buDropdown = document.getElementById('buDropdown');
            const countryDropdown = document.getElementById('countryDropdown'); // Assuming it's a dropdown now
            const buhDropdown = document.getElementById('buhDropdown');
            const submitBtn = document.getElementById('submitBtn');
            const fileInput = document.getElementById('fileInput');
            const fileInputLabel = document.getElementById('fileInputLabel');

            // Flag to track if all fields are properly selected
            let allFieldsSelected = true;

            // Check if platform is selected
            if (platformSelect && platformSelect.value) {
                platformSelect.classList.remove("error-select");
            } else {
                platformSelect.classList.add("error-select");
                allFieldsSelected = false;
            }

            // Check if BU (Business Unit) is selected
            if (buDropdown && buDropdown.value) {
                buDropdown.classList.remove("error-select");
            } else {
                buDropdown.classList.add("error-select");
                allFieldsSelected = false;
            }

            // Check if a country is selected (assuming it's a dropdown now)
            if (countryDropdown && countryDropdown.value) {
                countryDropdown.classList.remove("error-select");
            } else {
                countryDropdown.classList.add("error-select");
                allFieldsSelected = false;
            }

            // Check if BUH is selected
            if (buhDropdown && buhDropdown.value) {
                buhDropdown.classList.remove("error-select");
            } else {
                buhDropdown.classList.add("error-select");
                allFieldsSelected = false;
            }

            // Show or hide submit button based on if all fields are properly selected
            if (allFieldsSelected) {
                submitBtn.classList.remove("d-none"); // Show the submit button

                // Enable the file input and remove the disabled class from the label
                fileInput.removeAttribute('disabled');
                fileInputLabel.classList.remove('disabled');
                fileInputLabel.textContent = 'Upload your CSV file';
            } else {
                submitBtn.classList.add("d-none"); // Hide the submit button

                // Disable the file input and add the disabled class back to the label
                fileInput.setAttribute('disabled', true);
                fileInputLabel.classList.add('disabled');
                fileInputLabel.textContent = 'Filter first before importing';
            }
        }


        //----------submit-------------//
        submitBtn.addEventListener('click', (e) => {


            e.preventDefault();
            //create form data
            const formData = new FormData();
            const platformSelect = document.getElementById('platform');
            const buDropdown = document.getElementById('buDropdown');
            const buhDropdown = document.getElementById('buhDropdown');

            formData.append('csv_file', fileInput.files[0]);
            formData.append('platform', platformSelect.value);
            formData.append('country', countryDropdown.value);
            formData.append('bu', buDropdown.value);
            formData.append('buh', buhDropdown.value);

            console.log(countryDropdown.value);
            console.log(buDropdown.value);
            console.log(buhDropdown.value);




            submitBtn.classList.add('d-none');
            progressContainer.classList.remove('d-none')
            progressBar.style.width = '0%';
            progressMessage.textContent = 'Uploading...';
            progressMessage.classList.remove('d-none');
            errorMessage.classList.add('d-none'); // Hide any previous error message
            progressBar.style.width = '20%';

            fetch('{{ route('import') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            // Handle 500 error with custom message from the server
                            if (response.status === 500) {
                                throw new Error(errorData.message ||
                                    'Internal Server Error');
                            }
                            // Handle 422 validation errors
                            if (response.status === 422) {
                                const errors = errorData.errors;
                                let errorText = '';
                                for (const [key, errorMessages] of Object.entries(errors)) {
                                    errorText += errorMessages;
                                }
                                errorMessage.innerHTML = errorText;
                                throw new Error(errorText || 'Validation Error');
                            }
                            throw new Error(errorData.message || 'Upload failed');
                        });
                    }
                    // Check if the response is a CSV file
                    const contentDisposition = response.headers.get('Content-Disposition');
                    if (contentDisposition && contentDisposition.includes('attachment')) {
                        return response.blob();
                    } else {
                        return response.json();
                    }
                })

                .then(data => {
                    if (data.success) {

                        progressBar.style.width = '100%';
                        progressMessage.textContent = 'Upload complete!';

                        let valid_count = data.data.valid_count;
                        let invalid_count = data.data.invalid_count;
                        let duplicate_count = data.data.duplicate_count;
                        let unselected_country_count = data.data.unselected_country_count;
                        let total_count = valid_count + invalid_count + duplicate_count + unselected_country_count;

                        setTimeout(() => {

                            const {
                                invalid_rows,
                                duplicate_rows,
                                unselected_country_rows
                            } = data.data.file_links;

                            showDownloadPrompt(valid_count, invalid_count, duplicate_count, unselected_country_count,
                                total_count,
                                invalid_rows, duplicate_rows, unselected_country_rows);
                        }, 800);

                        progressMessage.classList.remove('d-none');
                    } else {
                        throw new Error(data.message || 'Upload failed');
                    }
                })
                .catch(error => {

                    progressBar.style.width = '0%';
                    progressMessage.classList.add('d-none');
                    errorMessage.textContent = error.message;
                    errorMessage.classList.remove('d-none');
                });
        });

        //show download promt
        function showDownloadPrompt(valid_count, invalid_count, duplicate_count, unselected_country_count, total_count, invalid_rows_link,
            duplicate_rows_link, unselected_country_rows_link) {
            // Create the modal element
            const downloadPrompt = document.createElement('div');

            downloadPrompt.style.position = 'fixed';
            downloadPrompt.style.top = '50%';
            downloadPrompt.style.left = '50%';
            downloadPrompt.style.transform = 'translate(-50%, -50%)';
            downloadPrompt.style.backgroundColor = '#fff';

            downloadPrompt.style.boxShadow = '0px 0px 10px rgba(0, 0, 0, 0.4)';
            downloadPrompt.style.zIndex = '1000';
            downloadPrompt.style.borderRadius = '8px';
            downloadPrompt.classList.add = 'modal-header'
            // downloadPrompt.style.width = '350px';
            // Add the logo image and text in a flex container
            const logoUrl = "{{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png') }}";
            const headerContent = `
                <div style="
                    padding: 15px; 
                    background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                    border-radius: 8px 8px 0 0;
                "   class="d-flex justify-content-between align-items-center">
                <div>
                        <p style=" margin-right: 30px; margin-bottom: 0;" class="headings">Import Status</p>
                        </div>
                        <div>
                        <img src="${logoUrl}" alt="Company Logo" style="height: 30px;">
                        </div>
                </div>
    `;
            const bodyContent = `
    <div style="padding : 20px" class="fonts">
        
         
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <p style="margin: 5px 0; font-size: 16px;">Total Rows:</p>
        <strong>${total_count}</strong>
    </div>
    <hr style="margin: 10px 0;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <p style="margin: 5px 0; font-size: 16px;">Successfully Imported Rows:</p>
        <strong>${valid_count}</strong>
    </div>
    <hr style="margin: 10px 0;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <p style="margin: 5px 0; font-size: 16px;">Invalid Rows:</p>
        <div style="display: flex; align-items: center;">
            <strong>${invalid_count}</strong>
            ${invalid_rows_link ? `<a href="${invalid_rows_link}" id="download-invalid-btn" style="color: #007bff; text-decoration: underline; margin-left: 10px;">Download</a>` : ''}
        </div>
    </div>
    <hr style="margin: 10px 0;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <p style="margin: 5px 0; font-size: 16px;">Duplicate Rows:</p>
        ${duplicate_rows_link ? `<a href="${duplicate_rows_link}" id="download-duplicate-btn" style="color: #007bff; text-decoration: underline; margin-left: 10px;">Download</a>` : ''}
        
        <div style="display: flex; align-items: center;">
            <strong>${duplicate_count}</strong>
        </div>
    </div>
    <hr style="margin: 10px 0;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <p style="margin: 5px 0; font-size: 16px;">Unselected CountryRows:</p>
        ${unselected_country_rows_link ? `<a href="${unselected_country_rows_link}" id="download-duplicate-btn" style="color: #007bff; text-decoration: underline; margin-left: 10px;">Download</a>` : ''}
        
        <div style="display: flex; align-items: center;">
            <strong>${unselected_country_count}</strong>
        </div>
    </div>
    <hr style="margin: 10px 0;">
        <div class="text-end">
            <button id="cancel-btn" class="btn" style="background-color: #6c757d; color: white; padding: 5px 10px; border-radius: 4px;">Close</button>
        </div>
    </div> 
      
    `;

            downloadPrompt.innerHTML = `
        ${headerContent}
        ${bodyContent}

        
    `;

            // Append the modal to the body
            document.body.appendChild(downloadPrompt);

            // Show the modal
            downloadPrompt.style.display = 'block';

            // Handle cancel button click
            document.getElementById('cancel-btn').addEventListener('click', () => {
                downloadPrompt.remove();
            });
        }
    </script>
@endsection
