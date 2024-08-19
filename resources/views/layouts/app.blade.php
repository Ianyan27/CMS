<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <!-- Bootstrap CSS -->
    {{-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- added bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ URL::asset('css/admin_style.css') }}">
</head>
<body>
    <div class="container-fluid">
        <div class="row shadow-sm py-3">
            <div class="col-md-6 d-flex align-items-center">
                <div class="logo">
                    <img src=" {{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png') }} " alt="Logo"
                        class="img-fluid" style="max-height: 50px;">
                </div>
            </div>
            <div class="col-md-6 d-flex justify-content-end align-items-center">
                <div class="profile d-flex align-items-center">
                    <img src=" {{ url('/images/Screenshot 2024-05-15 085107.png') }} " alt="Profile Picture"
                        class="rounded-circle img-fluid" style="max-height: 40px; margin-right: 10px;">
                    <div class="name">Gerome</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="dashboard col-md-2 py-3 my-3 border-educ rounded-right  h-auto">
                <ul class="nav flex-column">
                    {{-- <li class="nav-item">
                        <i cs="fa-solid fa-table-columns"></i>
                        <a class="nav-link" href="/dashboard"><i class="fa-solid fa-table-columns mr-3"></i>Dashboard</a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link" href="/"><i class="fa-regular fa-user mr-3"></i>Users</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="/contactdetails"><i
                                class="fa-solid fa-address-book mr-3"></i>Contact</a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link" href="/salesagent"><i
                                class="fa-solid fa-universal-access mr-3"></i>Sale Agent</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/importcopy"><i class="fa-solid fa-file-import mr-3"></i>Upload Files</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="/editcontactdetail">Edit Contact Detail</a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link" href="/contact-listing"><i class="fa-solid fa-address-book mr-3"></i>Contact Listing</a>
                    </li>
                </ul>
            </div>
            <div class="col table-container ml-3 mb-3 py-3 rounded">
                @yield('content')
            </div>
        </div>

    </div>

    <footer class="bg-educ color-white text-center py-3 mt-auto">
        © 2024 eduCLaaS Pte Ltd. All rights reserved.
    </footer>
    <script src="https://kit.fontawesome.com/4d2a01d4ef.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    {{-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> --}}
<!--added bootstrap5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

</body>

</html>
