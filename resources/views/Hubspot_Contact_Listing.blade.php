@extends('layouts.app')

@section('title', 'HubSpot Contact Listing Page')

@section('content')
    <div class="container-max-height">
        <link rel="stylesheet" href="{{ URL::asset('css/contact_listing.css') }}">
        <form id="hubspotContactsForm">
            @csrf
            <div class="table-title d-flex justify-content-between align-items-center mb-4">
                <h5 class="mr-3 my-2 headings">HubSpot Contact Listing</h5>
                <div class="d-flex">
                    <button class="btn mx-3" id="show-all">
                        All Contacts
                    </button>
                    <button class="archive-table btn mx-3" id="show-no-sync">
                        Unsynced Contacts
                    </button>
                    <button class="hubspot-btn btn mx-3" id="show-synced">
                        Synced Contacts
                    </button>
                </div>
                <div class="d-flex">
                    <button type="button" class="btn hover-action ml-auto" id="submitContacts">
                        Sync to HubSpot
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
                            <tr data-contact-id="{{ $contact->contact_pid }}">
                                <td><input type="checkbox" name="selectedContacts[]" value="{{ $contact->contact_pid }}">
                                </td>
                                <td>{{ $contact->name }}</td>
                                <td>{{ $contact->email }}</td>
                                <td>{{ $contact->phone }}</td>
                                <td class="sync-datetime">{{ $contact->datetime_of_hubspot_sync }}</td>
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
                            <tr data-contact-id="{{ $contact->contact_pid }}">
                                <td><input type="checkbox" name="selectedContacts[]" value="{{ $contact->contact_pid }}">
                                </td>
                                <td>{{ $contact->name }}</td>
                                <td>{{ $contact->email }}</td>
                                <td>{{ $contact->phone }}</td>
                                <td class="sync-datetime">{{ $contact->datetime_of_hubspot_sync }}</td>
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
                            <tr data-contact-id="{{ $contact->contact_pid }}">
                                <td><input type="checkbox" name="selectedContacts[]" value="{{ $contact->contact_pid }}">
                                </td>
                                <td>{{ $contact->name }}</td>
                                <td>{{ $contact->email }}</td>
                                <td>{{ $contact->phone }}</td>
                                <td class="sync-datetime">{{ $contact->datetime_of_hubspot_sync }}</td>
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

        document.getElementById('submitContacts').addEventListener('click', function() {
        const form = document.getElementById('hubspotContactsForm');
        const formData = new FormData(form);

        fetch('{{ route('submit-hubspot-contacts') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': formData.get('_token'),
                },
            })
            .then(response => response.json())
            .then(data => {
                // Create the prompt element
                const downloadPrompt = document.createElement('div');

                downloadPrompt.style.position = 'fixed';
                downloadPrompt.style.top = '50%';
                downloadPrompt.style.left = '50%';
                downloadPrompt.style.transform = 'translate(-50%, -50%)';
                downloadPrompt.style.backgroundColor = '#fff';
                downloadPrompt.style.padding = '20px';
                downloadPrompt.style.boxShadow = '0px 0px 10px rgba(0, 0, 0, 0.4)';
                downloadPrompt.style.zIndex = '1000';
                downloadPrompt.style.borderRadius = '8px';
                downloadPrompt.style.textAlign = 'center';

                if (data.success) {
                    downloadPrompt.innerHTML = `${data.message}`;
                } else {
                    downloadPrompt.innerHTML = `${data.message}`;
                }

                document.body.appendChild(downloadPrompt);

                // Automatically remove the prompt after a few seconds
                setTimeout(() => {
                    if (data.success) {
                        window.location.reload(); // Refresh the page
                    } else {
                        downloadPrompt.remove(); // Remove the prompt after showing the error
                    }
                }, 2000); // Adjust the time as needed
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while submitting contacts.');
            });
        });
    </script>
@endsection
