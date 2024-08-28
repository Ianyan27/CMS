@section('title', 'Sale Agent Dashboard')

@extends('layouts.app')
@extends('layouts.Add_Sales-Agent_Modal')

@section('content')
    {{-- 
    <style>
        .dropdown-menu {
            border-radius: 10px;
            border: 1px solid #B45F04;
            padding: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            width: 200px;
        }

        .dropdown-item {
            padding: 10px 15px;
            font-size: 16px;
            color: #333;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .dropdown-item:hover {
            background-color: #f0f0f0;
            border-radius: 5px;
        }

        .dropdown-item input[type="radio"] {
            margin-right: 10px;
        }

        .dropdown-toggle {
            cursor: pointer;
            padding: 5px;
            font-weight: bold;
        }

        .dropdown-toggle::after {
            display: none;
        }
    </style> --}}

    <link rel="stylesheet" href="{{ URL::asset('css/contact-detail.css') }}">
    <div class="container-max-height">
        <div class="table-title d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center">
                <h2 style="margin: 0 0.5rem 0 0.25rem;" class="font-educ headings">Sales Agents</h2>
                <!-- Search Bar Section -->
                <div class="search-box d-flex align-items-center mx-3" style="max-width: 350px;">
                    <input type="search" class="form-control mr-1" placeholder="Search ID" id="search-id" aria-label="Search">
                </div>
            </div>
            <div class="d-flex align-items-center mr-3">
                <button class="btn hover-action add-sales-agent-button" data-toggle="modal" data-target="#addSalesAgentModal"
                 style="padding: 10px 12px;">
                    <i class="fa-solid fa-square-plus"></i>
                </button>
            </div>
        </div>
        <div class="table-container">
            <table id="sales-agents-table" class="table table-hover mt-2">
                <thead class="font-educ text-left">
                    <tr>
                        <th scope="col">No #</th>
                        <th scope="col" id="name-header">Sale Agent Name
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-agent"
                                onclick="sortByColumn('agent', 'asc'); toggleSort('sortDown-agent', 'sortUp-agent')"></i>
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-agent"
                                onclick="sortByColumn('agent', 'desc'); toggleSort('sortUp-agent', 'sortDown-agent')"
                                style="display: none;"></i>
                        </th>
                        <th scope="col">Hubspot ID</th>
                        <th scope="colr">Owner Business Unit</th>
                        <th scope="col" id="country-header">Country
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-country"
                                onclick="sortByColumn('country', 'asc'); toggleSort('sortDown-country', 'sortUp-country')"></i>
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-country"
                                onclick="sortByColumn('country', 'desc'); toggleSort('sortUp-country', 'sortDown-country')"
                                style="display: none;"></i>
                        </th>
                        <th scope="col">Total In Progress</th>
                        <th scope="col">Total Hubspot Sync Contacts</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody class="text-left fonts">
                    @foreach ($owner as $owners)
                        <tr>
                            <td>{{ $owners->owner_pid }}</td>
                            <td>{{ $owners->owner_name }}</td>
                            <td>{{ $owners->owner_hubspot_id }}</td>
                            <td>{{ $owners->owner_business_unit }}</td>
                            <td>{{ $owners->country }}</td>
                            <td>{{ $owners->total_in_progress }}</td>
                            <td>{{ $owners->total_hubspot_sync }}</td>
                            <td>
                                <a href="{{ route('owner#view-owner', $owners->owner_pid) }}" class="btn hover-action" data-toggle="tooltip" title="View"
                                 style="padding: 10px 12px;">
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
    <script>
        document.getElementById('search-id').addEventListener('keyup', function() {
            var input = this.value.toLowerCase();
            var rows = document.querySelectorAll('#sales-agents-table tbody tr');

            rows.forEach(function(row) {
                var idCell = row.querySelector('td:first-child'); // Target the first column (ID)
                var idText = idCell.textContent || idCell.innerText;

                if (idText.toLowerCase().includes(input)) {
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
