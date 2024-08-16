@extends('layouts.app')

@section('title', 'Contact Listing Page')

@section('content')
    <div class="table-title d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <h2 class="ml-3 mb-2 font"><strong>Contact Listing Page</strong></h2>
        </div>
        <div class="search-box d-flex align-items-center mr-3 mb-2">
            <input type="search" class="form-control mr-1" placeholder="Search..." id="search-input" aria-label="Search">
            <button class="btn btn-secondary bg-educ mx-1" type="submit"><i
                    class="fa-solid fa-magnifying-glass"></i></button>
        </div>
    </div>
    <table class="table table-hover mt-2">
        <thead class="font text-center">
            <tr>
                <th scope="col"><input type="checkbox" name="" id=""></th>
                <th class="h5" scope="col">No #</th>
                <th class="h5" scope="col">Name <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                <th class="h5" scope="col">Email <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                <th class="h5" scope="col">Contact</th>
                <th class="h5" scope="col">Country <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
                <th class="h5" scope="col">
                    Status
                    <span class="ml-2" data-bs-toggle="tooltip" data-bs-placement="top"
                        title="Status of the contact: Active, Discarded, New, In Progress, Archived">
                        <i class="fa-solid fa-info-circle text-muted"></i>
                    </span>
                </th>
                <th class="h5" scope="col">Actions</th>
            </tr>
        </thead>
        <tbody class="text-center bg-row fonts">
            @forelse ($contacts as $contact)
                <tr>
                    <td><input type="checkbox" name="" id=""></td>
                    <td>{{ $contact['id'] }}</td>
                    <td>{{ $contact['name'] }}</td>
                    <td>{{ $contact['email'] }}</td>
                    <td>{{ $contact['phone'] }}</td>
                    <td>{{ $contact['country'] }}</td>
                    <td>
                        <span class="status-indicator"
                            style="background-color:
                            @if ($contact['status'] === 'hubspot-contact') #FFE8E2;color:#FF5C35;
                            @elseif ($contact['status'] === 'discard')
                                #FF7F86; color: #BD000C;
                            @elseif ($contact['status'] === 'in_progress')
                                #FFF3CD; color: #FF8300;
                            @elseif ($contact['status'] === 'new')
                                #CCE5FF ; color:  #318FFC;
                            @elseif ($contact['status'] === 'archived')
                            #E2E3E5; color: #303030; @endif
                        ">
                            @if ($contact['status'] === 'hubspot-contact')
                                HubSpot
                            @elseif ($contact['status'] === 'discard')
                                Discard
                            @elseif ($contact['status'] === 'in_progress')
                                In Progress
                            @elseif ($contact['status'] === 'new')
                                New
                            @elseif ($contact['status'] === 'archived')
                                Archive
                            @endif
                        </span>
                    </td>
                    <td>
                        <a href="/editcontactdetail" class="btn hover-action"><i class="fa-solid fa-eye "
                                style="font-size: 1.5rem"></i></a>
                        <a href="#" class="btn hover-action"><i class="fa-solid fa-pen-to-square"></i></a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No contacts found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
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
@endsection

@section('scripts')
    <!-- Add Bootstrap Tooltip Initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection
