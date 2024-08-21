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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="{{ URL::asset('css/admin_style.css') }}">
</head>
<body class="d-flex flex-column min-vh-100">
    <div class="container-fluid flex-grow-1">
        <div class="row shadow-sm py-3">
            <div class="col-md-6 d-flex align-items-center">
                <div class="logo">
                    <img src="{{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png') }}" alt="Logo"
                        class="img-fluid" style="max-height: 50px;">
                </div>
            </div>
            <div class="col-md-6 d-flex justify-content-end align-items-center">
                <div class="profile d-flex align-items-center">
                    <img src="{{ url('/images/Screenshot 2024-05-15 085107.png') }}" alt="Profile Picture"
                        class="rounded-circle img-fluid" style="max-height: 40px; margin-right: 10px;">
                    <div class="name">Signout</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div id="side-bar" class="dashboard py-3 border-right-educ rounded-right h-auto">
                <ul class="nav flex-column fonts">
                    {{-- <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() == 'dashboard' ? 'active-link' : '' }}"
                            href="{{ route('dashboard') }}">
                            <i class="fa-solid fa-table-columns mr-3"></i>Dashboard
                        </a>
                    </li> --}}
                    <li class="nav-item dashboard-link">
                        <a class="nav-link mb-2 {{ Route::currentRouteName() == 'user-list' ? 'active-link' : '' }}"
                            href="{{ route('user-list') }}">
                            <i class="fa-regular fa-user mr-3"></i> <span>Users</span>
                        </a>
                    </li>                    
                    <li class="nav-item dashboard-link">
                        <a class="nav-link mb-2 {{ Route::currentRouteName() == 'salesagent' ? 'active-link' : '' }}"
                            href="{{ route('salesagent') }}">
                            <i class="fa-solid fa-universal-access mr-3"></i></i> <span>Sale Agent</span>
                        </a>
                    </li>
                    <li class="nav-item dashboard-link">
                        <a class="nav-link mb-2 {{ Route::currentRouteName() == 'importcopy' ? 'active-link' : '' }}"
                            href="{{ route('importcopy') }}">
                            <i class="fa-solid fa-file-import mr-3"></i><span>Upload Files</span>
                        </a>
                    </li>
                    <li class="nav-item dashboard-link">
                        <a class="nav-link mb-2 {{ Route::currentRouteName() == 'contact-listing' ? 'active-link' : '' }}"
                            href="{{ route('contact-listing') }}">
                            <i class="fa-solid fa-address-book mr-3"></i><span>Contact Listing</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="col ml-3 mb-3 py-3">
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
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="{{ URL::asset('js/search_email.js') }}"></script>
    <script src="{{ URL::asset('js/sort.js') }}"></script>
    <script src="{{ URL::asset('js/filter_status.js') }}"></script>
</body>
</html>