@section('title', 'Transfer Contacts')

@extends('layouts.app')

<style>
    .progress-container {
            width: 100%;
            background-color: #f3f3f3;
            border: 1px solid #ccc;
            border-radius: 5px;
            height: 25px;
            margin-top: 20px;
        }

        .progress-bar {
            height: 100%;
            width: 0%;
            background-color: #4caf50;
            text-align: center;
            color: white;
            line-height: 25px;
            border-radius: 5px;
        }
        /* Ensure the collapse container is styled properly */
    .collapse {
        transition: height 0.3s ease;
    }

    /* Optionally, add more styling to the card */
    .card {
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    /* Style the container for alignment */
    .switch-container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* Style the switch */
    .switch {
        position: relative;
        display: inline-block;
        width: 100%; /* Full width of the parent container */
        max-width: 100px; /* Max width to keep the switch from becoming too large */
        height: 34px;
    }

    /* Hide default checkbox */
    .switch input {
        opacity: 0;
        width: 100%;
        height: 100%;
        margin: 0;
    }

    /* Slider styling */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
    }

    /* Slider before the toggle state */
    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    /* When the switch is checked, adjust slider background */
    input:checked + .slider {
        background-color: #4CAF50;
    }

    /* When the switch is checked, move the slider to the right */
    input:checked + .slider:before {
        transform: translateX(60px);
    }

    /* Optional: style for status text */
    .status-text {
        font-size: 16px;
    }

</style>
@section('content')
@if (Auth::check() && Auth::user()->role == 'BUH')
    @if (Session::has('success'))
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content rounded-0">
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
    @if (Session::has('warning'))
        <!-- Success Modal -->
        <div class="modal fade" id="warningModal" tabindex="-1" aria-labelledby="warningModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content rounded-0">
                    <div class="modal-header"
                        style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                    border:none;border-top-left-radius: 0; border-top-right-radius: 0;">
                        <h5 class="modal-title" id="warningModalLabel" style="color: #91264c"><strong>Error</strong>
                        </h5>
                    </div>
                    <div class="modal-body" style="color: #91264c;border:none;">
                        {{ Session::get('warning') }}
                    </div>
                    <div class="modal-footer" style="border:none;">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            style="background: #91264c; color:white;">OK</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (Session::has('error'))
        <!-- Success Modal -->
        <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content rounded-0">
                    <div class="modal-header"
                        style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                    border:none;border-top-left-radius: 0; border-top-right-radius: 0;">
                        <h5 class="modal-title" id="errorModalLabel" style="color: #91264c"><strong>Error</strong>
                        </h5>
                    </div>
                    <div class="modal-body" style="color: #91264c;border:none;">
                        {{ Session::get('error') }}
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
        <form class="transfer-form-container" action=" {{ route('owner#transfer') }} " method="POST">
            @csrf
            <input type="hidden" name="owner_pid" value=" {{ $owner->owner_pid }} " readonly>
            <div class="table-title d-flex justify-content-between align-items-center mb-3">
                <div class="position-relative">
                    <div class="d-flex align-items-center">
                        <h2 style="margin: 0 0.25rem 0 0.25rem;" class="font-educ headings">Transferable Contacts</h2>
                        <button id="infoButton" style="margin: 0 0.75rem; padding: 10px 12px;" type="button" class="btn hover-action" onclick="toggleInfoCollapse()">
                            <i style="font-size: 1.25rem;" class="fa-solid fa-circle-question"></i>
                        </button>
                        <div class="switch-container" style="margin: 0 0.5rem;">
                            <label class="switch">
                                <input type="checkbox" id="statusSwitch" data-owner-pid="{{ $owner->owner_pid }}" @if ($owner->status === 'active') checked @endif>
                                <span class="slider round"></span>
                            </label>
                            <span class="owner-status 
                                @if($owner->status === 'active')
                                status-text
                                @elseif($owner->status === 'inactive')
                                inactive-text
                                @endif">Status: 
                                @if ($owner->status === 'active')
                                Active
                                @elseif ($owner->status === 'inactive')
                                Inactive
                                @endif
                            </span>
                        </div>
                        <button type="button" class="btn hover-action" data-toggle="modal" data-target="#transferContact">
                            Transfer Contacts <i class="fa-solid fa-right-left"></i>
                        </button>
                    </div>
                    <!-- Info Container -->
                    <div id="infoCollapse" class="collapse container rounded-bottom p-0" 
                        style="display: none; 
                                position: absolute; 
                                top: 100%; left: 0; 
                                z-index: 1000; 
                                box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.3);">
                        <div class="card card-body">
                            <p>Total Contacts: {{$countAllContacts}} (New, In Progress, Hubspot, Archive, Discard) </p>
                            <p>Eligible for transfer: {{ $totalEligibleContacts }} (New, In Progress, Archive)</p>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center mr-3">
                    <div class="search-box d-flex align-items-center mr-3 mb-2">
                        <input type="search" class="form-control mr-1" placeholder="Search Name or Email..." id="search-input" aria-label="Search">
                        <button style="padding: 10px 12px;" class="btn hover-action mx-1" type="submit" data-toggle="tooltip" title="Search">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="table-container">
                <table id="contacts-table" class="table table-hover mt-2">
                    <thead class="font-educ text-left">
                        <tr>
                            <th scope="col"><input type="checkbox" id="select-all"></th>
                            <th scope="col">No #</th>
                            <th scope="col" id="name-header">Name
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-name"
                                    onclick="sortTable('name', 'asc'); toggleSort('sortDown-name', 'sortUp-name')"></i>
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-name"
                                    onclick="sortTable('name', 'desc'); toggleSort('sortUp-name', 'sortDown-name')"
                                    style="display: none;"></i>
                            </th>
                            <th scope="col" id="email-header">Email
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-email"
                                    onclick="sortTable('email', 'asc'); toggleSort('sortDown-email', 'sortUp-email')"></i>
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-email"
                                    onclick="sortTable('email', 'desc'); toggleSort('sortUp-email', 'sortDown-email')"
                                    style="display: none;"></i>
                            </th>
                            <th scope="col">Phone Contact</th>
                            <th scope="col" id="country-header">Country
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-country"
                                    onclick="sortTable('country', 'asc'); toggleSort('sortDown-country', 'sortUp-country')"></i>
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-country"
                                    onclick="sortTable('country', 'desc'); toggleSort('sortUp-country', 'sortDown-country')"
                                    style="display: none;"></i>
                            </th>
                            <th class="position-relative" scope="col">Status
                                <i style="cursor: pointer;" class="fa-solid fa-filter" id="filterIcon"
                                    onclick="toggleFilter()"></i>
                                <!-- Filter Container -->
                                <div id="filterContainer" class="filter-popup container rounded-bottom"
                                    style="display: none;">
                                    <div class="row">
                                        <div class="filter-option">
                                            <input class="ml-3" type="checkbox" id="new" name="status"
                                                value="New" onclick="applyFilter()">
                                            <label for="new" style= "color: #318FFC;">New</label>
                                        </div>
                                        <div class="filter-option">
                                            <input class="ml-3" type="checkbox" id="inProgress" name="status"
                                                value="InProgress" onclick="applyFilter()">
                                            <label for="inProgress" style="color: #FF8300;">In Progress</label>
                                        </div>
                                        <div class="filter-option">
                                            <input class="ml-3" type="checkbox" id="hubspot" name="status"
                                                value="HubSpot Contact" onclick="applyFilter()">
                                            <label for="hubspot" style="color: #FF5C35;">HubSpot</label>
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <th scope="col ">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-left fonts">
                        <?php $i = ($viewContact->currentPage() - 1) * $viewContact->perPage(); ?>
                        @forelse ($viewContact as $contact)
                            <tr data-status="{{ $contact['status'] }}">
                                <td>
                                    <input class="contact-checkbox" type="checkbox" name="contact_pid[]" value=" {{ 
                                        $contact->contact_pid ?? 
                                        $contact->contact_archive_pid ?? 
                                        $contact->contact_discard_pid 
                                        }} ">
                                </td>
                                <td>{{ ++$i }}</td>
                                <td>{{ $contact->name }}</td>
                                <td>{{ $contact->email }}</td>
                                <td>{{ $contact->contact_number }}</td>
                                <td>
                                    @inject('countryCodeMapper', 'App\Services\CountryCodeMapper')
                                    @php
                                        // Fetch the country code using the injected service
                                        $countryCode = $countryCodeMapper->getCountryCode($contact['country']);
                                    @endphp
                                    @if ($countryCode)
                                        <img src="{{ asset('flags/' . strtolower($countryCode) . '.svg') }}"
                                            alt="{{ $contact['country'] }}" width="20" height="15">
                                    @else
                                        <!-- Optional: Add a fallback image or text when the country code is not found -->
                                        <span>No flag available</span>
                                    @endif
                                    {{ $contact['country'] }}
                                </td>
                                @inject('contactModel', 'App\Models\Contact')
                                <td>
                                    <span class="status-indicator"
                                        style="background-color:
                                        @if ($contact['status'] === 'HubSpot Contact') #FFE8E2;color:#FF5C35;
                                        @elseif ($contact['status'] === 'Discard')
                                            #FF7F86; color: #BD000C;
                                        @elseif ($contact['status'] === 'InProgress')
                                            #FFF3CD; color: #FF8300;
                                        @elseif ($contact['status'] === 'New')
                                            #CCE5FF ; color:  #318FFC;
                                        @elseif ($contact['status'] === 'Archive')
                                        #E2E3E5; color: #303030; @endif
                                        ">
                                        @if ($contact['status'] === 'HubSpot Contact')
                                            HubSpot
                                        @elseif ($contact['status'] === 'Discard')
                                            Discard
                                        @elseif ($contact['status'] === 'InProgress')
                                            In Progress
                                        @elseif ($contact['status'] === 'New')
                                            New
                                        @elseif ($contact['status'] === 'Archive')
                                            Archive
                                        @endif
                                    </span> 
                                </td>
                                <td>
                                    <a href=" {{ route('owner#view-contact', $contact->contact_pid) }} "
                                        class="btn hover-action" style="padding:10px 12px;" data-toggle="tooltip" title="View">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <td colspan="8" class="text-center">
                                No Contact Found.
                            </td>
                        @endforelse
                    </tbody>
                </table>
                </div>
                <div class="modal fade" id="transferContact" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content rounded-0">
                            <div class="modal-header d-flex justify-content-between align-items-center"
                                style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                                border:none;border-top-left-radius: 0; border-top-right-radius: 0;">
                                <h5 class="modal-title" id="successModalLabel" style="color: #91264c">
                                    <strong>Transfer Contact</strong>
                                </h5>
                                <img src="{{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png') }}" alt="Company Logo"
                                    style="height: 30px;">
                            </div>
                            <div class="modal-body" style="color: #91264c;border:none;">
                                <div class="row" style="margin: 0.5rem 0 1.75rem 4px;">
                                    <p>Total Contacts Selected: <span id="selectedCount">0</span></p>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-center">From</p>
                                        <input style="width: 100%; 
                                                        padding: 0.25rem; 
                                                        white-space: nowrap; 
                                                        overflow: hidden; 
                                                        text-overflow: ellipsis; 
                                                        border-radius: 10px;" type="text" value="{{ $owner->owner_name }}" disabled>
                                    </div>
                                    <div class="col-6">
                                        <h5 class="text-center">Transfer Methods</h5>
                                        <div>
                                            <div class="d-flex justify-content-between align-items-center my-2">
                                                <input type="radio" name="transferMethod" id="roundRobin" value="Transfer Selected Contacts" checked>
                                                <label class="text-left" style="width: 180px; font-size: 1rem;" for="roundRobin">Transfer Selected Contacts</label>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center my-2">
                                                <input type="radio" name="transferMethod" id="assignAgent" value="Select all Contacts">
                                                <label class="text-left" style="width: 180px; font-size: 1rem;" for="assignAgent">Transfer All Contacts</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Progress Bar -->
                                <div class="progress-container">
                                    <div class="progress-bar" id="progress-bar">0%</div>
                                </div>
                            </div>
                            <div class="modal-footer" style="border:none;">
                                <button type="button" class="btn archive-table" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn hover-action" id="confirmTransfer">Confirm</button>
                            </div>                     
                        </div>
                    </div>
                </div>                
                </div>
                <div aria-label="Page navigation example " class="paginationContainer">
                    <ul class="pagination justify-content-center">
                        <!-- Previous Button -->
                        <li class="page-item {{ $viewContact->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link font-educ" href="{{ $viewContact->previousPageUrl() }}"
                                aria-label="Previous">&#60;</a>
                        </li>
                        <!-- First Page Button -->
                        @if ($viewContact->currentPage() > 3)
                            <li class="page-item">
                                <a class="page-link font-educ" href="{{ $viewContact->url(1) }}">1</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link font-educ" href="{{ $viewContact->url(2) }}">2</a>
                            </li>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        @endif
                        <!-- Middle Page Buttons -->
                        @for ($i = max($viewContact->currentPage() - 1, 1); $i <= min($viewContact->currentPage() + 1, $viewContact->lastPage()); $i++)
                            <li class="page-item {{ $i == $viewContact->currentPage() ? 'active' : '' }}">
                                <a class="page-link font-educ {{ $i == $viewContact->currentPage() ? 'active-bg' : '' }}"
                                    href="{{ $viewContact->url($i) }}">{{ $i }}</a>
                            </li>
                        @endfor
                        <!-- Last Page Button -->
                        @if ($viewContact->currentPage() < $viewContact->lastPage() - 2)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                            <li class="page-item">
                                <a class="page-link font-educ"
                                    href="{{ $viewContact->url($viewContact->lastPage() - 1) }}">{{ $viewContact->lastPage() - 1 }}</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link font-educ"
                                    href="{{ $viewContact->url($viewContact->lastPage()) }}">{{ $viewContact->lastPage() }}</a>
                            </li>
                        @endif
                        <!-- Next Button -->
                        <li class="page-item {{ !$viewContact->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link font-educ" href="{{ $viewContact->nextPageUrl() }}" aria-label="Next">&#62;</a>
                        </li>
                    </ul>
                </div>
            </div>
        </form>
    </div>        
    @else
        <div class="alert alert-danger text-center mt-5">
            <strong>Access Denied!</strong> You do not have permission to view this page.
        </div>
    @endif
    @if (Session::has('success'))
        <script>
            $(document).ready(function() {
                $('#successModal').modal('show');
            });
        </script>
    @endif
    <script>
        $(document).ready(function() {
            $('#errorModal').modal('show');
            @if (Session::has('warning'))
                $('#warningModal').modal('show');
            @endif
            @if (Session::has('error'))
                $('#errorModal').modal('show');
            @endif
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if ($errors->any())
                var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            @endif
        });
    </script>
    <script>
        function sortTable(columnName, order) {
        let table, rows, switching, i, x, y, shouldSwitch;
        table = document.querySelector(".table");
        switching = true;
        
        // Loop until no switching has been done
        while (switching) {
            switching = false;
            rows = table.rows;
            
            // Loop through all table rows except the first (headers)
            for (i = 1; i < (rows.length - 1); i++) {
                shouldSwitch = false;
                
                // Determine the column index based on columnName
                let columnIndex;
                if (columnName === 'name') {
                    columnIndex = 2; // Index for the 'Name' column
                } else if (columnName === 'email') {
                    columnIndex = 3; // Index for the 'Email' column
                } else if (columnName === 'phone') {
                    columnIndex = 4; // Index for the 'Phone Contact' column
                } else if (columnName === 'country') {
                    columnIndex = 5; // Index for the 'Country' column
                } else if (columnName === 'status') {
                    columnIndex = 6; // Index for the 'Status' column
                }

                // Compare the two elements in the column to see if they should switch
                x = rows[i].querySelectorAll("td")[columnIndex];
                y = rows[i + 1].querySelectorAll("td")[columnIndex];
                
                if (order === 'asc' && x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                    shouldSwitch = true;
                    break;
                } else if (order === 'desc' && x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                    shouldSwitch = true;
                    break;
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
        const rows = table.tBodies[0].rows; // Only get rows in the tbody

        // Loop through all rows, starting from index 0
        for (let i = 0; i < rows.length; i++) {
            // Ensure we're updating the "No #" column (index 1), not the checkbox (index 0)
            rows[i].querySelectorAll("td")[1].innerText = i + 1; // Assign row number starting from 1
        }
}



    </script>
    <script src=" {{ asset('js/update_status.js') }} "></script>
    <script src=" {{ asset('js/progress_bar.js') }} "></script>
    <script src=" {{ asset('js/transfer_contact.js') }} "></script>
    <script src=" {{ asset('js/checkbox_table.js') }} "></script>
    <script src=" {{ asset('js/search_name.js') }} "></script>
    <script src=" {{ asset('js/filter_status.js') }} "></script>
    <script src=" {{ asset('js/active_buttons.js') }} "></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection