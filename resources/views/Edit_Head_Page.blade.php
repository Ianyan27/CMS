@extends('layouts.app')

@section('title', 'Edit BUH User Page')

@section('content')
    <div class="container">
        <h2>Edit User: {{ $editUser->name }}</h2>

        <form action="{{ route('head.update-user', $editUser->id) }}" method="POST">
            @csrf
            @method('PUT') <!-- Specify the PUT method -->

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

            <div class="form-group">
                <label for="bu_id">Business Unit</label>
                <select name="bu_id" id="bu_id" class="form-control" required>
                    @foreach ($businessUnits as $bu)
                        <option value="{{ $bu->id }}" {{ $bu->id == $editUser->bu_id ? 'selected' : '' }}>{{ $bu->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="country_id">Country</label>
                <select name="country_id" id="country_id" class="form-control" required>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}" {{ $country->id == $editUser->country_id ? 'selected' : '' }}>{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="nationality">Nationality</label>
                <input type="text" class="form-control" id="nationality" name="nationality" value="{{ old('nationality', $editUser->nationality) }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
@endsection
