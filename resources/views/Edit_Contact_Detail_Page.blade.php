@section('title', 'Edit Contact Detail Page')

@extends('layouts.app')

@section('content')
    <div class="row border-educ rounded h-auto">
        <div class="col-md-5 border-right">
            <div class="table-title d-flex justify-content-between align-items-center my-3">
                <h2 class="mt-2 font-educ"><strong>Contact Detail</strong></h2>
                <a href="{{ route('contact#edit', $editContact->contact_pid) }}" class="btn hover-action mx-1" data-toggle="modal" data-target="#editContactModal">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>
                
            </div>
            <div class="row row-margin-bottom row-border-bottom mx-1">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" value=" {{$editContact->name}} " readonly>
                    </div>
                    <div class="form-group">
                        <label for="contact-number">Contact Number</label>
                        <input type="text" class="form-control" id="contact_number" value= " {{$editContact->contact_number}} " readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" value=" {{$editContact->email}} " readonly>
                    </div>
                    <div class="form-group">
                        <label for="country">Country</label>
                        <input type="text" class="form-control" id="country" value=" {{$editContact->country}} " readonly>
                    </div>
                </div>
            </div>
            <div class="row row-margin-bottom row-border-bottom mx-1">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input style="height: 125px;" type="text" class="form-control" id="address" value=" {{$editContact->address}} " readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="date-of-allocation">Date of Allocation</label>
                        <input type="datetime" class="form-control" id="date-of-allocation" value="{{$editContact->date_of_allocation}}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="qualification">Qualification</label>
                        <input type="text" class="form-control" id="qualification" value=" {{$editContact->qualification}} " readonly>
                    </div>
                </div>
            </div>
            <div class="row mx-1">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="skills">Skills</label>
                        <input type="text" class="form-control" id="skills" value="{{$editContact->skills}}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="source">Source</label>
                        <input type="text" class="form-control" id="source" value="{{$editContact->source}}" readonly>
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
        <div class="col-md-7 pl-5">
            <div class="d-flex justify-content-between align-items-center my-3">
                <h2 class="mt-2 font-educ"><strong>Activities Notifications</strong></h2>
                <div class="d-flex align-items-center">
                    <input type="search" class="form-control mr-1" placeholder="Search..." id="search-input"
                        aria-label="Search">
                    <button class="btn btn-secondary bg-educ mx-1" type="submit">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </div>
            <div class="btn-group mb-3" role="group" aria-label="Activity Filter Buttons">
                <button type="button" class="btn activity-button mx-2 active-activity-button">Activities</button>
                <button type="button" class="btn activity-button mx-2">Meetings</button>
                <button type="button" class="btn activity-button mx-2">Emails</button>
                <button type="button" class="btn activity-button mx-2">Calls</button>
                <button type="button" class="btn activity-button mx-2">Whatsapp</button>
            </div>
            <div class="activity-list">
                <div class="activity-date my-3 ml-2">
                    <h5 class="text-muted">July 2024</h5>
                </div>
                <div class="activity-item mb-3 border-educ rounded p-3">
                    <h5 class="font-educ-educ">Email Activities</h5>
                    <small>12-7-2024</small>
                    <p class="text-muted">Sales Agent John Smith sent a promotional email to John Doe about our new
                        product.</p>
                </div>
            </div>
            <div class="activity-list">
                <div class="activity-date my-3 ml-2">
                    <h5 class="text-muted">July 2024</h5>
                </div>
                <div class="activity-item mb-3 border-educ rounded p-3">
                    <h5 class="font-educ-educ">Phone Activities</h5>
                    <small>10-7-2024</small>
                    <p class="text-muted">Sales Agent John Smith sent a promotional email to John Doe about our new
                        product.</p>
                </div>
            </div>
            <div class="activity-list">
                <div class="activity-date my-3 ml-2">
                    <h5 class="text-muted">July 2024</h5>
                </div>
                <div class="activity-item mb-3 border-educ rounded p-3">
                    <h5 class="font-educ-educ">WhatsApp Activities</h5>
                    <small>8-7-2024</small>
                    <p class="text-muted">Sales Agent John Smith sent a promotional email to John Doe about our new
                        product.</p>
                </div>
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
            <button class="btn hover-action mx-1" data-toggle="modal" data-target="#addActivityModal">
                <i class="fa-solid fa-square-plus"></i>
            </button>
        </div>
    </div>

    <!-- Table -->
    <table class="table table-hover mt-2">
        <thead class="font-educ text-center">
            <tr>
                <th scope="col"><input type="checkbox" name="" id=""></th>
                <th class="h5" scope="col">No</th>
                <th class="h5" scope="col">Date</th>
                <th class="h5" scope="col">Type</th>
                <th class="h5" scope="col">Description</th>
                <th class="h5" scope="col">Attachment</th>
                <th class="h5" scope="col">Action</th>
            </tr>
        </thead>
        <tbody class="text-center bg-row fonts">
        @foreach ($engagement as $engagements)
            <tr>
                <td><input type="checkbox" name="" id=""></td>
                <td> {{$engagements->engagement_pid}} </td>
                <td> {{$engagements->date}} </td>
                <td> {{$engagements->activity_name}} </td>
                <td> {{$engagements->details}} </td>
                <td> {{$engagements->attachments}} </td>
                <td><a href="#" class="btn hover-action"><i class="fa-solid fa-pen-to-square"></i></a></td>
            </tr>
        @endforeach
    </table>

    <!-- Add Activity Modal -->
    <div class="modal fade" id="addActivityModal" tabindex="-1" aria-labelledby="addActivityModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addActivityModalLabel"><strong>Add New Activity</strong></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action=" {{route('contact#save_activity', $editContact->contact_pid)}} " 
                        method="POST" id="addActivityForm" enctype="multipart/form-data">
                        @csrf
                        <input type="text" name="contact_pid" value=" {{$editContact->contact_pid}} " 
                        readonly>
                        <div class="row row-margin-bottom row-border-bottom">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="activity-date">Date</label>
                                    <input type="date" name="activity-date" class="form-control" 
                                    id="activity-date" required>
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
                        <div class="row row-margin-bottom row-border-bottom">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="activity-description">Description</label>
                                    <textarea style="height: 100px; resize:none;" 
                                        class="form-control" id="activity-description" rows="3"
                                        name="activity-details"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="activity-attachment">Attachment</label>
                                    <!-- Restrict file upload to images only -->
                                    <input type="file" class="form-control" id="activity-attachment"
                                        name="activity-attachments" accept="image/*">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save Activity</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                <div class="modal-header">
                    <h5 class="modal-title" id="editContactModalLabel"><strong>Edit Contact</strong></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action=" {{ route('contact#save_edit', $editContact->contact_pid) }}" 
                        method="POST"  id="editContactForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="contact-name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                    value="{{$editContact->name}}"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="contact-email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{$editContact->email}}" required>
                                </div>
                                <div class="form-group">
                                    <label for="contact-country">Country</label>
                                    <input type="text" class="form-control" id="contact-country" 
                                    name="country" 
                                    value="{{$editContact->country}}" required>
                                </div>
                                <div class="form-group">
                                    <label for="contact-address">Address</label>
                                    <input class="form-control" style="height: 125px;" type="text" 
                                    name="address" id="address" value="{{$editContact->address}}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="contact-number">Contact Number</label>
                                        {{-- <button class="btn btn-outline-secondary" 
                                        type="button">Add</button> --}}
                                    </div>
                                    <div>
                                        <input type="text" class="form-control mb-2" id="contact-number" 
                                        name="contact_number"
                                            value="{{$editContact->contact_number}}" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="contact-qualification">Qualification</label>
                                        <button class="btn btn-outline-secondary" type="button">Add</button>
                                    </div>
                                    <div>
                                        <input type="text" class="form-control mb-2" 
                                        id="contact-qualification"
                                            value="{{$editContact->qualification}}" 
                                            name="qualification" required>
                                        {{-- <input type="text" class="form-control mb-2" 
                                        value="Master of Cyber Security"
                                            required> --}}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="contact-role">Job Role</label>
                                    <input type="text" class="form-control" name="job_role" id="contact-role"
                                        value="{{$editContact->job_role}}" required>
                                </div>
                                <div class="form-group">
                                    <label for="contact-skills">Skills</label>
                                    <input type="text" class="form-control" name="skills" 
                                    id="contact-skills" value="{{$editContact->skills}}"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="contact-allocation">Date of Allocation</label>
                                    <input type="datetime" class="form-control" id="contact-allocation"
                                        value="{{$editContact->date_of_allocation}}" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="contact-source">Source</label>
                                    <input type="text" class="form-control" id="contact-source" 
                                    value="{{$editContact->source}}"
                                        readonly>
                                </div>
                                <div class="form-group">
                                    <label for="contact-status">Status</label>
                                    <select class="form-control" id="contact-status" required>
                                        <option value="{{$editContact->status}}" selected> 
                                            {{$editContact->status}} </option>
                                        <option>In progress</option>
                                        <option>Completed</option>
                                        <option>Pending</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
