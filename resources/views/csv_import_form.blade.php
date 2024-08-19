@extends('layouts.app')

@section('title', 'Import Files Page')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="ml-3 mb-0 font-weight-bold color-white">File Import</h2>
    </div>

    <div class="p-3 rounded text-center border-dashed bg-white">
        <div class="d-flex align-items-center justify-content-center">
            <div class="mr-3 drop-files">
                <label for="file-upload" class="d-block mb-2 font-educ">Drag and drop your files</label>
                <span class="file-support mr-2">File formats we support
                    <i title="CSV" class="fa-solid fa-circle-question ml-1 font-educ"></i>
                </span>
            </div>

            <div class="ml-3 d-flex">
                <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="csv_file" id="file-upload" class="d-none" required>
                    
                    <button class="btn button-educ d-flex file-button" type="button" onclick="document.getElementById('file-upload').click();">
                        <div>
                            <i class="fa-brands fa-dropbox mr-2" style="font-size: 3rem;"></i>
                        </div>
                        <div style="align-items: center;">
                            <span>Import Manually</span>
                        </div>
                    </button>

                    <button class="btn button-educ d-flex file-button" type="submit">
                        <div>
                            <i class="fa-solid fa-upload mr-2" style="font-size: 3rem;"></i>
                        </div>
                        <div style="align-items: center;">
                            <span>Upload File</span>
                        </div>
                    </button>
                </form>
            </div>
        </div>

        @if ($errors->has('csv_file'))
            <div class="alert alert-danger mt-3">
                {{ $errors->first('csv_file') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success mt-3">
                {{ session('success') }}
            </div>
        @endif
    </div>
@endsection