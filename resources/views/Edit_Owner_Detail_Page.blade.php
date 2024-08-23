@section('title', "View Owner Details")

@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ URL::asset('css/contact_detail.css') }}">
<div class="row border-educ rounded h-auto">
    <div class="col-md-5 border-right" id="contact-detail">
        <div class="table-title d-flex justify-content-between align-items-center my-3">
            <h2 class="mt-2 ml-3 headings">Owner Detail</h2>
        </div>
        <div class="row row-margin-bottom row-border-bottom mx-1">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="font-educ" for="name">Name</label>
                    <input type="text" class="form-control fonts" id="name" value=" {{ $editOwner->owner_name }} "
                        readonly>
                </div>
                <div class="form-group">
                    <label class="font-educ" for="contact-number">Hubspot Id</label>
                    <input type="text" class="form-control fonts" id="contact_number"
                        value= " {{ $editOwner->owner_hubspot_id }} " readonly>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="font-educ" for="email">Email</label>
                    <input type="email" class="form-control fonts" id="email" value=" {{ $editOwner->owner_email_id }} "
                        readonly>
                </div>
                <div class="form-group">
                    <label class="font-educ" for="country">Country</label>
                    <input type="text" class="form-control fonts" id="country" value=" {{ $editOwner->country }} "
                        readonly>
                </div>
            </div>
        </div>
        <div class="row mx-1">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="font-educ" for="skills">Owner Business Unit</label>
                    <input type="text" class="form-control fonts" id="skills" value="{{ $editOwner->owner_business_unit }}"
                        readonly>
                </div>
                <div class="form-group">
                    <label class="font-educ" for="source">Total in Progress</label>
                    <input type="text" class="form-control fonts" id="source" value="{{ $editOwner->total_in_progress }}"
                        readonly>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="font-educ" for="job-role">Total Hubspot Sync</label>
                    <input type="text" class="form-control fonts" id="job-role" value=" {{ $editOwner->total_hubspot_sync }} "
                        readonly>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-7 px-3">
        <table class="table table-hover mt-2" id="contacts-table">
            <thead class="text-left font-educ">
                <th scope="col">No #</th>
                <th scope="col">Email</th>
                <th scope="col">Contact</th>
                <th scope="col">Status</th>
            </thead>
            <tbody>
                @foreach ($ownerContacts as $contact)
                    <tr>
                        <td> {{ $contact->contact_pid }} </td>
                        <td> {{ $contact->email }} </td>
                        <td> {{ $contact->contact_number }} </td>
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
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ URL::asset('js/contact_detail.js') }}"></script>
<script src="{{ URL::asset('js/status_color.js') }}"></script>
@endsection