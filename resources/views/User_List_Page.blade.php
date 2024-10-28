@section('title', 'User Listing Page')

@extends('layouts.app')
@section('content')
    @if (Auth::check() && Auth::user()->role == 'Admin')
        <script>
            $(document).ready(function() {
                $('#successModal').modal('show');
                $('#errorModal').modal('show');
            });
        </script>
        @if (Session::has('success'))
            <!-- Success Modal -->
            <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header"
                            style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                                border:none;border-top-left-radius: 0; border-top-right-radius: 0;">
                            <h5 class="modal-title" id="successModalLabel" style="color: #91264c"><strong>Success</strong>
                            </h5>
                        </div>
                        <div class="modal-body font-educ text-center">
                            {{ Session::get('success') }}
                        </div>
                        <div class="modal-footer" style="border:none;">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                style="background: #91264c; color:white;">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @error('email')
            <!-- Success Modal -->
            <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header"
                            style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                            border:none;border-top-left-radius: 0; border-top-right-radius: 0;">
                            <h5 class="modal-title" id="errorModalLabel" style="color: #91264c"><strong>Error</strong>
                            </h5>
                        </div>
                        <div class="modal-body font-educ text-center">
                            {{$message}}
                        </div>
                        <div class="modal-footer" style="border:none;">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                style="background: #91264c; color:white;">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        @enderror
        <div class="container-max-height">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <h2 class="my-2 mr-3 headings">User Listing Page</h2>
                    <button class="btn hover-action mx-3" type="button" data-toggle="modal" data-target="#addUserModal">
                        <i class="fa-solid fa-square-plus"></i>
                    </button>
                </div>
                <div class="search-box d-flex align-items-center mr-3 mb-2">
                    <input type="search" class="form-control mr-1" placeholder="Search by Email or Name" id="search-input"
                        aria-label="Search">
                    <button class="btn hover-action mx-1" type="submit" data-toggle="tooltip" title="Search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </div>
            <div class="table-container">
                <table class="table table-hover mt-2" id="user-table">
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
                            <th class="position-relative" scope="col">
                                Role
                                <i style="cursor: pointer;" class="fa-solid fa-filter" id="filterIcon"
                                    onclick="toggleFilterRole()"></i>
                                <!-- Filter Container -->
                                <div id="filterStatusContainer" class="filter-popup container rounded-bottom w-auto"
                                    style="display: none;">
                                    <div class="row" style="max-width: 175px;">
                                        <div class="filter-option">
                                            <input class="ml-3" type="checkbox" id="BUH" name="role"
                                                value="Admin" onclick="applyRoleFilter()">
                                            <label for="Admin">Admin</label>
                                        </div>
                                        <div class="filter-option">
                                            <input class="ml-3" type="checkbox" id="BUH" name="role"
                                                value="BUH" onclick="applyRoleFilter()">
                                            <label for="BUH">BUH</label>
                                        </div>
                                        <div class="filter-option">
                                            <input class="ml-3" type="checkbox" id="Sales_Admin" name="role"
                                                value="Sales_Admin" onclick="applyRoleFilter()">
                                            <label for="Sales_Admin">Sale Admin</label>
                                        </div>
                                        <div class="filter-option">
                                            <input class="ml-3" type="checkbox" id="Sales_Agent" name="role"
                                                value="Sales_Agent" onclick="applyRoleFilter()">
                                            <label for="Sales_Agent">Sale Agent</label>
                                        </div>
                                        <div class="filter-option">
                                            <input class="ml-3" type="checkbox" id="Head" name="role"
                                                value="Head" onclick="applyRoleFilter()">
                                            <label for="Head">Head</label>
                                        </div>
                                        <div class="filter-option">
                                            <input class="ml-3" type="checkbox" id="NA" name="role"
                                                value="" onclick="applyRoleFilter()">
                                            <label for="">Not Assigned</label>
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <th scope="col" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-left bg-row fonts">
                        <?php $i = ($userData->currentPage() - 1) * $userData->perPage() + 1; ?>
                        @forelse ($userData as $user)
                            <tr data-role="{{ $user->role }}">
                                <td>{{ $i++ }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if ($user->role == 'Admin')
                                        Admin
                                    @elseif ($user->role == 'Sales_Agent')
                                        Sales Agent
                                    @elseif($user->role == 'BUH')
                                        BUH
                                    @elseif($user->role == 'Head')
                                        Head
                                    @elseif($user->role == 'Sales_Admin')
                                        Sales Admin
                                    @elseif($user->role == ' ')
                                        Not Assigned
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a class="btn hover-action" data-toggle="modal"
                                        data-target="#editUserModal{{ $user->id }}">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    {{-- <a class="btn hover-action" data-toggle="modal"
                                        data-target="#deleteUserModal{{ $user->id }}">
                                        <i class="fa-solid fa-trash"></i>
                                    </a> --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No User Found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div aria-label="Page navigation example" class="paginationContainer">
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
                            <form action="{{ route('admin#update-user', $user->id) }}" method="POST">
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
                                            <select name="role" id="editRole{{ $user->id }}"
                                                class="form-control fonts dropdown-role" required>
                                                @if (Auth::user()->role == 'Admin')
                                                    <!-- Admin can view and select all roles -->
                                                    <option value="Admin" {{ $user->role == 'Admin' ? 'selected' : '' }}>
                                                        Admin</option>
                                                    {{-- <option value="BUH" {{ $user->role == 'BUH' ? 'selected' : '' }}>
                                                        Business Unit Head</option>
                                                    <option value="Sales_Agent"
                                                        {{ $user->role == 'Sales_Agent' ? 'selected' : '' }}>Sales Agent
                                                    </option> --}}
                                                    <option value="Head" {{ $user->role == 'Head' ? 'selected' : '' }}>
                                                        Head</option>
                                                    <option value="Sales_Admin"
                                                        {{ $user->role == 'Sales_Admin' ? 'selected' : '' }}>Sale Admin
                                                    </option>
                                                    <option value="NA" {{ $user->role == ' ' ? 'selected' : ' ' }}>Not
                                                        Assigned</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <!-- BUH Inputs -->
                                    {{-- <div class="col-md-12" id="buhDiv">
                                        <div class="form-group">
                                            <label for="headList" class="font-educ">Head</label>
                                            <select name="headList" id="headList" class="form-control fonts">
                                                <option value="">Select an Option</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="buhCountry" class="font-educ">Country</label>
                                            <select name="buhCountry" id="buhCountry" class="form-control fonts">
                                                <option value="">Select an Option</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Sale Agent Inputs -->
                                    <div class="col-md-12" id="saleAgentDiv">
                                        <div class="form-group">
                                            <label for="selectBUH" class="font-educ">BUH</label>
                                            <select name="selectBUH" id="selectBUH" class="form-control fonts">
                                                <option value="">Select an Option</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="saleAgentCountry" class="font-educ">Country</label>
                                            <select name="saleAgentCountry" id="saleAgentCountry"
                                                class="form-control fonts">
                                                <option value="">Select an Option</option>
                                            </select>
                                        </div>
                                    </div>--}}
                                </div> 
                                <div class="modal-footer" style="border: none">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn"
                                        style="background: #91264c; color:white;">Save</button>
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
                            <form action="{{ route('admin#delete-user', ['id' => $user->id]) }}" method="POST">
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
                        <form id="addUserForm" action="{{ route('admin#save-new-user') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name') }}" minlength="3" maxlength="50" required>

                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ old('email') }}" required>
                                <small class="text-danger" id="emailError"></small>

                            </div>
                            <div class="form-group">
                                <input type="hidden" class="form-control" id="password" name="password"
                                    value="creatingtestaccount" readonly>

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
    @else
        <div class="alert alert-danger text-center mt-5">
            <strong>Access Denied!</strong> You do not have permission to view this page.
        </div>
    @endif
    <!-- JavaScript for Validation -->
    <script src=" {{ asset('js/add_agent_validation.js') }} "></script>
    <script src=" {{ asset('js/sort.js') }} "></script>
    <script src=" {{ asset('js/search_input.js') }}"></script>
    <script src=" {{ asset('js/filter_role.js') }} "></script>
@endsection
