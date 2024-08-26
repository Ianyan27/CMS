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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ URL::asset('css/admin_style.css') }}">
</head>

<body>
    <div class="container-fluid">
        <div class="row shadow-sm py-3">
            <div class="col-md-6 d-flex align-items-center">
                <div class="logo">
                    <img src="{{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png') }}" alt="Logo"
                        class="img-fluid" style="max-height: 50px;">
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <!-- Your existing content -->
            <div class="d-flex justify-content-center align-items-center">
                <div class="text-center p-5" style="background-color: #F2F2F2; border-radius:10px">
                    <!-- Company Logo -->
                    <img src="{{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png') }}" alt="Company Logo" class="mb-4"
                        style="max-width: 150px;">
                    <!-- Welcome Message -->
                    <h5 class="mb-4"><strong>Welcome to eduCLaaS CMS system</strong></h5>
                    <!-- Sign in with Microsoft Button -->
                    <a href="#" class="btn d-flex align-items-center justify-content-center" data-toggle="modal" data-target="#loginModal"
                        style="background-color: #91264c; color: white; padding: 0.75rem 1.5rem; border-radius: 5px;">
                        <img src="{{ url('/images/image.png') }}" style="width: 30px; height:30px" alt="Microsoft Logo" class="mr-2">
                        Sign in with Microsoft
                    </a>
                </div>
            </div>
        </div>
    </div>    
    <footer class="bg-educ color-white text-center py-3 mt-auto">
        © 2024 eduCLaaS Pte Ltd. All rights reserved.
    </footer>
    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Microsoft Login</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('microsoft.login') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="email">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your Microsoft account email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://kit.fontawesome.com/4d2a01d4ef.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
