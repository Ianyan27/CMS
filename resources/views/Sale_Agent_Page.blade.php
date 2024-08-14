@section('title', 'Sale Agent Dashboard')

@extends('layouts.app')

@section('content')
<div class="table-title d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
        <h2 class="ml-3 mb-2 font"><strong>Contacts</strong></h2>
    </div>
    <div class="search-box d-flex align-items-center mr-3 mb-2">
        <input type="search" class="form-control mr-1" placeholder="Enter email" id="search-input" aria-label="Search">
        <button class="btn btn-secondary bg-educ mx-1" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        <i title="Enter the email you're looking for." class="fa-solid fa-circle-question font mx-2 text-center"></i>
    </div>
</div>
<table class="table mt-2">
    <thead class="font text-center">
        <tr>
            <th scope="col"><input type="checkbox" name="" id=""></th>
            <th class="h5" scope="col">No #</th>
            <th class="h5" scope="col">Agents <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
            <th class="h5" scope="col">Total Meeting</th>
            <th class="h5" scope="col">Meetings Completed</th>
            <th class="h5" scope="col">Status</th>
            <th class="h5" scope="col">Next Meeting Time</th>
            <th class="h5" scope="col">Duration</th>
            <th class="h5" scope="col">Canceled Meetings</th>
        </tr>
    </thead>
    <tbody class="text-center bg-row fonts">
        <tr>
            <td><input type="checkbox" name="" id=""></td>
            <td>1</td>
            <td>John Doe</td>
            <td>25</td>
            <td>20</td>
            <td><span class="agent-meeting p-2 rounded color-white">In a meeting</span></td>
            <td>08/13/2024, 2:30 PM</td>
            <td>00:30:12</td>
            <td>3</td>
        </tr>
        <tr>
            <td><input type="checkbox" name="" id=""></td>
            <td>2</td>
            <td>Jane Smith</td>
            <td>18</td>
            <td>15</td>
            <td><span class="agent-active p-2 rounded">Active</span></td>
            <td>08/13/2024, 4:00 PM</td>
            <td>00:20:11</td>
            <td>2</td>
        </tr>
        <tr>
            <td><input type="checkbox" name="" id=""></td>
            <td>3</td>
            <td>Robert Brown</td>
            <td>22</td>
            <td>19</td>
            <td><span class="agent-offline p-2 rounded">Offline</span></td>
            <td>08/13/2024, 1:00 PM</td>
            <td>01:20:11</td>
            <td>0</td>
        </tr>
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