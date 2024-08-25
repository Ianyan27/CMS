@section('title', 'Sale Agent Dashboard')

@extends('layouts.app')

@section('content')

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
    </style>

    <div class="container-max-height">
        <div class="table-title d-flex justify-content-between align-items-center mb-3">
            <h2 class="ml-3 mb-2 font-educ"><strong>Sales Agents</strong></h2>
            <div class="d-flex align-items-center mr-3">
                <button class="btn hover-action add-sales-agent-button" data-toggle="modal"
                    data-target="#addSalesAgentModal">
                    <i class="fa-solid fa-square-plus"></i>
                </button>
            </div>
        </div>
        <!-- Search Bar Section -->
        <div class="search-box d-flex align-items-center mb-2" style="max-width: 350px;">
            <input type="search" class="form-control mr-1" placeholder="Search" id="search-input" aria-label="Search">
            <button class="btn hover-action mx-1" type="submit" data-toggle="tooltip" title="Search">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
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
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-lef bg-row">
                    @foreach ($owner as $owners)
                        <tr>
                            <td> {{ $owners->owner_pid }} </td>
                            <td> {{ $owners->owner_name }} </td>
                            <td> {{ $owners->owner_hubspot_id }} </td>
                            <td> {{ $owners->owner_business_unit }} </td>
                            <td> {{ $owners->country }} </td>
                            <td> {{ $owners->total_in_progress }} </td>
                            <td> {{ $owners->total_hubspot_sync }} </td>
                            <td>
                                <a href=" {{ route('owner#view_owner', $owners->owner_pid) }} " class="btn hover-action"
                                    data-toggle="tooltip" title="View">
                                    <i class="fa-solid fa-eye "></i>
                                </a>
                                <a href="#" class="btn hover-action" data-toggle="tooltip" title="">
                                    <i class="fa-solid fa-pen-to-square"></i>
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
