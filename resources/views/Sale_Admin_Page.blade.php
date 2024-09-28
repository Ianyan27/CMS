@section('title', 'Sale Admin Page')

@extends('layouts.app')

@section('content')

<div class="container-max-height d-flex justify-content-center align-items-center">
    <div class="sale-admin-container rounded border-educ">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="headings">Import CSV</h5>
            <img src="{{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png') }}" alt="Company Logo"
                    style="height: 30px;">
        </div>
        <div class="row my-3">
            <div class="col-md-6 my-2 px-3">
                <div class="row">
                    <div class="alert alert-danger d-none" id="platformValidationMsg" role="alert"
                        style="font-size: medium">
                        Please Select Platform *
                    </div>
                    <label class="font-educ" for="platform">Select Platform:</label>
                    <select id="platform" class="w-100 platforms search-bar" name="platform" onchange="updatePlatformSelection(); updateSelectedValues()">
                        <option value="" selected disabled>Select Platform</option>
                        <option value="linkedin">LinkedIn</option>
                        <option value="apollo">Apollo</option>
                        <option value="raw">Raw</option>
                    </select>
                    <label class="font-educ" for="buDropdown">Select BU:</label>
                    <select id="buDropdown" class="w-100 platforms search-bar" name="business_unit" onchange="updateCountryCheckboxes(); updateSelectedValues()">
                        <option value="">Select BU</option>
                        <option value="SG Retail">SG Retail</option>
                        <option value="HED">HED</option>
                        <option value="Alliance">Alliance</option>
                        <option value="Enterprise International">Enterprise International</option>
                        <option value="Enterprise Singapore">Enterprise Singapore</option>
                        <option value="Talent Management">Talent Management</option>
                    </select>
                </div>
                <div class="row mt-2">
                    <label class="font-educ">Select Country:</label>
                    <div id="countryCheckboxes" class="w-100 platforms search-bar" name="country" style="height:auto"></div> <!-- Checkboxes will be dynamically added here -->
                </div>
                <div class="row mt-2">
                    <label class="font-educ">Selected Countries:</label>
                    <div id="selectedCountriesList" class="w-100 platforms search-bar" name="countries[]" style="min-height: 50px;"></div> <!-- List of selected countries -->
                </div>
                <div class="row mt-2">
                    <label class="font-educ" for="buhDropdown">Select BUH:</label>
                    <select id="buhDropdown" class="w-100 platforms search-bar" name="business_unit_head" onchange="updateSelectedValues()">
                        <option value="" selected disabled>Select BUH</option>
                    </select>
                </div>
            </div>
            <div class="summary-container shadow-lg col-md-5 mx-5">
                <div class="d-flex justify-content-center font-educ h3">
                    Summary
                </div>
                <div class="summary row">
                    <div class="col-6 font-educ">
                        <p>Selected Platform:</p>
                    </div>
                    <div class="col-6">
                        <span id="selectedPlatform">None</span>
                    </div>
                </div>
                <div class="summary row">
                    <div class="col-6 font-educ">
                        <p>Selected BU: </p>
                    </div>
                    <div class="col-6">
                        <span id="selectedBU">None</span>
                    </div>
                </div>
                <div class="summary row">
                    <div class="col-6 font-educ">
                        <p>Selected Countries: </p>
                    </div>
                    <div class="col-6">
                        <span id="selectedCountry">None</span>
                    </div>
                </div>
                <div class="summary row">
                    <div class="col-6 font-educ">
                        <p>Selected BUH:</p>
                    </div>
                    <div class="col-6">
                        <span id="selectedBUH">None</span>
                    </div>
                </div>
                <div class="text-center mb-4">
                    <div class="card-body justify-content-center align-items-center drop-zone" id="dropZone">
                        <div class="mx-5">
                            <h5 class="mb-4 font-educ">Drag and drop your files</h5>
                            <p class="mb-4" title="The uploaded file must be a file of type: csv">File
                                formats we support <i class="fas fa-info-circle"></i></p>
                        </div>
                        @csrf
                        <div>
                            <input accept=".csv" type="file" name="csv_file" required id="fileInput" class="d-none">
                        </div>
                        <div>
                            <label for="fileInput" class="btn hover-action mt-4">Import Manually</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    
    

@endsection

<script src=" {{ URL::asset('js/selection_before_import.js') }} "></script>