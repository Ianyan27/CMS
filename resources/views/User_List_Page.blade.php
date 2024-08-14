@section('title', 'User Listing Page')

@extends('layouts.app')

@section('content')
        <div class="table-title d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <h2 class="ml-3 mb-2">User Listing Page</h2>
                <a href="#" class="btn edit-button color-white ml-2 mb-2">New Sales Agent</a>
            </div>
            <div class="search-box d-flex align-items-center mr-3 mb-2">
                <input type="search" class="form-control mr-1" placeholder="Search..." id="search-input" aria-label="Search">
                <button class="btn btn-secondary bg-educ mx-1" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                <i title="Enter the email you're looking for." class="fa-solid fa-circle-question font mx-2 text-center"></i>
            </div>            
        </div>
        <table class="table table-striped table-hover mt-2">
            <thead class="font text-center">
                <tr>
                    <th scope="col"><input type="checkbox" name="" id=""></th>
                    <th class=" h5" scope="col">Index No# <i class=" ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                    <th class=" h5" scope="col">Name <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                    <th class=" h5" scope="col">Email <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                    <th class=" h5" scope="col">Role <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                    <th class=" h5" scope="col">Profile <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                    <th class=" h5" scope="col">Assigned at <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                    <th class=" h5" scope="col">Action <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                </tr>
            </thead>
            <tbody class="text-center bg-row fonts">
                <tr>
                    <td><input type="checkbox" name="" id=""></td>
                    <td>1</td>
                    <td>John Doe</td>
                    <td>john.doe@lithan.com</td>
                    <td>Admin</td>
                    <td><img src="{{url('/images/Screenshot 2024-05-15 085107.png')}}" alt="Sales Agent Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                    <td>2024-08-14</td>
                    <td>
                        <a href="#" class="btn bg-edit"><i class="fa-solid fa-pen-to-square color-white"></i></a>
                        <a href="#" class="btn bg-educ"><i class="fa-solid fa-trash color-white"></i></a>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="" id=""></td>
                    <td>2</td>
                    <td>Jane Smith</td>
                    <td>jane.smith@lithan.com</td>
                    <td>Sales Agent</td>
                    <td><img src="{{url('/images/Screenshot 2024-05-15 085107.png')}}" alt="Sales Agent Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                    <td>2024-08-14</td>
                    <td>
                        <a href="#" class="btn bg-edit"><i class="fa-solid fa-pen-to-square color-white"></i></a>
                        <a href="#" class="btn bg-educ"><i class="fa-solid fa-trash color-white"></i></a>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="" id=""></td>
                    <td>3</td>
                    <td>Michael Johnson</td>
                    <td>michael.johnson@lithan.com</td>
                    <td>Admin</td>
                    <td><img src="{{url('/images/Screenshot 2024-05-15 085107.png')}}" alt="Sales Agent Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                    <td>2024-08-13</td>
                    <td>
                        <a href="#" class="btn bg-edit"><i class="fa-solid fa-pen-to-square color-white"></i></a>
                        <a href="#" class="btn bg-educ"><i class="fa-solid fa-trash color-white"></i></a>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="" id=""></td>
                    <td>4</td>
                    <td>Emily Davis</td>
                    <td>emily.davis@lithan.com</td>
                    <td>Sales Agent</td>
                    <td><img src="{{url('/images/Screenshot 2024-05-15 085107.png')}}" alt="Sales Agent Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                    <td>2024-08-13</td>
                    <td>
                        <a href="#" class="btn bg-edit"><i class="fa-solid fa-pen-to-square color-white"></i></a>
                        <a href="#" class="btn bg-educ"><i class="fa-solid fa-trash color-white"></i></a>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="" id=""></td>
                    <td>5</td>
                    <td>Chris Brown</td>
                    <td>chris.brown@lithan.com</td>
                    <td>BUH</td>
                    <td><img src="{{url('/images/Screenshot 2024-05-15 085107.png')}}" alt="Sales Agent Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                    <td>2024-08-12</td>
                    <td>
                        <a href="#" class="btn bg-edit"><i class="fa-solid fa-pen-to-square color-white"></i></a>
                        <a href="#" class="btn bg-educ"><i class="fa-solid fa-trash color-white"></i></a>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="" id=""></td>
                    <td>6</td>
                    <td>Amy Wilson</td>
                    <td>amy.wilson@lithan.com</td>
                    <td>Sales Agent</td>
                    <td><img src="{{url('/images/Screenshot 2024-05-15 085107.png')}}" alt="Sales Agent Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                    <td>2024-08-12</td>
                    <td>
                        <a href="#" class="btn bg-edit"><i class="fa-solid fa-pen-to-square color-white"></i></a>
                        <a href="#" class="btn bg-educ"><i class="fa-solid fa-trash color-white"></i></a>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="" id=""></td>
                    <td>7</td>
                    <td>David White</td>
                    <td>david.white@lithan.com</td>
                    <td>Sales Agent</td>
                    <td><img src="{{url('/images/Screenshot 2024-05-15 085107.png')}}" alt="Sales Agent Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                    <td>2024-08-11</td>
                    <td>
                        <a href="#" class="btn bg-edit"><i class="fa-solid fa-pen-to-square color-white"></i></a>
                        <a href="#" class="btn bg-educ"><i class="fa-solid fa-trash color-white"></i></a>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="" id=""></td>
                    <td>8</td>
                    <td>Sarah Taylor</td>
                    <td>sarah.taylor@lithan.com</td>
                    <td>Admin</td>
                    <td><img src="{{url('/images/Screenshot 2024-05-15 085107.png')}}" alt="Sales Agent Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                    <td>2024-08-11</td>
                    <td>
                        <a href="#" class="btn bg-edit"><i class="fa-solid fa-pen-to-square color-white"></i></a>
                        <a href="#" class="btn bg-educ"><i class="fa-solid fa-trash color-white"></i></a>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="" id=""></td>
                    <td>9</td>
                    <td>James Miller</td>
                    <td>james.miller@lithan.com</td>
                    <td>BUH</td>
                    <td><img src="{{url('/images/Screenshot 2024-05-15 085107.png')}}" alt="Sales Agent Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                    <td>2024-08-10</td>
                    <td>
                        <a href="#" class="btn bg-edit"><i class="fa-solid fa-pen-to-square color-white"></i></a>
                        <a href="#" class="btn bg-educ"><i class="fa-solid fa-trash color-white"></i></a>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="" id=""></td>
                    <td>10</td>
                    <td>Olivia Martinez</td>
                    <td>olivia.martinez@lithan.com</td>
                    <td>Sales Agent</td>
                    <td><img src="{{url('/images/Screenshot 2024-05-15 085107.png')}}" alt="Sales Agent Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                    <td>2024-08-10</td>
                    <td>
                        <a href="#" class="btn bg-edit"><i class="fa-solid fa-pen-to-square color-white"></i></a>
                        <a href="#" class="btn bg-educ"><i class="fa-solid fa-trash color-white"></i></a>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="" id=""></td>
                    <td>11</td>
                    <td>Daniel Lewis</td>
                    <td>daniel.lewis@lithan.com</td>
                    <td>Sales Agent</td>
                    <td><img src="{{url('/images/Screenshot 2024-05-15 085107.png')}}" alt="Sales Agent Image" class="img-fluid rounded-circle" style="max-width: 50px;"></td>
                    <td>2024-08-09</td>
                    <td>
                        <a href="#" class="btn bg-edit"><i class="fa-solid fa-pen-to-square color-white"></i></a>
                        <a href="#" class="btn bg-educ"><i class="fa-solid fa-trash color-white"></i></a>
                    </td>
                </tr>
            </tbody>
        </table>
@endsection
