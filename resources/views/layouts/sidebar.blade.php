<aside class="admin-sidebar">
    <h2>Degree Planner</h2>
    <p>{{ ucfirst(auth()->user()->role) }} Panel</p>

    <nav>
        @if (auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('admin.data') }}">Database Overview</a>

            <details>
                <summary>Admin Management</summary>
                <div class="sidebar-submenu">
                    <a href="{{ route('admin.users') }}">Manage Users</a>
                    <a href="{{ route('admin.advisors') }}">Manage Advisors</a>
                    <a href="{{ route('admin.students') }}">Manage Students</a>
                    <a href="{{ route('admin.courses') }}">Manage Courses</a>
                    <a href="{{ route('admin.programs') }}">Manage Programs</a>
                    <a href="{{ route('admin.terms') }}">Manage Terms</a>
                    <a href="{{ route('admin.plans') }}">Manage Plans</a>
                </div>
            </details>

        @elseif (auth()->user()->role === 'advisor')
            <a href="{{ route('advisor.dashboard') }}">Dashboard</a>
            <a href="{{ route('advisor.students.index') }}">My Students</a>

        @elseif (auth()->user()->role === 'student')
            <a href="{{ route('student.dashboard') }}">My Degree Plan</a>
        @endif

        <a href="/">Home</a>
    </nav>
</aside>