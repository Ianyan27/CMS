@section('title', 'Edit Contact Detail Page')

@extends('layouts.app')

@section('content')
    {{-- css will edit to css file soon --}}

    <link rel="stylesheet" href="{{ URL::asset('css/contact_detail.css') }}">

    <div class="row border-educ rounded h-auto">
        <div class="col-md-5 border-right" id="contact-detail">
            <div class="table-title d-flex justify-content-between align-items-center my-3">
                <h2 class="mt-2 font-educ"><strong>Contact Detail</strong></h2>
                <a href="{{ route('contact#edit', $editContact->contact_pid) }}" class="btn hover-action mx-1"
                    data-toggle="modal" data-target="#editContactModal">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>

            </div>
            <div class="row row-margin-bottom row-border-bottom mx-1">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" value=" {{ $editContact->name }} "
                            readonly>
                    </div>
                    <div class="form-group">
                        <label for="contact-number">Contact Number</label>
                        <input type="text" class="form-control" id="contact_number"
                            value= " {{ $editContact->contact_number }} " readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" value=" {{ $editContact->email }} "
                            readonly>
                    </div>
                    <div class="form-group">
                        <label for="country">Country</label>
                        <input type="text" class="form-control" id="country" value=" {{ $editContact->country }} "
                            readonly>
                    </div>
                </div>
            </div>
            <div class="row row-margin-bottom row-border-bottom mx-1">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input style="height: 125px;" type="text" class="form-control" id="address"
                            value=" {{ $editContact->address }} " readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="date-of-allocation">Date of Allocation</label>
                        <input type="datetime" class="form-control" id="date-of-allocation"
                            value="{{ $editContact->date_of_allocation }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="qualification">Qualification</label>
                        <input type="text" class="form-control" id="qualification"
                            value=" {{ $editContact->qualification }} " readonly>
                    </div>
                </div>
            </div>
            <div class="row mx-1">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="skills">Skills</label>
                        <input type="text" class="form-control" id="skills" value="{{ $editContact->skills }}"
                            readonly>
                    </div>
                    <div class="form-group">
                        <label for="source">Source</label>
                        <input type="text" class="form-control" id="source" value="{{ $editContact->source }}"
                            readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="job-role">Job Role</label>
                        <input type="text" class="form-control" id="job-role" placeholder="Technology Associate"
                            readonly>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <input type="text" class="form-control" id="status" placeholder="New" readonly>
                    </div>
                </div>
            </div>


        </div>
        <div class="col-md-7 pl-5" id="activity-container">
            <div class="d-flex justify-content-between align-items-center my-3">
                <h2 class="mt-2 font-educ"><strong>Activities Notifications</strong></h2>
                <div class="d-flex align-items-center">
                    <input type="search" class="form-control mr-1" placeholder="Search..." id="search-input"
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
            @foreach ($engagement->groupBy(function ($date) {
            return \Carbon\Carbon::parse($date->date)->format('F Y'); // Group by month and year
        }) as $month => $activitiesInMonth)
                <div class="activity-list">
                    <div class="activity-date my-3 ml-2">
                        <h5 class="text-muted">{{ $month }}</h5>
                    </div>
                    @foreach ($activitiesInMonth as $activity)
                        <div class="activity-item mb-3 border-educ rounded p-3"
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
            <h2 class="ml-3 mb-2 font-educ">Activity Taken</h2>
        </div>
        <div class="d-flex align-items-center mr-3 mb-2">
            <!-- Button to trigger the modal -->
            <button class="btn hover-action add-activity-button" data-toggle="modal" data-target="#addActivityModal">
                <i class="fa-solid fa-square-plus"></i>
            </button>
        </div>
    </div>

    <!-- Table -->
    <table class="table table-hover mt-2">
        <thead class="font-educ text-left">
            <tr>
                <th class="h5" scope="col">No</th>
                <th class="h5" scope="col">Date</th>
                <th class="h5" scope="col">Type</th>
                <th class="h5" scope="col">Description</th>
                <th class="h5" scope="col">Attachment</th>
                <th class="h5" scope="col">Action</th>
            </tr>
        </thead>
        <tbody class="text-left bg-row">
            @foreach ($engagement as $engagements)
                <tr>
                    <td> {{ $engagements->engagement_pid }} </td>
                    <td> {{ $engagements->date }} </td>
                    <td> {{ $engagements->activity_name }} </td>
                    <td> {{ $engagements->details }} </td>
                    <td> {{ $engagements->attachments }} </td>
                    <td><a href="#" class="btn hover-action"><i class="fa-solid fa-pen-to-square"></i></a></td>
                </tr>
            @endforeach
    </table>

    <!-- Add Activity Modal -->
    <div class="modal fade" id="addActivityModal" tabindex="-1" aria-labelledby="addActivityModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title" id="editContactModalLabel">
                        <strong>Add New Activity</strong>
                    </h5>
                    <!-- Adding the logo on the right side -->
                    <img src="{{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png') }}" alt="Company Logo"
                        style="height: 30px;">

                </div>
                <div class="modal-body">
                    <form action=" {{ route('contact#save_activity', $editContact->contact_pid) }} " method="POST"
                        id="addActivityForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="contact_pid" value=" {{ $editContact->contact_pid }} " readonly>
                        <div class="row row-margin-bottom row-border-bottom">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="activity-date">Date</label>
                                    <input type="date" name="activity-date" class="form-control" id="activity-date"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="activity-type">Type</label>
                                    <select class="form-control" id="activity-type" name="activity-name"required>
                                        <option value="Email">Email</option>
                                        <option value="Phone">Phone</option>
                                        <option value="Meeting">Meeting</option>
                                        <option value="WhatsApp">WhatsApp</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <!-- Attachment Label and Button Side by Side -->
                                    <div class="attachment-container">
                                        <label for="activity-attachment">Attachment</label>
                                        <input type="file" id="activity-attachment" multiple accept="image/*"
                                            style="display: none;" name="activity-attachments">
                                        <button type="button" class="btn btn-upload"
                                            onclick="document.getElementById('activity-attachment').click();">
                                            <i class="fa-solid fa-upload"></i> Upload
                                        </button>
                                    </div>
                                    <div id="file-names" class="file-list"></div> <!-- Display filenames here -->
                                </div>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="activity-description">Description</label>
                                    <textarea style="height: 100px; " class="form-control" id="activity-description" rows="3"
                                        name="activity-details"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn" style="background: #91264c; color:white;">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <!-- Edit Contact Modal -->
    <div class="modal fade" id="editContactModal" tabindex="-1" aria-labelledby="editContactModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title" id="editContactModalLabel">
                        <strong>Edit Contact</strong>
                    </h5>
                    <!-- Adding the logo on the right side -->
                    <img src="{{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png') }}" alt="Company Logo"
                        style="height: 30px;">

                </div>
                <div class="modal-body">
                    <form action=" {{ route('contact#save_edit', $editContact->contact_pid) }}" method="POST"
                        id="editContactForm">
                        @csrf
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="contact-name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ $editContact->name }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="contact-email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ $editContact->email }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="contact-country">Country</label>
                                    <input type="text" class="form-control" id="contact-country" name="country"
                                        value="{{ $editContact->country }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="contact-address">Address</label>
                                    <input class="form-control" style="height: 125px;" type="text" name="address"
                                        id="address" value="{{ $editContact->address }}">
                                </div>
                            </div>
                            <!-- Middle Column -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="contact-number">Contact Number</label>
                                    </div>
                                    <div>
                                        <input type="text" class="form-control mb-2" id="contact-number"
                                            name="contact_number" value="{{ $editContact->contact_number }}" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="contact-qualification">Qualification</label>
                                    </div>
                                    <div>
                                        <input type="text" class="form-control mb-2" id="contact-qualification"
                                            value="{{ $editContact->qualification }}" name="qualification" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="contact-role">Job Role</label>
                                    <input type="text" class="form-control" name="job_role" id="contact-role"
                                        value="{{ $editContact->job_role }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="contact-skills">Skills</label>
                                    <input type="text" class="form-control" name="skills" id="contact-skills"
                                        value="{{ $editContact->skills }}" required>
                                </div>
                            </div>
                            <!-- Right Column -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="contact-allocation">Date of Allocation</label>
                                    <input type="datetime" class="form-control" id="contact-allocation"
                                        value="{{ $editContact->date_of_allocation }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="contact-source">Source</label>
                                    <input type="text" class="form-control" id="contact-source"
                                        value="{{ $editContact->source }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="contact-status">Status</label>
                                    <select class="form-control" id="contact-status" required>
                                        <option value="{{ $editContact->status }}" selected>
                                            {{ $editContact->status }} </option>
                                        <option>In progress</option>
                                        <option>Completed</option>
                                        <option>Pending</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn " style="background: #91264c; color:white;">Save</button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ URL::asset('js/contact_detail.js') }}"></script>

@endsection
