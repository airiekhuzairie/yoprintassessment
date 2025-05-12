<!DOCTYPE html>
<html>
<head>
    <title>CSV Upload</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<h2>Upload CSV File</h2>
<form method="POST" action="{{ route('csv.upload') }}" enctype="multipart/form-data">
    @csrf
    <input type="file" name="csv_file" required>
    <button type="submit">Upload</button>
</form>

<h3>Upload History</h3>
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Filename</th>
            <th>Status</th>
            <th>Uploaded At</th>
        </tr>
    </thead>
    <tbody id="uploadTable">
        @foreach($uploads as $upload)
            <tr>
                <td>{{ $upload->id }}</td>
                <td>{{ $upload->filename }}</td>
                <td>{{ $upload->status }}</td>
                <td>{{ $upload->created_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<script>
setInterval(() => {
    fetch("{{ route('csv.status') }}")
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('uploadTable');
            tbody.innerHTML = data.map(row => `
                <tr>
                    <td>${row.id}</td>
                    <td>${row.filename}</td>
                    <td>${row.status}</td>
                    <td>${row.created_at}</td>
                </tr>
            `).join('');
        });
}, 5000);
</script>
</body>
</html>
