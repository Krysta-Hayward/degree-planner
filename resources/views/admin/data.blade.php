<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Overview | Degree Planner</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
    <header class="site-header">
        <div class="logo">Degree Planner</div>
        <nav>
            <button class="admin-links"><a href="/">Home</a></button>
            <button class="admin-links"><a href="{{ route('admin.dashboard') }}">Admin Dashboard</a></button>
            <button class="admin-links"><a href="{{ route('admin.data') }}">Database Overview</a></button>
        </nav>
    </header>

    <main>
        <section>
            <h1>Database Overview</h1>
            <p>View the records currently stored in the degree planner.</p>
        </section>

        <section>
            <h2>Database Tables</h2>
        
            <table>
                <thead>
                    <tr>
                        <th>Table Name</th>
                        <th>Records</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tables as $name => $count)
                        <tr>
                            <td>{{ ucwords(str_replace('_', ' ', $name)) }}</td>
                            <td>{{ $count }}</td>
                            <td>
                                <a href="{{ route('admin.data', ['table' => $name]) }}">
                                    View Records
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        @if ($selectedTable)
            <section>
                <h2>{{ ucwords(str_replace('_', ' ', $selectedTable)) }}</h2>
                <p><a href="{{ route('admin.data') }}">Back to overview</a></p>

                @if ($records->isEmpty())
                    <p>No records found in this table.</p>
                @else
                    <div style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    @foreach ($columns as $column)
                                        <th>{{ ucwords(str_replace('_', ' ', $column)) }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($records as $record)
                                    <tr>
                                        @foreach ($columns as $column)
                                            <td>{{ $record->$column ?? '' }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p>Showing up to 100 records.</p>
                @endif
            </section>
        @endif
    </main>
</body>

</html>