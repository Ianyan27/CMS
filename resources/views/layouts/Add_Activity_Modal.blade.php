<!-- Add Activity Modal -->
<div class="modal fade" id="addActivityModal" tabindex="-1" aria-labelledby="addActivityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-0">
            <div class="modal-header d-flex justify-content-between align-items-center"
                style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                        border:none;border-top-left-radius: 0; border-top-right-radius: 0;">
                <h5 class="modal-title" id="editContactModalLabel">
                    <strong>Add New Activity</strong>
                </h5>
                <!-- Adding the logo on the right side -->
                <img src="{{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png') }}" alt="Company Logo"
                    style="height: 30px;">
            </div>
            <div class="modal-body">
                <form action=" {{ route('contact#save-activity', $editContact->contact_pid) }} " method="POST"
                    id="addActivityForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="contact_pid" value=" {{ $editContact->contact_pid }} " readonly>
                    <div class="row row-margin-bottom row-border-bottom">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-educ" for="activity-date">Date</label>
                                <input type="date" name="activity-date" class="form-control fonts" id="activity-date"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-educ" for="activity-type">Type</label>
                                <select class="form-control fonts" id="activity-type" name="activity-name"required>
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
                                    <label class="font-educ my-1" for="activity-attachment">Attachment</label>
                                    <input type="file" id="activity-attachment" multiple accept="image/*"
                                        style="opacity: 0; position: absolute; left: -9999px;"
                                        name="activity-attachments">
                                    <button type="button" class="btn hover-action"
                                        onclick="$('#activity-attachment').click();">
                                        <i class="fa-solid fa-upload"></i> Upload
                                    </button>
                                    <div id="attachment-error" class="text-danger mt-2" style="display: none;">
                                        Attachment is required.</div>
                                </div>
                                <div id="file-names" class="file-list"></div> <!-- Display filenames here -->
                            </div>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="font-educ" for="activity-description">Description</label>
                                <textarea style="height: 100px; " class="form-control fonts" id="activity-description" rows="3"
                                    name="activity-details" required></textarea>
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
