@if ($updateEngagement)
    @foreach ($engagements as $engagement)
        <!-- Edit Activity Modal -->
        <div class="modal fade" id="updateActivityModal{{$engagement->engagement_pid}}"
            tabindex="-1" aria-labelledby="updateActivityModalLabel{{$engagement->engagement_pid}}" aria-hidden="true">
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
                            method="POST" id="updateActivityForm-{{ $engagement->engagement_pid }}"
                            enctype="multipart/form-data">
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
                                                accept="image/*" style="opacity: 0; position: absolute; left: -9999px;"
                                                name="activity-attachments">
                                            <button type="button" class="btn hover-action"
                                                onclick="document.getElementById('activity-attachment-{{ $engagement->engagement_pid }}').click();"
                                                style="width: max-content">
                                                <i class="fa-solid fa-upload"></i> Upload
                                            </button>
                                            <div id="attachment-wrong-{{ $engagement->engagement_pid }}"
                                                class="text-danger mt-2" style="display: none;">
                                                Please upload only image files.</div>
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
            // getting image input
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
        <script>
            // Show modal image
            document.addEventListener('click', function(event) {
                if (event.target && event.target.id === 'attachmentImage') {
                    const imageUrl = event.target.getAttribute('data-image-url');
                    document.getElementById('modalImage').src = imageUrl;
                    $('#imageModal').modal('show');
                }
            });
        </script>
        <script>
            // JavaScript for Validating Attachment Types
            document.getElementById('activity-attachment-{{ $engagement->engagement_pid }}').addEventListener('change',
                function(event) {
                    const allowedExtensions = ['image/jpeg', 'image/png', 'image/jpg']; // Define allowed image types
                    const files = event.target.files;
                    let valid = true;

                    // Check each file type to ensure it's an allowed image type
                    for (let i = 0; i < files.length; i++) {
                        if (!allowedExtensions.includes(files[i].type)) {
                            valid = false;
                            break;
                        }
                    }

                    const attachmentWrong = document.getElementById(
                        'attachment-wrong-{{ $engagement->engagement_pid }}'); // Use dynamic ID
                    const output = document.getElementById('file-names-{{ $engagement->engagement_pid }}');

                    if (!valid) {
                        // Show error message if an invalid file is detected
                        attachmentWrong.style.display = 'block';
                        // Clear the file input to prevent uploading wrong files
                        event.target.value = '';
                        // Clear the filenames display
                        output.innerHTML = '';
                    } else {
                        // Hide error message if all files are valid
                        attachmentWrong.style.display = 'none';

                        // Display the filenames of valid image files
                        output.innerHTML = '';
                        for (let i = 0; i < files.length; i++) {
                            var listItem = document.createElement('div'); // Create the div element
                            listItem.classList.add("file-name"); // Add the "file-name" class
                            listItem.textContent = files[i].name; // Set the text content
                            output.appendChild(listItem); // Append the element to the output
                        }
                    }


                });

            // Prevent form submission if attachments are invalid
            document.getElementById('updateActivityForm-{{ $engagement->engagement_pid }}').addEventListener('submit',
                function(event) {
                    const fileInput = document.getElementById('activity-attachment-{{ $engagement->engagement_pid }}');
                    const attachmentWrong = document.getElementById('attachment-wrong-{{ $engagement->engagement_pid }}');

                    const attachmentBool = attachmentWrong.style.display === 'block';

                    // Check if files are present and if any are invalid
                    if (attachmentBool == true) {
                        event.preventDefault(); // Stop form submission
                    }
                });
        </script>
    @endforeach
@endif
