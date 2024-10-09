@section('title', 'Edit Contact Detail Page')

@extends('layouts.app')

@section('content')
    @extends('layouts.Update_Activity_Modal')
    @extends('layouts.Edit_Contact_Modal')
    @extends('layouts.Add_Activity_Modal')
    @extends('layouts.Attachment_Error_Modal')
    @if (
        (Auth::check() && Auth::user()->role == 'Admin') ||
            Auth::user()->role == 'BUH' ||
            Auth::user()->role == 'Sales_Agent')
        @if (session('success'))
            <!-- Trigger the modal with a button (hidden, will be triggered by JavaScript) -->
            <button id="successModalBtn" type="button" class="btn btn-primary" data-toggle="modal" data-target="#successModal"
                style="display: none;">
                Open Modal
            </button>
            <!-- Modal -->
            <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content rounded-0">
                        <div class="modal-header"
                            style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                            border:none;border-top-left-radius: 0; border-top-right-radius: 0;">
                            <h5 class="modal-title font-educ" id="successModalLabel">Success</h5>
                        </div>
                        <div class="modal-body" style="color: #91264c">
                            {{ session('success') }}
                        </div>
                        <div class="modal-footer" style="border:none">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Script to trigger the modal -->
            <script type="text/javascript">
                window.onload = function() {
                    document.getElementById('successModalBtn').click();
                };
            </script>
        @endif
        {{-- css will edit to css file soon --}}
        <link rel="stylesheet" href="{{ URL::asset('css/contact_detail.css') }}">
        <div class="row border-educ rounded">
            <div class="col-md-5 border-right" id="contact-detail">
                <div class="table-title d-flex justify-content-between align-items-center my-3">
                    <h2 class="mt-2 headings">Contact Detail</h2>
                    @if (Auth::check() && Auth::user()->role == 'Sales_Agent' || Auth::check() && Auth::user()->role == 'Admin')
                    <a href="{{ Auth::user()->role == 'Admin' 
                        ? route('admin#edit-contact', ['contact_pid' => $editContact->contact_pid ?? $editContact->contact_archive_pid ?? $editContact->contact_discard_pid])
                        : route('contact#edit', ['contact_pid' => $editContact->contact_pid ?? $editContact->contact_archive_pid ?? $editContact->contact_discard_pid]) }}" 
                    class="btn hover-action mx-1"
                    data-toggle="modal" data-target="#editContactModal">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
            
                        {{-- <a href="{{ route('contact#edit', $editContact->contact_pid) }}" class="btn hover-action mx-1"
                            data-toggle="modal" data-target="#editContactModal">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a> --}}
                    @endif
                </div>
                <div class="row row-margin-bottom row-border-bottom mx-1">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-educ" for="name">Name</label>
                            <h5 class="fonts p-1 text-truncate" id="name">{{ $editContact->name }}</h5>
                        </div>
                        <div class="form-group">
                            <label class="font-educ" for="contact-number">Contact Number</label>
                            <h5 class="fonts p-1 text-truncate" id="contact_number">{{ $editContact->contact_number }}</h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-educ" for="email">Email</label>
                            <h5 class="fonts p-1 text-truncate" id="email">{{ $editContact->email }}</h5>
                        </div>
                        <div class="form-group">
                            <label class="font-educ" for="country">Country</label>
                            <h5 class="fonts p-1 text-truncate" id="country">{{ $editContact->country }}</h5>
                        </div>
                    </div>
                </div>
                <div class="row row-margin-bottom row-border-bottom mx-1">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-educ" for="address">Address</label>
                            <h5 class="fonts p-1 text-truncate" id="address" style="min-height: 103px;">{{ $editContact->address }}</h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-educ" for="date-of-allocation">Date of Allocation</label>
                            <h5 class="fonts p-1 text-truncate" id="date-of-allocation">{{ $editContact->date_of_allocation }}</h5>
                        </div>
                        <div class="form-group">
                            <label class="font-educ" for="qualification">Qualification</label>
                            <h5 class="fonts p-1 text-truncate" id="qualification">{{ $editContact->qualification }}</h5>
                        </div>
                    </div>
                </div>
                <div class="row mx-1">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-educ" for="skills">Skills</label>
                            <h5 class="fonts p-1 text-truncate" id="skills">{{ $editContact->skills }}</h5>
                        </div>
                        <div class="form-group">
                            <label class="font-educ" for="source">Source</label>
                            <h5 class="fonts p-1 text-truncate" id="source">{{ $editContact->source }}</h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-educ" for="job-role">Job Role</label>
                            <h5 class="fonts p-1 text-truncate" id="job-role">{{ $editContact->job_role }}</h5>
                        </div>
                        <div class="form-group">
                            <label class="font-educ" for="status">Status</label>
                            <h5 class="fonts p-1 rounded text-truncate" id="status">
                                {{ trim(
                                    $editContact->status === 'HubSpot Contact'
                                        ? 'HubSpot'
                                        : ($editContact->status === 'discard'
                                            ? 'Discard'
                                            : ($editContact->status === 'InProgress'
                                                ? 'In Progress'
                                                : ($editContact->status === 'New'
                                                    ? 'New'
                                                    : ($editContact->status === 'Archive'
                                                        ? 'Archive'
                                                        : '')))),
                                ) }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-7 px-3" id="activity-container">
                <div class="d-flex justify-content-between align-items-center my-3">
                    <h2 class="mt-2 ml-2 headings">Activities Notifications</h2>
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
                <div class="activities">
                    @forelse ($engagements->groupBy(function ($date) {
                        return \Carbon\Carbon::parse($date->date)->format('F Y');
                        }) as $month => $activitiesInMonth)
                        <div class="activity-list" data-month="{{ $month }}">
                            <div class="activity-date my-3 ml-3">
                                <span class="text-muted">{{ $month }}</span>
                            </div>
                            @foreach ($activitiesInMonth as $activity)
                                <div class="activity-item mb-3 mx-3 border-educ rounded p-3"
                                    data-type="{{ strtolower($activity->activity_name) }}">
                                    <h5 class="font-educ">{{ $activity->activity_name }} Activities</h5>
                                    <small>{{ \Carbon\Carbon::parse($activity->date)->format('d-m-Y') }}</small>
                                    <p class="text-muted">{{ $activity->details }}</p>
                                </div>
                            @endforeach
                            {{-- No activity messages for specific types --}}
                            <div class="no-activity-message mb-3 mx-3 border-educ rounded p-3 d-none" data-type="meeting">
                                <h5 class="font-educ">Meetings</h5>
                                <p class="text-muted">No meetings taken.</p>
                            </div>
                            <div class="no-activity-message mb-3 mx-3 border-educ rounded p-3 d-none" data-type="email">
                                <h5 class="font-educ">Emails</h5>
                                <p class="text-muted">No emails taken.</p>
                            </div>
                            <div class="no-activity-message mb-3 mx-3 border-educ rounded p-3 d-none" data-type="phone">
                                <h5 class="font-educ">Calls</h5>
                                <p class="text-muted">No calls taken.</p>
                            </div>
                            <div class="no-activity-message mb-3 mx-3 border-educ rounded p-3 d-none"
                                data-type="whatsapp">
                                <h5 class="font-educ">WhatsApp</h5>
                                <p class="text-muted">No WhatsApp taken.</p>
                            </div>
                        </div>
                    @empty
                        <div class="no-activities text-center my-4">
                            <p class="text-muted">No activities found.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        <!-- Activity Taken Section -->
        <div class="table-title d-flex justify-content-between align-items-center mt-5">
            <div class="d-flex align-items-center">
                <h2 class="ml-2 mb-1 headings">Activity Taken</h2>
                <button class="btn hover-action mx-3" id="show-activities">
                    Activities Taken
                </button>
                <button class="btn archive-table mx-3" id="show-deleted-activities">
                    Archived Activities
                </button>
            </div>
            <div class="d-flex align-items-center mr-2 mb-2">
                <!-- Button to trigger the modal -->
                @if (Auth::check() && Auth::user()->role == 'Sales_Agent' || Auth::check() && Auth::user()->role == 'Admin' && $editContact->status !== 'HubSpot Contact')
                    <button style="display: block;" class="btn hover-action add-activity-button" data-toggle="modal"
                        data-target="#addActivityModal" id="addActivityBtn">
                        <i style="font-size: 22px;" class="fa-solid fa-square-plus p-1"></i>
                    </button>
                @endif
            </div>
        </div>
        <!-- Table -->
        <div class="table-container" style="max-height: 350px; overflow-y:auto; display: block;" id="activity-table">
            <table class="table table-hover mt-2">
                <thead class="font-educ text-left">
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Date</th>
                        <th scope="col">Created Date</th>
                        <th scope="col">Modified Date</th>
                        <th scope="col">Type</th>
                        <th scope="col">Description</th>
                        <th scope="col">Attachment</th>
                        
                        @if (Auth::check() && Auth::user()->role == 'Sales_Agent' || Auth::check() && Auth::user()->role == 'Admin' && $editContact->status !== 'HubSpot Contact')
                            <th scope="col" class="text-center">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="text-left bg-row">
                    <?php $i = 0; ?>
                    @forelse ($engagements as $engagement)
                        @php
                            // Decode the JSON or handle the attachments array properly
                            $attachments = json_decode($engagement->attachments, true); // Assuming it's a JSON string
                            $filename = $attachments[0] ?? ''; // Get the first filename from the array
                        @endphp
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ $engagement->date }}</td>
                            <td>{{ \Carbon\Carbon::parse($engagement->created_at)->format('Y-m-d H:i:s') }}</td> <!-- Created Date -->
                            <td>{{ \Carbon\Carbon::parse($engagement->updated_at)->format('Y-m-d H:i:s') }}</td> <!-- Modified Date -->
                            <td>{{ $engagement->activity_name }}</td>
                            <td>{{ $engagement->details }}</td>
                            <td>
                                @if ($filename)
                                    <a href="#table-container" id="attachmentImage"
                                        style="width: 100px; height: auto; cursor: pointer;"
                                        data-image-url="{{ $filename }}">
                                        View Attachment
                                    </a>
                                @else
                                    No Image Available
                                @endif
                            </td>
                            
                            @if (Auth::user()->role == 'Sales_Agent' || Auth::user()->role == 'Admin')
                                <td class="text-center">
                                    @if($editContact->status !== 'HubSpot Contact')
                                        <a href="{{ Auth::user()->role == 'Sales_Agent' ? route('contact#update-activity', ['contact_id' => $engagement->fk_engagements__contact_pid, 'activity_id' => $engagement->engagement_pid]) : '#' }}"
                                        data-toggle="modal"
                                        {{ Auth::user()->role == 'Sales_Agent' ? 'data-target=#updateActivityModal-' . $engagement->engagement_pid : '' }}
                                        class="btn hover-action">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <a class="btn hover-action" data-toggle="modal"
                                            data-target="#deleteUserModal{{ $engagement->engagement_pid }}">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No Activity Taken.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-container" style="max-height: 350px; overflow-y:auto; display: none;" id="deleted-activity-table">
            <table class="table table-hover mt-2">
                <thead class="font-educ text-left">
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Date</th>
                        <th scope="col">Created Date</th>
                        <th scope="col">Modified Date</th>
                        <th scope="col">Type</th>
                        <th scope="col">Description</th>
                        <th scope="col">Attachment</th>
                        
                        @if (Auth::check() && Auth::user()->role == 'Sales_Agent' || Auth::user()->role == 'Admin')
                            <th scope="col" class="text-center">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="text-left bg-row">
                    <?php $i = 0; ?>
                    @forelse ($deletedEngagement as $deletedActivity)
                        @php
                            // Decode the JSON or handle the attachments array properly
                            $attachments = json_decode($deletedActivity->attachments, true); // Assuming it's a JSON string
                            $filename = $attachments[0] ?? ''; // Get the first filename from the array
                        @endphp
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ $deletedActivity->date }}</td>
                            <td>{{ \Carbon\Carbon::parse($deletedActivity->created_at)->format('Y-m-d H:i:s') }}</td> <!-- Created Date -->
                            <td>{{ \Carbon\Carbon::parse($deletedActivity->updated_at)->format('Y-m-d H:i:s') }}</td> <!-- Modified Date -->
                            <td>{{ $deletedActivity->activity_name }}</td>
                            <td>{{ $deletedActivity->details }}</td>
                            <td>
                                "This activity is currently archived. Please restore the activity to view it."
                                {{-- @if ($filename)
                                    <a href="#table-container" id="attachmentImage"
                                        style="width: 100px; height: auto; cursor: pointer;"
                                        data-image-url="{{ $filename }}">
                                        View Attachment
                                    </a>
                                @else
                                    No Image Available
                                @endif --}}
                            </td>
                            @if (Auth::user()->role == 'Sales_Agent' || Auth::user()->role == 'Admin')
                                <td class="text-center">
                                    <a class="btn hover-action" data-toggle="modal"
                                        data-target="#retrieveActivityModal{{ $deletedActivity->id }}">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </a>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No Activity Taken.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
</div>

    @else
        <div class="alert alert-danger text-center mt-5">
            <strong>Access Denied!</strong> You do not have permission to view this page.
        </div>
    @endif
    </div>
    <!-- Bootstrap Modal for Image -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: max-content">
            <div class="modal-content">
                <div class="modal-body">
                    <img id="modalImage" class="img-fluid" src="" alt="Image"
                        style="max-width: 80vw!important">
                </div>
            </div>
        </div>
    </div>
    <script>
        document.querySelectorAll('.activity-button').forEach(button => {
            button.addEventListener('click', function() {
                let filter = this.getAttribute('data-filter');
                // Reset active button class
                document.querySelectorAll('.activity-button').forEach(btn => btn.classList.remove(
                    'active-activity-button'));
                this.classList.add('active-activity-button');
                // Show or hide activities based on filter
                document.querySelectorAll('.activity-item').forEach(item => {
                    if (filter === 'all' || item.getAttribute('data-type') === filter) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
                // Check if any activities are visible after filtering
                let visibleItems = document.querySelectorAll(`.activity-item[data-type="${filter}"]`);
                let noActivityMessage = document.querySelector(
                    `.no-activity-message[data-type="${filter}"]`);
                if (visibleItems.length === 0 && noActivityMessage) {
                    noActivityMessage.classList.remove('d-none');
                } else if (noActivityMessage) {
                    noActivityMessage.classList.add('d-none');
                }
                // Hide all no-activity messages except for the current filter
                document.querySelectorAll('.no-activity-message').forEach(msg => {
                    if (msg.getAttribute('data-type') !== filter) {
                        msg.classList.add('d-none');
                    }
                });
            });
        });
    </script>
    <script src=" {{ URL::asset('js/show_activity_table.js') }} "></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ URL::asset('js/contact_detail.js') }}"></script>
    <script src="{{ URL::asset('js/status_color.js') }}"></script>
    <script>
        @if ($errors->has('status'))
            $(document).ready(function() {
                $('#editContactModal').modal('show');
            });
        @endif
    </script>
    <script>
        $(document).ready(function() {
            // Function to show/hide activities and display "No activity" messages based on the filter
            function filterActivities(filterType) {
                $('.activity-list').each(function() {
                    var $list = $(this);
                    var $activityItems = $list.find('.activity-item');
                    var $noActivityMessages = $list.find('.no-activity-message');
                    var $activityDate = $list.find('.activity-date');

                    // Check if there are any visible activity items for the given filter
                    var hasVisibleActivities = $activityItems.filter(function() {
                        return $(this).data('type') === filterType || filterType === 'all';
                    }).length > 0;

                    // Show or hide activity items based on filter
                    if (filterType === 'all') {
                        $activityItems.show();
                    } else {
                        $activityItems.filter(function() {
                            return $(this).data('type') === filterType;
                        }).show();
                        $activityItems.filter(function() {
                            return $(this).data('type') !== filterType;
                        }).hide();
                    }

                    // Show or hide "No activity" and "activity-date" messages based on the presence of visible activities
                    if (hasVisibleActivities) {
                        $noActivityMessages.hide();
                        $activityDate.show();

                    } else {
                        $noActivityMessages.filter(function() {
                            return $(this).data('type') === filterType;
                        }).show();
                        $activityDate.hide();

                    }
                });
            }

            // Initially show all activities and "No activity" messages
            filterActivities('all');

            // Set up filter button click handlers
            $('.activity-button').on('click', function() {
                // Get the filter type from the button data attribute
                var filterType = $(this).data('filter');

                // Apply filter based on the button clicked
                filterActivities(filterType);

                // Optional: Update active button style
                $('.activity-button').removeClass('active-activity-button');
                $(this).addClass('active-activity-button');
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            /**
             * Image Modal
             */
            // Event listener for clicking on the filename span
            $('#attachmentImage').on('click', function() {
                // Get the image URL from the data attribute
                var imageUrl = $(this).data('image-url');

                // Set the src attribute of the modal image to the image URL
                $('#modalImage').attr('src', imageUrl);

                // Show the modal
                $('#imageModal').modal('show');
            });

            /**
             * Show the error for attachments
             */
            // Check if there are any errors related to the file upload
            @if ($errors->has('activity-attachments'))
                // Show the error modal if there are file upload errors
                $('#errorModal').modal('show');
            @endif

        });
    </script>
    <script>
    document.addEventListener('click', function(event) {
        if (event.target && event.target.id === 'attachmentImage') {
            const imageUrl = event.target.getAttribute('data-image-url');
            document.getElementById('modalImage').src = imageUrl;
            $('#imageModal').modal('show');
        }
    });
</script>

@endsection
