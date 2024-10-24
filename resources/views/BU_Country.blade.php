@section('title', 'BU & Countries')

@extends('layouts.app')

@include('layouts.BU_Modal')
@include('layouts.Country_Modal')

@section('content')
    @if ($errors->any() || session('country-error') || session('bu-error'))
        <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header"
                        style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                border:none;border-top-left-radius: 0; border-top-right-radius: 0;">
                        <h5 class="modal-title" id="errorModalLabel" style="color: #91264c"><strong>Error</strong>
                        </h5>
                    </div>
                    <div class="modal-body" style="color: #91264c;border:none;">
                        @if (session('country-error'))
                            {{ session('country-error') }}
                        @elseif (session('bu-error'))
                            {{ session('bu-error') }}
                        @else
                            {{ $errors->first() }}
                        @endif
                    </div>
                    <div class="modal-footer" style="border:none;">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            style="background: #91264c; color:white;">OK</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (Session::has('country-success') || Session::has('bu-success'))
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
                        @if (Session::has('country-success'))
                            {{ Session::get('country-success') }}
                        @elseif (Session::has('bu-success'))
                            {{ Session::get('bu-success') }}
                        @endif
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
            <table class=" table table-hover mt-2" id="bu-table">
                <thead class="text-left font-educ">
                    <tr class="text-left font-educ">
                        <th scope="col" style="width:20%;">No #</th>
                        <th scope="col" style="width:60%;" id="name-header">Name
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-name"
                                onclick="sortTable('name', 'asc'); toggleSort('sortDown-name', 'sortUp-name')"></i>
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-name"
                                onclick="sortTable('name', 'desc'); toggleSort('sortUp-name', 'sortDown-name')"
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
                            <td class="d-flex justify-content-center" style="gap: 0.75rem;">
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
                                    <a class="btn hover-action" data-toggle="modal" data-target="#deleteModal"
                                        data-entity-id="{{ $bu->id }}" data-entity-type="BU"
                                        data-section="sales-admin">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">No business units found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-container" id="countries">
            <table class=" table table-hover mt-2" id="countries-table">
                <thead class="text-left font-educ">
                    <tr class="text-left font-educ">
                        <th scope="col" style="width:20%;">No #</th>
                        <th scope="col" style="width:60%;" id="name-header">Name
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-name"
                                onclick="sortTable('name', 'asc'); toggleSort('sortDown-name', 'sortUp-name')"></i>
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-name"
                                onclick="sortTable('name', 'desc'); toggleSort('sortUp-name', 'sortDown-name')"
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
                            <td class="d-flex justify-content-center">
                                <a class="btn hover-action" data-toggle="modal"
                                    data-target="#editCountryModal{{ $country->id }}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                @if (Auth::user()->role == 'Admin')
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
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">No countries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div aria-label="Page navigation example " class="paginationContainer">
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
    </div>
    <script src="{{ asset('js/show_bu&country_table.js') }}"></script>
    <script src=" {{ asset('js/search_input.js') }}"></script>
    <script>
        // Get references to the buttons
        const buButton = document.getElementById('show-bu');
        const countryButton = document.getElementById('show-country');

        // Function to handle the active class toggle
        function toggleActiveClass(event) {
            // Remove 'active' class from both buttons
            buButton.classList.remove('active');
            countryButton.classList.remove('active');

            // Add 'active' class to the clicked button
            event.target.classList.add('active');
        }

        // Add event listeners to both buttons
        buButton.addEventListener('click', toggleActiveClass);
        countryButton.addEventListener('click', toggleActiveClass);
    </script>
@endsection
