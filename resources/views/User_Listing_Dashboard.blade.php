@section('title', 'User Listing Dashboard')

@extends('layouts.app')

@section('content')
<div class="table-container">
    <div class="table-title d-flex justify-content-between align-items-center mb-3">
        <h2>Users</h2>
        <a href="#" class="btn btn-primary">+</a>
    </div>
    <table class="table table-striped table-hover">
        <thead class="bg-educ">
            <tr>
                <th scope="col">No</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Role</th>
                <th scope="col">Img</th>
                <th scope="col">Assigned at</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>John Doe</td>
                <td>john.doe@example.com</td>
                <td>Admin</td>
                <td><img src="path_to_img" alt="User Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                <td>2024-08-14</td>
                <td>
                    <a href="#" class="btn btn-sm btn-warning">Edit</a>
                    <a href="#" class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
            <tr>
                <td>2</td>
                <td>Jane Smith</td>
                <td>jane.smith@example.com</td>
                <td>User</td>
                <td><img src="path_to_img" alt="User Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                <td>2024-08-14</td>
                <td>
                    <a href="#" class="btn btn-sm btn-warning">Edit</a>
                    <a href="#" class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
            <tr>
                <td>3</td>
                <td>Michael Johnson</td>
                <td>michael.johnson@example.com</td>
                <td>Admin</td>
                <td><img src="path_to_img" alt="User Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                <td>2024-08-13</td>
                <td>
                    <a href="#" class="btn btn-sm btn-warning">Edit</a>
                    <a href="#" class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
            <tr>
                <td>4</td>
                <td>Emily Davis</td>
                <td>emily.davis@example.com</td>
                <td>User</td>
                <td><img src="path_to_img" alt="User Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                <td>2024-08-13</td>
                <td>
                    <a href="#" class="btn btn-sm btn-warning">Edit</a>
                    <a href="#" class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
            <tr>
                <td>5</td>
                <td>Chris Brown</td>
                <td>chris.brown@example.com</td>
                <td>Moderator</td>
                <td><img src="path_to_img" alt="User Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                <td>2024-08-12</td>
                <td>
                    <a href="#" class="btn btn-sm btn-warning">Edit</a>
                    <a href="#" class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
            <tr>
                <td>6</td>
                <td>Amy Wilson</td>
                <td>amy.wilson@example.com</td>
                <td>User</td>
                <td><img src="path_to_img" alt="User Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                <td>2024-08-12</td>
                <td>
                    <a href="#" class="btn btn-sm btn-warning">Edit</a>
                    <a href="#" class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
            <tr>
                <td>7</td>
                <td>David White</td>
                <td>david.white@example.com</td>
                <td>User</td>
                <td><img src="path_to_img" alt="User Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                <td>2024-08-11</td>
                <td>
                    <a href="#" class="btn btn-sm btn-warning">Edit</a>
                    <a href="#" class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
            <tr>
                <td>8</td>
                <td>Sarah Taylor</td>
                <td>sarah.taylor@example.com</td>
                <td>Admin</td>
                <td><img src="path_to_img" alt="User Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                <td>2024-08-11</td>
                <td>
                    <a href="#" class="btn btn-sm btn-warning">Edit</a>
                    <a href="#" class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
            <tr>
                <td>9</td>
                <td>James Miller</td>
                <td>james.miller@example.com</td>
                <td>Moderator</td>
                <td><img src="path_to_img" alt="User Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                <td>2024-08-10</td>
                <td>
                    <a href="#" class="btn btn-sm btn-warning">Edit</a>
                    <a href="#" class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
            <tr>
                <td>10</td>
                <td>Olivia Martinez</td>
                <td>olivia.martinez@example.com</td>
                <td>User</td>
                <td><img src="path_to_img" alt="User Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                <td>2024-08-10</td>
                <td>
                    <a href="#" class="btn btn-sm btn-warning">Edit</a>
                    <a href="#" class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
            <tr>
                <td>11</td>
                <td>Daniel Lewis</td>
                <td>daniel.lewis@example.com</td>
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