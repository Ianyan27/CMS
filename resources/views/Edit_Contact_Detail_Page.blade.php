@section('title', 'Edit Contact Detail Page')

@extends('layouts.app')

@section('content')
    <div class="row border-educ rounded h-auto">
        <div class="col-md-5 border-right">
            <div class="table-title d-flex justify-content-between align-items-center my-3">
                <h2 class="mt-2 font"><strong>Contact Detail</strong></h2>
                <a href="#" class="btn hover-action ml-3">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>
            </div>
            <div class="row row-margin-bottom row-border-bottom">
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
            <div class="row row-margin-bottom row-border-bottom">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea style="height: 125px; resize:none;" class="form-control" id="address" placeholder="123, Jalan Bunga Raya, Taman Melati, 53100 Kuala Lumpur, Malaysia" readonly></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="date-of-allocation">Date of Allocation</label>
                        <input type="date" class="form-control" id="date-of-allocation" placeholder="13/2/2024" readonly>
                    </div>
                    <div class="form-group">
                        <label for="qualification">Qualification</label>
                        <input type="text" class="form-control" id="qualification" placeholder="Bachelor of Software Engineer" readonly>
                    </div>
                </div>
            </div>
            <div class="row">
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
                        <input type="text" class="form-control" id="job-role" placeholder="Technology Associate" readonly>
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
                <h2 class="mt-2 font"><strong>Activities Notifications</strong></h2>
                <div class="d-flex align-items-center">
                    <input type="search" class="form-control mr-1" placeholder="Search..." id="search-input" aria-label="Search">
                    <button class="btn btn-secondary bg-educ mx-1" type="submit">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </div>
            <div class="btn-group mb-3" role="group" aria-label="Activity Filter Buttons">
                <button type="button" class="btn activity-button active-activity-button">Activities</button>
                <button type="button" class="btn activity-button">Meetings</button>
                <button type="button" class="btn activity-button">Emails</button>
                <button type="button" class="btn activity-button">Calls</button>
            </div>
            <div class="activity-list">
                <div class="activity-date my-3 ml-2">
                    <h5 class="text-muted">July 2024</h5>
                </div>
                <div class="activity-item mb-3 border-educ rounded p-3">
                    <h5 class="font-educ">Email Activities</h5>
                    <small>12-7-2024</small>
                    <p class="text-muted">Sales Agent John Smith sent a promotional email to John Doe about our new product.</p>
                </div>
            </div>
            <div class="activity-list">
                <div class="activity-date my-3 ml-2">
                    <h5 class="text-muted">July 2024</h5>
                </div>
                <div class="activity-item mb-3 border-educ rounded p-3">
                    <h5 class="font-educ">Phone Activities</h5>
                    <small>10-7-2024</small>
                    <p class="text-muted">Sales Agent John Smith sent a promotional email to John Doe about our new product.</p>
                </div>
            </div>
            <div class="activity-list">
                <div class="activity-date my-3 ml-2">
                    <h5 class="text-muted">July 2024</h5>
                </div>
                <div class="activity-item mb-3 border-educ rounded p-3">
                    <h5 class="font-educ">WhatsApp Activities</h5>
                    <small>8-7-2024</small>
                    <p class="text-muted">Sales Agent John Smith sent a promotional email to John Doe about our new product.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="table-title d-flex justify-content-between align-items-center mt-5">
        <div class="d-flex align-items-center">
            <h2 class="ml-3 mb-2 font"><strong>Activity Taken</strong></h2>
        </div>
        <div class="d-flex align-items-center mr-3 mb-2">
            <button class="btn hover-action mx-1" type="submit"><i class="fa-solid fa-square-plus"></i></button>
        </div>
    </div>
    <table class="table table-hover mt-2">
        <thead class="font text-center">
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
@endsection