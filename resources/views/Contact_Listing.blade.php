@extends('layouts.app')

@section('title', 'Contact Listing Page')

@section('content')
    <div class="table-title d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <h2 class="mx-3 my-2 font-educ"><strong>Contact Listing</strong></h2>
            <button style="border-radius: 15px;" class="btn hover-action mx-3" id="show-contacts">
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
            <input type="search" class="form-control mr-1" placeholder="Search..." 
            id="search-input" aria-label="Search">
            <button class="btn hover-action mx-1" type="submit" data-toggle="tooltip" title="Search">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
    </div>
    <table class="table table-hover mt-2" id="contacts-table">
        <thead class="font-educ text-center">
            <tr>
                <th scope="col"><input type="checkbox" name="" id=""></th>
                <th class="h5" scope="col">No #</th>
                <th class="h5" scope="col" id="name-header">Name
                    <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" 
                        onclick="sortTable('name', 'asc')"></i>
                    <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" 
                        onclick="sortTable('name', 'desc')"></i>
                </th>
                <th class="h5" scope="col" id="email-header">Email
                    <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" 
                        onclick="sortTable('email', 'asc')"></i>
                    <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" 
                        onclick="sortTable('email', 'desc')"></i>
                </th>
                <th class="h5" scope="col">Contact

                </th>
                <th class="h5" scope="col">Country 
                    <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" 
                        onclick="sortTable('email', 'asc')"></i>
                    <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" 
                        onclick="sortTable('email', 'desc')"></i>
                </th>
                <th class="h5 position-relative" scope="col">
                    Status
                    <i style="cursor: pointer;" class="fa-solid fa-filter" 
                    id="filterIcon" onclick="toggleFilter()"></i>
    
                    <!-- Filter Container -->
                    <div id="filterContainer" class="filter-popup container" style="display: none;">
                        <div class="row">
                            <div class="filter-option mb-2 col-4">
                                <input type="checkbox" id="new" name="status" value="New">
                                <label for="new">New</label>
                            </div>
                            <div class="filter-option mb-2 col-4">
                                <input type="checkbox" id="inProgress" name="status" value="InProgress">
                                <label for="inProgress">In Progress</label>
                            </div>
                            <div class="filter-option mb-2 col-4">
                                <input type="checkbox" id="hubspot" name="status" value="HubSpot Contact">
                                <label for="hubspot">HubSpot</label>
                            </div>
                        </div>
                        <div class="row d-flex justify-content-center">
                            <button class="btn hover-action col-11" type="button" onclick="applyFilter()">Apply Filter</button>
                        </div>
                    </div>
                </th>
                <th class="h5" scope="col">Actions</th>
            </tr>
        </thead>
        <tbody class="text-center bg-row fonts">
            @forelse ($contacts as $contact)
            <tr data-status="{{ $contact['status'] }}">
                <td><input type="checkbox" name="" id=""></td>
                <td>{{ $contact['contact_pid'] }}</td>
                <td>{{ $contact['name'] }}</td>
                <td>{{ $contact['email'] }}</td>
                <td>{{ $contact['contact_number'] }}</td>
                <td>{{ $contact['country'] }}</td>
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
                        @elseif ($contact['status'] === 'InProgress')
                            In Progress
                        @elseif ($contact['status'] === 'New')
                            New
                        @endif
                    </span>
                </td>
                <td>
                    <a href=" {{ route('contact#view', $contact->contact_pid) }} " class="btn hover-action" data-toggle="tooltip" title="View">
                        <i class="fa-solid fa-eye " style="font-educ-size: 1.5rem"></i>
                    </a>
                    <a href="#" class="btn hover-action" data-toggle="tooltip" title="">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">No contacts found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <table class="table table-hover mt-2" id="archive-table">
        <thead class="font-educ text-center">
            <tr class="font-educ text-center">
                <th scope="col"><input type="checkbox" name="" id=""></th>
                <th class="h5" scope="col">No #</th>
                <th class="h5" scope="col">Name <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                <th class="h5" scope="col">Email <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                <th class="h5" scope="col">Contact</th>
                <th class="h5" scope="col">Country <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                <th class="h5" scope="col">
                    Status
                    <span class="ml-2" data-bs-toggle="tooltip" data-bs-placement="top"
                        title="Status of the contact: Active, Discarded, New, In Progress, Archived">
                        <i class="fa-solid fa-info-circle text-muted"></i>
                    </span>
                </th>
                <th class="h5" scope="col">Actions</th>
            </tr>
        </thead>
        <tbody class="text-center bg-row fonts">
            @foreach ($contactArchive as $archive)
            <tr>
                <td><input type="checkbox" name="" id=""></td>
                <td> {{$archive['contact_archive_pid']}} </td>
                <td> {{$archive['name']}} </td>
                <td> {{$archive['email']}} </td>
                <td> {{$archive['contact_number']}} </td>
                <td> {{$archive['country']}} </td>
                <td>
                    <span class="status-indicator"
                        style="background-color:
                        @if($archive['status'] === 'Archive')
                        #E2E3E5; color: #303030; 
                        @endif
                        ">
                        @if($archive['status'] === 'Archive')
                            Archive
                        @endif
                    </span>
                </td>
                <td>
                    <a href=" {{ route('contact#view', $contact->contact_pid) }} " class="btn hover-action" data-toggle="tooltip" title="View">
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
    <table class="table table-hover mt-2" id="discard-table">
        <thead class="font-educ text-center">
            <tr class="font-educ text-center">
                <th scope="col"><input type="checkbox" name="" id=""></th>
                <th class="h5" scope="col">No #</th>
                <th class="h5" scope="col">Name <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                <th class="h5" scope="col">Email <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                <th class="h5" scope="col">Contact</th>
                <th class="h5" scope="col">Country <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                <th class="h5" scope="col">
                    Status
                    <span class="ml-2" data-bs-toggle="tooltip" data-bs-placement="top"
                        title="Status of the contact: Active, Discarded, New, In Progress, Archived">
                        <i class="fa-solid fa-info-circle text-muted"></i>
                    </span>
                </th>
                <th class="h5" scope="col">Actions</th>
            </tr>
        </thead>
        <tbody class="text-center bg-row fonts">
            @foreach ($contactDiscard as $discard)
            <tr>
                <td><input type="checkbox" name="" id=""></td>
                <td> {{$discard['contact_discard_pid']}} </td>
                <td> {{$discard['name']}} </td>
                <td> {{$discard['email']}} </td>
                <td> {{$discard['contact_number']}} </td>
                <td> {{$discard['country']}} </td>
                <td>
                    <span class="status-indicator"
                        style="background-color:
                        @if($discard['status'] === 'Discard')
                            #FF7F86; color: #BD000C;
                        @endif
                    ">
                        @if($discard['status'] === 'Discard')
                            Discard
                        @endif
                    </span>
                </td>
                <td>
                    <a href=" {{ route('contact#view', $contact->contact_pid) }} " class="btn hover-action" data-toggle="tooltip" title="View">
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
    
    <footer aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
            <!-- Previous Button -->
            <li class="page-item {{ $contacts->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link font-educ-educ" href="{{ $contacts->previousPageUrl() }}" aria-label="Previous">&#60;</a>
            </li>
    
            <!-- First Page Button -->
            @if ($contacts->currentPage() > 2)
                <li class="page-item">
                    <a class="page-link font-educ" href="{{ $contacts->url(1) }}">1</a>
                </li>
            @endif
    
            <!-- Second Page Button -->
            @if ($contacts->currentPage() > 1)
                <li class="page-item">
                    <a class="page-link font-educ" href="{{ $contacts->url(2) }}">2</a>
                </li>
            @endif
    
            <!-- Current Page Button -->
            <li class="page-item active">
                <span class="page-link font-educ-educ">{{ $contacts->currentPage() }}</span>
            </li>
    
            <!-- Penultimate Page Button -->
            @if ($contacts->lastPage() > $contacts->currentPage() + 1)
                <li class="page-item">
                    <a class="page-link font-educ" href="{{ $contacts->url($contacts->lastPage() - 1) }}">{{ $contacts->lastPage() - 1 }}</a>
                </li>
            @endif
    
            <!-- Last Page Button -->
            @if ($contacts->lastPage() > $contacts->currentPage())
                <li class="page-item">
                    <a class="page-link font-educ" href="{{ $contacts->url($contacts->lastPage()) }}">{{ $contacts->lastPage() }}</a>
                </li>
            @endif
    
            <!-- Next Button -->
            <li class="page-item {{ !$contacts->hasMorePages() ? 'disabled' : '' }}">
                <a class="page-link font-educ-educ" href="{{ $contacts->nextPageUrl() }}" aria-label="Next">&#62;</a>
            </li>
        </ul>
    </footer> 
    <script>
        $(document).ready(function() {
            $('#archive-table').hide();
            $('#discard-table').hide();
            $('#show-contacts').click(function() {
                $('#contacts-table').show();
                $('#archive-table').hide();
                $('#discard-table').hide();
            });
        
            $('#show-archive').click(function() {
                $('#contacts-table').hide();
                $('#archive-table').show();
                $('#discard-table').hide();
            });
        
            $('#show-discard').click(function() {
                $('#contacts-table').hide();
                $('#archive-table').hide();
                $('#discard-table').show();
            });
        });
    </script>
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
