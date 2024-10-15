@section('title', 'BU & Countries')

@extends('layouts.app')

@include('layouts.BU_Modal')
@include('layouts.Country_Modal')

@section('content')
    @if (Auth::user()->role = 'Admin' && (Auth::user()->role = 'Sale_Admin'))
        {{-- Error modal --}}
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
        {{-- Success Modal --}}
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
            <div class="row">
                <div class="col-md-12">
                    <div id="bu-table">
                        <div class="d-flex justify-content-between align-items-center mr-3 my-2">
                            <div class="d-flex align-items-center">
                                <h5 class="mr-3 my-2 headings" style="margin-right: 10px; min-width: 235px;">BU Table</h5>
                                <button class="btn hover-action" data-toggle="modal" data-target="#addBUModal"
                                    style="padding: 10px 12px;">
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
                                <input type="search" class="form-control mr-1" placeholder="Search Name or Email..."
                                    id="search-input" aria-label="Search">
                                <button class="btn hover-action mx-1" type="submit" data-toggle="tooltip" title="Search">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                            </div>
                        </div>
                        <div class="table-container" id="contacts">
                            <table class=" table table-hover mt-2" id="contacts-table">
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
                                            <td class="d-flex justify-content-center">
                                                <a class="btn hover-action" data-toggle="modal"
                                                    data-target="#editBUModal{{ $bu->id }}">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
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
                        {{-- BU navigation --}}
                        <div aria-label="Page navigation example " class="paginationContainer">
                            <ul class="pagination justify-content-center">
                                <!-- Previous Button -->
                                <li class="page-item {{ $bus->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link font-educ" href="{{ $bus->previousPageUrl() }}"
                                        aria-label="Previous">&#60;</a>
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
                                    <a class="page-link font-educ" href="{{ $bus->nextPageUrl() }}"
                                        aria-label="Next">&#62;</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div id="country-table" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mr-3 my-2">
                            <div class="d-flex align-items-center">
                                <h5 class="mr-3 my-2 headings" style="margin-right: 10px; min-width: 235px;">Country Table</h5>
                                <button class="btn hover-action" data-toggle="modal" data-target="#addCountryModal"
                                    style="padding: 10px 12px;">
                                    <i class="fa-solid fa-square-plus"></i>
                                </button>
                                <button class="btn hover-action mx-3 bu-buttons" id="show-bu-2">
                                    BU
                                </button>
                                <button class="btn hover-action bu-buttons active" id="show-country-2">
                                    Country
                                </button>
                            </div>
                            <div class="search-box d-flex align-items-center mr-3 mb-2">
                                <input type="search" class="form-control mr-1" placeholder="Search Name or Email..."
                                    id="search-input" aria-label="Search">
                                <button class="btn hover-action mx-1" type="submit" data-toggle="tooltip"
                                    title="Search">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                            </div>
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
                        {{-- country navigation --}}
                        <div aria-label="Page navigation example " class="paginationContainer">
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
                                    <a class="page-link font-educ" href="{{ $countries->nextPageUrl() }}"
                                        aria-label="Next">&#62;</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {

                function hideAllTables() {
                    $('#bu-table, #country-table').hide();
                }

                $('#show-bu, #show-bu-2').click(function() {
                    hideAllTables();
                    $('#bu-table').show();
                });

                $('#show-country, #show-country-2').click(function() {
                    hideAllTables();
                    $('#country-table').show();
                });
            });
        </script>
        {{-- show modal --}}
        <script>
            $(document).ready(function() {
                @if ($errors->any() || session('country-error') || session('bu-error'))
                    $('#errorModal').modal('show');
                @endif

                @if (Session::has('country-success') || Session::has('bu-success'))
                    $('#successModal').modal('show');
                @endif
            });
        </script>
        {{-- sorting name --}}
        <script src=" {{ asset('js/sort.js') }} "></script>
        @else
            <div class="alert alert-danger text-center mt-5">
                <strong>Access Denied!</strong> You do not have permission to view this page.
            </div>
        @endif
@endsection
