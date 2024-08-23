@section('title', 'Edit User Page')

@extends('layouts.app')

@section('content')

<form action="{{ route('user#update-user', $editUser->id) }}" method="POST">
    @csrf
    <input type="hidden" name="userId" value="{{ $editUser->id }}">
    <div class="form-group">
        <label for="editName">Name</label>
        <input type="text" class="form-control" id="editName" name="name" value="{{ $editUser->name }}" required>
    </div>
    <div class="form-group">
        <label for="editEmail">Email</label>
        <input type="email" class="form-control" id="editEmail" name="email" value="{{ $editUser->email }}" required>
    </div>
    <div class="form-group">
        <label for="editRole">Role</label>
        <select name="role" id="role" class="form-control" required>
            <option value="User" {{ $editUser->role == 'User' ? 'selected' : '' }}>User</option>
            <option value="Sales_Agent" {{ $editUser->role == 'Sales_Agent' ? 'selected' : '' }}>Sales Agent</option>
            <option value="BUH" {{ $editUser->role == 'BUH' ? 'selected' : '' }}>Business Unit Head</option>
            <!-- Add more options as needed -->
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Save Changes</button>
</form>


@endsection