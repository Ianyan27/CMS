@section('title', 'User Listing Page')

@extends('layouts.app')

@section('content')
    <div class="table-title d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <h2 class="ml-3 mb-2 font-educ"><strong>User Listing Page</strong></h2>
        </div>
        <div class="search-box d-flex align-items-center mr-3 mb-2">
            <input type="search" class="form-control mr-1" placeholder="Search..." id="search-input" aria-label="Search">
            <button class="btn hover-action mx-1" type="submit" data-toggle="tooltip" title="Search">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
    </div>
    <table class="table table-hover mt-2">
        <thead class="font-educ text-left">
            <tr>
                <th class="h5" scope="col">No #</th>
                <th class="h5" scope="col" id="name-header">Name
                    <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-name" 
                        onclick="sortTable('name', 'asc'); toggleSort('sortDown-name', 'sortUp-name')"></i>
                    <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-name" 
                        onclick="sortTable('name', 'desc'); toggleSort('sortUp-name', 'sortDown-name')" style="display: none;"></i>
                </th>
                <th class="h5" scope="col" id="email-header">Email
                    <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-email" 
                        onclick="sortTable('email', 'asc'); toggleSort('sortDown-email', 'sortUp-email')"></i>
                    <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-email" 
                        onclick="sortTable('email', 'desc'); toggleSort('sortUp-email', 'sortDown-email')" style="display: none;"></i>
                </th>
                <th class="h5" scope="col" id="role-header">Role 
                    <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-role" 
                        onclick="sortTable('role', 'asc'); toggleSort('sortDown-role', 'sortUp-role')"></i>
                    <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-role" 
                        onclick="sortTable('role', 'desc'); toggleSort('sortUp-role', 'sortDown-role')" style="display: none;"></i>
                </th>                
                <th class="h5" scope="col">Profile </i></th>
                <th class="h5" scope="col">BU</i></th>
                <th class="h5" scope="col">Country</th>
                <th class="h5" scope="col">Status </i></th>
                <th class="h5" scope="col">Actions</th>
            </tr>
        </thead>
        <tbody class="text-left bg-row">
            <tr>
                <td>1</td>
                <td>John Doe</td>
                <td>john.doe@lithan.com</td>
                <td>Admin</td>
                <td><img src="{{ url('/images/user-1.jpg') }}" alt="Sales Agent Image" class="img-fluid rounded-circle"
                        style="max-width: 50px;"></td>
                <td>Sales and Marketting</td>
                <td>Philippines</td>
                <td><span style="font-size: 1.5rem;" class="p-2 rounded" data-toggle="tooltip" title="Active"><i
                            class="active fa-solid fa-circle-check"></i></span></td>
                <td>
                    <a href="#" class="btn hover-action" data-toggle="tooltip" title="Edit"><i
                            class="fa-solid fa-pen-to-square"></i></a>
                    <button type="button" class="btn hover-action" data-toggle="modal" data-target="#exampleModal">
                        <i class="fa-solid fa-trash "></i>
                    </button>
                </td>
            </tr>
            <tr>
                <td>2</td>
                <td>Jane Smith</td>
                <td>jane.smith@lithan.com</td>
                <td>Sales Agent</td>
                <td><img src="{{ url('/images/user-2.jpg') }}" alt="Sales Agent Image" class="img-fluid rounded-circle"
                        style="max-width: 50px;"></td>
                <td>Sales and Marketing</td>
                <td>Philippines</td>
                <td><span style="font-size: 1.5rem;" class="p-2 rounded" data-toggle="tooltip" title="Active"><i
                            class="active fa-solid fa-circle-check"></i></span></td>
                <td>
                    <a href="#" class="btn hover-action"><i class="fa-solid fa-pen-to-square"></i></a>
                    <button type="button" class="btn hover-action" data-toggle="modal" data-target="#exampleModal">
                        <i class="fa-solid fa-trash "></i>
                    </button>
                </td>
            </tr>
            <tr>
                <td>3</td>
                <td>Michael Johnson</td>
                <td>michael.johnson@lithan.com</td>
                <td>Admin</td>
                <td><img src="{{ url('/images/user-3.jpg') }}" alt="Sales Agent Image" class="img-fluid rounded-circle"
                        style="max-width: 50px;"></td>
                <td>Product Development</td>
                <td>Philippines</td>
                <td><span style="font-size: 1.5rem;color:rgba(70, 70, 70, 0.623);" class="p-2 rounded" data-toogle="tooltip"
                        title="Inactive"><i class="fa-solid fa-circle-xmark"></i><span></td>
                <td>
                    <a href="#" class="btn hover-action" data-toggle="tooltip" title="Edit"><i
                            class="fa-solid fa-pen-to-square"></i></a>
                    <button type="button" class="btn hover-action" data-toggle="modal" data-target="#exampleModal">
                        <i class="fa-solid fa-trash "></i>
                    </button>
                </td>
            </tr>
            <tr>
                <td>4</td>
                <td>Emily Davis</td>
                <td>emily.davis@lithan.com</td>
                <td>Sales Agent</td>
                <td><img src="{{ url('/images/user-4.jpg') }}" alt="Sales Agent Image" class="img-fluid rounded-circle"
                        style="max-width: 50px;"></td>
                <td>Finance and Accounting</td>
                <td>Philippines</td>
                <td><span style="font-size: 1.5rem;" class="p-2 rounded" data-toggle="tooltip" title="Active"><i
                            class="active fa-solid fa-circle-check"></i></span></td>
                <td>
                    <a href="#" class="btn hover-action" data-toggle="tooltip" title="Edit"><i
                            class="fa-solid fa-pen-to-square"></i></a>
                    <button type="button" class="btn hover-action" data-toggle="modal" data-target="#exampleModal">
                        <i class="fa-solid fa-trash "></i>
                    </button>
                </td>
            </tr>
            <tr>
                <td>5</td>
                <td>Chris Brown</td>
                <td>chris.brown@lithan.com</td>
                <td>BUH</td>
                <td><img src="{{ url('/images/user-5.jpg') }}" alt="Sales Agent Image" class="img-fluid rounded-circle"
                        style="max-width: 50px;"></td>
                <td>Human Resources</td>
                <td>Philippines</td>
                <td><span style="font-size: 1.5rem;color:rgba(70, 70, 70, 0.623);" class="p-2 rounded"
                        data-toogle="tooltip" title="Inactive"><i class="fa-solid fa-circle-xmark"></i><span></td>
                <td>
                    <a href="#" class="btn hover-action" data-toggle="tooltip" title="Edit"><i
                            class="fa-solid fa-pen-to-square"></i></a>
                    <button type="button" class="btn hover-action" data-toggle="modal" data-target="#exampleModal">
                        <i class="fa-solid fa-trash "></i>
                    </button>
                </td>
            </tr>
            <tr>
                <td>6</td>
                <td>Amy Wilson</td>
                <td>amy.wilson@lithan.com</td>
                <td>Sales Agent</td>
                <td><img src="{{ url('/images/user-6.jpg') }}" alt="Sales Agent Image" class="img-fluid rounded-circle"
                        style="max-width: 50px;"></td>
                <td>It and Support</td>
                <td>Philippines</td>
                <td><span style="font-size: 1.5rem;" class="p-2 rounded" data-toggle="tooltip" title="Active"><i
                            class="active fa-solid fa-circle-check"></i></span></td>
                <td>
                    <a href="#" class="btn hover-action" data-toggle="tooltip" title="Edit"><i
                            class="fa-solid fa-pen-to-square"></i></a>
                    <button type="button" class="btn hover-action" data-toggle="modal" data-target="#exampleModal">
                        <i class="fa-solid fa-trash "></i>
                    </button>
                </td>
            </tr>
            <tr>
                <td>7</td>
                <td>David White</td>
                <td>david.white@lithan.com</td>
                <td>Sales Agent</td>
                <td><img src="{{ url('/images/user-7.jpg') }}" alt="Sales Agent Image" class="img-fluid rounded-circle"
                        style="max-width: 50px;"></td>
                <td>Customer Service</td>
                <td>Philippines</td>
                <td><span style="font-size: 1.5rem;color:rgba(70, 70, 70, 0.623);" class="p-2 rounded"
                        data-toogle="tooltip" title="Inactive"><i class="fa-solid fa-circle-xmark"></i><span></td>
                <td>
                    <a href="#" class="btn hover-action" data-toggle="tooltip" title="Edit"><i
                            class="fa-solid fa-pen-to-square"></i></a>
                    <button type="button" class="btn hover-action" data-toggle="modal" data-target="#exampleModal">
                        <i class="fa-solid fa-trash "></i>
                    </button>
                </td>
            </tr>
            <tr>
                <td>8</td>
                <td>Laura Martinez</td>
                <td>laura.martinez@lithan.com</td>
                <td>BUH</td>
                <td><img src="{{ url('/images/user-8.jpg') }}" alt="Sales Agent Image" class="img-fluid rounded-circle"
                        style="max-width: 50px;"></td>
                <td>Legal and Compliance</td>
                <td>Philippines</td>
                <td><span style="font-size: 1.5rem;" class="p-2 rounded" data-toggle="tooltip" title="Active"><i
                            class="active fa-solid fa-circle-check"></i></span></td>
                <td>
                    <a href="#" class="btn hover-action" data-toggle="tooltip" title="Edit"><i
                            class="fa-solid fa-pen-to-square"></i></a>
                    <button type="button" class="btn hover-action" data-toggle="modal" data-target="#exampleModal">
                        <i class="fa-solid fa-trash "></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
    <div aria-label="Page navigation example " class="paginationContainer">
        <ul class="pagination justify-content-center">
            <!-- Previous Button -->
            <li class="page-item ">
                <a class="page-link font-educ" href="#" aria-label="Previous">&#60;</a>
            </li>

            <!-- First Page Button -->
            {{-- @if ($contacts->currentPage() > 3) --}}
                <li class="page-item">
                    <a class="page-link font-educ" href="#">1</a>
                </li>
                <li class="page-item">
                    <a class="page-link font-educ" href="#">2</a>
                </li>
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            {{-- @endif --}}

            <!-- Middle Page Buttons -->
            {{-- @for ($i = max($contacts->currentPage() - 1, 1); $i <= min($contacts->currentPage() + 1, $contacts->lastPage()); $i++) --}}
                <li class="page-item">
                    <a class="page-link font-educ" href="#"></a>
                </li>
            {{-- @endfor --}}

            <!-- Last Page Button -->
            {{-- @if ($contacts->currentPage() < $contacts->lastPage() - 2) --}}
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
                <li class="page-item">
                    <a class="page-link font-educ"
                        href="#"></a>
                </li>
                <li class="page-item">
                    <a class="page-link font-educ"
                        href="#"></a>
                </li>
            {{-- @endif --}}

            <!-- Next Button -->
            <li class="page-item">
                <a class="page-link font-educ" href="#" aria-label="Next">&#62;</a>
            </li>
        </ul>
    </div>
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
@endsection
