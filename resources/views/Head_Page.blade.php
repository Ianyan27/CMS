@section('title', 'User Listing Page')

@extends('layouts.app')

@section('content')
@if ((Auth::check() && Auth::user()->role == 'Head') || Auth::user()->role == 'Admin')
    <script>
        $(document).ready(function () {
            $('#successModal').modal('show');
            console.log('Modal triggered!');
        });
    </script>
    @if (Session::has('success'))
        <!-- Success Modal -->
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                                border:none;border-top-left-radius: 0; border-top-right-radius: 0;">
                        <h5 class="modal-title" id="successModalLabel" style="color: #91264c"><strong>Success</strong>
                        </h5>
                    </div>
                    <div class="modal-body" style="color: #91264c;border:none;">
                        {{ Session::get('success') }}
                    </div>
                    <div class="modal-footer" style="border:none;">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            style="background: #91264c; color:white;">OK</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="container-max-height">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <h2 class="my-2 mr-3 headings">Business Unit Head</h2>
                <button class="btn hover-action mx-3" type="button" data-toggle="modal" data-target="#addUserModal"
                    style="padding: 10px 12px;">
                    <i class="fa-solid fa-square-plus"></i>
                </button>
            </div>
            <div class="search-box d-flex align-items-center mr-3 mb-2">
                <input type="search" class="form-control mr-1" placeholder="Search by Email or Name" id="search-input"
                    aria-label="Search">
                <button class="btn hover-action mx-1" type="submit" data-toggle="tooltip" title="Search"
                    style="padding: 10px 12px;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </div>

        <div class="table-container">
            <table class="table table-hover mt-2">
                <thead class="text-left font-educ">
                    <tr>
                        <th scope="col">No #</th>

                        <th scope="col" id="bu-header">Business Unit
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-bu"
                                onclick="sortTable('bu_name', 'asc'); toggleSort('sortDown-bu', 'sortUp-bu')"></i>
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-bu"
                                onclick="sortTable('bu_name', 'desc'); toggleSort('sortUp-bu', 'sortDown-bu')"
                                style="display: none;"></i>
                        </th>
                        <th scope="col" id="country-header">Country
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-country"
                                onclick="sortTable('country_name', 'asc'); toggleSort('sortDown-country', 'sortUp-country')"></i>
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-country"
                                onclick="sortTable('country_name', 'desc'); toggleSort('sortUp-country', 'sortDown-country')"
                                style="display: none;"></i>
                        </th>

                        <th scope="col" id="buh-name">BUH Name
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-buh-name-unique"
                                onclick="sortTable('buh_name', 'asc'); toggleSort('sortDown-buh-name-unique', 'sortUp-buh-name-unique')"></i>
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-buh-name-unique"
                                onclick="sortTable('buh_name', 'desc'); toggleSort('sortUp-buh-name-unique', 'sortDown-buh-name-unique')"
                                style="display: none;"></i>
                        </th>
                        <th scope="col" id="buh-email">BUH Email
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-buh-email-unique"
                                onclick="sortTable('buh_email', 'asc'); toggleSort('sortDown-buh-email-unique', 'sortUp-buh-email-unique')"></i>
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-buh-email-unique"
                                onclick="sortTable('buh_email', 'desc'); toggleSort('sortUp-buh-email-unique', 'sortDown-buh-email-unique')"
                                style="display: none;"></i>
                        </th>
                        <th scope="col" id="nationality-header">Nationality
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-nationality"
                                onclick="sortTable('nationality', 'asc'); toggleSort('sortDown-nationality', 'sortUp-nationality')"></i>
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-nationality"
                                onclick="sortTable('nationality', 'desc'); toggleSort('sortUp-nationality', 'sortDown-nationality')"
                                style="display: none;"></i>
                        </th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-left bg-row fonts">
                    <?php    $i = ($userData->currentPage() - 1) * $userData->perPage() + 1; ?>
                    @forelse ($userData as $user)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $user->bu_name }}</td> <!-- Displaying Business Unit -->
                            <td>{{ $user->country_name }}</td> <!-- Displaying Country -->
                            <td>{{ $user->buh_name }}</td> <!-- Displaying BUH -->
                            <td>{{ $user->buh_email }}</td> <!-- Displaying BUH Email-->
                            <td>{{ $user->nationality }}</td> <!-- Displaying Nationality -->
                            <td>
                                <a class="btn hover-action" data-toggle="modal" data-target="#editUserModal{{ $user->id }}"
                                    style="padding: 10px 12px;">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <a class="btn hover-action" data-toggle="modal" data-target="#deleteUserModal{{ $user->id }}"
                                    style="padding: 10px 12px;">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No User Found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div aria-label="Page navigation example" class="paginationContainer">
            <ul class="pagination justify-content-center">
                <!-- Previous Button -->
                <li class="page-item {{ $userData->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link font-educ" href="{{ $userData->previousPageUrl() }}" aria-label="Previous">&#60;</a>
                </li>
                <!-- First Page Button -->
                @if ($userData->currentPage() > 3)
                    <li class="page-item">
                        <a class="page-link font-educ" href="{{ $userData->url(1) }}">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link font-educ" href="{{ $userData->url(2) }}">2</a>
                    </li>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif
                <!-- Middle Page Buttons -->
                @for ($i = max($userData->currentPage() - 1, 1); $i <= min($userData->currentPage() + 1, $userData->lastPage()); $i++)
                    <li class="page-item {{ $i == $userData->currentPage() ? 'active' : '' }}">
                        <a class="page-link font-educ {{ $i == $userData->currentPage() ? 'active-bg' : '' }}"
                            href="{{ $userData->url($i) }}">{{ $i }}</a>
                    </li>
                @endfor
                <!-- Last Page Button -->
                @if ($userData->currentPage() < $userData->lastPage() - 2)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                    <li class="page-item">
                        <a class="page-link font-educ"
                            href="{{ $userData->url($userData->lastPage() - 1) }}">{{ $userData->lastPage() - 1 }}</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link font-educ"
                            href="{{ $userData->url($userData->lastPage()) }}">{{ $userData->lastPage() }}</a>
                    </li>
                @endif
                <!-- Next Button -->
                <li class="page-item {{ !$userData->hasMorePages() ? 'disabled' : '' }}">
                    <a class="page-link font-educ" href="{{ $userData->nextPageUrl() }}" aria-label="Next">&#62;</a>
                </li>
            </ul>
        </div>
    </div>
    <!-- Edit User Modal -->
    @foreach ($userData as $user)
        <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1"
            aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content rounded-0">
                    <div class="modal-header d-flex justify-content-between align-items-center"
                        style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%); border:none;">
                        <h5 class="modal-title font-educ" id="editUserModalLabel{{ $user->id }}">
                            <strong>Edit BUH</strong>
                        </h5>
                    </div>
                    <form action="{{ route('head#update-user', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name{{ $user->id }}" class="form-label">BUH Name</label>
                                <input type="text" class="form-control" name="name" id="name{{ $user->id }}"
                                    value="{{ $user->buh_name }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="email{{ $user->id }}" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="email{{ $user->id }}"
                                    value="{{ $user->buh_email }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="nationality{{ $user->id }}" class="form-label">Nationality</label>
                                <input type="text" class="form-control" name="nationality" id="nationality{{ $user->id }}"
                                    value="{{ $user->nationality }}" required>
                            </div>
                            <div class="form-group">
                                <label for="bu">Business Unit (BU)</label>
                                <select id="buDropdowninedit{{ $user->id }}" class="w-75 platforms search-bar"
                                    name="business_unit" onchange="updateCountryDropdowninEdit({{ $user->id }});">
                                    <option value="">Select BU</option>
                                    @foreach ($businessUnit as $bu)
                                        <option value="{{ $bu->name }}" {{ $user->bu_name === $bu->name ? 'selected' : '' }}>
                                            {{ $bu->name }}</option>
                                    @endforeach
                                </select>
                                @error('bu_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="country">Country</label>
                                <select id="countryDropdown{{ $user->id }}" class="w-75 platforms search-bar" name="country"
                                    onchange="updateSelectedCountryAndBuh({{ $user->id }});">
                                    <option value="">Select Country</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->name }}" {{ $user->country_name === $country->name ? 'selected' : '' }}>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                @error('country_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn hover-action">Update User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    @foreach ($userData as $user)
        <!-- Delete User Modal -->
        <div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1"
            aria-labelledby="deleteUserModalLabel{{ $user->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center">
                    <div class="icon-container mx-auto">
                        <i class="fa-solid fa-trash"></i>
                    </div>
                    <div class="modal-header border-0"></div>
                    <div class="modal-body">
                        <p class="">You are about to delete this BUH</p>
                        <p class="text-muted">This will delete the user from your list.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        {{ $user->id }}
                        <form
                            action="{{ Auth::user()->role == 'Admin' ? route('admin#delete-buh', ['id' => $user->id]) : route('head#delete-user', $user->id) }}"
                            method="POST">
                            {{-- <form action="{{ route('head#delete-user', $user->id) }}" method="POST"> --}}
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Modal for Adding New User -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-0">
                <div class="modal-header" style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                                border:none;border-top-left-radius: 0; border-top-right-radius: 0;">
                    <h5 class="modal-title" id="addUserModalLabel" style="color: #91264c;">Create New User</h5>
                </div>
                <div class="modal-body" style="color: #91264c;">
                    <form id="addUserForm"
                        action="{{ Auth::user()->role == 'Admin' ? route('admin#save-buh') : route('head#save-user') }}"
                        method="POST">
                        {{-- <form id="addUserForm" action="{{ route('head#save-user') }}" method="POST"> --}}
                            @csrf
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}"
                                    minlength="3" maxlength="50" required>
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}"
                                    required>
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="nationality">Nationality</label>
                                <input type="nationality" class="form-control" id="nationality" name="nationality"
                                    value="{{ old('nationality') }}" required>
                                @error('nationality')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="role">Role</label>
                                <select class="form-control" id="role" name="role" required>
                                    <option value="BUH" selected>BUH</option> <!-- Only BUH can be selected -->
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="bu">Business Unit (BU)</label>
                                <select id="buDropdown" class="w-75 platforms search-bar" name="business_unit"
                                    onchange="updateCountryDropdown(); ">
                                    <option value="">Select BU</option>
                                    @foreach ($businessUnit as $bu)
                                        <option value="{{ $bu->name }}">{{ $bu->name }}</option>
                                    @endforeach
                                </select>
                                @error('bu_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="font-educ d-block" for="countryDropdown">Select Country</label>
                                <select id="countryDropdown" class="w-75 platforms search-bar" name="country"
                                    onchange="updateSelectedCountryAndBuh(); ">
                                    <option value="">Select Country</option>
                                </select>
                                @error('country_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                </div>
                <div class="modal-footer" style="border:none;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn hover-action" style="background: #91264c; color:white;">
                        Add BUH
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>
@else
    <div class="alert alert-danger text-center mt-5">
        <strong>Access Denied!</strong> You do not have permission to view this page.
    </div>
@endif

<script src="{{ asset('js/add_agent_validation.js') }}"></script>
<!-- <script src="{{ asset('js/sort.js') }}"></script> -->
<script>
    function sortTable(columnName, order) {
        let table, rows, switching, i, x, y, shouldSwitch;
        table = document.querySelector(".table");
        switching = true;

        // Loop until no switching has been done
        while (switching) {
            switching = false;
            rows = table.rows;

            for (i = 1; i < (rows.length - 1); i++) {
                shouldSwitch = false;

                // Determine the column index based on columnName
                let columnIndex;
                if (columnName === 'bu_name') {
                    columnIndex = 1; // Index for the 'Business Unit' column
                } else if (columnName === 'country_name') {
                    columnIndex = 2; // Index for the 'Country' column
                } else if (columnName === 'buh_name') {
                    columnIndex = 3; // Index for 'BUH' column
                } else if (columnName === 'buh_email') {
                    columnIndex = 4; // Index for 'BUH Email' column
                } else if (columnName === 'nationality') {
                    columnIndex = 5; // Index for 'Nationality' column
                }

                // Compare the two elements in the column to see if they should switch
                x = rows[i].querySelectorAll("td")[columnIndex];
                y = rows[i + 1].querySelectorAll("td")[columnIndex];

                if (x && y) {
                    if (order === 'asc' && x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    } else if (order === 'desc' && x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                }
            }
            if (shouldSwitch) {
                // If a switch has been marked, make the switch and mark the switch as done
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
            }
        }
        reassignRowNumbersTableContainer();
    }

    function reassignRowNumbersTableContainer() {
        const table = document.querySelector(".table");
        const rows = table.rows;
        const currentPage = {{ $currentPage }};
        const perPage = {{ $perPage }};
        const offset = (currentPage - 1) * perPage;

        for (let i = 1; i < rows.length; i++) {
            rows[i].querySelectorAll("td")[0].innerText = offset + i; // Reassign "No #" column (index 1)
        }
    }
</script>
<script>
    // Add an event listener to the search input field
    document.getElementById('search-input').addEventListener('input', function () {
        // Get the search query
        const searchQuery = this.value.toLowerCase();

        // Get all table rows
        const rows = document.querySelectorAll('.table tbody tr');

        // Loop through each row
        rows.forEach(function (row) {
            // Get the row's text content
            const buhName = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            const buhEmail = row.querySelector('td:nth-child(5)').textContent.toLowerCase();

            // Check if the row's text content matches the search query
            if (buhName.includes(searchQuery) || buhEmail.includes(searchQuery)) {
                // Show the row if it matches the search query
                row.style.display = '';
            } else {
                // Hide the row if it doesn't match the search query
                row.style.display = 'none';
            }
        });
    });
</script>
<script>
    $(document).ready(function () {
        // Event handler for when the country dropdown is changed
        $('#countryDropdown').on('change', function () {
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


        // Clear previous country options
        const countryDropdown = document.getElementById('countryDropdown');
        countryDropdown.innerHTML = '<option value="" selected disabled>Select Country</option>'; // Reset options



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

            })
            .catch(error => console.error('Error fetching BU data:', error));
    }

    function updateCountryDropdowninEdit(id) {
        console.log(id);
        console.log('buDropdowninedit' + id);
        var dropdownid = 'buDropdowninedit' + id;


        const buDropdown = document.getElementById(dropdownid);
        const selectedBU = buDropdown.value;
        console.log("Selected BU:", selectedBU);

        const tagid = 'countryDropdown'+id;
        console.log(tagid);

        // Clear previous country options
        const countryDropdown = document.getElementById(tagid);
        countryDropdown.innerHTML = '<option value="" selected disabled>Select Country</option>'; // Reset options

        // If no BU is selected, clear the country dropdown
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

                // Update country dropdown
                data.countries.forEach(country => {
                    const option = document.createElement('option');
                    option.value = country; // Ensure this matches your data structure
                    option.textContent = country; // Ensure this matches your data structure
                    countryDropdown.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching BU data:', error));
    }

    // Function to update selected country and automatically select the BUH
    // Function to update selected country and automatically select the BUH
    function updateSelectedCountryAndBuh() {

        console.log('some')
        const countryDropdown = document.getElementById('countryDropdown');
        const selectedCountry = countryDropdown.value;

        console.log("Selected country:", selectedCountry);

        // Update the selected country display
        document.getElementById('selectedCountry').textContent = selectedCountry || 'None';

        // Update the BUH dropdown based on the selected country
        const buhDropdown = document.getElementById('buhDropdown');

        if (!selectedCountry) {
            console.log("No country selected for BUH.");
            document.getElementById('selectedBUH').textContent = 'None';
            return;
        }

        // Log the BUH data to inspect it
        console.log("BUH data by country:", buhDropdown);

        // Get BUH for the selected country
        const buhValue = buhDropdown.value;


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



    // Function to reset all hidden containers
    function hideAll() {
        document.getElementById('country-container').classList.add('d-none');
        document.getElementById('buh-container').classList.add('d-none');
        document.getElementById('import-container').classList.add('d-none');
    }

    // Initially hide everything
    document.addEventListener('DOMContentLoaded', function () {
        hideAll();
    });
</script>
@endsection


<script src="{{ asset('js/add_agent_validation.js') }}"></script>
<script src="{{ asset('js/sort.js') }}"></script>

<!-- <script>
    $(document).ready(function() {
        $('#successModal').modal('show');
        // Show the Add User modal if needed
        $('#addUserModal').on('show.bs.modal', function() {
            $('#addUserForm')[0].reset(); // Reset form fields
        });

    });
</script> -->