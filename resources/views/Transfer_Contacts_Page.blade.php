@section('title', 'Transfer Contacts')

@extends('layouts.app')

@section('content')
@if (Auth::check() && Auth::user()->role == 'BUH')
    @if (Session::has('success'))
        <!-- Success Modal -->
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
                        {{ session('warning') }}
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
        <form action=" {{ route('owner#transfer') }} " method="POST">
            @csrf
            <div class="table-title d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center">
                    <h2 style="margin: 0 0.5rem 0 0.25rem;" class="font-educ headings">Transferable Contacts</h2>
                    <button type="button" class="btn btn-danger mx-4" data-toggle="modal" data-target="#transferContact">
                        Transfer Contacts <i class="fa-solid fa-right-left"></i>
                    </button>
                    <button type="button" class="btn hover-action" onclick="transferContacts({{ $owner->owner_pid }})">
                        Get Contacts
                    </button>
                </div>
                <div class="d-flex align-items-center mr-3">
                    <div class="search-box d-flex align-items-center mr-3 mb-2">
                        <input type="search" class="form-control mr-1" placeholder="Search Name or Email..." id="search-input"
                            aria-label="Search">
                        <button class="btn hover-action mx-1" type="submit" data-toggle="tooltip" title="Search">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="table-container">
                <table id="sales-agents-table" class="table table-hover mt-2">
                    <thead class="font-educ text-left">
                        <tr>
                            <th scope="col"><input type="checkbox" id="select-all"></th>
                            <th scope="col">No #</th>
                            <th scope="col" id="name-header">Name
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-agent"
                                    onclick="sortByColumn('agent', 'asc'); toggleSort('sortDown-agent', 'sortUp-agent')"></i>
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-agent"
                                    onclick="sortByColumn('agent', 'desc'); toggleSort('sortUp-agent', 'sortDown-agent')"
                                    style="display: none;"></i>
                            </th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone Contact</th>
                            <th scope="col" id="country-header">Country
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-country"
                                    onclick="sortByColumn('country', 'asc'); toggleSort('sortDown-country', 'sortUp-country')"></i>
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-country"
                                    onclick="sortByColumn('country', 'desc'); toggleSort('sortUp-country', 'sortDown-country')"
                                    style="display: none;"></i>
                            </th>
                            <th scope="col">Status</th>
                            <th scope="col ">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-left fonts">
                        <?php $i = ($viewContact->currentPage() - 1) * $viewContact->perPage(); ?>
                        @forelse ($viewContact as $contact)
                            <tr>
                                <td>
                                    <input class="contact-checkbox" type="checkbox" name="contact_pid[]" value=" {{ $contact->contact_pid }} ">
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
                                    <a href=" {{ route('owner#view-contact', $contact->contact_pid) }} "
                                        class="btn hover-action" data-toggle="tooltip" title="View">
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
                                <div class="progress my-3">
                                    <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
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
        {{-- @foreach ($viewContact as $owners)
            <div class="modal fade" id="deleteOwnerModal{{ $owners->owner_pid }}" tabindex="-1"
                aria-labelledby="deleteOwnerModalLabel{{ $owners->owner_pid }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-center">
                        <div class="icon-container mx-auto">
                            <i class="fa-solid fa-trash"></i>
                        </div>
                        <div class="modal-header border-0">
                        </div>
                        <div class="modal-body">
                            <p>You are about to delete this Sales Agent</p>
                            <p class="text-muted">This will delete your sales agent from your list.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <form action="{{ route('owner#delete', $owners->owner_pid) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach --}}
    @else
        <div class="alert alert-danger text-center mt-5">
            <strong>Access Denied!</strong> You do not have permission to view this page.
        </div>
@endif
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @if (Session::has('success'))
        <script type="text/javascript">
            $(document).ready(function() {
                $('#successModal').modal('show');
            });
        </script>
    @endif
    <script>
        $(document).ready(function() {
            $('#errorModal').modal('show');
        });
    </script>
    <script>
        $(document).ready(function() {
            @if (Session::has('warning'))
                $('#warningModal').modal('show');
            @endif
        });
    </script>
</script>
    </script>
    <script>
        function transferContacts(ownerPid) {
            
            // Construct URL directly
            let url = `/buh/get-contacts/${ownerPid}`;
            
            // Create a form dynamically
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            
            // Add CSRF token
            let csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Submit the form
            document.body.appendChild(form);
            form.submit();
        }
    </script>
    <script>
        document.getElementById('select-all').addEventListener('click', function(event) {
            const checkboxes = document.querySelectorAll('.contact-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = event.target.checked;
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.contact-checkbox');
            const selectedCount = document.getElementById('selectedCount');
            
            // Function to update the count
            function updateCount() {
                const checkedCount = document.querySelectorAll('.contact-checkbox:checked').length;
                selectedCount.textContent = checkedCount;
            }

            // Attach the updateCount function to each checkbox
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateCount);
            });

            // Also update the count if the "Select All" checkbox is clicked
            document.getElementById('select-all').addEventListener('change', updateCount);
        });

    </script>
    <script>
        document.getElementById('search-name').addEventListener('keyup', function() {
            var input = this.value.toLowerCase();
            var rows = document.querySelectorAll('#sales-agents-table tbody tr');

            rows.forEach(function(row) {
                var nameCell = row.querySelector('td:nth-child(1)'); // Target the first column (Name)
                var nameText = nameCell.textContent || nameCell.innerText;

                if (nameText.toLowerCase().includes(input)) {
                    row.style.display = ''; // Show the row if it matches the search input
                } else {
                    row.style.display = 'none'; // Hide the row if it doesn't match
                }
            });
        });
        document.addEventListener('DOMContentLoaded', function () {
            @if ($errors->any())
                var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            @endif
        });
    </script>
@endsection