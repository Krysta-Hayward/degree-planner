<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Degree Planner</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <h2>Degree Planner</h2>
            <p>Admin Panel</p>

            <nav>
                <a href="{{ route('admin.dashboard') }}" class="active">Dashboard</a>
                <a href="{{ route('admin.data') }}">Database Overview</a>
                <a href="/">Home</a>
            </nav>
        </aside>

        <main class="admin-content">
            <h1>Admin Dashboard</h1>
            <p>Overview of the Degree Planner database.</p>

            <div class="dashboard-cards">
                @foreach ($counts as $label => $count)
                    <div class="dashboard-card">
                        <h3>{{ $label }}</h3>
                        <p class="record-count">{{ $count }}</p>
                        <a href="{{ route('admin.data') }}">View Database</a>
                    </div>
                @endforeach
            </div>
        </main>
    </div>
</body>

</html>