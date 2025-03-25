@extends('layouts.app')

@section('content')
    {{-- Inline CSS for quick styling tweaks --}}
    <style>
        .headings {
            font-weight: 700;
            font-size: 1.75rem;
            margin-bottom: 1rem;
        }

        .card-title {
            font-weight: 600;
        }

        .card {
            border-radius: 6px;
        }

        .alert-info strong {
            font-weight: 600;
        }

        .stats-card {
            text-align: center;
        }

        .stats-card h2 {
            margin-top: 0.5rem;
        }

        .row-gap-3>.row+.row {
            margin-top: 1rem;
        }
    </style>
    {{-- Flash messages --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif

    @if (session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @php
        $summary = session('import_summary') ?? null;
    @endphp

    @if ($summary)
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Show the modal automatically
                var importModal = new bootstrap.Modal(document.getElementById('importSummaryModal'));
                importModal.show();
            });
        </script>
    @endif

    <!-- Modal -->
    <div class="modal fade" id="importSummaryModal" tabindex="-1" aria-labelledby="importSummaryLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importSummaryLabel">CSV Import Summary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @php
                        $validCount = $summary['valid_count'] ?? 0;
                        $invalidCount = $summary['invalid_count'] ?? 0;
                        $duplicateCount = $summary['duplicate_count'] ?? 0;
                        $removedCount = $summary['removed_count'] ?? 0;
                    @endphp

                    <p>Valid Contacts: {{ $validCount }}</p>
                    <p>
                        <a href="{{ route('contacts.download.valid') }}" class="btn btn-sm btn-primary"
                            @if ($validCount === 0) disabled @endif>
                            Download Valid Contacts
                        </a>
                    </p>

                    <p>Invalid Contacts: {{ $invalidCount }}</p>
                    <p>
                        <a href="{{ route('contacts.download.invalid') }}" class="btn btn-sm btn-danger"
                            @if ($invalidCount === 0) disabled @endif>
                            Download Invalid Contacts
                        </a>
                    </p>

                    <p>Duplicate Contacts: {{ $duplicateCount }}</p>
                    <p>
                        <a href="{{ route('contacts.download.duplicate') }}" class="btn btn-sm btn-danger"
                            @if ($duplicateCount === 0) disabled @endif>
                            Download Duplicate Contacts
                        </a>
                    </p>

                    <!-- New: Removed Contacts -->
                    <p>Removed Contacts: {{ $removedCount }}</p>
                    <p>
                        <a href="{{ route('contacts.download.removed') }}" class="btn btn-sm btn-warning"
                            @if ($removedCount === 0) disabled @endif>
                            Download Removed Contacts
                        </a>
                    </p>
                </div>
                <div class="modal-footer">
                    <!-- Cancel just closes the modal (and leaves session data intact).
                             If you want to clear session, create a route to do so. -->
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

                    <!-- Sync button calls the route that inserts valid records into DB -->
                    <form action="{{ route('contacts.sync.valid') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">Sync Valid Contacts</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <div class="header d-flex align-items-center justify-content-between">
        <h2 class="mt-3 headings" style="width: 50%;">HubSpot Contact Sync Dashboard</h2>
        <div class="header-buttons d-flex align-items-center justify-content-around" style="width: 50%;">
            <!-- Export Active Contacts Button -->
            <form action="{{ route('export.active.contacts') }}" method="GET">
                @csrf
                <button type="submit" class="btn hover-action">Export Hubspot Contacts</button>
            </form>
            <a href="{{ route('admin#hubspot-history') }}" class="btn hover-action">
                View Sync History
            </a>
            <a href="{{ route('admin#hubspot-retrieval-history') }}" class="btn hover-action">
                View Retrieval History
            </a>
        </div>
    </div>

    <!-- CSV Import Form -->
    <div class="row mt-4 gy-4">
        <div class="col-lg-12">
            <!-- Import Form -->
            <form action="{{ route('contacts.import.preview') }}" method="POST" enctype="multipart/form-data"
                class="me-3">
                @csrf
                <div class="row">
                    <div class="col-lg-12 mb-3 d-flex justify-content-between">
                        <label for="csvFile" class="form-label">Import CSV File</label>
                        <div>
                            <a href="{{ route('csv.template.download') }}" class="btn hover-action">Download Template</a>
                        </div>
                    </div>
                    <div class="col-lg-12 mb-3">
                        <input class="form-control" type="file" id="csvFile" name="file" accept=".csv">
                    </div>
                </div>
                <button type="submit" class="btn hover-action">Import Contacts</button>
            </form>
        </div>
    </div>

    {{-- Batch Processing Info --}}
    <div class="row mt-4 gy-3">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5>Batch Processing Information</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-secondary">
                        <p><strong>How it works:</strong></p>
                        <ul>
                            <li>Each sync retrieves contacts within the date range shown above.</li>
                            <li>Contacts are processed in batches of 1,000.</li>
                            <li>Partial batches are kept in buffer for the next sync.</li>
                            <li>The end date of each successful sync becomes the start date for the next sync.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 stats-card">
                <div class="card-body">
                    <h5 class="card-title">Total Contacts</h5>
                    <h2 id="total-contacts">{{ number_format($totalContacts) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4 gy-3">
        <div class="col-md-5">
            <div class="card h-100">
                <div class="card-header">
                    <h5>Sync Details</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Current Window Start</dt>
                        <dd class="col-sm-8" id="window-start">
                            {{ $syncStatus->start_window ? $syncStatus->start_window->format('Y-m-d H:i:s') : '-' }}
                        </dd>

                        <dt class="col-sm-4">Current Window End</dt>
                        <dd class="col-sm-8" id="window-end">
                            {{ $syncStatus->end_window ? $syncStatus->end_window->format('Y-m-d H:i:s') : '-' }}
                        </dd>

                        <dt class="col-sm-4">Total Synced</dt>
                        <dd class="col-sm-8" id="total-synced">
                            {{ number_format($syncStatus->total_synced) }}
                        </dd>

                        <dt class="col-sm-4">Total Errors</dt>
                        <dd class="col-sm-8" id="total-errors">
                            {{ number_format($syncStatus->total_errors) }}
                        </dd>

                        <dt class="col-sm-4">Next Scheduled Sync</dt>
                        <dd class="col-sm-8" id="next-sync">
                            @if ($syncStatus->next_sync_timestamp)
                                <span class="text-success">
                                    {{ $syncStatus->next_sync_timestamp->format('Y-m-d H:i:s') }}
                                </span>
                                <small class="text-muted d-block">
                                    {{ $syncStatus->next_sync_timestamp->diffForHumans() }}
                                </small>
                            @else
                                <span class="text-muted">Not scheduled</span>
                                <a href="#" class="btn btn-sm btn-outline-secondary ms-2" data-bs-toggle="modal"
                                    data-bs-target="#scheduleModal">
                                    Schedule Now
                                </a>
                            @endif
                        </dd>
                    </dl>

                    @if ($syncStatus->error_log)
                        <div class="alert alert-danger mt-3">
                            <h6>Error Log</h6>
                            <pre>{{ $syncStatus->error_log }}</pre>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card h-100 stats-card">
                <div class="card-body">
                    <h5 class="card-title">Sync Status</h5>
                    <h2 id="sync-status" class="text-capitalize">{{ $syncStatus->status }}</h2>

                    {{-- Progress bar if running --}}
                    <div id="progress-container" class="mt-2 {{ $syncStatus->status !== 'running' ? 'd-none' : '' }}">
                        <div class="progress">
                            <div id="sync-progress" class="progress-bar" role="progressbar" style="width: 0%;"
                                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                0%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card h-100 stats-card">
                <div class="card-body">
                    <h5 class="card-title">Last Sync</h5>
                    <h2 id="last-sync">
                        {{ $lastSyncDate ? $lastSyncDate->diffForHumans() : 'Never' }}
                    </h2>
                    <small>
                        {{ $lastSyncDate ? $lastSyncDate->format('Y-m-d H:i:s') : '-' }}
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Next Sync Window</h5>
                    <div class="alert alert-info mb-2">
                        <strong>Dates for the next synchronization window:</strong>
                    </div>
                    <dl class="row">
                        <dt class="col-sm-4">Start</dt>
                        <dd class="col-sm-8" id="display-start-date">
                            {{ Carbon\Carbon::parse($nextStartDate)->format('Y-m-d H:i:s') }}
                        </dd>
                        <dt class="col-sm-4">End</dt>
                        <dd class="col-sm-8" id="display-end-date">
                            {{ Carbon\Carbon::parse($endDate)->format('Y-m-d H:i:s') }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    {{-- Manual Sync & Sync by Modified Date --}}
    <div class="row mt-4 gy-3">
        {{-- Manually Trigger Sync --}}
        <div class="col-lg-12">
            <div class="card h-100">
                <div class="card-header">
                    <h5>Trigger Sync</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin#hubspot-sync') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date (Greater Than)</label>
                            <input type="datetime-local" class="form-control" id="start_date" name="start_date"
                                value="{{ Carbon\Carbon::parse($nextStartDate)->format('Y-m-d\TH:i') }}">
                            <div class="form-text">Default is the last sync timestamp</div>
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date (Less Than)</label>
                            <input type="datetime-local" class="form-control" id="end_date" name="end_date"
                                value="{{ Carbon\Carbon::now()->format('Y-m-d\TH:i') }}">
                            <div class="form-text">Default is current time</div>
                        </div>
                        <button type="submit" class="btn hover-action" id="start-sync-btn"
                            {{ $syncStatus->status === 'running' ? 'disabled' : '' }}>
                            <i class="fas fa-sync"></i> Sync Next Batch
                        </button>

                        @if ($syncStatus->status === 'running')
                            <button type="button" class="btn btn-danger" id="cancel-sync-btn">
                                Cancel Sync
                            </button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
        {{-- <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Sync by Modified Date</h5>
                </div>
                <div class="card-body">
                    <p>
                        Use this option to sync contacts based on when they were last modified in HubSpot.
                        This will catch both new contacts and updates to existing contacts.
                    </p>

                    @if ($syncStatus->status === 'running')
                        <div class="alert alert-info">
                            A sync is currently in progress. Please wait for it to complete.
                        </div>
                    @else
                        <form action="{{ route('admin#hubspot-start-modified-sync') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="modified_start_date" class="form-label">Start Date</label>
                                <input type="datetime-local" class="form-control" id="modified_start_date"
                                    name="start_date">
                                <small class="form-text text-muted">
                                    Leave blank to use last sync:
                                    {{ $syncStatus->last_modified_sync_timestamp ?? 'None (will use default)' }}
                                </small>
                            </div>
                            <div class="mb-3">
                                <label for="modified_end_date" class="form-label">End Date</label>
                                <input type="datetime-local" class="form-control" id="modified_end_date"
                                    name="end_date">
                                <small class="form-text text-muted">
                                    Leave blank to use current time
                                </small>
                            </div>
                            <button type="submit" class="btn hover-action">
                                <i class="fas fa-sync"></i> Start Modified Sync
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div> --}}
    </div>

    <div class="row mt-4 gy-3">
        <div class="dashboard-section">
            <h3>CSV Import History</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Uploaded By</th>
                        <th>Uploaded At</th>
                        <th class="text-center">Download</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($csvImports as $import)
                        <tr>
                            <td>{{ $import->file_name }}</td>
                            <td>{{ $import->user_id ? \App\Models\User::find($import->user_id)->name : 'N/A' }}</td>
                            <td>{{ $import->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="text-center">
                                <a href="{{ route('csv.download', $import->id) }}" class="btn hover-action"><i
                                        class="fa-solid fa-download"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    {{-- Schedule Sync Modal --}}
    <div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="scheduleModalLabel">Schedule Next Sync</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin#hubspot-schedule-sync') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="schedule_time" class="form-label">Next Sync Time</label>
                            <input type="datetime-local" class="form-control" id="schedule_time" name="schedule_time"
                                value="{{ Carbon\Carbon::tomorrow()->startOfDay()->format('Y-m-d\TH:i') }}" required>
                            <div class="form-text">Set when the next sync should run automatically.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Schedule Sync</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Scripts (Bootstrap + Polling) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Poll for status updates if sync is running
        let pollingInterval;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function updateStatus() {
            fetch('{{ route('admin#hubspot-status') }}')
                .then(response => response.json())
                .then(data => {
                    // Update status
                    document.getElementById('sync-status').textContent = data.status;

                    // Update progress elements
                    document.getElementById('total-synced').textContent = new Intl.NumberFormat().format(data
                        .total_synced);

                    // Show/hide progress bar
                    const progressContainer = document.getElementById('progress-container');
                    if (data.status === 'running') {
                        progressContainer.classList.remove('d-none');
                        document.getElementById('start-sync-btn').disabled = true;
                        if (!document.getElementById('cancel-sync-btn')) {
                            const cancelBtn = document.createElement('button');
                            cancelBtn.type = 'button';
                            cancelBtn.id = 'cancel-sync-btn';
                            cancelBtn.className = 'btn btn-danger ms-2';
                            cancelBtn.textContent = 'Cancel Sync';
                            cancelBtn.addEventListener('click', cancelSync);
                            document.getElementById('start-sync-btn').after(cancelBtn);
                        }
                    } else {
                        progressContainer.classList.add('d-none');
                        document.getElementById('start-sync-btn').disabled = false;
                        const cancelBtn = document.getElementById('cancel-sync-btn');
                        if (cancelBtn) {
                            cancelBtn.remove();
                        }
                        // Stop polling if sync is not running
                        clearInterval(pollingInterval);
                    }

                    // Update start date for next sync if available
                    if (data.last_sync) {
                        const lastSyncDate = new Date(data.last_sync);
                        const formattedDate = lastSyncDate.toISOString().slice(0, 16);
                        document.getElementById('start_date').value = formattedDate;
                        document.getElementById('display-start-date').textContent = lastSyncDate.toLocaleString();
                    }
                })
                .catch(error => console.error('Error fetching status:', error));
        }

        function cancelSync() {
            if (confirm('Are you sure you want to cancel the current sync?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin#hubspot-cancel') }}';

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;

                form.appendChild(csrfInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Start polling if sync is running
        if (document.getElementById('sync-status').textContent.trim().toLowerCase() === 'running') {
            pollingInterval = setInterval(updateStatus, 5000); // Poll every 5 seconds
        }

        // Setup cancel button event if present
        const cancelBtn = document.getElementById('cancel-sync-btn');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', cancelSync);
        }
    </script>
@endsection
