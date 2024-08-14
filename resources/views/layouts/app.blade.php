<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title')</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <style>
            *{
                font-family:"sans-serif", Montserrat;
            }
            table{
                border-collapse: collapse;
            }
            .fonts{
                font-size: 16px;
                font-weight: 550;
            }
            .color-white{
                color:white;
            }
            .bg-row{
                background-color: white;
            }
            .bg-edit{
                background-color: #2684A4;
            }
            .bg-educ{
                background-color:#91264c;
            }
            .border-educ{
                border: 1px solid #91264c;
            }
            .font{
                color:#91264c;
            }
            .table-border{
                border-color: #b35071;
            }
            .bg-dashboard{
                background-color: #f2f2f2;
            }
            .edit-button{
                background-color: #64c4b5;
                color: #000000;
            }
            .nav-link:hover{
                background-color: #2684A4;
                color: #ffffff;
                border-radius: 4px;
            }
            .delete-button{
                background-color: #70183d;
                color: #f2f2f2;
            }
            .edit-button:hover{
                background-color: #f2f2f2;
            }
            .delete-button:hover{
                color: #000000;
                background-color: #f2f2f2;
            }
            .fa-circle-question{
                font-size: 20px;
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
                        <div class="name">Gerome</div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 bg-dashboard ml-4 my-3 h-auto pt-4 border-educ rounded">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link font" href="/">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font" href="#">Roles</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font" href="/">Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font" href="#">Contacts</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font" href="#">Countries</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font" href="#">Upload</a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-9 table-container pt-4 ml-4 my-3 border-educ rounded">
                    @yield('content')
                </div>
            </div>
        </div>

        <footer class="bg-light text-center py-3 mt-auto">
            © 2024 eduCLaaS Pte Ltd. All rights reserved.
        </footer>

        <script src="https://kit.fontawesome.com/4d2a01d4ef.js" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
</html>
