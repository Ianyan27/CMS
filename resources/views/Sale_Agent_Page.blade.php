@section('title', 'Sale Agent Dashboard')

@extends('layouts.app')
@extends('layouts.Add_Sales-Agent_Modal')

@section('content')
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"
                    style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                border:none;border-top-left-radius: 0; border-top-right-radius: 0;">
                    <h5 class="modal-title" id="successModalLabel" style="color: #91264c"><strong>Error</strong>
                    </h5>
                </div>
                <div class="modal-body" style="color: #91264c;border:none;">
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    @endif
                </div>
                <div class="modal-footer" style="border:none;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        style="background: #91264c; color:white;">OK</button>
                </div>
            </div>
        </div>
    </div>
    @if (Auth::check() && Auth::user()->role == 'BUH')
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
                    <h2 style="margin: 0 0.5rem 0 0.25rem;" class="font-educ headings">Sales Agents</h2>
                    <button class="btn hover-action add-sales-agent-button" data-toggle="modal"
                        data-target="#addSalesAgentModal" style="padding: 10px 12px;">
                        <i class="fa-solid fa-square-plus"></i>
                    </button>
                </div>
                <div class="d-flex align-items-center mr-3">
                    <div class="search-box d-flex align-items-center ml-3">
                        <input type="search" class="form-control mr-1" placeholder="Search Name" id="search-name"
                            aria-label="Search">
                        <button style="padding: 10px 12px;" class="btn hover-action" type="button" data-toggle="tooltip"
                            title="Search">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="table-container">
                <table id="sales-agents-table" class="table table-hover mt-2">
                    <thead class="font-educ text-left">
                        <tr>
                            <th scope="col" id="name-header">Name
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-agent"
                                    onclick="sortByColumn('agent', 'asc'); toggleSort('sortDown-agent', 'sortUp-agent')"></i>
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-agent"
                                    onclick="sortByColumn('agent', 'desc'); toggleSort('sortUp-agent', 'sortDown-agent')"
                                    style="display: none;"></i>
                            </th>
                            <th scope="col">Hubspot ID</th>
                            <th scope="col" id="country-header">Country
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-country"
                                    onclick="sortByColumn('country', 'asc'); toggleSort('sortDown-country', 'sortUp-country')"></i>
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-country"
                                    onclick="sortByColumn('country', 'desc'); toggleSort('sortUp-country', 'sortDown-country')"
                                    style="display: none;"></i>
                            </th>
                            <th scope="col" class="text-center" data-toggle="tooltip" data-placement="top" title="Total contacts in Interested, Archive, and Discard tables">Total Assign Contacts</th>
                            <th scope="col" class="text-center" data-toggle="tooltip" data-placement="top" title="Total contacts synced in HubSpot">Total Hubspot Sync</th>
                            <th scope="col" class="text-center" data-toggle="tooltip" data-placement="top" title="Total engaging contacts">Total In Progress</th>

                            <th scope="col ">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-left fonts">
                        @foreach ($owner as $owners)
                            <tr>
                                <td>{{ $owners->owner_name }}</td>
                                <td>{{ $owners->owner_hubspot_id }}</td>
                                <td>
                                    @inject('countryCodeMapper', 'App\Services\CountryCodeMapper')

                                    @php
                                        // Fetch the country code using the injected service
                                        $countryCode = $countryCodeMapper->getCountryCode($owners['country']);
                                    @endphp

                                    @if ($countryCode)
                                        <img src="{{ asset('flags/' . strtolower($countryCode) . '.svg') }}"
                                            alt="{{ $owners['country'] }}" width="20" height="15">
                                    @else
                                        <!-- Optional: Add a fallback image or text when the country code is not found -->
                                        <span>No flag available</span>
                                    @endif
                                    {{ $owners['country'] }}
                                </td>
                                <td class="text-center">{{ $owners->total_assign_contacts }}</td>
                                <td class="text-center">{{ $owners->total_hubspot_sync }}</td>
                                <td class="text-center">{{ $owners->total_in_progress }}</td>
                                <td>
                                    <a href="{{ route('owner#view-owner', $owners->owner_pid) }}" class="btn hover-action"
                                        data-toggle="tooltip" title="View" style="padding: 10px 12px;">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <!-- Delete button triggers the modal -->
                                    <a class="btn hover-action" style="padding: 10px 12px;" data-toggle="modal"
                                        data-target="#deleteOwnerModal{{ $owners->owner_pid }}">
                                        <i class="fa-solid fa-trash"></i>
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
                    <li class="page-item {{ $owner->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link font-educ" href="{{ $owner->previousPageUrl() }}"
                            aria-label="Previous">&#60;</a>
                    </li>
                    <!-- First Page Button -->
                    @if ($owner->currentPage() > 3)
                        <li class="page-item">
                            <a class="page-link font-educ" href="{{ $owner->url(1) }}">1</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link font-educ" href="{{ $owner->url(2) }}">2</a>
                        </li>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                    <!-- Middle Page Buttons -->
                    @for ($i = max($owner->currentPage() - 1, 1); $i <= min($owner->currentPage() + 1, $owner->lastPage()); $i++)
                        <li class="page-item {{ $i == $owner->currentPage() ? 'active' : '' }}">
                            <a class="page-link font-educ {{ $i == $owner->currentPage() ? 'active-bg' : '' }}"
                                href="{{ $owner->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor
                    <!-- Last Page Button -->
                    @if ($owner->currentPage() < $owner->lastPage() - 2)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                        <li class="page-item">
                            <a class="page-link font-educ"
                                href="{{ $owner->url($owner->lastPage() - 1) }}">{{ $owner->lastPage() - 1 }}</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link font-educ"
                                href="{{ $owner->url($owner->lastPage()) }}">{{ $owner->lastPage() }}</a>
                        </li>
                    @endif
                    <!-- Next Button -->
                    <li class="page-item {{ !$owner->hasMorePages() ? 'disabled' : '' }}">
                        <a class="page-link font-educ" href="{{ $owner->nextPageUrl() }}" aria-label="Next">&#62;</a>
                    </li>
                </ul>
            </div>
        </div>
        </div>
        @foreach ($owner as $owners)
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
        @endforeach
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
    <script>
        $(document).ready(function() {
            $('#addSalesAgentForm').on('submit', function(e) {
                var email = $('#email').val();
                var validDomains = ['lithan.com', 'educlaas.com', 'learning.educlaas.com'];
                var emailDomain = email.split('@')[1];
                var isValid = validDomains.indexOf(emailDomain) !== -1;

                if (email && !isValid) {
                    
                    $('#emailError').text(
                        'The email address must be one of the following domains: lithan.com, educlaas.com, learning.educlaas.com'
                    );
                    e.preventDefault(); // Prevent form submission
                } else {
                    $('#emailError').text(''); // Clear any previous error message
                }
            });

            // Optional: Clear the error message when the modal is hidden
            $('#errorModal').on('hidden.bs.modal', function() {
                $('#emailError').text('');
            });
        });
    </script>
@endsection

<script>
    $(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); 
});
</script>