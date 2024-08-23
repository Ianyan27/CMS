@extends('layouts.app')

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
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Role</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody class="text-left bg-row">
                @foreach($userData as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>
                        <!-- Trigger Edit Modal -->
                        <button type="button" class="btn hover-action" data-toggle="modal" data-target="#editUserModal{{ $user->id }}">
                            Edit
                        </button>
                        <!-- Trigger Delete Modal -->
                        <button type="button" class="btn hover-action" data-toggle="modal" data-target="#exampleModal">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>

                <!-- Edit User Modal -->
                <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editUserModalLabel{{ $user->id }}">Edit User</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('user#update-user', $user->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="userId" value="{{ $user->id }}">
                                    <div class="form-group">
                                        <label for="editName{{ $user->id }}">Name</label>
                                        <input type="text" class="form-control" id="editName{{ $user->id }}" name="name" value="{{ $user->name }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="editEmail{{ $user->id }}">Email</label>
                                        <input type="email" class="form-control" id="editEmail{{ $user->id }}" name="email" value="{{ $user->email }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="editRole{{ $user->id }}">Role</label>
                                        <select name="role" id="editRole{{ $user->id }}" class="form-control" required>
                                            <option value="User" {{ $user->role == 'User' ? 'selected' : '' }}>User</option>
                                            <option value="Sales_Agent" {{ $user->role == 'Sales_Agent' ? 'selected' : '' }}>Sales Agent</option>
                                            <option value="BUH" {{ $user->role == 'BUH' ? 'selected' : '' }}>Business Unit Head</option>
                                            <!-- Add more options as needed -->
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Delete User Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content text-center">
                            <div class="icon-container mx-auto">
                                <i class="fa-solid fa-trash"></i>
                            </div>
                            <div class="modal-header border-0">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span style="font-size:2.5rem;" aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p class="">You are about to delete this User List</p>
                                <p class="text-muted">This will delete your user from your list.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>        
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
