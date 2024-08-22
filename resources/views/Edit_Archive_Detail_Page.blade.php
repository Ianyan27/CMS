@section('title', 'Edit Contact Detail Page')

@extends('layouts.app')
@extends('layouts.Update_Activity_Modal')
@extends('layouts.Edit_Archive_Modal')
@extends('layouts.Add_Activity_Archive_Modal')
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
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
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
    {{-- css will edit to css file soon --}}
    <link rel="stylesheet" href="{{ URL::asset('css/contact_detail.css') }}">
    <div class="row border-educ rounded h-auto">
        <div class="col-md-5 border-right" id="contact-detail">
            <div class="table-title d-flex justify-content-between align-items-center my-3">
                <h2 class="mt-2 ml-3 headings">Contact Detail</h2>
                <a href="{{ route('archive#edit', $editArchive->contact_archive_pid) }}" class="btn hover-action mx-1"
                    data-toggle="modal" data-target="#editArchiveModal">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>
            </div>
            <div class="row row-margin-bottom row-border-bottom mx-1">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-educ" for="name">Name</label>
                        <input type="text" class="form-control fonts" id="name" value=" {{ $editArchive->name }} "
                            readonly>
                    </div>
                    <div class="form-group">
                        <label class="font-educ" for="contact-number">Contact Number</label>
                        <input type="text" class="form-control fonts" id="contact_number"
                            value= " {{ $editArchive->contact_number }} " readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-educ" for="email">Email</label>
                        <input type="email" class="form-control fonts" id="email" value=" {{ $editArchive->email }} "
                            readonly>
                    </div>
                    <div class="form-group">
                        <label class="font-educ" for="country">Country</label>
                        <input type="text" class="form-control fonts" id="country" value=" {{ $editArchive->country }} "
                            readonly>
                    </div>
                </div>
            </div>
            <div class="row row-margin-bottom row-border-bottom mx-1">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-educ" for="address">Address</label>
                        <input style="height: 125px;" type="text" class="form-control fonts" id="address"
                            value=" {{ $editArchive->address }} " readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-educ" for="date-of-allocation">Date of Allocation</label>
                        <input type="datetime" class="form-control fonts" id="date-of-allocation"
                            value="{{ $editArchive->date_of_allocation }}" readonly>
                    </div>
                    <div class="form-group">
                        <label class="font-educ" for="qualification">Qualification</label>
                        <input type="text" class="form-control fonts" id="qualification"
                            value=" {{ $editArchive->qualification }} " readonly>
                    </div>
                </div>
            </div>
            <div class="row mx-1">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-educ" for="skills">Skills</label>
                        <input type="text" class="form-control fonts" id="skills" value="{{ $editArchive->skills }}"
                            readonly>
                    </div>
                    <div class="form-group">
                        <label class="font-educ" for="source">Source</label>
                        <input type="text" class="form-control fonts" id="source" value="{{ $editArchive->source }}"
                            readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-educ" for="job-role">Job Role</label>
                        <input type="text" class="form-control fonts" id="job-role" value=" {{ $editArchive->job_role }} "
                            readonly>
                    </div>
                    <div class="form-group">
                        <label class="font-educ" for="status">Status</label>
                        <input type="text" class="form-control fonts" id="status" 
                            value="{{ trim(
                                $editArchive->status === 'HubSpot Contact' ? 'HubSpot' :
                                ($editArchive->status === 'Discard' ? 'Discard' :
                                ($editArchive->status === 'InProgress' ? 'In Progress' :
                                ($editArchive->status === 'New' ? 'New' :
                                ($editArchive->status === 'Archive' ? 'Archive' : ''))))
                            ) }}" readonly>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-7 px-3" id="activity-container">
            <div class="d-flex justify-content-between align-items-center my-3">
                <h2 class="mt-2 ml-2 headings">Activities Notifications</h2>
                <div class="d-flex align-items-center">
                    <input type="search" class="form-control fonts mr-1" placeholder="Search..." id="search-input"
                        aria-label="Search">
                    <button class="btn btn-secondary bg-educ mx-1" type="button" id="search-button">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
        </div>
        <!-- Filter Buttons -->
        <div class="btn-group mb-3" role="group" aria-label="Activity Filter Buttons">
            <button type="button" class="btn activity-button mx-2 active-activity-button"
                data-filter="all">Activities</button>
            <button type="button" class="btn activity-button mx-2" data-filter="meeting">Meetings</button>
            <button type="button" class="btn activity-button mx-2" data-filter="email">Emails</button>
            <button type="button" class="btn activity-button mx-2" data-filter="phone">Calls</button>
            <button type="button" class="btn activity-button mx-2" data-filter="whatsapp">Whatsapp</button>
        </div>
        {{-- Iterating all the activities from all contacts --}}
        <div class="activities">
            @foreach ($engagementArchive->groupBy(function ($date) {
            return \Carbon\Carbon::parse($date->date)->format('F Y'); // Group by month and year
        }) as $month => $activitiesInMonth)
                <div class="activity-list">
                    <div class="activity-date my-3 ml-3">
                        <span class="text-muted">{{ $month }}</span>
                    </div>
                    @foreach ($activitiesInMonth as $activity)
                        <div class="activity-item mb-3 mx-3 border-educ rounded p-3"
                            data-type="{{ strtolower($activity->activity_name) }}">
                            <h5 class="font-educ">{{ $activity->activity_name }} Activities</h5>
                            <small>{{ \Carbon\Carbon::parse($activity->date)->format('d-m-Y') }}</small>
                            <p class="text-muted">{{ $activity->details }}</p>
                            {{-- @if ($activity->attachments)
                                <p class="text-muted">Attachment: <a href="{{ Storage::url($activity->attachments) }}"
                                        target="_blank">View File</a></p>
                            @endif --}}
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
    </div>
    <!-- Activity Taken Section -->
    <div class="table-title d-flex justify-content-between align-items-center mt-5">
        <div class="d-flex align-items-center">
            <h2 class="ml-2 mb-1 headings">Activity Taken</h2>
        </div>
        <div class="d-flex align-items-center mr-2 mb-2">
            <!-- Button to trigger the modal -->
            <button class="btn hover-action add-activity-button" data-toggle="modal" data-target="#addDiscardActivityModal">
                <i style="font-size: 22px;" class="fa-solid fa-square-plus p-1"></i>
            </button>
        </div>
    </div>
    <!-- Table -->
    <table class="table table-hover mt-2">
        <thead class="font-educ text-left">
            <tr>
                <th scope="col">No</th>
                <th scope="col">Date</th>
                <th scope="col">Type</th>
                <th scope="col">Description</th>
                <th scope="col">Attachment</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody class="text-left bg-row">
            @foreach ($engagementArchive as $engagement)
                <tr>
                    <td> {{ $engagement->engagement_discard_pid }} </td>
                    <td> {{ $engagement->date }} </td>
                    <td> {{ $engagement->activity_name }} </td>
                    <td> {{ $engagement->details }} </td>
                    <td> {{ $engagement->attachments }} </td>
                    <td>
                        {{-- <a href=" {{route('contact#update_activity', $engagement->fk_engagements__contact_pid)}}" data-toggle="modal" data-target="#updateActivityModal"> --}}
                            <i  class=" btn hover-action fa-solid fa-pen-to-square" ></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ URL::asset('js/contact_detail.js') }}"></script>
    <script src="{{ URL::asset('js/status_color.js') }}"></script>
@endsection