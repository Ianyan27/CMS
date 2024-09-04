@extends('layouts.app')

@section('title', 'HubSpot Contact Listing Page')

@section('content')
@if (Auth::check() && Auth::user()->role == 'BUH')
    <div class="container-max-height">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <h5 class="mr-3 my-2 headings">HubSpot Contact Listing</h5>
                <button class="btn archive-table mx-3 active" id="show-no-sync">
                    View Unsynced
                </button>
                <button class="btn hubspot-btn mx-3" id="show-synced">
                    View Synced
                </button>
            </div>
            <div class="d-flex align-items-center mr-3">
                <form id="hubspotContactsForm">
                    @csrf
                    <div class="d-flex">
                        <button type="button" class="btn hover-action ml-auto" id="submitContacts">
                            <i class="fa-brands fa-hubspot" style="margin: 0"></i><span>Batch Sync</span>
                        </button>
                    </div>
            </div>
        </div>
        <!-- Table for No Sync Contacts -->
        <div class="table-container" id="no-sync">
            <table class="table table-hover mt-2">
                <thead class="text-left font-educ">
                    <tr class="text-left font-educ">
                        <th scope="col"></th>
                        <th scope="col">No#</th> <!-- Index column header -->
                        <th scope="col">Name
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-name"
                                onclick="sortTable('name', 'asc'); toggleSort('sortDown-name', 'sortUp-name')"></i>
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-name"
                                onclick="sortTable('name', 'desc'); toggleSort('sortUp-name', 'sortDown-name')"
                                style="display: none;"></i>
                        </th>
                        <th scope="col">Email
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-email"
                                onclick="sortTable('email', 'asc'); toggleSort('sortDown-email', 'sortUp-email')"></i>
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-email"
                                onclick="sortTable('email', 'desc'); toggleSort('sortUp-email', 'sortDown-email')"
                                style="display: none;"></i>
                        </th>
                        <th scope="col">Phone</th>
                    </tr>
                </thead>
                <tbody class="text-left bg-row fonts">
                    @forelse ($hubspotContactsNoSync as $index => $contact)
                        <tr>
                            <td><input type="checkbox" name="selectedContacts[]" value="{{ $contact->contact_pid }}"></td>
                            <td>{{ $index + 1 }}</td> <!-- Index value -->
                            <td>{{ $contact->name }}</td>
                            <td>{{ $contact->email }}</td>
                            <td>{{ $contact->phone }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No contacts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-container" id="synced" style="display:none;">
            <table class="table table-hover mt-2">
                <thead class="text-left font-educ">
                    <tr>
                        <th>No#</th> <!-- Index column header -->
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Synced Time</th>
                    </tr>
                </thead>
                <tbody class="text-left bg-row fonts">
                    @foreach ($hubspotContactsSynced as $index => $contact)
                        <tr data-contact-id="{{ $contact->contact_pid }}">
                            <td>{{ $index + 1 }}</td> <!-- Index value -->
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
    @else
        <div class="alert alert-danger text-center mt-5">
            <strong>Access Denied!</strong> You do not have permission to view this page.
        </div>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        const showNoSyncBtn = document.getElementById('show-no-sync');
        const showSyncedBtn = document.getElementById('show-synced');

        function updateActiveButton(buttonToActivate) {
            showNoSyncBtn.classList.remove('active');
            showSyncedBtn.classList.remove('active');
            buttonToActivate.classList.add('active');
        }

        showNoSyncBtn.addEventListener('click', function() {
            updateActiveButton(showNoSyncBtn);
            // Add your code to show "Unsynced" content here
        });

        showSyncedBtn.addEventListener('click', function() {
            updateActiveButton(showSyncedBtn);
            // Add your code to show "Synced" content here
        });

        // Initial state
        updateActiveButton(showNoSyncBtn); // Default to "Unsynced" active
    });
        const showNoSyncBtn = document.getElementById('show-no-sync');
        const showSyncedBtn = document.getElementById('show-synced');
        const noSyncContainer = document.getElementById('no-sync');
        const syncedContainer = document.getElementById('synced');

        // Function to hide all tables
        function hideAllTables() {
            noSyncContainer.style.display = 'none';
            syncedContainer.style.display = 'none';
        }

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
            // Log all selected contacts
            const selectedContacts = formData.getAll('selectedContacts[]');
            console.log(selectedContacts);

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
