<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advisor Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/advisor.css') }}">
</head>
<body>

    <header class="header">
        <div class="container">
            <div>
                <h1>Advisor Dashboard</h1>
                <p class="subtitle">{{ auth()->user()->name }} · {{ auth()->user()->role }}</p>
            </div>
            <!-- Log out Button -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-button">Log out</button>
            </form>
        </div>
    </header>

    <main class="container">

        <section class="stats">
            <div class="card">
                <p class="card-label">Assigned students</p>
                <p class="card-number">{{ $studentCount ?? 0 }}</p>
            </div>
            <div class="card">
                <p class="card-label">Plans awaiting review</p>
                <p class="card-number">0</p>
            </div>
            <div class="card">
                <p class="card-label">Needs attention</p>
                <p class="card-number">0</p>
            </div>
        </section>

        <section class="sections">
            <div class="card">
                <h2>Plans awaiting review</h2>
                <p class="empty">No plans waiting for review.</p>
            </div>
            <div class="card">
                <h2>Students needing attention</h2>
                <p class="empty">No students flagged.</p>
            </div>
        </section>

        <a href="{{ route('advisor.students.index') }}" class="button">View all students</a>

    </main>
</body>
</html>