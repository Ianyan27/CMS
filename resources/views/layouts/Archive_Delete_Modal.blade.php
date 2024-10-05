@foreach ($engagementArchive as $engagement)
    <div class="modal fade" id="deleteUserModal{{ $engagement->engagement_archive_pid }}" tabindex="-1"
        aria-labelledby="deleteUserModalLabel{{ $engagement->engagement_archive_pid }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="icon-container mx-auto">
                    <i class="fa-solid fa-trash"></i>
                </div>
                <div class="modal-header border-0"></div>
                <div class="modal-body">
                    <!-- Updated message -->
                    <p class="font-weight-bold">What would you like to do with this activity?</p>
                    <p class="text-muted">You can archive the activity (it can be restored later).</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <!-- Archive Form -->
                    <form action="{{ Auth::user()->role == 'Admin' ? 
                    route('admin#archiveActivity', ['engagement_archive_pid' => $engagement->engagement_archive_pid]) : 
                    route('archiveContactActivities', ['engagement_archive_pid' => $engagement->engagement_archive_pid]) }}"
                        method="POST" style="margin-right: 10px;">
                        @csrf
                        <button type="submit" class="btn archive-table">Archive</button>
                    </form>
                    {{-- <form action="{{ route('archiveActivity', ['engagement_pid' => $engagement->engagement_pid]) }}"
                        method="POST" style="margin-right: 10px;">
                        @csrf
                        <button type="submit" class="btn archive-table">Archive</button>
                    </form> --}}
                </div>
            </div>
        </div>
    </div>
@endforeach
@foreach ($deletedEngagement as $deletedActivity)
    <!-- Retrieve (Restore) Modal -->
    <div class="modal fade" id="retrieveActivityModal{{ $deletedActivity->id }}" tabindex="-1"
        aria-labelledby="retrieveActivityModal{{ $deletedActivity->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="icon-container mx-auto">
                    <i class="fa-solid fa-undo-alt"></i>
                </div>
                <div class="modal-header border-0">
                </div>
                <div class="modal-body">
                    <!-- Retrieve Message -->
                    <p class="font-weight-bold">Are you sure you want to restore this activity?</p>
                    <p class="text-muted">This activity will be restored and moved back to the active activities list.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin#retrieveArchivedActivity', ['id' => $deletedActivity->id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn hover-action">Restore</button>
                    </form>
                    <form action="{{ Auth::user()->role == 'Admin' ? 
                    route('admin#deleteArchivedActivity', ['engagement_archive_pid' => $deletedActivity->fk_engagements__contact_pid]) : 
                    route('deleteArchiveActivity', ['engagement_archive_pid' => $deletedActivity->fk_engagements__contact_pid]) }}"
                        method="POST">
                        @csrf
                        <button type="submit" class="btn discard-table">Delete Permanently</button>
                    </form>
                    {{-- <form action="{{ route('deleteActivity', ['engagement_pid' => $deletedActivity->fk_engagements__contact_pid]) }}"
                        method="POST">
                        @csrf
                        <button type="submit" class="btn discard-table">Delete Permanently</button>
                    </form> --}}
                </div>
            </div>
        </div>
    </div>
@endforeach