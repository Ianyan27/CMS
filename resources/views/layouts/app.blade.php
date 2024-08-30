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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ URL::asset('css/admin_style.css') }}">
</head>
<body class="d-flex flex-column">
    <div class="container-fluid flex-grow-1">
        <div class="row shadow-sm py-3" style="max-height: 81.98px;">
            <div class="col-md-6 col-sm-5 d-flex align-items-center">
                <div class="logo">
                    <img src="{{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png') }}" alt="Logo"
                        class="img-fluid" style="max-height: 50px;">
                </div>
            </div>
            <div class="col-md-6 col-sm-5 d-flex justify-content-end align-items-center">
                <div class="dropdown">
                    <button class="btn hover-action dropdown-toggle" type="button" id="dropdownMenuButton" aria-haspopup="true" aria-expanded="false">
                        {{ Auth::user()->name }}
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="padding: 5px 0;">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline" style="margin:0;">
                            @csrf
                            <button type="submit" class="dropdown-item btn discard-table" 
                            style="padding: 6px 11px;">Logout</button>
                        </form>
                    </div>
                </div>
            </div>            
        </div>        
        <div class="row my-4 content-height">
            <div id="side-bar" class="col-md-auto col-sm-auto dashboard rounded-right  navigation-width right-shadow">
                <ul class="nav flex-column fonts mt-2">
                    {{-- <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() == 'dashboard' ? 'active-link' : '' }}"
                            href="{{ route('dashboard') }}">
                            <i class="fa-solid fa-table-columns mr-3"></i>Dashboard
                        </a>
                    </li> --}}
                    <ul class="navbar-nav">
                        @if (Auth::check() && Auth::user()->role == 'Admin')
                            <li class="{{ Route::currentRouteName() != 'view-user' ? 'nav-item' : '' }} dashboard-link {{ Route::currentRouteName() == 'view-user' ? 'active-link' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('view-user') }}">
                                    <i class="fa-solid fa-user"></i><span>User List</span>
                                </a>
                            </li>
                        @endif

                        @if (Auth::check() && Auth::user()->role == 'BUH')
                            <li class="{{ Route::currentRouteName() != 'importcsv' ? 'nav-item' : '' }} dashboard-link {{ Route::currentRouteName() == 'importcsv' ? 'active-link' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('importcsv') }}">
                                    <i class="fa-solid fa-file-arrow-up"></i><span>Import Files</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteName() != 'owner#view' ? 'nav-item' : '' }} dashboard-link {{ Route::currentRouteName() == 'owner#view' ? 'active-link' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('owner#view') }}">
                                    <i class="fa-solid fa-universal-access"></i><span>Sales Agent</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteName() != 'hubspot-contact' ? 'nav-item' : '' }} dashboard-link {{ Route::currentRouteName() == 'hubspot-contact' ? 'active-link' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('hubspot-contact') }}">
                                    <i class="fa-brands fa-hubspot"></i><span>Hubspot Contacts</span>
                                </a>
                            </li>
                        @endif

                        @if (Auth::check() && Auth::user()->role == 'Sales_Agent')
                            <li class="{{ Route::currentRouteName() != 'contact-listing' ? 'nav-item' : '' }} dashboard-link {{ Route::currentRouteName() == 'contact-listing' ? 'active-link' : '' }}">
                                <a class="nav-link"
                                    href="{{ route('contact-listing') }}">
                                    <i class="fa-solid fa-address-book"></i><span>Contact Listing</span>
                                </a>
                            </li>
                        @endif
                    </ul>

                </ul>
            </div>
            <div class="col-11 px-4 min-height content-width">
                @yield('content')
            </div>
        </div>
    </div>
    <footer style="position: sticky;bottom:0;" class="bg-educ color-white text-center py-3 mt-auto">
        © 2024 eduCLaaS Pte Ltd. All rights reserved.
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dropdownButton = document.getElementById('dropdownMenuButton');
            const dropdownMenu = document.querySelector('.dropdown-menu');
    
            dropdownButton.addEventListener('click', function () {
                dropdownMenu.classList.toggle('show');
            });
    
            // Optional: Close dropdown when clicking outside
            document.addEventListener('click', function (event) {
                if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    dropdownMenu.classList.remove('show');
                }
            });
        });
    </script>    
    <script src="https://kit.fontawesome.com/4d2a01d4ef.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>
    <script src="{{ URL::asset('js/search_email.js') }}"></script>
    <script src="{{ URL::asset('js/sort.js') }}"></script>
    <script src="{{ URL::asset('js/filter_status.js') }}"></script>
</body>
</html>