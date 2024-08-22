@section('title', 'Sale Agent Dashboard')

@extends('layouts.app')

@section('content')

    <style>
        /* Dropdown Styling */
        .dropdown-menu {
            border-radius: 10px;
            border: 1px solid #B45F04;
            padding: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            width: 200px;
            /* Adjust the width to your preference */
        }

        .dropdown-item {
            padding: 10px 15px;
            font-size: 16px;
            color: #333;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .dropdown-item:hover {
            background-color: #f0f0f0;
            border-radius: 5px;
        }

        .dropdown-item input[type="radio"] {
            margin-right: 10px;
        }

        /* Customizing the dropdown toggle appearance */
        .dropdown-toggle {
            cursor: pointer;
            padding: 5px;
            font-weight: bold;
        }

        .dropdown-toggle::after {
            display: none;
        }
    </style>

<div class="container-max-height">
    <div class="container-fluid mb-2">
        <div class="table-title d-flex justify-content-between align-items-center mb-3">
            <h2 class="ml-3 mb-2 font-educ"><strong>Sales Agents</strong></h2>
            <div class="d-flex align-items-center">
                <button class="btn hover-action add-sales-agent-button mr-3" data-toggle="modal"
                    data-target="#addSalesAgentModal">
                    <i style="font-size: 22px;" class="fa-solid fa-square-plus p-1"></i>
                </button>
            </div>
        </div>

        <!-- Search Bar Section -->
        <div class="search-box d-flex align-items-center mb-2" style="max-width: 350px;">
            <input type="search" class="form-control mr-1" placeholder="Search" id="search-input" aria-label="Search">
            <button class="btn hover-action mx-1" type="submit" data-toggle="tooltip" title="Search">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>

    </div>

    <div class="">
        <table id="sales-agents-table" class="table table-striped table-bordered mt-2">
            <thead class="font-educ text-center">
                <tr>
                    <th class="h5" scope="col">No #</th>
                    <th scope="col" id="name-header">Agents
                        <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-agent"
                            onclick="sortByColumn('agent', 'asc'); toggleSort('sortDown-agent', 'sortUp-agent')"></i>
                        <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-agent"
                            onclick="sortByColumn('agent', 'desc'); toggleSort('sortUp-agent', 'sortDown-agent')"
                            style="display: none;"></i>
                    </th>
                    <th scope="col" id="country-header">Country
                        <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a" id="sortDown-country"
                            onclick="sortByColumn('country', 'asc'); toggleSort('sortDown-country', 'sortUp-country')"></i>
                        <i class="ml-2 fa-sharp fa-solid fa-arrow-up-a-z" id="sortUp-country"
                            onclick="sortByColumn('country', 'desc'); toggleSort('sortUp-country', 'sortDown-country')"
                            style="display: none;"></i>
                    </th>
                    <th class="h5" scope="col">
                        <div class="dropdown">
                            <span>Total Assigned Contacts</span>
                            <span class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false"><i style="cursor: pointer;" class="fa-solid fa-filter" id="filterIcon"
                                    onclick="toggleFilter()" aria-hidden="true"></i></span>

                            <div class="dropdown-menu"
                                style="border-radius: 10px; border: 1px solid #B45F04; padding: 10px;">
                                <label class="dropdown-item">
                                    <input type="radio" name="filter" value="3days"> By 3 days
                                </label>
                                <label class="dropdown-item">
                                    <input type="radio" name="filter" value="week"> By Week
                                </label>
                                <label class="dropdown-item">
                                    <input type="radio" name="filter" value="month"> By Month
                                </label>
                                <label class="dropdown-item">
                                    <input type="radio" name="filter" value="quarter"> By Quarter
                                </label>
                                <label class="dropdown-item">
                                    <input type="radio" name="filter" value="year"> By Year
                                </label>
                            </div>
                        </div>
                    </th>
                    <th class="h5" scope="col">Total Hubspot Sync Contacts</th>
                    <th class="h5" scope="col">Status </th>
                    <th class="h5" scope="col">Current Engaging Contacts</th>
                    <th class="h5" scope="col">View List/Edit</th>
                </tr>
            </thead>
            <tbody class="text-center bg-row fonts">
                <tr>
                    <td>1</td>
                    <td>
                        <div class="d-flex align-items-center">
                            {{-- <img src="/path/to/avatar.png" alt="John Doe" class="avatar"> --}}
                            <i class="fa-solid fa-user mr-4" style="font-size: 50px"></i>
                            <div>
                                <p class="mb-2">John Doe</p>
                                <p class="text-muted small mb-0">john.doe@example.com</p>
                            </div>
                        </div>
                    </td>
                    <td>Malaysia</td>
                    <td>20</td>
                    <td>8</td>
                    <td>
                        <span class="status-indicator" style="background-color:#9bffb2; color: green;">
                            Active
                        </span>
                    </td>
                    <td>6</td>
                    <td><a href=" #" class="btn hover-action" data-toggle="tooltip" title="View">
                            <i class="fa-solid fa-eye "></i>
                        </a>
                        <a href="#" class="btn hover-action" data-toggle="tooltip" title="">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-user mr-4" style="font-size: 50px"></i>
                            <div>
                                <p class="mb-2">Jane Smith</p>
                                <p class="text-muted small mb-0">jane.smith@example.com</p>
                            </div>
                        </div>
                    </td>
                    <td>USA</td>
                    <td>25</td>
                    <td>12</td>
                    <td>
                        <span class="status-indicator" style="background-color:#E2E3E5; color: #303030;">
                            On Leave
                        </span>
                    </td>
                    <td>10</td>
                    <td><a href="#" class="btn hover-action" data-toggle="tooltip" title="View">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <a href="#" class="btn hover-action" data-toggle="tooltip" title="Edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-user mr-4" style="font-size: 50px"></i>
                            <div>
                                <p class="mb-2">Carlos Martinez</p>
                                <p class="text-muted small mb-0">carlos.martinez@example.com</p>
                            </div>
                        </div>
                    </td>
                    <td>Spain</td>
                    <td>18</td>
                    <td>10</td>
                    <td>
                        <span class="status-indicator" style="background-color:#9bffb2; color: green;">
                            Active
                        </span>
                    </td>
                    <td>9</td>
                    <td><a href="#" class="btn hover-action" data-toggle="tooltip" title="View">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <a href="#" class="btn hover-action" data-toggle="tooltip" title="Edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-user mr-4" style="font-size: 50px"></i>
                            <div>
                                <p class="mb-2">Aisha Khan</p>
                                <p class="text-muted small mb-0">aisha.khan@example.com</p>
                            </div>
                        </div>
                    </td>
                    <td>Pakistan</td>
                    <td>22</td>
                    <td>15</td>
                    <td>
                        <span class="status-indicator" style="background-color:#9bffb2; color: green;">
                            Active
                        </span>
                    </td>
                    <td>14</td>
                    <td><a href="#" class="btn hover-action" data-toggle="tooltip" title="View">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <a href="#" class="btn hover-action" data-toggle="tooltip" title="Edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>5</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-user mr-4" style="font-size: 50px"></i>
                            <div>
                                <p class="mb-2">Li Wei</p>
                                <p class="text-muted small mb-0">li.wei@example.com</p>
                            </div>
                        </div>
                    </td>
                    <td>China</td>
                    <td>30</td>
                    <td>20</td>
                    <td>
                        <span class="status-indicator" style="background-color:#E2E3E5; color: #303030;">
                            On Leave
                        </span>
                    </td>
                    <td>18</td>
                    <td><a href="#" class="btn hover-action" data-toggle="tooltip" title="View">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <a href="#" class="btn hover-action" data-toggle="tooltip" title="Edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>6</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-user mr-4" style="font-size: 50px"></i>
                            <div>
                                <p class="mb-2">Emily Davis</p>
                                <p class="text-muted small mb-0">emily.davis@example.com</p>
                            </div>
                        </div>
                    </td>
                    <td>UK</td>
                    <td>24</td>
                    <td>14</td>
                    <td>
                        <span class="status-indicator" style="background-color:#E2E3E5; color: #303030;">
                            On Leave
                        </span>
                    </td>
                    <td>12</td>
                    <td><a href="#" class="btn hover-action" data-toggle="tooltip" title="View">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <a href="#" class="btn hover-action" data-toggle="tooltip" title="Edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>7</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-user mr-4" style="font-size: 50px"></i>
                            <div>
                                <p class="mb-2">Ahmed Al-Farsi</p>
                                <p class="text-muted small mb-0">ahmed.al-farsi@example.com</p>
                            </div>
                        </div>
                    </td>
                    <td>UAE</td>
                    <td>28</td>
                    <td>18</td>
                    <td>
                        <span class="status-indicator" style="background-color:#9bffb2; color: green;">
                            Active
                        </span>
                    </td>
                    <td>15</td>
                    <td><a href="#" class="btn hover-action" data-toggle="tooltip" title="View">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <a href="#" class="btn hover-action" data-toggle="tooltip" title="Edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>8</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-user mr-4" style="font-size: 50px"></i>
                            <div>
                                <p class="mb-2">Anna Ivanova</p>
                                <p class="text-muted small mb-0">anna.ivanova@example.com</p>
                            </div>
                        </div>
                    </td>
                    <td>Russia</td>
                    <td>19</td>
                    <td>10</td>
                    <td>
                        <span class="status-indicator" style="background-color:#9bffb2; color: green;">
                            Active
                        </span>
                    </td>
                    <td>9</td>
                    <td><a href="#" class="btn hover-action" data-toggle="tooltip" title="View">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <a href="#" class="btn hover-action" data-toggle="tooltip" title="Edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>9</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-user mr-4" style="font-size: 50px"></i>
                            <div>
                                <p class="mb-2">Sophia Garcia</p>
                                <p class="text-muted small mb-0">sophia.garcia@example.com</p>
                            </div>
                        </div>
                    </td>
                    <td>Argentina</td>
                    <td>15</td>
                    <td>8</td>
                    <td>
                        <span class="status-indicator" style="background-color:#9bffb2; color: green;">
                            Active
                        </span>
                    </td>
                    <td>7</td>
                    <td><a href="#" class="btn hover-action" data-toggle="tooltip" title="View">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <a href="#" class="btn hover-action" data-toggle="tooltip" title="Edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>10</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-user mr-4" style="font-size: 50px"></i>
                            <div>
                                <p class="mb-2">Yuki Tanaka</p>
                                <p class="text-muted small mb-0">yuki.tanaka@example.com</p>
                            </div>
                        </div>
                    </td>
                    <td>Japan</td>
                    <td>21</td>
                    <td>13</td>
                    <td>
                        <span class="status-indicator" style="background-color:#E2E3E5; color: #303030;">
                            On Leave
                        </span>
                    </td>
                    <td>11</td>
                    <td><a href="#" class="btn hover-action" data-toggle="tooltip" title="View">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <a href="#" class="btn hover-action" data-toggle="tooltip" title="Edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>

    <footer aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
            <li class="page-item">
                <a class="page-link font" href="#">&#60;</a>
            </li>
            <li class="page-item"><a class="page-link font" href="#">1</a></li>
            <li class="page-item"><a class="page-link font" href="#">2</a></li>
            <li class="page-item disabled">
                <span class="page-link font">...</span>
            </li>
            <li class="page-item"><a class="page-link font" href="#">9</a></li>
            <li class="page-item"><a class="page-link font" href="#">10</a></li>
            <li class="page-item">
                <a class="page-link font" href="#">&#62;</a>
            </li>
        </ul>
    </footer>
    </div>
</div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Dropdown toggle functionality
            $('.dropdown-toggle').click(function() {
                $(this).siblings('.dropdown-menu').toggle();
            });

            // Hide the dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.dropdown').length) {
                    $('.dropdown-menu').hide();
                }
            });

            // Handle the filter selection
            $('.dropdown-item input[type="radio"]').change(function() {
                var selectedValue = $(this).val();
                // alert("Selected Filter: " + selectedValue); 
                $('.dropdown-menu').hide();
            });


            /



        });
    </script>

@endsection
