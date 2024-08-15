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

        <div class="row mb-4">
            <!-- Overview Cards -->
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <a href="#" class="card text-center bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Users</h5>
                                <p class="card-text">120</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="#" class="card text-center bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Contacts</h5>
                                <p class="card-text">340</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="#" class="card text-center bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Sales Agents</h5>
                                <p class="card-text">45</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Graph -->
            <div class="col-md-6">
                <h2 class="section-title">Contacts Per Agent</h2>
                <canvas id="contactsPerAgentChart"></canvas>
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('contactsPerAgentChart').getContext('2d');

        // Hardcoded example data
        const agentNames = ['John Doe', 'Jane Smith', 'Alice Johnson', 'Bob Brown', 'Emily Davis'];
        const contactsPerAgent = [30, 25, 20, 15, 10]; // Example numbers

        // Creating the bar chart
        const contactsPerAgentChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: agentNames,
                datasets: [{
                    label: 'Contacts Managed',
                    data: contactsPerAgent,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: Math.max(...contactsPerAgent) + 10
                    }
                }
            }
        });
    </script>
@endsection
