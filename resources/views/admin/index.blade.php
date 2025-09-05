<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.bundle.min.css') }}" rel="stylesheet">
</head>

<body class="d-flex" style="min-height: 100vh;">

    <!-- Sidebar -->
    @include('components.sidebar')

    <!-- Main Content -->
    <div class="flex-grow-1 p-4 bg-light">
        <h1>Dashboard</h1>
        <div class="card mt-4">
            <div class="card-body">
                Selamat datang <strong>{{ auth()->user()->username }}</strong>! Ini adalah dashboard utama.
            </div>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

</body>
</html>
