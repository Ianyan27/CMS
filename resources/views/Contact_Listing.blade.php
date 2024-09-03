@extends('layouts.app')

@section('title', 'Contact Listing Page')

@section('content')

    {{-- Success pop up --}}
    @if (session('success'))
        <!-- Trigger the modal with a button (hidden, will be triggered by JavaScript) -->
        <button id="successModalBtn" type="button" class="btn btn-primary" data-toggle="modal" data-target="#successModal"
            style="display: none;">
            Open Modal
        </button>
        <!-- Modal -->
        <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header"
                        style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                border:none;">
                        <h5 class="modal-title font-educ" id="successModalLabel">Success</h5>
                    </div>
                    <div class="modal-body">
                        {{ session('success') }}
                    </div>
                    <div class="modal-footer" style="border:none">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <!-- Script to trigger the modal -->
                        <script type="text/javascript">
                            window.onload = function() {
                                document.getElementById('successModalBtn').click();
                            };
                        </script>


                        {{-- Error pop up --}}
                    @elseif (session('error'))
                        <!-- Trigger the modal with a button (hidden, will be triggered by JavaScript) -->
                        <button id="errorModalBtn" type="button" class="btn btn-primary" data-toggle="modal"
                            data-target="#errorModal" style="display: none;">
                            Open Modal
                        </button>

                        <!-- Error Modal -->
                        <div class="modal fade" id="errorModal" tabindex="-1" role="dialog"
                            aria-labelledby="errorModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header"
                                        style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                border:none;">
                                        <h5 class="modal-title font-educ" id="errorModalLabel">Error</h5>
                                    </div>
                                    <div class="modal-body">
                                        {{ session('error') }}
                                    </div>
                                    <div class="modal-footer" style="border:none">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Script to trigger the modal -->
                        <script type="text/javascript">
                            window.onload = function() {
                                document.getElementById('errorModalBtn').click();

                            };
                        </script>
    @endif

    <div class="container-max-height">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <h5 class="mr-3 my-2 headings">Contact Listing</h5>
                <button class="btn hover-action mx-3" id="show-contacts">
                    Interested Contacts
                </button>
                <button class="archive-table btn mx-3" id="show-archive">
                    Archive Contacts
                </button>
                <button class="discard-table btn mx-3" id="show-discard">
                    Discard Contacts
                </button>
            </div>
            <div class="search-box d-flex align-items-center mr-3 mb-2">
                <input type="search" class="form-control mr-1" placeholder="Search..." id="search-input"
                    aria-label="Search">
                <button class="btn hover-action mx-1" type="submit" data-toggle="tooltip" title="Search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </div>
        <div class="table-container" id="contacts">
            <table class=" table table-hover mt-2" id="contacts-table">
                <thead class="text-left font-educ">
                    <tr class="text-left font-educ">
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
                        <th scope="col">Contact
                        </th>
                        <th scope="col">Country
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-country"
                                onclick="sortTable('country', 'asc'); toggleSort('sortDown-country', 'sortUp-country')"></i>
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-country"
                                onclick="sortTable('country', 'desc'); toggleSort('sortUp-country', 'sortDown-country')"
                                style="display: none;"></i>
                        </th>
                        <th class=" position-relative" scope="col">
                            Status
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
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody class="text-left bg-row fonts">
                    <?php $i = 0; ?>
                    @forelse ($contacts as $contact)
                        <tr data-status="{{ $contact['status'] }}">
                            <td>{{ ++$i }}</td>
                            <td>{{ $contact['name'] }}</td>
                            <td>{{ $contact['email'] }}</td>
                            <td>{{ $contact['contact_number'] }}</td>
                            @inject('countryCodeMapper', 'App\Services\CountryCodeMapper')
                            <td>
                                <img src="{{ asset('flags/' . strtolower($countryCodeMapper->getCountryCode($contact['country'])) . '.svg') }}"
                                    alt="{{ $contact['country'] }}" width="20" height="15">
                                {{ $contact['country'] }}
                            </td>
                            <td>
                                <span class="status-indicator"
                                    style="background-color:
                                @if ($contact['status'] === 'HubSpot Contact') #FFE8E2;color:#FF5C35;
                                @elseif ($contact['status'] === 'discard')
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
                                    @elseif ($contact['status'] === 'discard')
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
                                <a href=" {{ route('contact#view', $contact->contact_pid) }} " class="btn hover-action"
                                    data-toggle="tooltip" title="View">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No contacts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-container" id="archive">
            <table class="table table-hover mt-2" id="archive-table">
                <thead class="text-left font-educ">
                    <tr class="text-left font-educ">
                        <th scope="col">No #</th>
                        <th scope="col">Name <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                        <th scope="col">Email <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                        <th scope="col">Contact</th>
                        <th scope="col">Country <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                        <th scope="col">
                            Status
                            <span class="ml-2" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="Status of the contact: Active, Discarded, New, In Progress, Archived">
                                <i class="fa-solid fa-info-circle text-muted"></i>
                            </span>
                        </th>
                        <th scope="col">Action

                        </th>
                    </tr>
                </thead>
                <tbody class="text-left bg-row">
                    <?php $i = 0; ?>
                    @forelse ($contactArchive as $archive)
                        <tr>
                            <td> {{ ++$i }} </td>
                            <td> {{ $archive['name'] }} </td>
                            <td> {{ $archive['email'] }} </td>
                            <td> {{ $archive['contact_number'] }} </td>
                            <td>
                                @inject('countryCodeMapper', 'App\Services\CountryCodeMapper')

                                @php
                                    // Fetch the country code using the injected service
                                    $countryCode = $countryCodeMapper->getCountryCode($archive['country']);
                                @endphp

                                @if ($countryCode)
                                    <img src="{{ asset('flags/' . strtolower($countryCode) . '.svg') }}"
                                        alt="{{ $archive['country'] }}" width="20" height="15">
                                @else
                                    <!-- Optional: Add a fallback image or text when the country code is not found -->
                                    <span>No flag available</span>
                                @endif
                                {{ $archive['country'] }}
                            </td>
                            <td>
                                <span class="status-indicator"
                                    style="background-color:
                            @if ($archive['status'] === 'Archive') #E2E3E5; color: #303030; @endif
                            ">
                                    @if ($archive['status'] === 'Archive')
                                        Archive
                                    @endif
                                </span>
                            </td>
                            <td>
                                <a href=" {{ route('archive#view', $archive->contact_archive_pid) }} "
                                    class="btn hover-action" data-toggle="tooltip" title="View">
                                    <i class="fa-solid fa-eye " style="font-educ-size: 1.5rem"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7"> No Archive Contacts</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-container" id="discard">
            <table class="table table-hover mt-2" id="discard-table">
                <thead class="font-educ text-left">
                    <tr class="font-educ text-left">
                        <th scope="col">No #</th>
                        <th scope="col">Name <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                        <th scope="col">Email <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                        <th scope="col">Contact</th>
                        <th scope="col">Country <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                        <th scope="col">
                            Status
                            <span class="ml-2" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="Status of the contact: Active, Discarded, New, In Progress, Archived">
                                <i class="fa-solid fa-info-circle text-muted"></i>
                            </span>
                            </td>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody class="text-left bg-row">
                    <?php $i = 0; ?>
                    @foreach ($contactDiscard as $discard)
                        <tr>
                            <td> {{ ++$i }} </td>
                            <td> {{ $discard['name'] }} </td>
                            <td> {{ $discard['email'] }} </td>
                            <td> {{ $discard['contact_number'] }} </td>
                            <td>
                                <img src="{{ asset('flags/' . strtolower($countryCodeMapper->getCountryCode($discard['country'])) . '.svg') }}"
                                    alt="{{ $discard['country'] }}" width="20" height="15">
                                {{ $discard['country'] }}
                            </td>
                            <td>
                                <span class="status-indicator"
                                    style="background-color:
                            @if ($discard['status'] === 'Discard') #FF7F86; color: #BD000C; @endif
                            ">
                                    @if ($discard['status'] === 'Discard')
                                        Discard
                                    @endif
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('discard#view', ['contact_discard_pid' => $discard->contact_discard_pid]) }}"
                                    class="btn hover-action" data-toggle="tooltip" title="View">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div aria-label="Page navigation example " class="paginationContainer">
            <ul class="pagination justify-content-center">
                <!-- Previous Button -->
                <li class="page-item {{ $contacts->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link font-educ" href="{{ $contacts->previousPageUrl() }}"
                        aria-label="Previous">&#60;</a>
                </li>
                <!-- First Page Button -->
                @if ($contacts->currentPage() > 3)
                    <li class="page-item">
                        <a class="page-link font-educ" href="{{ $contacts->url(1) }}">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link font-educ" href="{{ $contacts->url(2) }}">2</a>
                    </li>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif
                <!-- Middle Page Buttons -->
                @for ($i = max($contacts->currentPage() - 1, 1); $i <= min($contacts->currentPage() + 1, $contacts->lastPage()); $i++)
                    <li class="page-item {{ $i == $contacts->currentPage() ? 'active' : '' }}">
                        <a class="page-link font-educ {{ $i == $contacts->currentPage() ? 'active-bg' : '' }}"
                            href="{{ $contacts->url($i) }}">{{ $i }}</a>
                    </li>
                @endfor
                <!-- Last Page Button -->
                @if ($contacts->currentPage() < $contacts->lastPage() - 2)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                    <li class="page-item">
                        <a class="page-link font-educ"
                            href="{{ $contacts->url($contacts->lastPage() - 1) }}">{{ $contacts->lastPage() - 1 }}</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link font-educ"
                            href="{{ $contacts->url($contacts->lastPage()) }}">{{ $contacts->lastPage() }}</a>
                    </li>
                @endif
                <!-- Next Button -->
                <li class="page-item {{ !$contacts->hasMorePages() ? 'disabled' : '' }}">
                    <a class="page-link font-educ" href="{{ $contacts->nextPageUrl() }}" aria-label="Next">&#62;</a>
                </li>
            </ul>
        </div>
    </div>
    <script>
        const showContactsBtn = document.getElementById('show-contacts');
        const showArchiveBtn = document.getElementById('show-archive');
        const showDiscardBtn = document.getElementById('show-discard');

        const contactsContainer = document.getElementById('contacts');
        const archiveContainer = document.getElementById('archive');
        const discardContainer = document.getElementById('discard');

        // Function to hide all tables
        function hideAllTables() {
            contactsContainer.style.display = 'none';
            archiveContainer.style.display = 'none';
            discardContainer.style.display = 'none';
        }

        // Show Contacts Table (default)
        showContactsBtn.addEventListener('click', function() {
            hideAllTables();
            contactsContainer.style.display = 'block';
        });

        // Show Archive Table
        showArchiveBtn.addEventListener('click', function() {
            hideAllTables();
            archiveContainer.style.display = 'block';
        });

        // Show Discard Table
        showDiscardBtn.addEventListener('click', function() {
            hideAllTables();
            discardContainer.style.display = 'block';
        });
    </script>
    {{-- Active button --}}
    <script>
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
    </script>
@endsection
@section('scripts')
    <!-- Add Bootstrap Tooltip Initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection
