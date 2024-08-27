@extends('layouts.app')

@section('title', 'HubSpot Contact Listing Page')

@section('content')
    <div class="container-max-height">
        <link rel="stylesheet" href="{{ URL::asset('css/contact_listing.css') }}">
        <form id="hubspotContactsForm" method="POST" action="{{ route('submitHubspotContacts') }}">
            @csrf
            <div class="table-title d-flex justify-content-between align-items-center mb-4">
                <h5 class="mr-3 my-2 headings">HubSpot Contact Listing</h5>
                <div class="d-flex">
                    <button class="btn mx-3" id="show-all">
                        All Contacts
                    </button>
                    <button class="btn mx-3" id="show-no-sync">
                        No Sync Contacts
                    </button>
                    <button class="btn mx-3" id="show-synced">
                        Synced Contacts
                    </button>
                </div>
                <div class="d-flex">

                    <button type="submit" class="btn hover-action ml-auto">
                        Submit Selected Contacts
                    </button>
                </div>
            </div>



            <!-- Table for All HubSpot Contacts -->
            <div class="table-container" id="all-contacts">
                <table class="table table-hover mt-2">
                    <thead class="text-left font-educ">
                        <tr>
                            <th>Select</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Last Synced</th>
                        </tr>
                    </thead>
                    <tbody class="text-left bg-row fonts">
                        @foreach ($hubspotContacts as $contact)
                            <tr>
                                <td><input type="checkbox" name="selectedContacts[]" value="{{ $contact->contact_pid }}">
                                </td>
                                <td>{{ $contact->name }}</td>
                                <td>{{ $contact->email }}</td>
                                <td>{{ $contact->phone }}</td>
                                <td>{{ $contact->datetime_of_hubspot_sync }}</td>
                            </tr>
                        @endforeach
                        @if (count($hubspotContacts) == 0)
                            <tr>
                                <td colspan="5" class="text-center">No contacts found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Table for No Sync Contacts -->
            <div class="table-container" id="no-sync" style="display:none;">
                <table class="table table-hover mt-2">
                    <thead class="text-left font-educ">
                        <tr>
                            <th>Select</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Last Synced</th>
                        </tr>
                    </thead>
                    <tbody class="text-left bg-row fonts">
                        @foreach ($hubspotContactsNoSync as $contact)
                            <tr>
                                <td><input type="checkbox" name="selectedContacts[]" value="{{ $contact->contact_pid }}">
                                </td>
                                <td>{{ $contact->name }}</td>
                                <td>{{ $contact->email }}</td>
                                <td>{{ $contact->phone }}</td>
                                <td>{{ $contact->datetime_of_hubspot_sync }}</td>
                            </tr>
                        @endforeach
                        @if (count($hubspotContactsNoSync) == 0)
                            <tr>
                                <td colspan="5" class="text-center">No contacts found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Table for Synced Contacts -->
            <div class="table-container" id="synced" style="display:none;">
                <table class="table table-hover mt-2">
                    <thead class="text-left font-educ">
                        <tr>
                            <th>Select</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Last Synced</th>
                        </tr>
                    </thead>
                    <tbody class="text-left bg-row fonts">
                        @foreach ($hubspotContactsSynced as $contact)
                            <tr>
                                <td><input type="checkbox" name="selectedContacts[]" value="{{ $contact->contact_pid }}">
                                </td>
                                <td>{{ $contact->name }}</td>
                                <td>{{ $contact->email }}</td>
                                <td>{{ $contact->phone }}</td>
                                <td>{{ $contact->datetime_of_hubspot_sync }}</td>
                            </tr>
                        @endforeach
                        @if (count($hubspotContactsSynced) == 0)
                            <tr>
                                <td colspan="5" class="text-center">No contacts found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>


        </form>

        <!-- Pagination -->
        <div aria-label="Page navigation example" class="paginationContainer">
            <ul class="pagination justify-content-center">
                <!-- Previous Button -->
                <li class="page-item {{ $hubspotContacts->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link font-educ" href="{{ $hubspotContacts->previousPageUrl() }}"
                        aria-label="Previous">&#60;</a>
                </li>
                <!-- First Page Button -->
                @if ($hubspotContacts->currentPage() > 3)
                    <li class="page-item">
                        <a class="page-link font-educ" href="{{ $hubspotContacts->url(1) }}">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link font-educ" href="{{ $hubspotContacts->url(2) }}">2</a>
                    </li>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif
                <!-- Middle Page Buttons -->
                @for ($i = max($hubspotContacts->currentPage() - 1, 1); $i <= min($hubspotContacts->currentPage() + 1, $hubspotContacts->lastPage()); $i++)
                    <li class="page-item {{ $i == $hubspotContacts->currentPage() ? 'active' : '' }}">
                        <a class="page-link font-educ {{ $i == $hubspotContacts->currentPage() ? 'active-bg' : '' }}"
                            href="{{ $hubspotContacts->url($i) }}">{{ $i }}</a>
                    </li>
                @endfor
                <!-- Last Page Button -->
                @if ($hubspotContacts->currentPage() < $hubspotContacts->lastPage() - 2)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                    <li class="page-item">
                        <a class="page-link font-educ"
                            href="{{ $hubspotContacts->url($hubspotContacts->lastPage() - 1) }}">{{ $hubspotContacts->lastPage() - 1 }}</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link font-educ"
                            href="{{ $hubspotContacts->url($hubspotContacts->lastPage()) }}">{{ $hubspotContacts->lastPage() }}</a>
                    </li>
                @endif
                <!-- Next Button -->
                <li class="page-item {{ !$hubspotContacts->hasMorePages() ? 'disabled' : '' }}">
                    <a class="page-link font-educ" href="{{ $hubspotContacts->nextPageUrl() }}" aria-label="Next">&#62;</a>
                </li>
            </ul>
        </div>
    </div>

    <script>
        const showAllBtn = document.getElementById('show-all');
        const showNoSyncBtn = document.getElementById('show-no-sync');
        const showSyncedBtn = document.getElementById('show-synced');

        const allContactsContainer = document.getElementById('all-contacts');
        const noSyncContainer = document.getElementById('no-sync');
        const syncedContainer = document.getElementById('synced');

        // Function to hide all tables
        function hideAllTables() {
            allContactsContainer.style.display = 'none';
            noSyncContainer.style.display = 'none';
            syncedContainer.style.display = 'none';
        }

        // Show All Contacts Table (default)
        showAllBtn.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent form submission
            hideAllTables();
            allContactsContainer.style.display = 'block';
        });

        // Show No Sync Contacts Table
        showNoSyncBtn.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent form submission
            hideAllTables();
            noSyncContainer.style.display = 'block';
        });

        // Show Synced Contacts Table
        showSyncedBtn.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent form submission
            hideAllTables();
            syncedContainer.style.display = 'block';
        });
    </script>
@endsection
