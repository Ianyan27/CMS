<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import CSV</title>
    <script>
        // Check if there is an error message to display in a popup
        @if(session('error'))
            window.onload = function() {
                alert("{{ session('error') }}");
            };
        @endif
    </script>
</head>
<body>
    <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="csv_file" required>
        <button type="submit">Import CSV</button>
    </form>
</body>
</html>
 
 