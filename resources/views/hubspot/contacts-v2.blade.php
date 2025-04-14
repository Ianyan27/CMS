@extends('layouts.app')

@section('content')
    <style>
        /* Table Styling */
        .hubspot-table thead th {
            background-color: #7b1438;
            /* Match sidebar */
            color: #ffffff;
        }

        .hubspot-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .hubspot-table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        /* Pagination Styling */

        svg{
            width: 1.5rem;
            height: 1.5rem;
            /* Adjust size of SVG icons */
        }
        .pagination {
            font-size: 0.75rem;
            /* smaller text size */
        }

        .pagination .page-link {
            padding: 0.25rem 0.5rem;
            /* smaller padding */
            font-size: 0.75rem;
            /* reduce font size */
            line-height: 1.2;
            color: #7b1438;
            border-color: #7b1438;
        }

        .pagination .page-item.active .page-link {
            background-color: #7b1438;
            border-color: #7b1438;
            color: #fff;
        }

        .pagination .page-link:hover {
            background-color: #a3194e;
            color: #fff;
        }
    </style>

    <div class="container mt-4">
        <h2 class="mb-4">HubSpot Contacts V2</h2>

        @if ($contacts->isEmpty())
            <div class="alert alert-info">
                No contacts found in the database.
            </div>
        @else
            <div class="table-responsive">
                <table class="table hubspot-table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Hubspot ID</th>
                            <th>Country</th>
                            <th>Country From</th>
                            <th>Business Unit</th>
                            <th>Ad Channel</th>
                            <th>Your Specialization</th>
                            <th>Campaign Group</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contacts as $contact)
                            <tr>
                                <td>{{ $contact->hubspot_id }}</td>
                                <td>{{ $contact->country }}</td>
                                <td>{{ $contact->country_from }}</td>
                                <td>{{ $contact->business_unit }}</td>
                                <td>{{ $contact->ad_channel }}</td>
                                <td>{{ $contact->your_specialization }}</td>
                                <td>{{ $contact->campaign_group }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $contacts->links() }}
            </div>
        @endif
    </div>
@endsection
