@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container">
        <!-- Page Heading -->
        <div class="row mb-4">
            <div class="col-md-12">
                <h1 class="page-title">Dashboard</h1>
            </div>
        </div>

        <!-- Overview Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <a href="#" class="card text-center bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Users</h5>
                        <p class="card-text">120</p>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="#" class="card text-center bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Contacts</h5>
                        <p class="card-text">340</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="row">
            <div class="col-md-12 mb-3">
                <h2 class="section-title">Recent Activities</h2>
                <ul class="list-group border-educ">
                    <li class="list-group-item">User John Doe added a new contact.</li>
                    <li class="list-group-item">User Jane Smith updated her profile.</li>
                    <li class="list-group-item">New user registration: Alice Johnson.</li>
                </ul>
            </div>
        </div>
    </div>
@endsection
