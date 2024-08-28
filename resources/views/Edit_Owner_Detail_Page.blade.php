@section('title', "View Owner Details")

@extends('layouts.app')
@extends('layouts.Edit_Owner_Modal')
@section('content')
@if (session('success'))
    <!-- Trigger the modal with a button (hidden, will be triggered by JavaScript) -->
    <button id="successModalBtn" type="button" class="btn btn-primary" data-toggle="modal" data-target="#successModal" style="display: none;">
        Open Modal
    </button>
    <!-- Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                border:none;">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                </div>
                <div class="modal-body">
                    {{ session('success') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Script to trigger the modal -->
    <script type="text/javascript">
        window.onload = function() {
            document.getElementById('successModalBtn').click();
        };
    </script>
@endif
<link rel="stylesheet" href="{{ URL::asset('css/contact_detail.css') }}">
    <div class="row border-educ rounded mb-3 owner-container">
        <div class="col-md-5 border-right" id="contact-detail">
            <div class="table-title d-flex justify-content-between align-items-center my-3">
                <h2 class="mt-2 ml-3 headings">Sale Agent Detail</h2>
                <a href="{{ route('owner#update', $editOwner->owner_pid) }}" class="btn hover-action mx-1"
                    data-toggle="modal" data-target="#editOwnerModal">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>
            </div>
            <div class="row mx-1 mb-1 d-flex align-items-center">
                <div class="col-md-6 d-flex justify-content-center">
                    <div class="form-group">
                        <img src="{{ asset('images/user-1.jpg') }}" alt="User Image" id="user-image">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="font-educ" for="contact-number">Hubspot Id</label>
                        <input type="text" class="form-control fonts" id="contact_number"
                               value="{{ $editOwner->owner_hubspot_id }}" readonly>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-educ" for="country">Country</label>
                        <input type="text" class="form-control fonts" id="country" value="{{ $editOwner->country }}" readonly>
                    </div>
                </div>
            </div>            
            <div class="row mx-1 mb-1">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="font-educ" for="skills">Name</label>
                        <input type="text" class="form-control fonts" id="skills" value="{{ $editOwner->owner_name }}"
                            readonly>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-educ" for="email">Email</label>
                        <input type="email" class="form-control fonts" id="email" value=" {{ $editOwner->owner_email_id }} "
                            readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-educ" for="job-role">Business Unit</label>
                        <input type="text" class="form-control fonts" id="job-role" value=" {{ $editOwner->owner_business_unit }} "
                            readonly>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-7 px-3">
            <div class="d-flex justify-content-between align-items-center my-3">
                <h2 class="mt-2 ml-2 headings">Sales Engagement</h2>
                <!-- <a class="btn hover-action font" href=" {{ route('owner#view') }} ">
                    <i class="fa-solid fa-left-long"></i>
                </a> -->
            </div>
            <div class="row">
                <div class="col">
                    <div class="d-flex justify-content-center btn-group mb-3" role="group" aria-label="Sales Engagement Buttons">
                        <button type="button" class="btn activity-button mx-2" data onclick="showSection('totalContactsSection')">
                            Total Contacts Allocated
                        </button>
                        <button type="button" class="btn activity-button mx-2" onclick="showSection('hubspotContactsSection')">
                            Total Sync HubSpot Contact
                        </button>
                        <button type="button" class="btn activity-button mx-2" onclick="showSection('engagingContactsSection')">
                            Current Engaging Contact
                        </button>
                    </div>
                </div>
            </div>
            <div class="row pt-2">
                <div class="col-12">
                    <!-- Section for Total Contacts Allocated -->
                    <div id="totalContactsSection" class="text-center py-3 section-content">
                        <span class="d-block font-educ" style="font-size: 2rem; font-weight: 500;">{{ $totalContacts }}</span>
                        <h3 class="font-educ" style="font-size: 2.5rem; font-weight:450;">Total Contacts Allocated</h3>
                    </div>
                    <!-- Section for Total Sync HubSpot Contact -->
                    <div id="hubspotContactsSection" class="text-center py-3 section-content" style="display: none;">
                        <span class="d-block font-educ" style="font-size: 2rem; font-weight: 500;">{{ $hubspotContactsCount }}</span>
                        <h3 class="font-educ" style="font-size: 2.5rem; font-weight:450;">Total Sync HubSpot Contact</h3>
                    </div>
                    <!-- Section for Current Engaging Contact -->
                    <div id="engagingContactsSection" class="text-center py-3 section-content" style="display: none;">
                        <span class="d-block font-educ" style="font-size: 2rem; font-weight: 500;">{{ $hubspotCurrentEngagingContact }}</span>
                        <h3 class="font-educ" style="font-size: 2.5rem; font-weight:450;">Current Engaging Contact</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="owner-contacts-container">
        <link rel="stylesheet" href="{{ URL::asset('css/contact_listing.css') }}">
        <div class="table-title d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <h5 class="mr-3 my-2 headings">Contact Listing</h5>
                <button class="btn hover-action mx-3" id="show-ownerContacts">
                    Interested Contacts
                </button>
                <button class="archive-table btn mx-3" id="show-archive">
                    Archive Contacts
                </button>
                <button class="discard-table btn mx-3" id="show-discard">
                    Discard Contacts
                </button>
            </div>
            <div class="search-box d-flex align-items-center mr-3 mb-2">
                <input type="search" class="form-control mr-1" placeholder="Search..." id="search-input" aria-label="Search">
                <button class="btn hover-action mx-1" type="submit" data-toggle="tooltip" title="Search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </div>
        <div class="">
            <table class=" table table-hover mt-2" id="ownerContacts-table">
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
                        <th scope="col">Contact
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
                            <i style="cursor: pointer;" class="fa-solid fa-filter" id="filterIcon" onclick="toggleFilter()"></i>
                            <!-- Filter Container -->
                            <div id="filterContainer" class="filter-popup container rounded-bottom" style="display: none;">
                                <div class="row">
                                    <div class="filter-option">
                                        <input class="ml-3" type="checkbox" id="new" name="status" value="New"
                                            onclick="applyFilter()">
                                        <label for="new" style= "color: #318FFC;">New</label>
                                    </div>
                                    <div class="filter-option">
                                        <input class="ml-3" type="checkbox" id="inProgress" name="status" value="InProgress"
                                            onclick="applyFilter()">
                                        <label for="inProgress" style="color: #FF8300;">In Progress</label>
                                    </div>
                                    <div class="filter-option">
                                        <input class="ml-3" type="checkbox" id="hubspot" name="status" value="HubSpot Contact"
                                            onclick="applyFilter()">
                                        <label for="hubspot" style="color: #FF5C35;">HubSpot</label>
                                    </div>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-left bg-row fonts">
                        @forelse ($ownerContacts as $contact)
                            <tr data-status="{{ $contact['status'] }}">
                                <td>{{ $contact['contact_pid'] }}</td>
                                <td>{{ $contact['name'] }}</td>
                                <td>{{ $contact['email'] }}</td>
                                <td>{{ $contact['contact_number'] }}</td>
                                @inject('countryCodeMapper', 'App\Services\CountryCodeMapper')
                                <td>
                                    <img src="{{ asset('flags/' . strtolower($countryCodeMapper->getCountryCode($contact['country'])) . '.svg') }}"
                                        alt="{{ $contact['country'] }}" width="20" height="15">
                                    {{ $contact['country'] }}
                                </td>
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No ownerContacts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="">
                <table class="table table-hover mt-2" id="archive-table">
                    <thead class="text-left font-educ">
                        <tr class="text-left">
                            <th scope="col">No #</th>
                            <th scope="col">Name <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                            <th scope="col">Email <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                            <th scope="col">Contact</th>
                            <th scope="col">Country <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                            <th scope="col">
                                Status
                                <span class="ml-2" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="Status of the contact: Active, Discarded, New, In Progress, Archived">
                                    <i class="fa-solid fa-info-circle text-muted"></i>
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-left bg-row">
                        @foreach ($ownerArchive as $archive)
                            <tr>
                                <td> {{ $archive['contact_archive_pid'] }} </td>
                                <td> {{ $archive['name'] }} </td>
                                <td> {{ $archive['email'] }} </td>
                                <td> {{ $archive['contact_number'] }} </td>
                                <td>
                                    <img src="{{ asset('flags/' . strtolower($countryCodeMapper->getCountryCode($archive['country'])) . '.svg') }}"
                                        alt="{{ $archive['country'] }}" width="20" height="15">
                                    {{ $archive['country'] }}
                                </td>
                                <td>
                                    <span class="status-indicator"
                                        style="background-color:
                                @if ($archive['status'] === 'Archive') #E2E3E5; color: #303030; @endif
                                ">
                                        @if ($archive['status'] === 'Archive')
                                            Archive
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    {{-- <a href=" {{ route('archive#view', $archive->contact_archive_pid) }} " class="btn hover-action" --}}
                                        data-toggle="tooltip" title="View">
                                        <i class="fa-solid fa-eye " style="font-educ-size: 1.5rem"></i>
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
            <div class="">
                <table class="table table-hover mt-2" id="discard-table">
                    <thead class="font-educ text-left">
                        <tr class="font-educ text-left">
                            <th scope="col">No #</th>
                            <th scope="col">Name <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                            <th scope="col">Email <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                            <th scope="col">Contact</th>
                            <th scope="col">Country <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                            <th scope="col">
                                Status
                                <span class="ml-2" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="Status of the contact: Active, Discarded, New, In Progress, Archived">
                                    <i class="fa-solid fa-info-circle text-muted"></i>
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-left bg-row">
                        @foreach ($ownerDiscard as $discard)
                            <tr>
                                <td> {{ $discard['contact_discard_pid'] }} </td>
                                <td> {{ $discard['name'] }} </td>
                                <td> {{ $discard['email'] }} </td>
                                <td> {{ $discard['contact_number'] }} </td>
                                <td>
                                    <img src="{{ asset('flags/' . strtolower($countryCodeMapper->getCountryCode($discard['country'])) . '.svg') }}"
                                        alt="{{ $discard['country'] }}" width="20" height="15">
                                    {{ $discard['country'] }}
                                </td>
                                <td>
                                    <span class="status-indicator"
                                        style="background-color:
                                    @if ($discard['status'] === 'Discard') #FF7F86; color: #BD000C; @endif
                                    ">
                                    @if ($discard['status'] === 'Discard')
                                        Discard
                                    @endif
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- <div aria-label="Page navigation example " class="paginationContainer">
                <ul class="pagination justify-content-center">
                    <!-- Previous Button -->
                    <li class="page-item {{ $ownerContacts->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link font-educ" href="{{ $ownerContacts->previousPageUrl() }}"
                            aria-label="Previous">&#60;</a>
                    </li>
                    <!-- First Page Button -->
                    @if ($ownerContacts->currentPage() > 3)
                        <li class="page-item">
                            <a class="page-link font-educ" href="{{ $ownerContacts->url(1) }}">1</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link font-educ" href="{{ $ownerContacts->url(2) }}">2</a>
                        </li>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                    <!-- Middle Page Buttons -->
                    @for ($i = max($ownerContacts->currentPage() - 1, 1); $i <= min($ownerContacts->currentPage() + 1, $ownerContacts->lastPage()); $i++)
                        <li class="page-item {{ $i == $ownerContacts->currentPage() ? 'active' : '' }}">
                            <a class="page-link font-educ {{ $i == $ownerContacts->currentPage() ? 'active-bg' : '' }}"
                                href="{{ $ownerContacts->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor
                    <!-- Last Page Button -->
                    @if ($ownerContacts->currentPage() < $ownerContacts->lastPage() - 2)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                        <li class="page-item">
                            <a class="page-link font-educ"
                                href="{{ $ownerContacts->url($ownerContacts->lastPage() - 1) }}">{{ $ownerContacts->lastPage() - 1 }}</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link font-educ"
                                href="{{ $ownerContacts->url($ownerContacts->lastPage()) }}">{{ $ownerContacts->lastPage() }}</a>
                        </li>
                    @endif
                    <!-- Next Button -->
                    <li class="page-item {{ !$ownerContacts->hasMorePages() ? 'disabled' : '' }}">
                        <a class="page-link font-educ" href="{{ $ownerContacts->nextPageUrl() }}" aria-label="Next">&#62;</a>
                    </li>
                </ul>
            </div>
        </div> --}}
        <script>
            $(document).ready(function() {
                $('#archive-table').hide();
                $('#discard-table').hide();
                $('#show-ownerContacts').click(function() {
                    $('#ownerContacts-table').show();
                    $('#archive-table').hide();
                    $('#discard-table').hide();
                });
                $('#show-archive').click(function() {
                    $('#ownerContacts-table').hide();
                    $('#archive-table').show();
                    $('#discard-table').hide();
                });
    
                $('#show-discard').click(function() {
                    $('#ownerContacts-table').hide();
                    $('#archive-table').hide();
                    $('#discard-table').show();
                });
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
<script src="{{ URL::asset('js/contact_detail.js') }}"></script>
<script src="{{ URL::asset('js/status_color.js') }}"></script>
@endsection