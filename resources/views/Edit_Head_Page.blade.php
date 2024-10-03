@section('title', 'Edit BUH User Page')

@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit User: {{ $editUser->name }}</h2>
        <form action="{{ route('user#update-user', $editUser->id) }}" method="POST">
            @csrf
            <input type="hidden" name="userId" value="{{ $editUser->id }}">
            <div class="form-group">
                <label for="editName">Name</label>
                <input type="text" class="form-control" id="editName" name="name" value="{{ old('name', $editUser->name) }}" required>
            </div>
            <div class="form-group">
                <label for="editEmail">Email</label>
                <input type="email" class="form-control" id="editEmail" name="email" value="{{ old('email', $editUser->email) }}" required>
            </div>
            <div class="form-group">
                <label for="editRole">Role</label>
                <select name="role" id="editRole" class="form-control" required>
                    <option value="BUH" {{ $editUser->role == 'BUH' ? 'selected' : '' }}>Business Unit Head</option>
                    <option value="Other" {{ $editUser->role == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
@endsection
