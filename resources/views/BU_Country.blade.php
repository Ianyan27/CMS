@section('title', 'BU & Countries')

@extends('layouts.app')

@include('layouts.BU_Modal')
@include('layouts.Country_Modal')

@section('content')
    @if ($errors->any())
        <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header"
                        style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                        border:none;border-top-left-radius: 0; border-top-right-radius: 0;">
                        <h5 class="modal-title" id="errorModalLabel" style="color: #91264c"><strong>Error</strong>
                        </h5>
                    </div>
                    <div class="modal-body text-center" style="color: #91264c;border:none;">
                        {{ $errors->first() }}
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
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <h5 class="mr-3 my-2 headings" style="margin-right: 10px; min-width: 235px;">BU Table</h5>
                <button class="btn hover-action" id="business-unit" data-toggle="modal" data-target="#addBUModal"
                    style="padding: 10px 12px;">
                    <i class="fa-solid fa-square-plus"></i>
                </button>
                <button class="btn hover-action" id="addCountryButton" data-toggle="modal" data-target="#addCountryModal"
                    style="padding: 10px 12px; display: none;">
                    <i class="fa-solid fa-square-plus"></i>
                </button>
                <button class="btn hover-action mx-3 bu-buttons active" id="show-bu">
                    BU
                </button>
                <button class="btn hover-action bu-buttons" id="show-country">
                    Country
                </button>
            </div>
            <div class="search-box d-flex align-items-center mr-3 mb-2">
                <input type="search" class="form-control mr-1" placeholder="Search Name" id="search-input"
                    aria-label="Search">
                <button class="btn hover-action mx-1" type="submit" data-toggle="tooltip" title="Search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </div>
        <div class="table-container" id="bu">
            <table class="table table-hover mt-2" id="bu-table">
                <thead class="text-left font-educ">
                    <tr class="text-left font-educ">
                        <th scope="col" style="width:20%;">No #</th>
                        <th scope="col" style="width:60%;" id="name-header">Name
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-name-first"
                                onclick="sortTable('bu-table','name', 'asc'); toggleSort('sortDown-name-first', 'sortUp-name-first')"></i>
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-name-first"
                                onclick="sortTable('bu-table','name', 'desc'); toggleSort('sortUp-name-first', 'sortDown-name-first')"
                                style="display: none;"></i>
                        </th>
                        <th scope="col" style="width:20%;" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-left bg-row fonts">
                    <?php $i = ($bus->currentPage() - 1) * $bus->perPage(); ?>
                    @forelse ($bus as $bu)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ $bu->name }}</td>
                            <td class="text-center">
                                <a class="btn hover-action" data-toggle="modal"
                                    data-target="#editBUModal{{ $bu->id }}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                @if (Auth::user()->role == 'Admin')
                                    <a class="btn hover-action" data-toggle="modal" data-target="#deleteModal"
                                        data-entity-id="{{ $bu->id }}" data-entity-type="BU" data-section="admin">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                @else
                                    {{-- <a class="btn hover-action" data-toggle="modal" data-target="#deleteModal"
                                        data-entity-id="{{ $bu->id }}" data-entity-type="BU"
                                        data-section="sales-admin">
                                        <i class="fa-solid fa-trash"></i>
                                    </a> --}}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No business units found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-container" id="countries">
            <table class="table table-hover mt-2" id="country-table">
                <thead class="text-left font-educ">
                    <tr class="text-left font-educ">
                        <th scope="col" style="width:20%;">No #</th>
                        <th scope="col" style="width:60%;" id="name-header">Name
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-name-second"
                                onclick="sortTable('country-table','name', 'asc'); toggleSort('sortDown-name-second', 'sortUp-name-second')"></i>
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-name-second"
                                onclick="sortTable('country-table','name', 'desc'); toggleSort('sortDown-name-second', 'sortUp-name-second')"
                                style="display: none;"></i>
                        </th>
                        <th scope="col" style="width:20%;" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-left bg-row fonts">
                    <?php $i = ($countries->currentPage() - 1) * $countries->perPage(); ?>
                    @forelse ($countries as $country)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ $country->name }}</td>
                            <td class="d-flex justify-content-center" style="gap: 0.75rem;">
                                <a class="btn hover-action" data-toggle="modal"
                                    data-target="#editCountryModal{{ $country->id }}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                {{-- @if (Auth::user()->role == 'Admin')
                                    <a class="btn hover-action" data-toggle="modal" data-target="#deleteModal"
                                        data-entity-id="{{ $country->id }}" data-entity-type="Country"
                                        data-section="admin">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                @else
                                    <a class="btn hover-action" data-toggle="modal" data-target="#deleteModal"
                                        data-entity-id="{{ $country->id }}" data-entity-type="Country"
                                        data-section="sales-admin">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                @endif --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No countries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div aria-label="Page navigation example " class="paginationContainer" id="bu-pagination">
            <ul class="pagination justify-content-center">
                <!-- Previous Button -->
                <li class="page-item {{ $bus->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link font-educ" href="{{ $bus->previousPageUrl() }}" aria-label="Previous">&#60;</a>
                </li>
                <!-- First Page Button -->
                @if ($bus->currentPage() > 3)
                    <li class="page-item">
                        <a class="page-link font-educ" href="{{ $bus->url(1) }}">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link font-educ" href="{{ $bus->url(2) }}">2</a>
                    </li>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif
                <!-- Middle Page Buttons -->
                @for ($i = max($bus->currentPage() - 1, 1); $i <= min($bus->currentPage() + 1, $bus->lastPage()); $i++)
                    <li class="page-item {{ $i == $bus->currentPage() ? 'active' : '' }}">
                        <a class="page-link font-educ {{ $i == $bus->currentPage() ? 'active-bg' : '' }}"
                            href="{{ $bus->url($i) }}">{{ $i }}</a>
                    </li>
                @endfor
                <!-- Last Page Button -->
                @if ($bus->currentPage() < $bus->lastPage() - 2)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                    <li class="page-item">
                        <a class="page-link font-educ"
                            href="{{ $bus->url($bus->lastPage() - 1) }}">{{ $bus->lastPage() - 1 }}</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link font-educ"
                            href="{{ $bus->url($bus->lastPage()) }}">{{ $bus->lastPage() }}</a>
                    </li>
                @endif
                <!-- Next Button -->
                <li class="page-item {{ !$bus->hasMorePages() ? 'disabled' : '' }}">
                    <a class="page-link font-educ" href="{{ $bus->nextPageUrl() }}" aria-label="Next">&#62;</a>
                </li>
            </ul>
        </div>
        <div aria-label="Page navigation example " class="paginationContainer" id="country-pagination">
            <ul class="pagination justify-content-center">
                <!-- Previous Button -->
                <li class="page-item {{ $countries->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link font-educ" href="{{ $countries->previousPageUrl() }}"
                        aria-label="Previous">&#60;</a>
                </li>
                <!-- First Page Button -->
                @if ($countries->currentPage() > 3)
                    <li class="page-item">
                        <a class="page-link font-educ" href="{{ $countries->url(1) }}">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link font-educ" href="{{ $countries->url(2) }}">2</a>
                    </li>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif
                <!-- Middle Page Buttons -->
                @for ($i = max($countries->currentPage() - 1, 1); $i <= min($countries->currentPage() + 1, $countries->lastPage()); $i++)
                    <li class="page-item {{ $i == $countries->currentPage() ? 'active' : '' }}">
                        <a class="page-link font-educ {{ $i == $countries->currentPage() ? 'active-bg' : '' }}"
                            href="{{ $countries->url($i) }}">{{ $i }}</a>
                    </li>
                @endfor
                <!-- Last Page Button -->
                @if ($countries->currentPage() < $countries->lastPage() - 2)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                    <li class="page-item">
                        <a class="page-link font-educ"
                            href="{{ $countries->url($countries->lastPage() - 1) }}">{{ $countries->lastPage() - 1 }}</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link font-educ"
                            href="{{ $countries->url($countries->lastPage()) }}">{{ $countries->lastPage() }}</a>
                    </li>
                @endif
                <!-- Next Button -->
                <li class="page-item {{ !$countries->hasMorePages() ? 'disabled' : '' }}">
                    <a class="page-link font-educ" href="{{ $countries->nextPageUrl() }}" aria-label="Next">&#62;</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header"
                    style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                        border:none;border-top-left-radius: 0; border-top-right-radius: 0;">
                    <h5 class="modal-title" id="addUserModalLabel" style="color: #91264c;">Success</h5>
                </div>
                <div class="modal-body text-center font-educ">
                    {{ session('success') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            @if (session('success'))
                $('#successModal').modal('show');
            @endif
            @error('country-name')
                $('#errorModal').modal('show');
            @enderror
            @error('bu-name')
                $('#errorModal').modal('show');
            @enderror
        });
    </script>
    <script src=" {{ URL::asset('js/show_bu&country_table.js') }} "></script>
    <script src=" {{ URL::asset('js/search_input.js') }} "></script>
    <script>
        function toggleSort(downIconId, upIconId) {
            const sortDown = document.getElementById(downIconId);
            const sortUp = document.getElementById(upIconId);

            if (sortDown.style.display === "none") {
                sortDown.style.display = "inline";
                sortUp.style.display = "none";
            } else {
                sortDown.style.display = "none";
                sortUp.style.display = "inline";
            }
        }

        function sortTable(tableId, columnName, order) {
            let table = document.getElementById(tableId),
                rows, switching, i, x, y, shouldSwitch, columnIndex;

            // Determine column index based on the column name
            if (columnName === "name") columnIndex = 1;
            else if (columnName === "email") columnIndex = 2;
            else if (columnName === "role" || columnName === "country") columnIndex = 3;
            else if (columnName === "country" || columnName === "bu") columnIndex = 4;

            switching = true;

            // Loop until no switching is needed
            while (switching) {
                switching = false;
                rows = table.getElementsByTagName("tr");

                for (i = 1; i < rows.length - 1; i++) {
                    shouldSwitch = false;
                    x = rows[i].querySelectorAll("td")[columnIndex];
                    y = rows[i + 1].querySelectorAll("td")[columnIndex];

                    if (order === "asc" && x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    } else if (order === "desc" && x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                }
                if (shouldSwitch) {
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                }
            }
            reassignRowNumbers(tableId);
        }

        function reassignRowNumbers(tableId) {
            const table = document.getElementById(tableId);
            const rows = table.getElementsByTagName("tr");

            for (let i = 1; i < rows.length; i++) {
                rows[i].querySelectorAll("td")[0].innerText = i; // Update "No #" column (index 0)
            }
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select elements for buttons and tables
            const showBUButton = document.getElementById('show-bu');
            const showCountryButton = document.getElementById('show-country');
            const buTable = document.getElementById('bu');
            const countryTable = document.getElementById('countries');
            const addBUButton = document.getElementById('business-unit');
            const addCountryButton = document.getElementById('addCountryButton');
            const buPagination = document.getElementById('bu-pagination');
            const countryPagination = document.getElementById('country-pagination');

            // Confirm element selection
            console.log("Elements selected:", {
                showBUButton,
                showCountryButton,
                buTable,
                countryTable,
                addBUButton,
                addCountryButton
            });

            // Event listener for "BU" button
            showBUButton.addEventListener('click', function() {
                console.log("BU Button Clicked"); // Debugging log
                buTable.style.display = 'block';
                addBUButton.style.display = 'block';
                buPagination.style.display = 'block';
                countryTable.style.display = 'none';
                addCountryButton.style.display = 'none';
                countryPagination.style.display = 'none';
                showBUButton.classList.add('active');
                showCountryButton.classList.remove('active');
            });

            // Event listener for "Country" button
            showCountryButton.addEventListener('click', function() {
                console.log("Country Button Clicked"); // Debugging log
                countryTable.style.display = 'block';
                addCountryButton.style.display = 'block';
                countryPagination.style.display = 'block';
                buTable.style.display = 'none';
                addBUButton.style.display = 'none';
                buPagination.style.display = 'none';
                showCountryButton.classList.add('active');
                showBUButton.classList.remove('active');
            });
        });
    </script>
@endsection
