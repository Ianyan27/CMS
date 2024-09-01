<!-- Edit Contact Modal -->
<div class="modal fade" id="editContactModal" tabindex="-1" aria-labelledby="editContactModalLabel"
aria-hidden="true">
<div class="modal-dialog modal-lg">
    <div class="modal-content rounded-0">
        <div class="modal-header d-flex justify-content-between align-items-center"
        style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                        border:none;border-top-left-radius: 0; border-top-right-radius: 0;">
            <h5 class="modal-title" id="editContactModalLabel">
                <strong>Edit Contact</strong>
            </h5>
            <!-- Adding the logo on the right side -->
            <img src="{{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png') }}" alt="Company Logo"
                style="height: 30px;">
        </div>
        <div class="modal-body">
            <form action="{{ route('contact#update-contact',['contact_pid' => $editContact->contact_pid, 'id' => $user->id]) }}" 
                method="POST" id="editContactForm">
                @csrf
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-educ" for="contact-name">Name</label>
                            <input type="text" class="form-control fonts" id="name" name="name"
                                value="{{ $editContact->name }}" required>
                        </div>
                        <div class="form-group">
                            <label class="font-educ" for="contact-email">Email</label>
                            <input type="email" class="form-control fonts" id="email" name="email"
                                value="{{ $editContact->email }}" required>
                        </div>
                        <div class="form-group">
                            <label class="font-educ" for="contact-country">Country</label>
                            <input type="text" class="form-control fonts" id="contact-country" name="country"
                                value="{{ $editContact->country }}" required>
                        </div>
                        <div class="form-group">
                            <label class="font-educ" for="contact-address">Address</label>
                            <input class="form-control fonts" style="height: 103px;" type="text" name="address"
                                id="address" value="{{ $editContact->address }}">
                        </div>
                    </div>
                    <!-- Middle Column -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="font-educ" for="contact-number">Contact Number</label>
                            </div>
                            <div>
                                <input type="text" class="form-control fonts" id="contact-number"
                                    name="contact_number" value="{{ $editContact->contact_number }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="font-educ" for="contact-qualification">Qualification</label>
                            </div>
                            <div>
                                <input type="text" class="form-control fonts" id="contact-qualification"
                                    value="{{ $editContact->qualification }}" name="qualification" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-educ" for="contact-role">Job Role</label>
                            <input type="text" class="form-control fonts" name="job_role" id="contact-role"
                                value="{{ $editContact->job_role }}" required>
                        </div>
                        <div class="form-group">
                            <label class="font-educ" for="contact-skills">Skills</label>
                            <input type="text" class="form-control fonts" name="skills" id="contact-skills"
                                value="{{ $editContact->skills }}" required>
                        </div>
                    </div>
                    <!-- Right Column -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-educ" for="contact-allocation">Date of Allocation</label>
                            <input type="datetime" class="form-control fonts" id="contact-allocation"
                                value="{{ $editContact->date_of_allocation }}" readonly>
                        </div>
                        <div class="form-group">
                            <label class="font-educ" for="contact-source">Source</label>
                            <input type="text" class="form-control fonts" id="contact-source"
                                value="{{ $editContact->source }}" readonly>
                        </div>
                        <div class="form-group">
                            <label class="font-educ" for="contact-status">Status</label>
                            <select class="form-control fonts" id="contact-status" name="status" required>
                                <option value="InProgress" {{ $editContact->status === 'InProgress' ? 'selected' : '' }}>
                                    In Progress
                                </option>
                                <option value="HubSpot Contact" {{ $editContact->status === 'HubSpot Contact' ? 'selected' : '' }}>
                                    HubSpot
                                </option>
                                <option value="Discard" {{ $editContact->status === 'discard' ? 'selected' : '' }}>
                                    Discard
                                </option>
                                <option value="New" {{ $editContact->status === 'New' ? 'selected' : '' }}>
                                    New
                                </option>
                                <option value="Archive" {{ $editContact->status === 'Archive' ? 'selected' : '' }}>
                                    Archive
                                </option>
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