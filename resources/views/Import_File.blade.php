@section('title', 'Import Files Page')

@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="ml-3 mb-0 font-weight-bold color-white">File Import</h2>
</div>
<div class="p-3 rounded text-center border-dashed bg-white">
    <div class="d-flex align-items-center justify-content-center">
        <div class="mr-3 font-educ drop-files">
            <label for="file-upload" class="d-block mb-2">Drag and drop your files</label>
            <span class="file-support mr-2">File formats we support<i title="CSV" class="fa-solid fa-circle-question ml-1"></i></span>
        </div>
        <div class="ml-3 d-flex">
            <button class="btn button-educ d-flex file-button">
                <div><i class="fa-brands fa-dropbox mr-2" style="font-size: 3rem;"></i></div>
                <div style="align-items: center;"><span>Upload Manually</span></div>
            </button>
        </div>        
    </div>
</div>
<div class="border mt-4 rounded p-3 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="pl-2">Uploaded Files (10)</div>
        <div class="d-flex align-items-center">
            <span class="mr-2 mb-0">Sort by:</span>
            <select name="" id="" class="form-control form-control-sm mb-0">
                <option value="0">Recently Added</option>
            </select>
        </div>
    </div>
    <div class="border-educ-border d-flex justify-content-between align-items-center p-3 rounded mb-2">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-file-excel mr-2"></i>
            <p class="mb-0">Newly Added File</p>
        </div>
        <div class="d-flex align-items-center">
            <div class="progress" style="width: 200px; margin-right: 10px; border-radius: 50px;">
                <div class="progress-bar" role="progressbar" style="width: 50%; background-color: #91264c;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <span>50% Uploading...</span>
        </div>
    </div>
    <div class="border-educ-border d-flex justify-content-between align-items-center p-3 rounded mb-2">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-file-excel mr-2"></i>
            <p class="mb-0">Newly Added File</p>
        </div>
        <div class="d-flex align-items-center">
            <div class="file-info mr-3">5 MB</div>
            <div class="file-info mr-3">John Doe</div>
            <a href="#" class="btn hover-action"><i class="fa-solid fa-eye"></i></a>
        </div>
    </div>
    
    <div class="border-educ-border d-flex justify-content-between align-items-center p-3 rounded mb-2">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-file-excel mr-2"></i>
            <p class="mb-0">Newly Added File</p>
        </div>
        <div class="d-flex align-items-center">
            <div class="file-info mr-3">8 MB</div>
            <div class="file-info mr-3">Jane Smith</div>
            <a href="#" class="btn hover-action"><i class="fa-solid fa-eye"></i></a>
        </div>
    </div>
    
    <div class="border-educ-border d-flex justify-content-between align-items-center p-3 rounded mb-2">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-file-excel mr-2"></i>
            <p class="mb-0">Newly Added File</p>
        </div>
        <div class="d-flex align-items-center">
            <div class="file-info mr-3">12 MB</div>
            <div class="file-info mr-3">Robert Brown</div>
            <a href="#" class="btn hover-action"><i class="fa-solid fa-eye"></i></a>
        </div>
    </div>
    
    <div class="border-educ-border d-flex justify-content-between align-items-center p-3 rounded mb-2">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-file-excel mr-2"></i>
            <p class="mb-0">Newly Added File</p>
        </div>
        <div class="d-flex align-items-center">
            <div class="file-info mr-3">3 MB</div>
            <div class="file-info mr-3">Emily White</div>
            <a href="#" class="btn hover-action"><i class="fa-solid fa-eye"></i></a>
        </div>
    </div>
    
    <div class="border-educ-border d-flex justify-content-between align-items-center p-3 rounded mb-2">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-file-excel mr-2"></i>
            <p class="mb-0">Newly Added File</p>
        </div>
        <div class="d-flex align-items-center">
            <div class="file-info mr-3">7 MB</div>
            <div class="file-info mr-3">Michael Green</div>
            <a href="#" class="btn hover-action"><i class="fa-solid fa-eye"></i></a>
        </div>
    </div>
    
    <div class="border-educ-border d-flex justify-content-between align-items-center p-3 rounded mb-2">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-file-excel mr-2"></i>
            <p class="mb-0">Newly Added File</p>
        </div>
        <div class="d-flex align-items-center">
            <div class="file-info mr-3">10 MB</div>
            <div class="file-info mr-3">Linda Black</div>
            <a href="#" class="btn hover-action"><i class="fa-solid fa-eye"></i></a>
        </div>
    </div>
    
    <div class="border-educ-border d-flex justify-content-between align-items-center p-3 rounded mb-2">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-file-excel mr-2"></i>
            <p class="mb-0">Newly Added File</p>
        </div>
        <div class="d-flex align-items-center">
            <div class="file-info mr-3">6 MB</div>
            <div class="file-info mr-3">David Johnson</div>
            <a href="#" class="btn hover-action"><i class="fa-solid fa-eye"></i></a>
        </div>
    </div>
    
    <div class="border-educ-border d-flex justify-content-between align-items-center p-3 rounded mb-2">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-file-excel mr-2"></i>
            <p class="mb-0">Newly Added File</p>
        </div>
        <div class="d-flex align-items-center">
            <div class="file-info mr-3">4 MB</div>
            <div class="file-info mr-3">Sarah Wilson</div>
            <a href="#" class="btn hover-action"><i class="fa-solid fa-eye"></i></a>
        </div>
    </div>
    
    <div class="border-educ-border d-flex justify-content-between align-items-center p-3 rounded mb-2">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-file-excel mr-2"></i>
            <p class="mb-0">Newly Added File</p>
        </div>
        <div class="d-flex align-items-center">
            <div class="file-info mr-3">9 MB</div>
            <div class="file-info mr-3">Brian Lee</div>
            <a href="#" class="btn hover-action"><i class="fa-solid fa-eye"></i></a>
        </div>
    </div>
    
    <div class="border-educ-border d-flex justify-content-between align-items-center p-3 rounded mb-2">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-file-excel mr-2"></i>
            <p class="mb-0">Newly Added File</p>
        </div>
        <div class="d-flex align-items-center">
            <div class="file-info mr-3">11 MB</div>
            <div class="file-info mr-3">Lisa Adams</div>
            <a href="#" class="btn hover-action"><i class="fa-solid fa-eye"></i></a>
        </div>
    </div>
</div>
@endsection
