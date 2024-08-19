@extends('layouts.app')

@section('title', 'Import Files Page')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Files Page</title>
    <script>
        // Check if there is an error message to display in a popup
        @if(session('error'))
            window.onload = function() {
                alert("{{ session('error') }}");
            };
        @endif
    </script>
</head>
<body class="bg-gray-100">
    <div class="max-w-lg mx-auto mt-12 p-6 bg-white border border-dashed border-gray-300 rounded-lg text-center">
        <h4 class="text-red-600 text-lg mb-4">Drag and drop your files</h4>
        <p class="text-sm text-gray-500 mb-4">File formats we support <i class="fas fa-info-circle"></i></p>
        <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="csv_file" required class="hidden" id="fileInput">
            <label for="fileInput" class="inline-block bg-purple-800 text-white py-2 px-4 rounded-lg cursor-pointer">
                Import Manually
            </label>
        </form>
    </div>
</body>
</html>
@endsection
