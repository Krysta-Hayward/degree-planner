<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Degree Planner')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
    <header class="site-header">
        <div class="logo">Degree Planner</div>

        <nav>
            <button class="button-links"><a href="/">Home</a></button>
            <button class="button-links">Programs</button>
            <button class="button-links">Courses</button>

            @auth
                @if (auth()->user()->role === 'admin')
                    <button class="button-links">
                        <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                    </button>
                    <button class="button-links">
                        <a href="{{ route('admin.data') }}">Database Overview</a>
                    </button>
                @endif

                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button class="button-links" type="submit">Log Out</button>
                </form>
            @else
                <button class="button-links">
                    <a href="{{ route('login') }}">Log In</a>
                </button>
            @endauth
        </nav>
    </header>

    <main>
        @yield('content')
    </main>
</body>

</html>