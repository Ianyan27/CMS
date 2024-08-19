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
                        <a class="nav-link" href="/dashboard">
                            <i class="fa-solid fa-table-columns mr-3"></i>Dashboard
                        </a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link" href="/"><i class="fa-regular fa-user mr-3"></i>Users
                        </a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="/contactdetails"><i
                                class="fa-solid fa-address-book mr-3"></i>Contact</a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link" href="/salesagent"><i
                                class="fa-solid fa-universal-access mr-3"></i>Sale Agent
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/importcopy"><i class="fa-solid fa-file-import mr-3">
                            </i>Upload Files
                        </a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="/editcontactdetail">Edit Contact Detail</a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link" href="/contact-listing">
                            <i class="fa-solid fa-address-book mr-3"></i>Contact Listing
                        </a>
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
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('search-input').addEventListener('input', function() {
            let input = this.value.toLowerCase();
            let table = document.querySelector('.table');
            let rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                let emailCell = row.querySelectorAll('td')[3]; // Assuming email is in the 4th column
                let email = emailCell.textContent.toLowerCase();

                if (email.includes(input)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        function sortTable(columnName, order) {
            let table, rows, switching, i, x, y, shouldSwitch;
            table = document.querySelector(".table");
            switching = true;
            
            // Loop until no switching has been done
            while (switching) {
                switching = false;
                rows = table.rows;
                
                // Loop through all table rows except the first (headers)
                for (i = 1; i < (rows.length - 1); i++) {
                    shouldSwitch = false;
                    
                    // Determine the column index based on columnName
                    let columnIndex;
                    if (columnName === 'name') {
                        columnIndex = 2; // Index for the 'Name' column
                    }else if(columnName === 'email'){
                        columnIndex = 3; // Indes for the 'Email' column
                    }else if(columnName === 'role') {
                        columnIndex = 4; // Index for the 'Role' column
                    }
                    
                    // Compare the two elements in the column to see if they should switch
                    x = rows[i].querySelectorAll("td")[columnIndex];
                    y = rows[i + 1].querySelectorAll("td")[columnIndex];
                    
                    if (order === 'asc' && x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    } else if (order === 'desc' && x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                }
                
                if (shouldSwitch) {
                    // If a switch has been marked, make the switch and mark the switch as done
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                }
            }
        }
        function toggleFilter() {
            const filterContainer = document.getElementById('filterContainer');
            filterContainer.style.display = (filterContainer.style.display === 'none' 
            || filterContainer.style.display === '') ? 'block' : 'none';
        }

        // Apply the filter to the table
        function applyFilter() {
            const checkboxes = document.querySelectorAll('#filterContainer input[type="checkbox"]');
            const rows = document.querySelectorAll('#contacts-table tbody tr');
            let selectedFilters = [];

            // Gather selected filters
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    selectedFilters.push(checkbox.value);
                }
            });

            // Show/hide rows based on filter
            rows.forEach(row => {
                const status = row.getAttribute('data-status');
                if (selectedFilters.length === 0 || selectedFilters.includes(status)) {
                    row.style.display = ''; // Show row
                } else {
                    row.style.display = 'none'; // Hide row
                }
            });
            toggleFilter();
        }
</script>

    </script>
</body>

</html>
