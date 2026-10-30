<aside class="admin-sidebar">
    <h2>Degree Planner</h2>
    <p>{{ ucfirst(auth()->user()->role) }} Panel</p>

    <nav>
        @if (auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                Dashboard
            </a>

            <a href="{{ route('admin.users') }}">Users</a>
            <a href="{{ route('admin.advisors') }}">Advisors</a>
            <a href="{{ route('admin.students') }}">Students</a>
            <a href="{{ route('admin.courses') }}">Courses</a>
            <a href="{{ route('admin.plans') }}">Plans</a>
            <a href="{{ route('admin.prereqs') }}">Prerequisites</a>
            <a href="{{ route('admin.programs') }}">Programs</a>
            <a href="{{ route('admin.terms') }}">Terms</a>

        @elseif (auth()->user()->role === 'advisor')
            @include('partials.advisor.navbar')
        @endif

    </nav>
</aside>