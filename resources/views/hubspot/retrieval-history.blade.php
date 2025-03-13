@extends('layouts.app')
@section('content')
    
    <div class="container py-4">
        <h1 class="mb-4">HubSpot Contact Retrieval History</h1>
        
        <div class="card mb-4">
            <div class="card-header">
                <h5>Retrieval Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title">Total Retrievals</h5>
                                <h2>{{ $retrievals->count() }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title">Total Contacts Retrieved</h5>
                                <h2>{{ number_format($retrievals->sum('retrieved_count')) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title">Average Batch Size</h5>
                                <h2>{{ number_format($retrievals->average('retrieved_count'), 0) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5>History Log</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Contacts Retrieved</th>
                                <th>Retrieved At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($retrievals as $retrieval)
                                <tr>
                                    <td>{{ $retrieval->id }}</td>
                                    <td>{{ $retrieval->start_date->format('Y-m-d H:i:s') }}</td>
                                    <td>{{ $retrieval->end_date->format('Y-m-d H:i:s') }}</td>
                                    <td>{{ number_format($retrieval->retrieved_count) }}</td>
                                    <td>{{ $retrieval->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    {{ $retrievals->links() }}
                </div>
            </div>
        </div>
        
        <div class="text-end mt-3">
            <a href="{{ route('admin#hubspot-dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
@endsection