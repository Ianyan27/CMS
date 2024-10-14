@section('title', 'View BUH Details')

@extends('layouts.app')
@section('content')
    @if ((Auth::check() && Auth::user()->role == 'Admin') || Auth::user()->role == 'Head')
        @if (session('success'))
            <!-- Trigger the modal with a button (hidden, will be triggered by JavaScript) -->
            <button id="successModalBtn" type="button" class="btn btn-primary" data-toggle="modal" data-target="#successModal"
                style="display: none;">
                Open Modal
            </button>
            <!-- Modal -->
            <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content rounded-0">
                        <div class="modal-header"
                            style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                        border:none;border-top-left-radius: 0; border-top-right-radius: 0;">
                            <h5 class="modal-title font-educ" id="successModalLabel">Success</h5>
                        </div>
                        <div class="modal-body font-educ text-center">
                            {{ session('success') }}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <link rel="stylesheet" href="{{ URL::asset('css/contact_detail.css') }}">
        <div class="row border-educ rounded mb-3 owner-container">
            <div class="col-md-5 border-right" id="contact-detail">
                <div class="table-title d-flex justify-content-between align-items-center my-3">
                    <h2 class="mt-2 ml-3 headings">BUH Detail</h2>
                    @if ((Auth::check() && Auth::user()->role == 'Head') || (Auth::check() && Auth::user()->role == 'Admin'))
                        <a class="btn hover-action" data-toggle="modal"
                            data-target="#editUserModal{{ $buhData->id }}">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        {{-- <a style="padding: 10px 12px;"
                            href="{{ Auth::user()->role == 'Admin'
                                ? route('admin#update-sale-agent', ['id' => $owner->id])
                                : route('buh#update-sale-agent', ['id' => $owner->id]) }}"
                            class="btn hover-action mx-1" data-toggle="modal" data-target="#editOwnerModal">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a> --}}
                        {{-- <a style="padding: 10px 12px;" href="{{ route('owner#update', $owner->owner_pid) }}"
                            class="btn hover-action mx-1" data-toggle="modal" data-target="#editOwnerModal">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a> --}}
                    @endif
                </div>
                <div class="row mx-1 mb-1">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-educ" for="name">Name</label>
                            <h5 class="fonts text-truncate" id="name">{{ $buhData->buh_name }}</h5>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-educ" for="email">Email</label>
                            <h5 class="fonts text-truncate" id="email">{{ $buhData->buh_email }}</h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-educ" for="business-unit">Business Unit</label>
                            <h5 class="fonts text-truncate" id="business-unit">{{ $buhData->bu_name }}</h5>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-educ" for="country">Country</label>
                            <h5 class="fonts text-truncate" id="country">{{ $buhData->buh_nationality }}</h5>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-7 px-3">
                <div class="d-flex justify-content-between align-items-center my-3">
                    <h2 class="mt-2 ml-2 headings">Sales Agent</h2>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="d-flex justify-content-center btn-group mb-3" role="group"
                            aria-label="Sales Engagement Buttons">
                            <button type="button" class="btn hover-action activity-button mx-2 rounded active"
                                onclick="showSection('hubspotContactsSection')">
                                Total Sale Agents
                            </button>
                            <button type="button" class="btn hover-action activity-button mx-2 rounded"
                                onclick="showSection('engagingContactsSection')">
                                Total Disabled Sale Agents
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row pt-2">
                    <div class="col-12">
                        <div id="hubspotContactsSection" class="text-center py-3 section-content">
                            <span class="d-block font-educ"
                                style="font-size: 2rem; font-weight: 500;">{{ $totalSaleAgents }}</span>
                            <h3 class="font-educ" style="font-size: 2.5rem; font-weight:450;">Total Sale Agents</h3>
                        </div>
                        <div id="engagingContactsSection" class="text-center py-3 section-content" style="display: none;">
                            <span class="d-block font-educ"
                                style="font-size: 2rem; font-weight: 500;">{{ $totalDisabledSaleAgents }}</span>
                            <h3 class="font-educ" style="font-size: 2.5rem; font-weight:450;">Total Disabled Sale Agents</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="owner-contacts-container">
            <link rel="stylesheet" href="{{ URL::asset('css/contact_listing.css') }}">
            <div class="table-title d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <h5 class="mr-3 my-2 headings">Lists of Sale Agents</h5>
                </div>
                <div class="search-box d-flex align-items-center mr-3 mb-2">
                    <input type="search" class="form-control mr-1" placeholder="Search..." id="search-input"
                        aria-label="Search">
                    <button class="btn hover-action mx-1" type="submit"
                        data-toggle="tooltip" title="Search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </div>
            <div class="table-container" id="contacts">
                <table class="table table-hover mt-2" id="contacts-table">
                    <thead class="text-left font-educ">
                        <tr>
                            <th scope="col">No #</th>
                            <th scope="col" id="name-header">Name
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-name"
                                    onclick="sortTable('name', 'asc'); toggleSort('sortDown-name', 'sortUp-name')"></i>
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-name"
                                    onclick="sortTable('name', 'desc'); toggleSort('sortUp-name', 'sortDown-name')"
                                    style="display: none;"></i>
                            </th>
                            <th scope="col" id="email-header">Email
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-email"
                                    onclick="sortTable('email', 'asc'); toggleSort('sortDown-email', 'sortUp-email')"></i>
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-email"
                                    onclick="sortTable('email', 'desc'); toggleSort('sortUp-email', 'sortDown-email')"
                                    style="display: none;"></i>
                            </th>
                            <th scope="col">Country
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-country"
                                    onclick="sortTable('country', 'asc'); toggleSort('sortDown-country', 'sortUp-country')"></i>
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-country"
                                    onclick="sortTable('country', 'desc'); toggleSort('sortUp-country', 'sortDown-country')"
                                    style="display: none;"></i>
                            </th>
                            <th class=" position-relative" scope="col">
                                Status
                                <i style="cursor: pointer;" class="fa-solid fa-filter" id="filterIcon"
                                    onclick="toggleFilter()"></i>
                                <!-- Filter Container -->
                                <div id="filterContainer" class="filter-popup container rounded-bottom"
                                    style="display: none;">
                                    <div class="row">
                                        <div class="filter-option">
                                            <input class="ml-3" type="checkbox" id="new" name="status"
                                                value="New" onclick="applyFilter()">
                                            <label for="new" style= "color: #318FFC;">New</label>
                                        </div>
                                        <div class="filter-option">
                                            <input class="ml-3" type="checkbox" id="inProgress" name="status"
                                                value="InProgress" onclick="applyFilter()">
                                            <label for="inProgress" style="color: #FF8300;">In Progress</label>
                                        </div>
                                        <div class="filter-option">
                                            <input class="ml-3" type="checkbox" id="hubspot" name="status"
                                                value="HubSpot Contact" onclick="applyFilter()">
                                            <label for="hubspot" style="color: #FF5C35;">HubSpot</label>
                                        </div>
                                    </div>
                            </th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-left bg-row fonts">
                        <?php $i = ($buhSaleAgents->currentPage() - 1) * $buhSaleAgents->perPage(); ?>
                        @forelse ($buhSaleAgents as $buhSaleAgent)
                            <tr data-status="{{ $buhSaleAgent['status'] }}">
                                <td>{{ ++$i }}</td>
                                <td>{{ $buhSaleAgent['name'] }}</td>
                                <td>{{ $buhSaleAgent['email'] }}</td>
                                @inject('countryCodeMapper', 'App\Services\CountryCodeMapper')
                                <td>
                                    @php
                                        // Fetch the country code using the injected service
                                        $countryCode = $countryCodeMapper->getCountryCode($buhSaleAgent['nationality']);
                                    @endphp
                                    @if ($countryCode)
                                        <img src="{{ asset('flags/' . strtolower($countryCode) . '.svg') }}"
                                            alt="{{ $buhSaleAgent['nationality'] }}" width="20" height="15">
                                    @else
                                        <!-- Optional: Add a fallback image or text when the country code is not found -->
                                        <span>No flag available</span>
                                    @endif
                                    {{ $buhSaleAgent['nationality'] }}
                                </td>
                                <td>
                                    <span class="status-indicator
                                    @if ($buhSaleAgent->status === 'active') status-active
                                    @elseif($buhSaleAgent->status === 'inactive')inactive-status 
                                    @endif" 
                                    style="background-color: 
                                        @if($buhSaleAgent->status == 'active') #DFF6DF; /* Light green */
                                        @elseif($buhSaleAgent->status == 'inactive') #FDE2E2; /* Light red */
                                        @endif;
                                    color: 
                                        @if($buhSaleAgent->status == 'active') #2F7F2F; /* Dark green */
                                        @elseif($buhSaleAgent->status == 'inactive') #7F2F2F; /* Dark red */
                                        @endif;">
                                        @if ($buhSaleAgent['status'] === 'inactive')
                                            Inactive
                                        @elseif ($buhSaleAgent['status'] === 'active')
                                            Active
                                        @endif
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ Auth::user()->role == 'Admin' ? route('admin#view-sale-agent', $buhSaleAgent->id) : route('buh#view-sale-agent', $buhSaleAgent->id) }}"
                                        class="btn hover-action">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No ownerContacts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div aria-label="Page navigation example " class="paginationContainer">
                <ul class="pagination justify-content-center">
                    <!-- Previous Button -->
                    <li class="page-item {{ $buhSaleAgents->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link font-educ" href="{{ $buhSaleAgents->previousPageUrl() }}"
                            aria-label="Previous">&#60;</a>
                    </li>
                    <!-- First Page Button -->
                    @if ($buhSaleAgents->currentPage() > 3)
                        <li class="page-item">
                            <a class="page-link font-educ" href="{{ $buhSaleAgents->url(1) }}">1</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link font-educ" href="{{ $buhSaleAgents->url(2) }}">2</a>
                        </li>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                    <!-- Middle Page Buttons -->
                    @for ($i = max($buhSaleAgents->currentPage() - 1, 1); $i <= min($buhSaleAgents->currentPage() + 1, $buhSaleAgents->lastPage()); $i++)
                        <li class="page-item {{ $i == $buhSaleAgents->currentPage() ? 'active' : '' }}">
                            <a class="page-link font-educ {{ $i == $buhSaleAgents->currentPage() ? 'active-bg' : '' }}"
                                href="{{ $buhSaleAgents->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor
                    <!-- Last Page Button -->
                    @if ($buhSaleAgents->currentPage() < $buhSaleAgents->lastPage() - 2)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                        <li class="page-item">
                            <a class="page-link font-educ"
                                href="{{ $buhSaleAgents->url($buhSaleAgents->lastPage() - 1) }}">{{ $buhSaleAgents->lastPage() - 1 }}</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link font-educ"
                                href="{{ $buhSaleAgents->url($buhSaleAgents->lastPage()) }}">{{ $buhSaleAgents->lastPage() }}</a>
                        </li>
                    @endif
                    <!-- Next Button -->
                    <li class="page-item {{ !$buhSaleAgents->hasMorePages() ? 'disabled' : '' }}">
                        <a class="page-link font-educ" href="{{ $buhSaleAgents->nextPageUrl() }}"
                            aria-label="Next">&#62;</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="modal fade" id="editUserModal{{ $buhData->id }}" tabindex="-1"
            aria-labelledby="editUserModalLabel{{ $buhData->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content rounded-0">
                    <div class="modal-header d-flex justify-content-between align-items-center"
                        style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                    border:none;border-top-left-radius: 0; border-top-right-radius: 0;">
                        <h5 class="modal-title font-educ" id="editUserModalLabel{{ $buhData->id }}">
                            <strong>Edit BUH</strong>
                        </h5>
                    </div>
                    <form action="{{ route('head#update-buh', $buhData->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name{{ $buhData->id }}" class="form-label font-educ">BUH Name</label>
                                <input type="text" class="form-control" name="name" id="name{{ $buhData->id }}"
                                    value="{{ $buhData->buh_name }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="email{{ $buhData->id }}" class="form-label font-educ">Email</label>
                                <input type="email" class="form-control" name="email" id="email{{ $buhData->id }}"
                                    value="{{ $buhData->buh_email }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="nationality{{ $buhData->id }}" class="form-label font-educ">Nationality</label>
                                <input type="text" class="form-control" name="nationality" id="nationality{{ $buhData->id }}"
                                    value="{{ $buhData->buh_nationality }}" required>
                            </div>
                            <div class="form-group">
                                <label for="bu" class="form-label font-educ">Business Unit (BU)</label>
                                <select id="buDropdowninedit{{ $buhData->id }}" class="platforms search-bar form-control"
                                    name="business_unit" onchange="updateCountryDropdowninEdit({{ $buhData->id }});">
                                    <option value="">Select BU</option>
                                    @foreach ($businessUnit as $bu)
                                        <option value="{{ $bu->name }}" {{ $buhData->bu_name === $bu->name ? 'selected' : '' }}>
                                            {{ $bu->name }}</option>
                                    @endforeach
                                </select>
                                @error('bu_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="country" class="form-label font-educ">Country</label>
                                <select id="countryDropdown{{ $buhData->id }}" class="platforms search-bar form-control" name="country"
                                    onchange="updateSelectedCountryAndBuh({{ $buhData->id }});">
                                    <option value="">Select Country</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->name }}" {{ $buhData->country_name === $country->name ? 'selected' : '' }}>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                @error('country_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn hover-action">Update User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-danger text-center mt-5">
            <strong>Access Denied!</strong> You do not have permission to view this page.
        </div>
    @endif
    <script src="{{ asset('js/show_hide_contacts_table.js') }}"></script>
    <script src="{{ asset('js/active_activity_buttons.js') }}"></script>
    <script src="{{ asset('js/sort.js') }}"></script>
    <script src="{{ asset('js/active_buttons.js') }}"></script>
    <script>
        $(document).ready(function() {
            @if (session('success'))
                $('#successModal').modal('show');
            @endif
        });
    </script>
    <script>
        function showSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('.section-content').forEach(function(section) {
                section.style.display = 'none';
            });
            // Show the selected section
            document.getElementById(sectionId).style.display = 'block';
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection
