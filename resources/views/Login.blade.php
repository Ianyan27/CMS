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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

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
        </div>

    </div>

    <div class="d-flex justify-content-center align-items-center min-vh-100">
        <div class="text-center p-5" style="background-color: #F2F2F2; border-radius:10px">
            <!-- Company Logo -->
            <img src=" {{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png') }}" alt="Company Logo" class="mb-4"
                style="max-width: 150px;">

            <!-- Welcome Message -->
            <h5 class="mb-4 "><strong>Welcome to eduCLaaS CMS system</strong></h5>

            <!-- Sign in with Microsoft Button -->
            <a href="#" class="btn d-flex align-items-center justify-content-center"
                style="background-color: #91264c; color: white; padding: 0.75rem 1.5rem; border-radius: 5px;">
                <img src="{{ url('/images/image.png') }}" style="width: 30px; height:30px" alt="Microsoft Logo"
                    style="max-width: 20px;" class="mr-2">
                Sign in with Microsoft
            </a>
        </div>
    </div>


    <footer class="bg-educ color-white text-center py-3 mt-auto">
        © 2024 eduCLaaS Pte Ltd. All rights reserved.
    </footer>
    <script src="https://kit.fontawesome.com/4d2a01d4ef.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
