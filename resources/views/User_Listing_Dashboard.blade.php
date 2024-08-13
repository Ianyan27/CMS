@section('title', 'User Listing Dashboard')

@extends('layouts.app')

@section('content')
<div class="table-container">
    <div class="table-title d-flex justify-content-between align-items-center mb-3">
        <h2>Users</h2>
        <a href="#" class="btn btn-primary">+</a>
    </div>
    <table class="table table-striped table-hover">
        <thead class="bg-educ">
            <tr>
                <th scope="col">No</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Role</th>
                <th scope="col">Img</th>
                <th scope="col">Assigned at</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>John Doe</td>
                <td>john.doe@example.com</td>
                <td>Admin</td>
                <td><img src="path_to_img" alt="User Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                <td>2024-08-14</td>
                <td>
                    <a href="#" class="btn btn-sm btn-warning">Edit</a>
                    <a href="#" class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
            <!-- More rows as needed -->
        </tbody>
    </table>
</div>

@endsection