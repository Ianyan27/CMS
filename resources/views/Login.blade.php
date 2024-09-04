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
    <div class="container-fluid d-flex flex-column" style="min-height: 100vh; padding: 0; overflow-x: hidden;">
        <div class="row shadow-sm py-3" style="margin: 0;">
            <div class="col-md-6 d-flex align-items-center">
                <div class="logo">
                    <img src="{{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png') }}" alt="Logo"
                        class="img-fluid" style="max-height: 50px;">
                </div>
            </div>
        </div>
        <div class="container-fluid d-flex justify-content-center align-items-center flex-grow-1" style="padding: 0;">
            <div class="text-center p-5 border-educ" style="background-color: white; border-radius: 10px;">
                <i style="color:#91264c; font-size: 5rem; background-color: #f0f0f0; padding: 1rem; border-radius: 50%; height: 120px; width: 120px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);"
                    class="fa-solid fa-user mb-3"></i>
                <h5 class="mb-4"><strong>Hi There! Welcome to Educlass CMS System</strong></h5>
                <p class="text-muted">To keep connected, consider signing in using your official Microsoft account</p>
                <!-- Sign in with Microsoft Button -->
                <a href="{{ route('login.microsoft') }}" class="btn d-flex align-items-center justify-content-center"
                    style="background-color: #91264c; color: white; padding: 0.75rem 1.5rem; border-radius: 5px;">
                    <img src="{{ url('/images/image.png') }}" style="width: 30px; height: 30px;" alt="Microsoft Logo"
                        class="mr-2">
                    Sign in with Microsoft
                </a>
            </div>
        </div>
        <footer class="bg-educ color-white text-center py-3" style="width: 100%; margin: 0;">
            © 2024 eduCLaaS Pte Ltd. All rights reserved.
        </footer>
    </div>
    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header"
                    style="height: 90px; background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%); border:none;">
                    <h5 class="modal-title" id="loginModalLabel" style="color: #91264c;"><strong>Microsoft
                            Login</strong></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: red;">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('login.microsoft') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="email" style="color: #91264c;">Email address</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="Enter your Microsoft account email" required>
                        </div>
                        <button type="submit" class="btn" style="background: #91264c; color: white;">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Error Modal -->
    @if ($errors->any())
        <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="errorModalLabel">Login Error</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!-- Scripts -->
    <script src="https://kit.fontawesome.com/4d2a01d4ef.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Show Error Modal if there are errors -->
    @if ($errors->any())
        <script>
            $(document).ready(function() {
                $('#errorModal').modal('show');
            });
        </script>
    @endif

</body>

</html>
