<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Degree Planner')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    <script src="{{ asset('js/theme.js') }}"></script>
</head>

<body>
    <header class="site-header">
        <div class="logo">Degree Planner</div>

        <nav>
            <button class="button-links"><a href="/">Home</a></button>
            <button class="button-links">Programs</button>
            <button class="button-links">Courses</button>
            <button class="button-links theme-toggle" type="button" aria-pressed="false">Dark Mode</button>

            @auth
                @if (auth()->user()->role === 'admin')
                    <button class="button-links">
                        <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                    </button>
                    <button class="button-links">
                        <a href="{{ route('admin.data') }}">Database Overview</a>
                    </button>
                @elseif (auth()->user()->role === 'advisor')
                    <button class="button-links">
                        <a href="{{ route('advisor.dashboard') }}">Advisor Dashboard</a>
                    </button>
                @endif

                @if (auth()->user()->role === 'student')
                    <button class="button-links">
                        <a href="{{ route('student.dashboard') }}">My Degree Plan</a>
                    </button>
                @endif

                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button class="button-links" type="submit">Log Out</button>
                </form>

                @auth
                    <span>
                        {{ Auth::user()->name }}
                        ({{ ucfirst(Auth::user()->role) }})
                    </span>
                @endauth

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