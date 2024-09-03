@if ($updateEngagement)
@foreach ($engagements as $engagment)
<!-- Edit Activity Modal -->
<div class="modal fade updateActivityModal" id="updateActivityModal-{{ $engagement->engagement_pid }}"
    tabindex="-1" aria-labelledby="updateActivityModalLabel-{{ $engagement->engagement_pid }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-0">
            <!-- Modal Header -->
            <div class="modal-header d-flex justify-content-between align-items-center"
            style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                border:none;border-top-left-radius: 0; border-top-right-radius: 0;">
                <h5 class="modal-title" id="updateActivityModalLabel"><strong>Update Activity</strong></h5>
                <!-- Logo on the right side -->
                <img src="{{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png') }}" alt="Company Logo"
                    style="height: 30px;">
            </div>
            <!-- Modal Body -->
            <div class="modal-body">
                <form
                    action="{{ route('contact#save-update-activity', ['contact_pid' => $engagement->fk_engagements__contact_pid, 'activity_id' => $engagement->engagement_pid]) }}"
                    method="POST" id="addActivityForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="contact_pid"
                        value="{{ $engagement->fk_engagements__contact_pid }}">
                    <input type="hidden" name="activity_id" value="{{ $engagement->engagement_pid }}">
                    <div class="row row-margin-bottom row-border-bottom">
                        <div class="col-md-6">
                            <!-- Date Field -->
                            <div class="form-group">
                                <label class="font-educ" for="activity-date">Date</label>
                                <input type="date" name="activity-date" class="form-control fonts"
                                    id="activity-date"
                                    value="{{ \Carbon\Carbon::parse($engagement->date)->format('Y-m-d') }}"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Type Dropdown -->
                            <div class="form-group">
                                <label class="font-educ" for="activity-type">Type</label>
                                <select class="form-control fonts" id="activity-type" name="activity-name"
                                    required>
                                    <option value="Email"
                                        {{ $engagement->activity_name == 'Email' ? 'selected' : '' }}>Email
                                    </option>
                                    <option value="Phone"
                                        {{ $engagement->activity_name == 'Phone' ? 'selected' : '' }}>Phone
                                    </option>
                                    <option value="Meeting"
                                        {{ $engagement->activity_name == 'Meeting' ? 'selected' : '' }}>
                                        Meeting
                                    </option>
                                    <option value="WhatsApp"
                                        {{ $engagement->activity_name == 'WhatsApp' ? 'selected' : '' }}>
                                        WhatsApp
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Attachment Upload -->
                            <div class="form-group">
                                <div class="attachment-container">
                                    <label class="font-educ my-1"
                                        for="activity-attachment-{{ $engagement->engagement_pid }}">Attachment</label>
                                    <input type="file"
                                        id="activity-attachment-{{ $engagement->engagement_pid }}" multiple
                                        accept="image/*" style="display: none;" name="activity-attachments[]">
                                    <button type="button" class="btn hover-action"
                                        onclick="document.getElementById('activity-attachment-{{ $engagement->engagement_pid }}').click();"
                                        style="width: max-content">
                                        <i class="fa-solid fa-upload"></i> Upload
                                    </button>
                                    <div id="file-names-{{ $engagement->engagement_pid }}" class="file-list ">
                                    </div> <!-- Display filenames here -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Description Field -->
                            <div class="form-group">
                                <label class="font-educ" for="activity-description">Description</label>
                                <textarea class="form-control fonts" id="activity-description" name="activity-details" rows="3"
                                    style="height: 100px;">{{ $engagement->details }}</textarea>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn"
                            style="background: #91264c; color:white;">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('activity-attachment-{{ $engagement->engagement_pid }}').addEventListener('change',
        function() {
            var fileList = this.files;
            var output = document.getElementById('file-names-{{ $engagement->engagement_pid }}');
            output.innerHTML = ''
            for (var i = 0; i < fileList.length; i++) {
                var listItem = document.createElement('div'); // Create the div element
                listItem.classList.add("file-name"); // Add the "file-name" class
                listItem.textContent = fileList[i].name; // Set the text content
                output.appendChild(listItem); // Append the element to the output
            }
        });
</script>
@endforeach
@endif