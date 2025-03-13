@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4">HubSpot Sync History</h1>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Contacts</h5>
                        <h2>{{ number_format($totalContacts) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Last Successful Sync</h5>
                        <h2>{{ $syncStatus->last_successful_sync ? $syncStatus->last_successful_sync->diffForHumans() : 'Never' }}</h2>
                        <p>{{ $syncStatus->last_successful_sync ? $syncStatus->last_successful_sync->format('Y-m-d H:i:s') : '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h5>Recent Contacts</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>HubSpot ID</th>
                                <th>Email</th>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Created in HubSpot</th>
                                <th>Last Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentContacts as $contact)
                                <tr>
                                    <td>{{ $contact->hubspot_id }}</td>
                                    <td>{{ $contact->email }}</td>
                                    <td>{{ $contact->firstname }} {{ $contact->lastname }}</td>
                                    <td>{{ $contact->gender }}</td>
                                    <td>{{ $contact->hubspot_created_at ? $contact->hubspot_created_at->format('Y-m-d H:i:s') : '-' }}</td>
                                    <td>{{ $contact->updated_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="text-end">
            <a href="{{ route('admin#hubspot-dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
@endsection