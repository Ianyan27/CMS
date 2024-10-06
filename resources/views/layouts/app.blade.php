<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <!-- Bootstrap CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ URL::asset('css/admin_style.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>

<body class="d-flex flex-column">
    <div class="container-fluid flex-grow-1">
        <div class="row shadow-sm py-3" style="max-height: 81.98px;">
            <!-- Updated navbar section -->
            <div class="col-auto d-flex align-items-center position-relative">
                <button class="navbar-toggler" type="button">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            <div class="col d-flex align-items-center">
                <div class="logo ms-2">
                    <img src="{{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png') }}" alt="Logo"
                        class="img-fluid" style="max-height: 50px;">
                </div>
                <!-- Empty space to allow the username to align right -->
                <div class="ms-auto d-flex align-items-center">
                    <div class="dropdown">
                        <button class="btn hover-action dropdown-toggle" type="button" id="dropdownMenuButton"
                            aria-haspopup="true" aria-expanded="false" style="border:none;">
                            <i class="fa-solid fa-user" style="padding: 0 5px 0 0;"></i>{{ Auth::user()->name }}
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="padding: 5px 0;">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item btn discard-table"
                                    style="padding: 6px 20px;">
                                    <i class="fa-solid fa-right-from-bracket" style="padding: 0 10px 0 0;"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row my-4 content-height">
            <div id="side-bar" class="col-md-auto col-sm-auto dashboard rounded-right navigation-width right-shadow">
                <ul class="nav flex-column fonts my-2">
                    <!-- Sidebar Navigation Items -->
                    <ul class="navbar-nav">
                        @if (Auth::check() && Auth::user()->role == 'Admin')
                            <li
                                class="{{ Route::currentRouteName() != 'admin#index' ? 'nav-item' : '' }} dashboard-link {{ Route::currentRouteName() == 'admin#index' ? 'active-link' : '' }}">
                                <a class="nav-link" href="{{ route('admin#index') }}">
                                    <i class="fa-solid fa-user"></i><span>Users</span>
                                </a>
                            </li>
                        @endif
                        {{-- <li
                            class="{{ Route::currentRouteName() != 'admin#contact-listing' ? 'nav-item' : '' }} dashboard-link {{ Route::currentRouteName() == 'admin#contact-listing' ? 'active-link' : '' }}">
                            <a class="nav-link " href="{{ route('admin#contact-listing') }}">
                                <i class="fa-solid fa-address-book"></i><span>Contacts</span>
                            </a>
                        </li> --}}
                        {{-- <li
                            class="{{ Route::currentRouteName() != 'importcsv' ? 'nav-item' : '' }} dashboard-link {{ Route::currentRouteName() == 'importcsv' ? 'active-link' : '' }}">
                            <a class="nav-link" href="{{ route('importcsv') }}">
                                <i class="fa-solid fa-file-arrow-up disabled"></i><span>Import CSV</span>
                            </a>
                        </li> --}}
                        @if (Auth::check() && Auth::user()->role =='BUH' || Auth::user()->role == 'Admin')
                        <li
                            class="{{ in_array(Route::currentRouteName(), ['admin#viewSaleAgent', 'owner#view']) ? 'active-link' : 'nav-item' }} dashboard-link">
                            <a class="nav-link"
                                href="{{ route(Auth::user()->role == 'Admin' ? 'admin#viewSaleAgent' : 'owner#view') }}">
                                <i class="fa-solid fa-universal-access"></i><span>Sales Agent</span>
                            </a>
                        </li>
                        <li
                            class="{{ in_array(Route::currentRouteName(), ['admin#hubspot-contact', 'hubspot-contact']) ? 'active-link' : 'nav-item' }} dashboard-link">
                            <a class="nav-link"
                                href="{{ route(Auth::user()->role == 'Admin' ? 'admin#hubspot-contact' : 'hubspot-contact') }}">
                                <i class="fa-brands fa-hubspot"></i><span>Hubspot Contacts</span>
                            </a>
                        </li>
                        <li
                            class="{{ Route::currentRouteName() != 'sale_admin' ? 'nav-item' : '' }} dashboard-link {{ Route::currentRouteName() == 'sale_admin' ? 'active-link' : '' }}">
                            <a class="nav-link" href="{{ route('admin#sales-admin') }}">
                                <i class="fa-solid fa-file-arrow-up"></i><span>Import CSV</span>
                            </a>
                        </li>
                        @endif
                        <li
                            class="{{ in_array(Route::currentRouteName(), ['admin#contact-listing', 'contact-listing']) ? 'active-link' : 'nav-item' }} dashboard-link">
                            <a class="nav-link"
                                href="{{ route(Auth::user()->role == 'Admin' ? 'admin#contact-listing' : 'contact-listing') }}">
                                <i class="fa-solid fa-address-book"></i><span>Contacts</span>
                        <li
                        class="{{ in_array(Route::currentRouteName(), ['head.index', 'head']) ? 'active-link' : 'nav-item' }} dashboard-link">
                            <a class="nav-link" href="{{ route('head.index') }}">
                                <i class="fa-solid fa-file-arrow-up"></i><span>Head Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </ul>
            </div>
            <div class="col-11 px-4 min-height content-width mb-4">
                @yield('content')
            </div>
        </div>
    </div>
    <footer style="position: sticky; bottom: 0;" class="bg-educ color-white text-center py-3 mt-auto">
        © 2024 eduCLaaS Pte Ltd. All rights reserved.
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownButton = document.getElementById('dropdownMenuButton');
            const dropdownMenu = document.querySelector('.dropdown-menu');
            const sidebar = document.getElementById('side-bar');
            const navbarToggler = document.querySelector('.navbar-toggler');
            const hamburgerDropdown = document.getElementById('hamburgerDropdown');

            dropdownButton.addEventListener('click', function() {
                dropdownMenu.classList.toggle('show');
            });

            // Toggle sidebar and dropdown on hamburger menu click
            navbarToggler.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                hamburgerDropdown.style.display = hamburgerDropdown.style.display === 'none' ||
                    hamburgerDropdown.style.display === '' ? 'block' : 'none';
            });

            // Position dropdown below the hamburger menu
            const rect = navbarToggler.getBoundingClientRect();
            hamburgerDropdown.style.top = `${rect.bottom}px`;
            hamburgerDropdown.style.left = `${rect.left}px`;

            // Optional: Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target) && !
                    navbarToggler.contains(event.target)) {
                    dropdownMenu.classList.remove('show');
                    hamburgerDropdown.style.display = 'none';
                }
            });
        });
    </script>
    <script src="https://kit.fontawesome.com/4d2a01d4ef.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>
    <script src="{{ URL::asset('js/search_email.js') }}"></script>
    <script src="{{ URL::asset('js/filter_status.js') }}"></script>
</body>

</html>
