<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HubSpot Contacts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        h1 {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        table th {
            background-color: #f2f2f2;
        }
        .error {
            color: red;
        }
        .button {
            padding: 8px 12px;
            background: #007BFF;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <h1>HubSpot Contacts (50 Records)</h1>

    <!-- Refresh Contacts button -->
    <a class="button" href="{{ route('admin#hubspot-contacts') }}">Refresh Contacts</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Gender</th>
            </tr>
        </thead>
        <tbody>
            @forelse($contacts as $contact)
                <tr>
                    <td>{{ $contact['id'] }}</td>
                    <td>{{ $contact['properties']['firstname'] ?? '' }}</td>
                    <td>{{ $contact['properties']['lastname'] ?? '' }}</td>
                    <td>{{ $contact['properties']['email'] ?? '' }}</td>
                    <td>{{ $contact['properties']['gender'] ?? '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">No contacts found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
