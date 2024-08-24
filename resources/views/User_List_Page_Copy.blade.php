@extends('layouts.app')
@extends('layouts.Delete_Prompt_Modal')
@section('title', 'User Listing Page')

@section('content')
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
<div class="container-max-height">
    <div class="table-title d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <h5 class="ml-3 my-2 headings">User Listing Page</h5>
        </div>
        <div>
            <button type="button" class="btn hover-action" data-toggle="modal" data-target="#addUserModal">
                Add User
            </button>
        </div>
    </div>
    <div class="table-container">
        <table class="table table-hover mt-2">
            <thead class="font-educ text-left">
                <tr>
                    <th scope="col">No #</th>
                    <th scope="col" id="name-header">Name
                        <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-name"
                            onclick="sortTable('name', 'asc'); toggleSort('sortDown-name', 'sortUp-name')"></i>
                        <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-name"
                            onclick="sortTable('name', 'desc'); toggleSort('sortUp-name', 'sortDown-name')"
                            style="display: none;"></i>
                    </th>
                    <th scope="col" id="email-header">Email
                        <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-email"
                            onclick="sortTable('email', 'asc'); toggleSort('sortDown-email', 'sortUp-email')"></i>
                        <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-email"
                            onclick="sortTable('email', 'desc'); toggleSort('sortUp-email', 'sortDown-email')"
                            style="display: none;"></i>
                    </th>
                    <th scope="col">Role</th>
                    <th scope="col">BU</th>
                    <th scope="col">Country</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody class="text-left bg-row fonts">
                @forelse($userData as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>Delivery Management</td>
                    <td>Myanmar</td>
                    <td>
                        <!-- Trigger Edit Modal -->
                        <a class="btn hover-action" data-toggle="modal" data-target="#editUserModal{{ $user->id }}">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <!-- Trigger Delete Modal -->
                        <a style="min-height: 24px;" class="btn hover-action" data-toggle="modal" data-target="#exampleModal">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No Contacts Found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>        
    </div>
    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title" id="editUserModalLabel{{ $user->id }}">
                        <strong>Edit User</strong>
                    </h5>
                    <img src="{{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png') }}" alt="Company Logo" style="height: 30px;">
                </div>
                <div class="modal-body">
                    <form action="{{ route('user#update-user', $user->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-educ" for="editName{{ $user->id }}">Name</label>
                                    <input type="text" class="form-control fonts" id="editName{{ $user->id }}" name="name" value="{{ $user->name }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="font-educ" for="editEmail{{ $user->id }}">Email</label>
                                    <input type="email" class="form-control fonts" id="editEmail{{ $user->id }}" name="email" value="{{ $user->email }}" readonly>
                                </div>
                            </div>
                            <!-- Right Column -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-educ" for="editRole{{ $user->id }}">Role</label>
                                    <select name="role" id="editRole{{ $user->id }}" class="form-control fonts" required>
                                        <option value="User" {{ $user->role == 'User' ? 'selected' : '' }}>User</option>
                                        <option value="Sales_Agent" {{ $user->role == 'Sales_Agent' ? 'selected' : '' }}>Sales Agent</option>
                                        <option value="BUH" {{ $user->role == 'BUH' ? 'selected' : '' }}>Business Unit Head</option>
                                        <!-- Add more options as needed -->
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn" style="background: #91264c; color:white;">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal for Adding New User -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Create New User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('save-user') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="role" id="role" class="form-control" value="User" readonly>
                        </div>
                        <button type="submit" class="btn btn-primary">Create User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection