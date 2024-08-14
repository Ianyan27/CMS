@section('title', 'User Listing Page')

@extends('layouts.app')

@section('content')
    <div class="table-container bg-dashboard pt-4 my-3 rounded">
        <div class="table-title d-flex justify-content-between align-items-center">
            <h2 class="ml-3 mb-2">Users</h2>
            <a href="#" class="btn btn-primary mr-3 mb-2">+</a>
        </div>
        <table class="table table-hover">
            <thead class="font text-center">
                <tr>
                    <th scope="col"><input type="checkbox" name="" id=""></th>
                    <th scope="col">No</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email (filter_icon)</th>
                    <th scope="col">Role</th>
                    <th scope="col">Profile</th>
                    <th scope="col">Assigned at</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <tr>
                    <td><input type="checkbox" name="" id=""></td>
                    <td>1</td>
                    <td>John Doe</td>
                    <td>john.doe@lithan.com</td>
                    <td>Admin</td>
                    <td><img src="{{url('/images/Screenshot 2024-05-15 085107.png')}}" alt="User Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                    <td>2024-08-14</td>
                    <td>
                        <a href="#" class="btn btn-sm btn-warning">Edit</a>
                        <a href="#" class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="" id=""></td>
                    <td>2</td>
                    <td>Jane Smith</td>
                    <td>jane.smith@lithan.com</td>
                    <td>User</td>
                    <td><img src="path_to_img" alt="User Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                    <td>2024-08-14</td>
                    <td>
                        <a href="#" class="btn btn-sm btn-warning">Edit</a>
                        <a href="#" class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="" id=""></td>
                    <td>3</td>
                    <td>Michael Johnson</td>
                    <td>michael.johnson@lithan.com</td>
                    <td>Admin</td>
                    <td><img src="path_to_img" alt="User Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                    <td>2024-08-13</td>
                    <td>
                        <a href="#" class="btn btn-sm btn-warning">Edit</a>
                        <a href="#" class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="" id=""></td>
                    <td>4</td>
                    <td>Emily Davis</td>
                    <td>emily.davis@lithan.com</td>
                    <td>User</td>
                    <td><img src="path_to_img" alt="User Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                    <td>2024-08-13</td>
                    <td>
                        <a href="#" class="btn btn-sm btn-warning">Edit</a>
                        <a href="#" class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="" id=""></td>
                    <td>5</td>
                    <td>Chris Brown</td>
                    <td>chris.brown@lithan.com</td>
                    <td>Moderator</td>
                    <td><img src="path_to_img" alt="User Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                    <td>2024-08-12</td>
                    <td>
                        <a href="#" class="btn btn-sm btn-warning">Edit</a>
                        <a href="#" class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="" id=""></td>
                    <td>6</td>
                    <td>Amy Wilson</td>
                    <td>amy.wilson@lithan.com</td>
                    <td>User</td>
                    <td><img src="path_to_img" alt="User Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                    <td>2024-08-12</td>
                    <td>
                        <a href="#" class="btn btn-sm btn-warning">Edit</a>
                        <a href="#" class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="" id=""></td>
                    <td>7</td>
                    <td>David White</td>
                    <td>david.white@lithan.com</td>
                    <td>User</td>
                    <td><img src="path_to_img" alt="User Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                    <td>2024-08-11</td>
                    <td>
                        <a href="#" class="btn btn-sm btn-warning">Edit</a>
                        <a href="#" class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="" id=""></td>
                    <td>8</td>
                    <td>Sarah Taylor</td>
                    <td>sarah.taylor@lithan.com</td>
                    <td>Admin</td>
                    <td><img src="path_to_img" alt="User Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                    <td>2024-08-11</td>
                    <td>
                        <a href="#" class="btn btn-sm btn-warning">Edit</a>
                        <a href="#" class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="" id=""></td>
                    <td>9</td>
                    <td>James Miller</td>
                    <td>james.miller@lithan.com</td>
                    <td>Moderator</td>
                    <td><img src="path_to_img" alt="User Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                    <td>2024-08-10</td>
                    <td>
                        <a href="#" class="btn btn-sm btn-warning">Edit</a>
                        <a href="#" class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="" id=""></td>
                    <td>10</td>
                    <td>Olivia Martinez</td>
                    <td>olivia.martinez@lithan.com</td>
                    <td>User</td>
                    <td><img src="path_to_img" alt="User Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                    <td>2024-08-10</td>
                    <td>
                        <a href="#" class="btn btn-sm btn-warning">Edit</a>
                        <a href="#" class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="" id=""></td>
                    <td>11</td>
                    <td>Daniel Lewis</td>
                    <td>daniel.lewis@lithan.com</td>
                    <td>User</td>
                    <td><img src="path_to_img" alt="User Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                    <td>2024-08-09</td>
                    <td>
                        <a href="#" class="btn btn-sm btn-warning">Edit</a>
                        <a href="#" class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
            </tbody>        
        </table>
    </div>

@endsection