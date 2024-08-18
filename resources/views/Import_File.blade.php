@section('title', 'Import Files Page')

@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="ml-3 mb-0 font-weight-bold color-white">File Import</h2>
</div>
<div class="p-3 rounded text-center border-dashed bg-white">
    <div class="d-flex align-items-center justify-content-center">
        <div class="mr-3 drop-files">
            <label for="file-upload" class="d-block mb-2 font-educ">Drag and drop your files</label>
            <span class="file-support mr-2">File formats we support<i title="CSV" class="fa-solid fa-circle-question ml-1 font-educ"></i></span>
        </div>
        <div class="ml-3 d-flex">
            <button class="btn button-educ d-flex file-button">
                <div><i class="fa-brands fa-dropbox mr-2" style="font-size: 3rem;"></i></div>
                <div style="align-items: center;"><span>Import Manually</span></div>
            </button>
        </div>        
    </div>
</div>
<div class="border mt-4 rounded p-3 bg-white">
    <div class="row mb-2 align-items-center">
        <div class="col">
            <span class="font-weight-bold">Imported Files (10)</span>
        </div>
        <div class="col text-right">
            <label for="sortFiles" class="mr-2 mb-0">Sorted By:</label>
            <select name="sortFiles" id="sortFiles" class="form-control d-inline-block w-auto">
                <option value="">Recently Added</option>
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
    <div class="border-educ-border p-3">
        <div class="row align-items-center">
            <div class="col-6">
                <p class="mb-0">Newly Added File</p>
            </div>
            <div class="col-2 text-center">
                <span>5MB</span>
            </div>
            <div class="col-2 text-right">
                <span>John Doe</span>
            </div>
            <div class="col-2 text-right">
                <a href="#" class="btn hover-action"><i class="fa-solid fa-eye"></i></a>
            </div>
        </div>
    </div>
    <div class="border-educ-border p-3">
        <div class="row align-items-center">
            <div class="col-6">
                <p class="mb-0">Newly Added File</p>
            </div>
            <div class="col-2 text-center">
                <span>10MB</span>
            </div>
            <div class="col-2 text-right">
                <span>Jane Smith</span>
            </div>
            <div class="col-2 text-right">
                <a href="#" class="btn hover-action"><i class="fa-solid fa-eye"></i></a>
            </div>
        </div>
    </div>
    
    <div class="border-educ-border p-3">
        <div class="row align-items-center">
            <div class="col-6">
                <p class="mb-0">Newly Added File</p>
            </div>
            <div class="col-2 text-center">
                <span>15MB</span>
            </div>
            <div class="col-2 text-right">
                <span>Michael Johnson</span>
            </div>
            <div class="col-2 text-right">
                <a href="#" class="btn hover-action"><i class="fa-solid fa-eye"></i></a>
            </div>
        </div>
    </div>
    
    <div class="border-educ-border p-3">
        <div class="row align-items-center">
            <div class="col-6">
                <p class="mb-0">Newly Added File</p>
            </div>
            <div class="col-2 text-center">
                <span>20MB</span>
            </div>
            <div class="col-2 text-right">
                <span>Emily Davis</span>
            </div>
            <div class="col-2 text-right">
                <a href="#" class="btn hover-action"><i class="fa-solid fa-eye"></i></a>
            </div>
        </div>
    </div>
    
    <div class="border-educ-border p-3">
        <div class="row align-items-center">
            <div class="col-6">
                <p class="mb-0">Newly Added File</p>
            </div>
            <div class="col-2 text-center">
                <span>25MB</span>
            </div>
            <div class="col-2 text-right">
                <span>David Brown</span>
            </div>
            <div class="col-2 text-right">
                <a href="#" class="btn hover-action"><i class="fa-solid fa-eye"></i></a>
            </div>
        </div>
    </div>
    
    <div class="border-educ-border p-3">
        <div class="row align-items-center">
            <div class="col-6">
                <p class="mb-0">Newly Added File</p>
            </div>
            <div class="col-2 text-center">
                <span>30MB</span>
            </div>
            <div class="col-2 text-right">
                <span>Alice Wilson</span>
            </div>
            <div class="col-2 text-right">
                <a href="#" class="btn hover-action"><i class="fa-solid fa-eye"></i></a>
            </div>
        </div>
    </div>
    
    <div class="border-educ-border p-3">
        <div class="row align-items-center">
            <div class="col-6">
                <p class="mb-0">Newly Added File</p>
            </div>
            <div class="col-2 text-center">
                <span>35MB</span>
            </div>
            <div class="col-2 text-right">
                <span>Chris Lee</span>
            </div>
            <div class="col-2 text-right">
                <a href="#" class="btn hover-action"><i class="fa-solid fa-eye"></i></a>
            </div>
        </div>
    </div>
    
    <div class="border-educ-border p-3">
        <div class="row align-items-center">
            <div class="col-6">
                <p class="mb-0">Newly Added File</p>
            </div>
            <div class="col-2 text-center">
                <span>40MB</span>
            </div>
            <div class="col-2 text-right">
                <span>Laura Martinez</span>
            </div>
            <div class="col-2 text-right">
                <a href="#" class="btn hover-action"><i class="fa-solid fa-eye"></i></a>
            </div>
        </div>
    </div>
    
    <div class="border-educ-border p-3">
        <div class="row align-items-center">
            <div class="col-6">
                <p class="mb-0">Newly Added File </p>
            </div>
            <div class="col-2 text-center">
                <span>45MB</span>
            </div>
            <div class="col-2 text-right">
                <span>Kevin Garcia</span>
            </div>
            <div class="col-2 text-right">
                <a href="#" class="btn hover-action"><i class="fa-solid fa-eye"></i></a>
            </div>
        </div>
    </div>
    
    <div class="border-educ-border p-3">
        <div class="row align-items-center">
            <div class="col-6">
                <p class="mb-0">Newly Added File</p>
            </div>
            <div class="col-2 text-center">
                <span>50MB</span>
            </div>
            <div class="col-2 text-right">
                <span>Olivia Clark</span>
            </div>
            <div class="col-2 text-right">
                <a href="#" class="btn hover-action"><i class="fa-solid fa-eye"></i></a>
            </div>
        </div>
    </div>
    
    <div class="border-educ-border p-3">
        <div class="row align-items-center">
            <div class="col-6">
                <p class="mb-0">Newly Added File</p>
            </div>
            <div class="col-2 text-center">
                <span>55MB</span>
            </div>
            <div class="col-2 text-right">
                <span>Daniel Lewis</span>
            </div>
            <div class="col-2 text-right">
                <a href="#" class="btn hover-action"><i class="fa-solid fa-eye"></i></a>
            </div>
        </div>
    </div>
    
</div>
@endsection
