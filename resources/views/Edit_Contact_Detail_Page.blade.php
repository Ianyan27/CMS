@section('title', 'Edit Contact Detail Page')

@extends('layouts.app')

@section('content')
    <div class="row border-educ rounded h-auto">
        <div class="col-md-5 border-right">
            <div class="table-title d-flex justify-content-between align-items-center my-3">
                <h2 class="mt-2 font-educ"><strong>Contact Detail</strong></h2>
                <button class="btn hover-action mx-1" data-toggle="modal" data-target="#editContactModal">
                    <i class="fa-solid fa-pen-to-square"></i>
                </button>
            </div>
            <div class="row row-margin-bottom row-border-bottom mx-1">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" placeholder="John Doe" readonly>
                    </div>
                    <div class="form-group">
                        <label for="contact-number">Contact Number</label>
                        <input type="text" class="form-control" id="contact-number" placeholder="+659300224" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" placeholder="johndoe@gmail.com" readonly>
                    </div>
                    <div class="form-group">
                        <label for="country">Country</label>
                        <input type="text" class="form-control" id="country" placeholder="Malaysia" readonly>
                    </div>
                </div>
            </div>
            <div class="row row-margin-bottom row-border-bottom mx-1">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea style="height: 125px; resize:none;" class="form-control" id="address"
                            placeholder="123, Jalan Bunga Raya, Taman Melati, 53100 Kuala Lumpur, Malaysia" readonly></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="date-of-allocation">Date of Allocation</label>
                        <input type="date" class="form-control" id="date-of-allocation" placeholder="13/2/2024" readonly>
                    </div>
                    <div class="form-group">
                        <label for="qualification">Qualification</label>
                        <input type="text" class="form-control" id="qualification"
                            placeholder="Bachelor of Software Engineer" readonly>
                    </div>
                </div>
            </div>
            <div class="row mx-1">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="skills">Skills</label>
                        <input type="text" class="form-control" id="skills" placeholder="Communication" readonly>
                    </div>
                    <div class="form-group">
                        <label for="source">Source</label>
                        <input type="text" class="form-control" id="source" placeholder="Facebook" readonly>
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
            <tr>
                <td><input type="checkbox" name="" id=""></td>
                <td>1</td>
                <td>8/7/2024</td>
                <td>Whatsapp</td>
                <td>Sales Agent John Smith sent a message to the exsisting contact about the courses information</td>
                <td>Screenshot.png</td>
                <td><a href="#" class="btn hover-action"><i class="fa-solid fa-pen-to-square"></i></a></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="" id=""></td>
                <td>2</td>
                <td>10/7/2024</td>
                <td>Phone</td>
                <td>Sales Agent John Smith sent a message to the exsisting contact about the courses information</td>
                <td>Screenshot.png</td>
                <td><a href="#" class="btn hover-action"><i class="fa-solid fa-pen-to-square"></i></a></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="" id=""></td>
                <td>3</td>
                <td>12/7/2024</td>
                <td>Email</td>
                <td>Sales Agent John Smith sent a message to the exsisting contact about the courses information</td>
                <td>Screenshot.png</td>
                <td><a href="#" class="btn hover-action"><i class="fa-solid fa-pen-to-square"></i></a></td>
            </tr>
        </tbody>
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
                    <form id="addActivityForm">
                        <div class="row row-margin-bottom row-border-bottom">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="activity-date">Date</label>
                                    <input type="date" class="form-control" id="activity-date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="activity-type">Type</label>
                                    <select class="form-control" id="activity-type" required>
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
                                    <textarea style="height: 100px; resize:none;" class="form-control" id="activity-description" rows="3"
                                        required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="activity-attachment">Attachment</label>
                                    <!-- Restrict file upload to images only -->
                                    <input type="file" class="form-control" id="activity-attachment"
                                        accept="image/*">
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
                    <form id="editContactForm">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="contact-name">Name</label>
                                    <input type="text" class="form-control" id="contact-name" value="John Doe"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="contact-email">Email</label>
                                    <input type="email" class="form-control" id="contact-email"
                                        value="johndoe@gmail.com" required>
                                </div>
                                <div class="form-group">
                                    <label for="contact-country">Country</label>
                                    <input type="text" class="form-control" id="contact-country" value="Malaysia"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="contact-address">Address</label>
                                    <textarea class="form-control" id="contact-address" rows="3" required>123, Jalan Bunga Raya, Taman Melati, 53100 Kuala Lumpur, Malaysia.</textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="contact-number">Contact Number</label>
                                        <button class="btn btn-outline-secondary" type="button">Add</button>
                                    </div>
                                    <div>
                                        <input type="text" class="form-control mb-2" id="contact-number"
                                            value="+659300224" required>
                                        <input type="text" class="form-control mb-2" value="+60174074712" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="contact-qualification">Qualification</label>
                                        <button class="btn btn-outline-secondary" type="button">Add</button>
                                    </div>
                                    <div>
                                        <input type="text" class="form-control mb-2" id="contact-qualification"
                                            value="Bachelor of Software Engineering" required>
                                        <input type="text" class="form-control mb-2" value="Master of Cyber Security"
                                            required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="contact-role">Job Role</label>
                                    <input type="text" class="form-control" id="contact-role"
                                        value="johndoe@gmail.com" required>
                                </div>
                                <div class="form-group">
                                    <label for="contact-skills">Skills</label>
                                    <input type="text" class="form-control" id="contact-skills" value="Communication"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="contact-allocation">Date of Allocation</label>
                                    <input type="date" class="form-control" id="contact-allocation"
                                        value="2024-03-21" required>
                                </div>
                                <div class="form-group">
                                    <label for="contact-source">Source</label>
                                    <input type="text" class="form-control" id="contact-source" value="Facebook"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="contact-status">Status</label>
                                    <select class="form-control" id="contact-status" required>
                                        <option selected>In progress</option>
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
