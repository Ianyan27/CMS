@section('title', 'Sale Agent Dashboard')

@extends('layouts.app')
@extends('layouts.Add_Sales-Agent_Modal')
@extends('layouts.Delete_Sales-Agent_Prompt_Modal')

@section('content')
    @if (Session::has('success'))
    @endif
    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"
                    style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                        border:none;border-top-left-radius: 0; border-top-right-radius: 0;">
                    <h5 class="modal-title" id="successModalLabel" style="color: #91264c"><strong>Success</strong></h5>
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


    <link rel="stylesheet" href="{{ URL::asset('css/contact-detail.css') }}">
    <div class="container-max-height">
        <div class="table-title d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center">
                <h2 style="margin: 0 0.5rem 0 0.25rem;" class="font-educ headings">Sales Agents</h2>
                <!-- Search Bar Section -->
                <div class="search-box d-flex align-items-center ml-3">
                    <input type="search" class="form-control mr-1" placeholder="Search Name" id="search-name"
                        aria-label="Search">
                    <button style="padding: 10px 12px;" class="btn hover-action" type="button" data-toggle="tooltip"
                        title="Search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </div>
            <div class="d-flex align-items-center mr-3">
                <button class="btn hover-action add-sales-agent-button" data-toggle="modal"
                    data-target="#addSalesAgentModal" style="padding: 10px 12px;">
                    <i class="fa-solid fa-square-plus"></i>
                </button>
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
                        <th scope="col" class="text-center">Total Assign Contacts</th>
                        <th scope="col" class="text-center">Total Hubspot Sync</th>
                        <th scope="col" class="text-center">Total In Progress</th>
                        <th scope="col ">Action</th>
                    </tr>
                </thead>
                <tbody class="text-left fonts">
                    @foreach ($owner as $owners)
                        <tr>
                            <td>{{ $owners->owner_name }}</td>
                            <td>{{ $owners->owner_hubspot_id }}</td>
                            <td>{{ $owners->country }}</td>
                            <td class="text-center">{{ $owners->total_assign_contacts }}</td>
                            <td class="text-center">{{ $owners->total_hubspot_sync }}</td>
                            <td class="text-center">{{ $owners->total_in_progress }}</td>
                            <td>
                                <a href="{{ route('owner#view-owner', $owners->owner_pid) }}" class="btn hover-action"
                                    data-toggle="tooltip" title="View" style="padding: 10px 12px;">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <!-- Delete button triggers the modal -->
                                <a href="#" class="btn hover-action" style="padding: 10px 12px;" data-toggle="modal"
                                    data-target="#deleteModal" onclick="setDeleteAction('#')">
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
                    <a class="page-link font-educ" href="{{ $owner->previousPageUrl() }}" aria-label="Previous">&#60;</a>
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
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Dropdown toggle functionality
            $('.dropdown-toggle').click(function() {
                $(this).siblings('.dropdown-menu').toggle();
            });
            // Hide the dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.dropdown').length) {
                    $('.dropdown-menu').hide();
                }
            });
            // Handle the filter selection
            $('.dropdown-item input[type="radio"]').change(function() {
                var selectedValue = $(this).val();
                // alert("Selected Filter: " + selectedValue); 
                $('.dropdown-menu').hide();
            });
        });
    </script>
@endsection
