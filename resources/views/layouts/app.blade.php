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
    <style>
        * {
            font-family: "sans-serif", Montserrat;
        }

        table {
            border-collapse: collapse;
        }

        .fonts {
            font-size: 16px;
            font-weight: 550;
        }

        .font-size {}

        .color-white {
            color: white;
        }

        .bg-row {
            background-color: white;
        }

        .bg-edit {
            background-color: #2684A4;
        }

        .bg-educ {
            background-color: #91264c;
        }

        .border-educ {
            border: 1px solid #91264c;
        }

        .font-educ {
            color: #91264c;
        }

        .table-border {
            border-color: #b35071;
        }

        .dashboard {
            background-color: #91264c;
            color: white;
            font-size: 1.15rem;
        }

        .edit-button {
            background-color: #64c4b5;
            color: #1c1c1e;
        }

        .nav-link:hover {
            background-color: #91264c;
            color: white;
            border-radius: 4px;
        }

        .delete-button {
            background-color: #70183d;
            color: white;
        }

        .fa-circle-question {
            font-size: 20px;
        }

        .fa-pen-to-square,
        .fa-trash {
            font-size: 1.5rem;
        }

        .hover-action {
            border: 1px solid #91264c;
            color: #91264c;
            font-style: center;
        }

        .hover-action:hover {
            background-color: #91264c;
            color: white;
        }

        .agent-meeting,
        .agent-active,
        .agent-offline {
            width: 125px;
            display: inline-block;
            text-align: center;
        }

        .active {
            color: #75ce8a;
        }

        .offline {
            color: #E2E2E2;
        }

        .meeting {
            color: #91264c;
        }

        .agent-meeting {
            background-color: #A47786;
            color: white;
        }

        .agent-active {
            background-color: #A0C8A9;
            color: #1c1c1e;
        }

        .agent-offline {
            background-color: #E2E2E2;
            color: #1c1c1e;
        }

        .agent-meeting:hover {
            border: 1px solid #A47786;
            background-color: white;
            color: #1c1c1e;
        }

        .agent-active:hover {
            border: 1px solid #A0C8A9;
            background-color: white;
            color: #1c1c1e;
        }

        .agent-offline:hover {
            border: 1px solid #E2E2E2;
            background-color: white;
            color: #1c1c1e;
        }
        .border-dashed{
            border:1px dashed black;
        }
        .file-support{
            font-size: 14px;
        }
        .file-info{
            width: 100px;
            text-align: center;
        }
        .bg-white{
            background-color:white;
        }
        .border-educ{
            border: 1px solid #91264c;
        }
        .button-educ{
            background-color:#91264c;
            color: white;
        }
        .button-educ:hover{
            border:1px solid #91264c;
            background-color: white;
            color: #1c1c1e;
        }
        .drop-files{
            font-size: 1.25rem;
        }
        .file-button i{
            font-size: 1.5rem;
        }
        .file-button{
            display: flex;
            align-items: center;
            justify-content: center; 
        }
        .border-educ-border{
            border-bottom: 1px solid #91264c;
        }
        .border-right{
            border-right: 1px solid #1c1c1e;
        }
        .activity-button{
            border-bottom: 1px solid gray;
            font-weight: bold;
        }
        .active-activity-button{
            border-bottom:3px solid #1c1c1e;
        }
        .custom-row {
            border-bottom: 1px solid #ddd;
            margin-bottom: 15px;
            padding-bottom: 15px;
        }
        .custom-row:last-child {
            border-bottom: none;
        }
    </style>

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
                    <li class="nav-item">
                        <i cs="fa-solid fa-table-columns"></i>
                        <a class="nav-link color-white" href="/dashboard"><i
                                class="fa-solid fa-table-columns mr-3"></i>Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link color-white " href="/"><i class="fa-regular fa-user mr-3"></i>Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link color-white" href="/contactdetails"><i
                                class="fa-solid fa-address-book mr-3"></i>Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link color-white" href="/salesagent"><i
                                class="fa-solid fa-universal-access mr-3"></i>Sale Agent</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link color-white" href="/importcopy"><i class="fa-solid fa-file-import mr-3"></i>Upload Files</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link color-white" href="/editcontactdetail">Edit Contact Detail</a>
                    </li>
                </ul>
            </div>
            <div class="col table-container ml-2 mb-3 py-3 rounded">
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
</body>

</html>
