@section('title', 'User Listing Page')

@extends('layouts.app')

@section('content')
    @if ((Auth::check() && Auth::user()->role == 'Head') || Auth::user()->role == 'Admin')
        <script>
            $(document).ready(function() {
                $('#successModal').modal('show');
            });
        </script>
        @if (Session::has('success'))
            <!-- Success Modal -->
            <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header"
                            style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
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
            <div class="table-title d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center">
                    <h2 style="margin: 0 0.5rem 0 0.25rem;" class="headings">Business Unit Head</h2>
                    <button class="btn hover-action d-flex align-items-center justify-content-center" data-toggle="modal"
                        data-target="#addUserModal" style="padding: 10px 12px;">
                        <i class="fa-solid fa-square-plus"></i>
                    </button>
                </div>
                <div class="f-flex align-items-center mr-3">
                    <div class="search-box d-flex align-items-center mr-3 mb-2">
                        <input type="search" class="form-control mr-1" placeholder="Search by Email or Name" id="search-input"
                            aria-label="Search">
                        <button class="btn hover-action mx-1" type="submit" data-toggle="tooltip" title="Search">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
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
                            <th scope="col" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-left bg-row fonts">
                        <?php $i = ($userData->currentPage() - 1) * $userData->perPage() + 1; ?>
                        @forelse ($userData as $user)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $user->bu_name }}</td> <!-- Displaying Business Unit -->
                                <td>{{ $user->country_name }}</td> <!-- Displaying Country -->
                                <td>{{ $user->buh_name }}</td> <!-- Displaying BUH -->
                                <td>{{ $user->buh_email }}</td> <!-- Displaying BUH Email-->
                                <td>{{ $user->nationality }}</td> <!-- Displaying Nationality -->
                                <td class="d-flex justify-content-center">
                                    <a class="btn hover-action" href=" {{ route('admin#view-buh-detail', ['id' => $user->id]) }} ">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                     <a class="btn hover-action" data-toggle="modal"
                                        data-target="#deleteUserModal{{ $user->id }}">
                                        <i class="fa-solid fa-trash"></i>
                                    </a> 
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No User Found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div aria-label="Page navigation example" class="paginationContainer">
                <ul class="pagination justify-content-center">
                    <!-- Previous Button -->
                    <li class="page-item {{ $userData->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link font-educ" href="{{ $userData->previousPageUrl() }}"
                            aria-label="Previous">&#60;</a>
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
    {{-- <!-- Edit User Modal -->
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
                    <form action="{{ route('head#update-BUH', $user->id) }}" method="POST">
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
    @endforeach --}}
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
                                <select id="buDropdown" class="form-control platforms search-bar" name="business_unit"
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
                                <select id="countryDropdown" class="form-control platforms search-bar" name="country"
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
<script>
    function toggleSort(downIconId, upIconId) {
        const sortDown = document.getElementById(downIconId);
        const sortUp = document.getElementById(upIconId);

        if (sortDown.style.display === 'none') {
            sortDown.style.display = 'inline';
            sortUp.style.display = 'none';
        } else {
            sortDown.style.display = 'none';
            sortUp.style.display = 'inline';
        }
    }

    function sortTable(columnName, order) {
        let table = document.querySelector(".table");
        let rows = table.rows;
        let switching = true;

        // Determine column index based on columnName
        const columnIndex = {
            'bu_name': 1,
            'country_name': 2,
            'buh_name': 3,
            'buh_email': 4,
            'nationality': 5
        }[columnName];

        // Loop until no switching is done
        while (switching) {
            switching = false;
            for (let i = 1; i < (rows.length - 1); i++) {
                let x = rows[i].querySelectorAll("td")[columnIndex];
                let y = rows[i + 1].querySelectorAll("td")[columnIndex];
                if (x && y) {
                    let shouldSwitch = (order === 'asc' && x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) || 
                                        (order === 'desc' && x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase());
                    if (shouldSwitch) {
                        rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                        switching = true;
                        break;
                    }
                }
            }
        }
        reassignRowNumbersTableContainer();
    }

    function reassignRowNumbersTableContainer() {
        const table = document.querySelector(".table");
        const rows = table.rows;
        const offset = ({{ $currentPage }} - 1) * {{ $perPage }};

        for (let i = 1; i < rows.length; i++) {
            rows[i].querySelectorAll("td")[0].innerText = offset + i;
        }
    }

    function updateCountryDropdown() {
        const buDropdown = document.getElementById('buDropdown');
        const selectedBU = buDropdown.value;
        const countryDropdown = document.getElementById('countryDropdown');

        countryDropdown.innerHTML = '<option value="" selected disabled>Select Country</option>';

        if (!selectedBU) return;

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
        const buDropdown = document.getElementById(`buDropdowninedit${id}`);
        const selectedBU = buDropdown.value;
        const countryDropdown = document.getElementById(`countryDropdown${id}`);

        countryDropdown.innerHTML = '<option value="" selected disabled>Select Country</option>';

        if (!selectedBU) return;

        fetch(`{{ route('get.bu.data') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ business_unit: selectedBU })
        })
        .then(response => response.json())
        .then(data => {
            data.countries.forEach(country => {
                const option = document.createElement('option');
                option.value = country;
                option.textContent = country;
                countryDropdown.appendChild(option);
            });
        })
        .catch(error => console.error('Error fetching BU data:', error));
    }

    function updateSelectedCountryAndBuh() {
        const countryDropdown = document.getElementById('countryDropdown');
        const selectedCountry = countryDropdown.value;

        document.getElementById('selectedCountry').textContent = selectedCountry || 'None';

        const buhDropdown = document.getElementById('buhDropdown');
        const buhValue = buhDropdown.value;

        if (buhValue) {
            const option = document.createElement('option');
            option.value = buhValue;
            option.textContent = buhValue;
            buhDropdown.appendChild(option);
            buhDropdown.value = buhValue;
            document.getElementById('selectedBUH').textContent = buhValue;
        } else {
            document.getElementById('selectedBUH').textContent = 'None';
        }
    }

    function hideAll() {
        document.getElementById('country-container').classList.add('d-none');
        document.getElementById('buh-container').classList.add('d-none');
        document.getElementById('import-container').classList.add('d-none');
    }

    document.addEventListener('DOMContentLoaded', function () {
        hideAll();
    });
</script>
@endsection
