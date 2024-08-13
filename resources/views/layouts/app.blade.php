<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title')</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <style>
            .bg-educ{
                background-color: #91264c;
                color: #fff;
            }
        </style>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row shadow-sm py-3">
                <div class="col-md-6 d-flex align-items-center">
                    <div class="logo">
                        <img src=" {{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png')}} " alt="Logo" class="img-fluid" style="max-height: 50px;">
                    </div>
                </div>
                <div class="col-md-6 d-flex justify-content-end align-items-center">
                    <div class="profile d-flex align-items-center">
                        <img src=" {{url('/images/Screenshot 2024-05-15 085107.png')}} " alt="Profile Picture" class="rounded-circle img-fluid" style="max-height: 40px; margin-right: 10px;">
                        <div class="name">User Name</div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 bg-dark text-white vh-100 pt-4">
                    <h3>Navigation</h3>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Roles</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="user_list_dashboard">Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Contacts</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Countries</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Upload</a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-9">
                    <div class="content p-4">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>

        <footer class="bg-light text-center py-3 mt-auto">
            © 2024 eduCLaaS Pte Ltd. All rights reserved.
        </footer>

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
</html>