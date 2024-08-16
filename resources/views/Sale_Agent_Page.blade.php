@section('title', 'Sale Agent Dashboard')

@extends('layouts.app')

@section('content')
<div class="table-title d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
        <h2 class="ml-3 mb-2 font-educ"><strong>Sales Agent</strong></h2>
    </div>
    <div class="search-box d-flex align-items-center mr-3 mb-2">
        <input type="search" class="form-control mr-1" placeholder="Search" id="search-input" aria-label="Search">
        <button class="btn hover-action mx-1" type="submit" data-toggle="tooltip" title="Search">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
    </div>
</div>
<table class="table mt-2">
    <thead class="font-educ text-center">
        <tr>
            <th scope="col"><input type="checkbox" name="" id=""></th>
            <th class="h5" scope="col">No #</th>
            <th class="h5" scope="col">Agents <i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
            <th class="h5" scope="col">Total Meeting</th>
            <th class="h5" scope="col">Meetings Completed</th>
            <th class="h5" scope="col">Status<i class="ml-2 fa-sharp fa-solid fa-arrow-down-z-a"></i></th>
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
            <td><span style="font-size: 1.5rem;" data-toggle="tooltip" title="In a Meeting" class="p-2 rounded"><i class="meeting fa-solid fa-circle"></i></span></td>
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
            <td><span style="font-size: 1.5rem;" class="p-2 rounded" data-toggle="tooltip" title="Active"><i class="active fa-solid fa-circle-check"></i></span></td>
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
            <td><span style="font-size: 1.5rem;color:rgba(70, 70, 70, 0.623);" class="p-2 rounded" data-toggle="tooltip" title="Offline"><i class="fa-solid fa-circle-xmark"></i><span></td>
            <td>08/13/2024, 1:00 PM</td>
            <td>01:20:11</td>
            <td>0</td>
        </tr>
        <tr>
            <td><input type="checkbox" name="" id=""></td>
            <td>4</td>
            <td>Emily White</td>
            <td>28</td>
            <td>23</td>
            <td><span style="font-size: 1.5rem;" data-toggle="tooltip" title="In a Meeting" class="p-2 rounded"><i class="meeting fa-solid fa-circle"></i></span></td>
            <td>08/13/2024, 3:45 PM</td>
            <td>00:45:30</td>
            <td>4</td>
        </tr>
        <tr>
            <td><input type="checkbox" name="" id=""></td>
            <td>5</td>
            <td>Michael Green</td>
            <td>20</td>
            <td>18</td>
            <td><span style="font-size: 1.5rem;" class="p-2 rounded" data-toggle="tooltip" title="Active"><i class="active fa-solid fa-circle-check"></i></span></td>
            <td>08/13/2024, 5:00 PM</td>
            <td>00:25:45</td>
            <td>5</td>
        </tr>
        <tr>
            <td><input type="checkbox" name="" id=""></td>
            <td>6</td>
            <td>Linda Black</td>
            <td>24</td>
            <td>17</td>
            <td><span style="font-size: 1.5rem;color:rgba(70, 70, 70, 0.623);" class="p-2 rounded" data-toggle="tooltip" title="Offline"><i class="fa-solid fa-circle-xmark"></i><span></td>
            <td>08/13/2024, 6:00 PM</td>
            <td>01:10:30</td>
            <td>1</td>
        </tr>
        <tr>
            <td><input type="checkbox" name="" id=""></td>
            <td>7</td>
            <td>David Johnson</td>
            <td>27</td>
            <td>22</td>
            <td><span style="font-size: 1.5rem;" data-toggle="tooltip" title="In a Meeting" class="p-2 rounded"><i class="meeting fa-solid fa-circle"></i></span></td>
            <td>08/13/2024, 7:15 PM</td>
            <td>00:50:22</td>
            <td>3</td>
        </tr>
        <tr>
            <td><input type="checkbox" name="" id=""></td>
            <td>8</td>
            <td>Sarah Wilson</td>
            <td>21</td>
            <td>19</td>
            <td><span style="font-size: 1.5rem;" class="p-2 rounded" data-toggle="tooltip" title="Active"><i class="active fa-solid fa-circle-check"></i></span></td>
            <td>08/13/2024, 8:00 PM</td>
            <td>00:35:20</td>
            <td>4</td>
        </tr>
        <tr>
            <td><input type="checkbox" name="" id=""></td>
            <td>9</td>
            <td>Brian Lee</td>
            <td>26</td>
            <td>21</td>
            <td><span style="font-size: 1.5rem;color:rgba(70, 70, 70, 0.623);" class="p-2 rounded" data-toggle="tooltip" title="Offline"><i class="fa-solid fa-circle-xmark"></i><span></td>
            <td>08/13/2024, 9:30 PM</td>
            <td>01:05:10</td>
            <td>2</td>
        </tr>
        <tr>
            <td><input type="checkbox" name="" id=""></td>
            <td>10</td>
            <td>Lisa Adams</td>
            <td>23</td>
            <td>16</td>
            <td><span style="font-size: 1.5rem;" data-toggle="tooltip" title="In a Meeting" class="p-2 rounded"><i class="meeting fa-solid fa-circle"></i></span></td>
            <td>08/13/2024, 10:45 PM</td>
            <td>00:55:40</td>
            <td>1</td>
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