@extends('layouts.app')

@section('title', 'Contacts Detail Page')

@section('content')
    <div class="table-title d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <h2 class="ml-3 mb-2">Contacts Detail Page</h2>
            <a href="#" class="btn edit-button color-white ml-2 mb-2">Edit Contact</a>
        </div>
    </div>
    <div class="contact-detail-container">
        <div class="contact-info">
            <h4>Contact Information</h4>
            <ul>
                <li><strong>Name:</strong> John Doe</li>
                <li><strong>Email:</strong> john.doe@example.com</li>
                <li><strong>Phone:</strong> 123-456-7890</li>
                <li><strong>Address:</strong> 123 Main St, Anytown, USA 12345</li>
            </ul>
        </div>
        <div class="contact-notes">
            <h4>Contact Notes</h4>
            <ul>
                <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li>
                <li>Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</li>
                <li>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</li>
            </ul>
        </div>
    </div>
@endsection
