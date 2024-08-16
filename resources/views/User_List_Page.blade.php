@section('title', 'User Listing Page')

@extends('layouts.app')

@section('content')
    <div class="table-title d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <h2 class="ml-3 mb-2 font"><strong>User Listing Page</strong></h2>
        </div>
        <div class="search-box d-flex align-items-center mr-3 mb-2">
            <input type="search" class="form-control mr-1" placeholder="Search..." id="search-input" aria-label="Search">
            <button class="btn btn-secondary bg-educ mx-1" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </div>
    </div>
    <table class="table table-hover mt-2">
        <thead class="font text-center">
            <tr>
                <th scope="col"><input type="checkbox" name="" id=""></th>
                <th class="h5" scope="col">No #</th>
                <th class="h5" scope="col">Name <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                <th class="h5" scope="col">Email <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                <th class="h5" scope="col">Role <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                <th class="h5" scope="col">Profile </i></th>
                <th class="h5" scope="col">BU</i></th>
                <th class="h5" scope="col">Country</th>
                <th class="h5" scope="col">Status </i></th>
                <th class="h5" scope="col">Actions</th>
            </tr>
        </thead>
        <tbody class="text-center bg-row fonts">
            <tr>
                <td><input type="checkbox" name="" id=""></td>
                <td>1</td>
                <td>John Doe</td>
                <td>john.doe@lithan.com</td>
                <td>Admin</td>
                <td><img src="{{url('/images/user-1.jpg')}}" alt="Sales Agent Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                <td>Sales and Marketting</td>
                <td>Philippines</td>
                <td><span style="font-size: 1.5rem;" class="p-2 rounded" data-toggle="tooltip" title="Active"><i class="active fa-solid fa-circle-check"></i></span></td>
                <td>
                    <a href="#" class="btn hover-action" data-toggle="tooltip" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                    <a href="#" class="btn hover-action" data-toggle="tooltip" title="Delete"><i class="fa-solid fa-trash "></i></a>
                </td>
            </tr>
            <tr>
                <td><input type="checkbox" name="" id=""></td>
                <td>2</td>
                <td>Jane Smith</td>
                <td>jane.smith@lithan.com</td>
                <td>Sales Agent</td>
                <td><img src="{{url('/images/user-2.jpg')}}" alt="Sales Agent Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                <td>Sales and Marketing</td>
                <td>Philippines</td>
                <td><span style="font-size: 1.5rem;" class="p-2 rounded" data-toggle="tooltip" title="Active"><i class="active fa-solid fa-circle-check"></i></span></td>
                <td>
                    <a href="#" class="btn hover-action"><i class="fa-solid fa-pen-to-square"></i></a>
                    <a href="#" class="btn hover-action"><i class="fa-solid fa-trash"></i></a>
                </td>
            </tr>
            <tr>
                <td><input type="checkbox" name="" id=""></td>
                <td>3</td>
                <td>Michael Johnson</td>
                <td>michael.johnson@lithan.com</td>
                <td>Admin</td>
                <td><img src="{{url('/images/user-3.jpg')}}" alt="Sales Agent Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                <td>Product Development</td>
                <td>Philippines</td>
                <td><span style="font-size: 1.5rem;color:rgba(70, 70, 70, 0.623);" class="p-2 rounded" data-toogle="tooltip" title="Inactive"><i class="fa-solid fa-circle-xmark"></i><span></td>
                <td>
                    <a href="#" class="btn hover-action" data-toggle="tooltip" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                    <a href="#" class="btn hover-action" data-toggle="tooltip" title="Delete"><i class="fa-solid fa-trash "></i></a>
                </td>
            </tr>
            <tr>
                <td><input type="checkbox" name="" id=""></td>
                <td>4</td>
                <td>Emily Davis</td>
                <td>emily.davis@lithan.com</td>
                <td>Sales Agent</td>
                <td><img src="{{url('/images/user-4.jpg')}}" alt="Sales Agent Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                <td>Finance and Accounting</td>
                <td>Philippines</td>
                <td><span style="font-size: 1.5rem;" class="p-2 rounded" data-toggle="tooltip" title="Active"><i class="active fa-solid fa-circle-check"></i></span></td>
                <td>
                    <a href="#" class="btn hover-action" data-toggle="tooltip" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                    <a href="#" class="btn hover-action" data-toggle="tooltip" title="Delete"><i class="fa-solid fa-trash "></i></a>
                </td>
            </tr>
            <tr>
                <td><input type="checkbox" name="" id=""></td>
                <td>5</td>
                <td>Chris Brown</td>
                <td>chris.brown@lithan.com</td>
                <td>BUH</td>
                <td><img src="{{url('/images/user-5.jpg')}}" alt="Sales Agent Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                <td>Human Resources</td>
                <td>Philippines</td>
                <td><span style="font-size: 1.5rem;color:rgba(70, 70, 70, 0.623);" class="p-2 rounded" data-toogle="tooltip" title="Inactive"><i class="fa-solid fa-circle-xmark"></i><span></td>
                <td>
                    <a href="#" class="btn hover-action" data-toggle="tooltip" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                    <a href="#" class="btn hover-action" data-toggle="tooltip" title="Delete"><i class="fa-solid fa-trash "></i></a>
                </td>
            </tr>
            <tr>
                <td><input type="checkbox" name="" id=""></td>
                <td>6</td>
                <td>Amy Wilson</td>
                <td>amy.wilson@lithan.com</td>
                <td>Sales Agent</td>
                <td><img src="{{url('/images/user-6.jpg')}}" alt="Sales Agent Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                <td>It and Support</td>
                <td>Philippines</td>
                <td><span style="font-size: 1.5rem;" class="p-2 rounded" data-toggle="tooltip" title="Active"><i class="active fa-solid fa-circle-check"></i></span></td>
                <td>
                    <a href="#" class="btn hover-action" data-toggle="tooltip" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                    <a href="#" class="btn hover-action" data-toggle="tooltip" title="Delete"><i class="fa-solid fa-trash "></i></a>
                </td>
            </tr>
            <tr>
                <td><input type="checkbox" name="" id=""></td>
                <td>7</td>
                <td>David White</td>
                <td>david.white@lithan.com</td>
                <td>Sales Agent</td>
                <td><img src="{{url('/images/user-7.jpg')}}" alt="Sales Agent Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                <td>Customer Service</td>
                <td>Philippines</td>
                <td><span style="font-size: 1.5rem;color:rgba(70, 70, 70, 0.623);" class="p-2 rounded" data-toogle="tooltip" title="Inactive"><i class="fa-solid fa-circle-xmark"></i><span></td>
                <td>
                    <a href="#" class="btn hover-action" data-toggle="tooltip" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                    <a href="#" class="btn hover-action" data-toggle="tooltip" title="Delete"><i class="fa-solid fa-trash "></i></a>
                </td>
            </tr>
            <tr>
                <td><input type="checkbox" name="" id=""></td>
                <td>8</td>
                <td>Laura Martinez</td>
                <td>laura.martinez@lithan.com</td>
                <td>BUH</td>
                <td><img src="{{url('/images/user-8.jpg')}}" alt="Sales Agent Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                <td>Legal and Compliance</td>
                <td>Philippines</td>
                <td><span style="font-size: 1.5rem;" class="p-2 rounded" data-toggle="tooltip" title="Active"><i class="active fa-solid fa-circle-check"></i></span></td>
                <td>
                    <a href="#" class="btn hover-action" data-toggle="tooltip" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                    <a href="#" class="btn hover-action" data-toggle="tooltip" title="Delete"><i class="fa-solid fa-trash "></i></a>
                </td>
            </tr>
        </tbody>
    </table>
    <footer aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
            <li class="page-item">
                <a class="page-link font" href="#">&#60;</a>
            </li>
            <li class="page-item"><a class="page-link font" href="#">1</a></li>
            <li class="page-item"><a class="page-link font" href="#">2</a></li>
            <li class="page-item disabled">
                <span class="page-link font">...</span>
            </li>
            <li class="page-item"><a class="page-link font" href="#">9</a></li>
            <li class="page-item"><a class="page-link font" href="#">10</a></li>
            <li class="page-item">
                <a class="page-link font" href="#">&#62;</a>
            </li>
        </ul>
    </footer>
@endsection