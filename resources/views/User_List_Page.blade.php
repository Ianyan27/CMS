@section('title', 'User Listing Page')

@extends('layouts.app')
@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="container-max-height">
        <div class="d-flex align-items-center my-3">
            <h2 class="mt-2 ml-2 headings">User Listing Page</h2>
            <div class="mx-3">
                <button class="btn hover-action mx-1 position-button" type="button" data-toggle="modal" data-target="#addUserModal">
                    <i class="fa-solid fa-square-plus"></i>
                </button>
            </div>
        </div>        
        <div class="table-container">
            <table class="table table-hover mt-2">
                <thead class="text-left font-educ">
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
                        <th scope="col" id="role-header">Role
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-role"
                                onclick="sortTable('role', 'asc'); toggleSort('sortDown-role', 'sortUp-role')"></i>
                            <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-role"
                                onclick="sortTable('role', 'desc'); toggleSort('sortUp-role', 'sortDown-role')"
                                style="display: none;"></i>
                        </th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-left bg-row fonts">
                    @foreach ($userData as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role }}</td>
                            <td>
                                <a class="btn hover-action" data-toggle="modal" data-target="#editUserModal{{ $user->id }}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <a class="btn hover-action" data-toggle="modal" data-target="#deleteUserModal{{ $user->id }}">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div aria-label="Page navigation example " class="paginationContainer">
                <ul class="pagination justify-content-center">
                    <!-- Previous Button -->
                    <li class="page-item {{ $userData->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link font-educ" href="{{ $userData->previousPageUrl() }}"
                            aria-label="Previous">&#60;</a>
                    </li>
                    <!-- First Page Button -->
                    @if ($userData->currentPage() > 3)
                        <li class="page-item">
                            <a class="page-link font-educ" href="{{ $userData->url(1) }}">1</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link font-educ" href="{{ $userData->url(2) }}">2</a>
                        </li>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                    <!-- Middle Page Buttons -->
                    @for ($i = max($userData->currentPage() - 1, 1); $i <= min($userData->currentPage() + 1, $userData->lastPage()); $i++)
                        <li class="page-item {{ $i == $userData->currentPage() ? 'active' : '' }}">
                            <a class="page-link font-educ {{ $i == $userData->currentPage() ? 'active-bg' : '' }}"
                                href="{{ $userData->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor
                    <!-- Last Page Button -->
                    @if ($userData->currentPage() < $userData->lastPage() - 2)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                        <li class="page-item">
                            <a class="page-link font-educ"
                                href="{{ $userData->url($userData->lastPage() - 1) }}">{{ $userData->lastPage() - 1 }}</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link font-educ"
                                href="{{ $userData->url($userData->lastPage()) }}">{{ $userData->lastPage() }}</a>
                        </li>
                    @endif
                    <!-- Next Button -->
                    <li class="page-item {{ !$userData->hasMorePages() ? 'disabled' : '' }}">
                        <a class="page-link font-educ" href="{{ $userData->nextPageUrl() }}" aria-label="Next">&#62;</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
        <!-- Edit User Modal -->
        @foreach ($userData as $user)
            <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1"
                aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content rounded-0">
                        <div class="modal-header d-flex justify-content-between align-items-center"
                        style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                        border:none;border-top-left-radius: 0; border-top-right-radius: 0;">
                            <h5 class="modal-title" id="editUserModalLabel{{ $user->id }}">
                                <strong style="color: #91264c">Edit User</strong>
                            </h5>
                            <img src="{{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png') }}" alt="Company Logo"
                                style="height: 30px;">
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('user#update-user', $user->id) }}" method="POST">
                                @csrf
                                <div class="row">
                                    <!-- Left Column -->
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="font-educ" for="editName{{ $user->id }}">Name</label>
                                            <input type="text" class="form-control fonts"
                                                id="editName{{ $user->id }}" name="name"
                                                value="{{ $user->name }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-educ" for="editEmail{{ $user->id }}">Email</label>
                                            <input type="email" class="form-control fonts"
                                                id="editEmail{{ $user->id }}" name="email"
                                                value="{{ $user->email }}" readonly>
                                        </div>
                                    </div>
                                    <!-- Right Column -->
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="font-educ" for="editRole{{ $user->id }}">Role</label>
                                            <select name="role" id="editRole{{ $user->id }}" class="form-control fonts" required>
                                                @if (Auth::user()->role == 'Admin')
                                                    <!-- Admin can view and select all roles -->
                                                    <option value="Admin" {{ 
                                                    $user->role == 'Admin' ? 'selected' : '' 
                                                    }}>Admin</option>
                                                    <option value="BUH" {{ 
                                                    $user->role == 'BUH' ? 'selected' : '' 
                                                    }}>Business Unit Head</option>
                                                    <option value="" {{ 
                                                        $user->role == '' ? 'selected' : '' 
                                                    }}>Not Assigned</option>
                                                @elseif (Auth::user()->role == 'BUH')
                                                    <!-- BUH can only view and select Sales Agent -->
                                                    <option value="Sales_Agent" {{ 
                                                    $user->role == 'Sales_Agent' ? 'selected' : ''
                                                    }}>Sales Agent</option>
                                                    <option value="" {{ 
                                                        $user->role == 'null' ? 'selected' : '' 
                                                    }}>Not Assigned</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer" style="border: none">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn" style="background: #91264c; color:white;">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <!-- Delete User Modal -->
        @foreach ($userData as $user)
            <div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1"
                aria-labelledby="deleteUserModalLabel{{ $user->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-center">
                        <div class="icon-container mx-auto">
                            <i class="fa-solid fa-trash"></i>
                        </div>
                        <div class="modal-header border-0">
                        </div>
                        <div class="modal-body">
                            <p class="">You are about to delete this User List</p>
                            <p class="text-muted">This will delete the user from your list.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <!-- Update the form action to point to your delete route -->
                            <form action="{{ route('user#delete-user', $user->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <!-- Modal for Adding New User -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-0">
                    <div class="modal-header"
                        style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                        border:none;border-top-left-radius: 0; border-top-right-radius: 0;">
                        <h5 class="modal-title" id="addUserModalLabel" style="color: #91264c;">Create New User</h5>
                    </div>
                    <div class="modal-body" style="color: #91264c">
                        <form action="{{ route('user#save-user') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name') }}" required>
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ old('email') }}" required>
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <input type="hidden" class="form-control" id="password" name="password"
                                    value="creatingtestaccount" readonly>
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <input type="hidden" class="form-control" id="password_confirmation"
                                    name="password_confirmation" value="creatingtestaccount" readonly>
                            </div>
                            <div class="modal-footer" style="border: none">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary"
                                    style="background: #91264c; color: white;">Create User</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
