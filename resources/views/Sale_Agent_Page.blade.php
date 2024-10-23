@section('title', 'Sale Agent Dashboard')

@extends('layouts.app')
@extends('layouts.Add_Sales-Agent_Modal')

@section('content')
    @if (Session::has('success'))
        <script type="text/javascript">
            $(document).ready(function() {
                $('#successModal').modal('show');
            });
        </script>
    @endif
    @if ((Auth::check() && Auth::user()->role == 'BUH') || (Auth::check() && Auth::user()->role == 'Admin' ) || (Auth::check() && Auth::user()->role == 'Head' ))
        @if ($errors->any() || session('error'))
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
                            {{ session('error') }}
                        </div>
                        <div class="modal-footer" style="border:none;">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                style="background: #91264c; color:white;">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
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
        <div class="container-max-height">
            <div class="table-title d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center">
                    <h2 style="margin: 0 0.5rem 0 0.25rem;" class="font-educ headings">Sales Agents</h2>
                    <button class="btn hover-action" data-toggle="modal" data-target="#addSalesAgentModal"
                        style="padding: 10px 12px;">
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
                            <th scope="col">No#</th>
                            <th scope="col" id="name-header">Name
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-name"
                                    onclick="sortTable('name', 'asc'); toggleSort('sortDown-name', 'sortUp-name')"></i>
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-name"
                                    onclick="sortTable('name', 'desc'); toggleSort('sortUp-name', 'sortDown-name')"
                                    style="display: none;"></i>
                            </th>
                            <th scope="col">Hubspot ID</th>
                            <th scope="col" id="country-header">Country
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-country"
                                    onclick="sortTable('country', 'asc'); toggleSort('sortDown-country', 'sortUp-country')"></i>
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-country"
                                    onclick="sortTable('country', 'desc'); toggleSort('sortUp-country', 'sortDown-country')"
                                    style="display: none;"></i>
                            </th>
                            <th scope="col" id="bu-header">BU
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-bu"
                                    onclick="sortTable('bu', 'asc'); toggleSort('sortDown-bu', 'sortUp-bu')"></i>
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-bu"
                                    onclick="sortTable('bu', 'desc'); toggleSort('sortUp-bu', 'sortDown-bu')"
                                    style="display: none;"></i>
                            </th>
                            <th scope="col" class="text-center" data-toggle="tooltip" data-placement="top"
                                title="Total contacts in Interested, Archive, and Discard tables">Total Assign Contacts
                            </th>
                            <th scope="col" class="text-center" data-toggle="tooltip" data-placement="top"
                                title="Total contacts synced in HubSpot">Total Hubspot Sync</th>
                            <th scope="col" class="text-center" data-toggle="tooltip" data-placement="top"
                                title="Total engaging contacts">Total In Progress</th>
                            <th class="position-relative" scope="col">
                                Status
                                <i style="cursor: pointer;" class="fa-solid fa-filter" id="filterIcon"
                                    onclick="toggleFilterStatus()"></i>
                                <!-- Filter Container -->
                                <div id="filterStatusContainer" class="filter-popup container rounded-bottom"
                                    style="display: none;">
                                    <div class="row">
                                        <div class="filter-option">
                                            <input class="ml-3" type="checkbox" id="active" name="status"
                                                value="active" onclick="applyStatusFilter()">
                                            <label for="active" style= "color: #006400;">Active</label>
                                        </div>
                                        <div class="filter-option">
                                            <input class="ml-3" type="checkbox" id="inactive" name="status"
                                                value="inactive" onclick="applyStatusFilter()">
                                            <label for="inactive" style="color: #8b0000;">Inactive</label>
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <th scope="col" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-left fonts">
                        <?php $i = ($owner->currentPage() - 1) * $owner->perPage() + 1; ?>
                        @foreach ($owner as $owners)
                            <tr data-status="{{ $owners->status }}">
                                <td> {{ $i++ }} </td>
                                <td>{{ $owners->name }}</td>
                                <td>{{ $owners->hubspot_id }}</td>
                                <td>
                                    @inject('countryCodeMapper', 'App\Services\CountryCodeMapper')
                                    @php
                                        // Fetch the country code using the injected service
                                        $countryCode = $countryCodeMapper->getCountryCode($owners['nationality']);
                                    @endphp
                                    @if ($countryCode)
                                        <img src="{{ asset('flags/' . strtolower($countryCode) . '.svg') }}"
                                            alt="{{ $owners['country'] }}" width="20" height="15">
                                    @else
                                        <!-- Optional: Add a fallback image or text when the country code is not found -->
                                        <span>No flag available</span>
                                    @endif
                                    {{ $owners['nationality'] }}
                                </td>
                                <td>
                                    {{ $owners['business_unit'] }}
                                </td>
                                @inject('contactModel', 'App\Models\Contact')
                                @inject('contactArchiveModel', 'App\Models\ContactArchive')
                                @inject('contactDiscardModel', 'App\Models\ContactDiscard')
                                <td class="text-center">
                                    {{ $contactModel->where('fk_contacts__sale_agent_id', $owners->id)->count() +
                                        $contactArchiveModel->where('fk_contacts__sale_agent_id', $owners->id)->count() +
                                        $contactDiscardModel->where('fk_contacts__sale_agent_id', $owners->id)->count() }}
                                </td>
                                <td class="text-center">{{ $owners->total_hubspot_sync }}</td>
                                <td class="text-center">{{ $owners->total_in_progress }}</td>
                                <td>
                                    <span class="status-indicator"
                                        style="background-color:  
                                            @if ($owners->status === 'active') #90ee90; color: #006400;
                                            @elseif($owners->status === 'inactive')#ff7f7f; color: #8b0000; @endif">
                                        @if ($owners->status === 'active')
                                            Active
                                        @elseif ($owners->status === 'inactive')
                                            Inactive
                                        @endif
                                    </span>
                                </td>
                                <td class="d-flex justify-content-center align-items-center">
                                    {{-- <a href=" {{ route('owner#transfer-contact', $owners->owner_pid) }} "
                                        class="btn hover-action" style="padding: 10px 12px;">
                                        <i class="fa-solid fa-right-left"></i>
                                    </a> --}}
                                    <a href="{{ Auth::user()->role == 'Admin' ? route('admin#transfer-contact', ['id' => $owners->id]) : route('buh#transfer-contact', $owners->id) }}"
                                        class="btn hover-action @if (Auth::user()->role == 'Admin') d-none @endif" style="padding: 10px 12px;">
                                        <i class="fa-solid fa-right-left"></i>
                                    </a>
                                    <a href="{{ Auth::user()->role == 'Admin' ? route('admin#view-sale-agent', $owners->id) : route('buh#view-sale-agent', $owners->id) }}"
                                        class="btn hover-action mx-2" style="padding: 10px 12px;">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    {{-- <a href="{{ route('owner#view-owner', $owners->owner_pid) }}"
                                        class="btn hover-action mx-2" style="padding: 10px 12px;">
                                        <i class="fa-solid fa-eye"></i>
                                    </a> --}}
                                    {{-- <a class="btn hover-action" style="padding: 10px 12px;" data-toggle="modal"
                                        data-target="#deleteOwnerModal{{ $owners->owner_pid }}">
                                        <i class="fa-solid fa-trash"></i>
                                    </a> --}}
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
            <div class="modal fade" id="deleteOwnerModal{{ $owners->id }}" tabindex="-1"
                aria-labelledby="deleteOwnerModalLabel{{ $owners->id }}" aria-hidden="true">
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
                            <form action="{{ route('buh#delete-sale-agent', $owners->id) }}" method="post">
                                {{-- <form action="{{ route('buh#delete-sale-agent', $owners->id) }}" method="post"> --}}
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
    <script src=" {{ asset('js/filter_status.js') }} "></script>
    <script src=" {{ asset('js/agent_form_handler.js') }} "></script>
    <script src=" {{ asset('js/search_name.js') }} "></script>
    <script src=" {{ asset('js/sort.js') }} "></script>
@endsection
