{{-- <!-- Edit Activity Modal -->
<div class="modal fade" id="updateActivityModal" tabindex="-1" aria-labelledby="updateActivityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title" id="updateActivityModalLabel"><strong>Update Activity</strong></h5>
                <!-- Logo on the right side -->
                <img src="{{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png') }}" alt="Company Logo" style="height: 30px;">
            </div>
            <!-- Modal Body -->
            <div class="modal-body">
                <form action="{{ route('contact#save_update_activity', $editContact->contact_pid) }}" method="POST" id="updateActivityForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row row-margin-bottom row-border-bottom">
                        <div class="col-md-6">
                            <!-- Date Field -->
                            <div class="form-group">
                                <label class="font-educ" for="activity-date">Date</label>
                                <input type="date" name="activity-date" class="form-control fonts" id="activity-date" 
                                value="{{ \Carbon\Carbon::parse($updateEngagement->date)->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Type Dropdown -->
                            <div class="form-group">
                                <label class="font-educ" for="activity-type">Type</label>
                                <select class="form-control fonts" id="activity-type" name="activity-name" required>
                                    <option value="Email" {{ $updateEngagement->activity_name == 'Email' ? 'selected' : '' }}>Email</option>
                                    <option value="Phone" {{ $updateEngagement->activity_name == 'Phone' ? 'selected' : '' }}>Phone</option>
                                    <option value="Meeting" {{ $updateEngagement->activity_name == 'Meeting' ? 'selected' : '' }}>Meeting</option>
                                    <option value="WhatsApp" {{ $updateEngagement->activity_name == 'WhatsApp' ? 'selected' : '' }}>WhatsApp</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Attachment Upload -->
                            <div class="form-group d-flex align-items-center">
                                <label class="font-educ my-1" for="activity-attachment">Attachment</label>
                                <button type="button" class="btn hover-action ml-3" onclick="document.getElementById('activity-attachment').click();">
                                    <i class="fa-solid fa-upload"></i> Upload
                                </button>
                                <input type="file" id="activity-attachment" name="activity-attachments" accept="image/*" style="display: none;" multiple>
                                <div id="file-names" class="file-list mt-2"></div> <!-- Display filenames here -->
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Description Field -->
                            <div class="form-group">
                                <label class="font-educ" for="activity-description">Description</label>
                                <textarea class="form-control fonts" id="activity-description" name="activity-details" rows="3" style="height: 100px;"></textarea>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn" style="background: #91264c; color:white;">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> --}}