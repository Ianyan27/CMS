@extends('layouts.app')

@section('title', 'Contact Listing Page')

@section('content')
    @if (
        (Auth::check() && Auth::user()->role == 'Sales_Agent') ||
            Auth::user()->role == 'Admin' ||
            Auth::user()->role == 'BUH')
        <div class="container-max-height">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <h5 class="mr-3 my-2 headings">Contact Listing</h5>
                    <button class="btn hover-action mx-3" id="show-contacts">
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
                    <input type="search" class="form-control mr-1" placeholder="Search Name or Email..." id="search-input"
                        aria-label="Search">
                    <button class="btn hover-action mx-1" type="submit" data-toggle="tooltip" title="Search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </div>
            <div class="table-container" id="contacts">
                <table class=" table table-hover mt-2" id="contacts-table">
                    <thead class="text-left font-educ">
                        <tr class="text-left font-educ">
                            <th scope="col">No #</th>
                            <th scope="col" id="name-header">Name
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-name-first"
                                    onclick="sortTable('contacts-table','name', 'asc'); toggleSort('sortDown-name-first', 'sortUp-name-first')"></i>
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-name-first"
                                    onclick="sortTable('contacts-table','name', 'desc'); toggleSort('sortUp-name-first', 'sortDown-name-first')"
                                    style="display: none;"></i>
                            </th>
                            <th scope="col" id="email-header">Email
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-email-first"
                                    onclick="sortTable('contacts-table','email', 'asc'); toggleSort('sortDown-email-first', 'sortUp-email-first')"></i>
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-email-first"
                                    onclick="sortTable('contacts-table','email', 'desc'); toggleSort('sortUp-email-first', 'sortDown-email-first')"
                                    style="display: none;"></i>
                            </th>
                            <th scope="col">Contact
                            </th>
                            <th scope="col">Country
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-country-first"
                                    onclick="sortTable('contacts-table','country', 'asc'); toggleSort('sortDown-country-first', 'sortUp-country-first')"></i>
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-country-first"
                                    onclick="sortTable('contacts-table','country', 'desc'); toggleSort('sortUp-country-first', 'sortDown-country-first')"
                                    style="display: none;"></i>
                            </th>
                            @if (Auth::user()->role === 'Admin')
                                <!-- Display Sales Agent only for Admins -->
                                <th>
                                    Sales Agent
                                </th>
                            @endif
                            <th class=" position-relative" scope="col">
                                Last Modified
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
                                </div>
                            </th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-left bg-row fonts">
                        <?php $i = ($contacts->currentPage() - 1) * $contacts->perPage(); ?>
                        @forelse ($contacts as $contact)
                            <tr data-status="{{ $contact['status'] }}">
                                <td>{{ ++$i }}</td>
                                <td>{{ $contact['name'] }}</td>
                                <td>{{ $contact['email'] }}</td>
                                <td>{{ $contact['contact_number'] }}</td>
                                @inject('countryCodeMapper', 'App\Services\CountryCodeMapper')
                                <td>
                                    <img src="{{ asset('flags/' . strtolower($countryCodeMapper->getCountryCode($contact['country'])) . '.svg') }}"
                                        alt="{{ $contact['country'] }}" width="20" height="15">
                                    {{ $contact['country'] }}
                                </td>
                                @if (Auth::user()->role === 'Admin')
                                    <!-- Display Sales Agent only for Admins -->
                                    <td>{{ $contact->saleAgent->name ?? 'Not Assigned' }}</td>
                                @endif
                                <td>{{ $contact['updated_at'] }}</td>   
                                <td>
                                    <span class="status-indicator"
                                        style="background-color:
                                        @if ($contact['status'] === 'HubSpot Contact') #FFE8E2; color: #FF5C35;
                                        @elseif ($contact['status'] === 'discard')
                                            #FF7F86; color: #BD000C;
                                        @elseif ($contact['status'] === 'InProgress')
                                            #FFF3CD; color: #FF8300; padding: 5px 10px;
                                        @elseif ($contact['status'] === 'New')
                                            #CCE5FF; color: #318FFC;
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
                                <td>
                                    <a href="{{ Auth::user()->role == 'Admin'
                                        ? route('admin#view-contact', ['contact_pid' => $contact->contact_pid])
                                        : (Auth::user()->role == 'BUH'
                                            ? route('buh#view-contact', ['contact_pid' => $contact->contact_pid])
                                            : (Auth::user()->role == 'Sales_Agent'
                                                ? route('sale-agent#view', ['contact_pid' => $contact->contact_pid])
                                                : route('sale-agent#view', ['contact_pid' => $contact->contact_pid]))) }}"
                                        class="btn hover-action" data-toggle="tooltip" title="View">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No contacts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    
                </table>
            </div>
            <div class="table-container" id="archive">
                <table class="table table-hover mt-2" id="archive-table">
                    <thead class="text-left font-educ">
                        <tr class="text-left font-educ">
                            <th scope="col">No #</th>
                            <th scope="col">Name 
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-name-second"
                                    onclick="sortTable('archive-table','name', 'asc'); toggleSort('sortDown-name-second', 'sortUp-name-second')"></i>
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-name-second"
                                    onclick="sortTable('archive-table','name', 'desc'); toggleSort('sortUp-name-second', 'sortDown-name-second')"
                                    style="display: none;"></i>
                            </th>
                            <th scope="col">Email 
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-email-second"
                                    onclick="sortTable('archive-table','email', 'asc'); toggleSort('sortDown-email-second', 'sortUp-email-second')"></i>
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-email-second"
                                    onclick="sortTable('archive-table','email', 'desc'); toggleSort('sortUp-email-second', 'sortDown-email-second')"
                                    style="display: none;"></i>
                            </th>
                            <th scope="col">Contact</th>
                            <th scope="col">Country 
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-country-second"
                                    onclick="sortTable('archive-table','country', 'asc'); toggleSort('sortDown-country-second', 'sortUp-country-second')"></i>
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-country-second"
                                    onclick="sortTable('archive-table','country', 'desc'); toggleSort('sortUp-country-second', 'sortDown-country-second')"
                                    style="display: none;"></i>
                            </th>
                            <th scope="col">
                                Status
                                <span class="ml-2" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="Status of the contact: Active, Discarded, New, In Progress, Archived">
                                    <i class="fa-solid fa-info-circle text-muted"></i>
                                </span>
                            </th>
                            <th scope="col">Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-left bg-row">
                        <?php $i = ($contactArchive->currentPage() - 1) * $contactArchive->perPage(); ?>
                        @forelse ($contactArchive as $archive)
                            <tr>
                                <td> {{ ++$i }} </td>
                                <td> {{ $archive['name'] }} </td>
                                <td> {{ $archive['email'] }} </td>
                                <td> {{ $archive['contact_number'] }} </td>
                                <td>
                                    @inject('countryCodeMapper', 'App\Services\CountryCodeMapper')
                                    @php
                                        // Fetch the country code using the injected service
                                        $countryCode = $countryCodeMapper->getCountryCode($archive['country']);
                                    @endphp
                                    @if ($countryCode)
                                        <img src="{{ asset('flags/' . strtolower($countryCode) . '.svg') }}"
                                            alt="{{ $archive['country'] }}" width="20" height="15">
                                    @else
                                        <!-- Optional: Add a fallback image or text when the country code is not found -->
                                        <span>No flag available</span>
                                    @endif
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
                                    <a href=" {{ Auth::user()->role == 'Admin'
                                        ? route('admin#archive-view', $archive->contact_archive_pid)
                                        : route('archive#view', $archive->contact_archive_pid) }} "
                                        class="btn hover-action" data-toggle="tooltip" title="View">
                                        <i class="fa-solid fa-eye " style="font-educ-size: 1.5rem"></i>
                                    </a>
                                    {{-- <a href=" {{ route('archive#view', $archive->contact_archive_pid) }} "
                                        class="btn hover-action" data-toggle="tooltip" title="View">
                                        <i class="fa-solid fa-eye " style="font-educ-size: 1.5rem"></i>
                                    </a> --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center"> No Archive Contacts</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="table-container" id="discard">
                <table class="table table-hover mt-2" id="discard-table">
                    <thead class="font-educ text-left">
                        <tr class="font-educ text-left">
                            <th scope="col">No #</th>
                            <th scope="col">Name 
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-name-third"
                                    onclick="sortTable('discard-table','name', 'asc'); toggleSort('sortDown-name-third', 'sortUp-name-third')"></i>
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-name-third"
                                    onclick="sortTable('discard-table','name', 'desc'); toggleSort('sortUp-name-third', 'sortDown-name-third')"
                                    style="display: none;"></i>
                            </th>
                            <th scope="col">Email 
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-email-third"
                                    onclick="sortTable('discard-table','email', 'asc'); toggleSort('sortDown-email-third', 'sortUp-email-third')"></i>
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-email-third"
                                    onclick="sortTable('discard-table','email', 'desc'); toggleSort('sortUp-email-third', 'sortDown-email-third')"
                                    style="display: none;"></i>
                            </th>
                            <th scope="col">Contact</th>
                            <th scope="col">Country 
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-country-third"
                                    onclick="sortTable('discard-table','country', 'asc'); toggleSort('sortDown-country-third', 'sortUp-country-third')"></i>
                                <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-country-third"
                                    onclick="sortTable('discard-table','country', 'desc'); toggleSort('sortUp-country-third', 'sortDown-country-third')"
                                    style="display: none;"></i>
                            </th>
                            <th scope="col">
                                Status
                                <span class="ml-2" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="Status of the contact: Active, Discarded, New, In Progress, Archived">
                                    <i class="fa-solid fa-info-circle text-muted"></i>
                                </span>
                                </td>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-left bg-row">
                        <?php $i = ($contactDiscard->currentPage() - 1) * $contactDiscard->perPage(); ?>
                        @forelse ($contactDiscard as $discard)
                            <tr>
                                <td> {{ ++$i }} </td>
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
                                <td>
                                    <a href="{{ route('discard#view', ['contact_discard_pid' => $discard->contact_discard_pid]) }}"
                                        class="btn hover-action" data-toggle="tooltip" title="View">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No Discard Contacts</td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
            <div aria-label="Page navigation example " class="paginationContainer">
                <ul class="pagination justify-content-center">
                    <!-- Previous Button -->
                    <li class="page-item {{ $contacts->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link font-educ" href="{{ $contacts->previousPageUrl() }}"
                            aria-label="Previous">&#60;</a>
                    </li>
                    <!-- First Page Button -->
                    @if ($contacts->currentPage() > 3)
                        <li class="page-item">
                            <a class="page-link font-educ" href="{{ $contacts->url(1) }}">1</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link font-educ" href="{{ $contacts->url(2) }}">2</a>
                        </li>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                    <!-- Middle Page Buttons -->
                    @for ($i = max($contacts->currentPage() - 1, 1); $i <= min($contacts->currentPage() + 1, $contacts->lastPage()); $i++)
                        <li class="page-item {{ $i == $contacts->currentPage() ? 'active' : '' }}">
                            <a class="page-link font-educ {{ $i == $contacts->currentPage() ? 'active-bg' : '' }}"
                                href="{{ $contacts->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor
                    <!-- Last Page Button -->
                    @if ($contacts->currentPage() < $contacts->lastPage() - 2)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                        <li class="page-item">
                            <a class="page-link font-educ"
                                href="{{ $contacts->url($contacts->lastPage() - 1) }}">{{ $contacts->lastPage() - 1 }}</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link font-educ"
                                href="{{ $contacts->url($contacts->lastPage()) }}">{{ $contacts->lastPage() }}</a>
                        </li>
                    @endif
                    <!-- Next Button -->
                    <li class="page-item {{ !$contacts->hasMorePages() ? 'disabled' : '' }}">
                        <a class="page-link font-educ" href="{{ $contacts->nextPageUrl() }}" aria-label="Next">&#62;</a>
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
    @else
        <div class="alert alert-danger text-center mt-5">
            <strong>Access Denied!</strong> You do not have permission to view this page.
        </div>
    @endif
    <script>
        $(document).ready(function() {
            @if (session('success'))
                $('#successModal').modal('show');
            @endif
        });
    </script>
    <script src=" {{ asset('js/show_hide_contacts_table.js') }} "></script>
    <script src=" {{ asset('js/active_buttons.js') }} "></script>
    <script src=" {{ asset('js/search_input.js') }}" ></script>
    <script src=" {{ asset('js/sort.js') }} "></script>
@endsection
@section('scripts')
    <!-- Add Bootstrap Tooltip Initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection
