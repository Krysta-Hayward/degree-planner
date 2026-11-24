<aside class="admin-sidebar">
    <h2>Degree Planner</h2>
    <p>{{ ucfirst(auth()->user()->role) }} Panel</p>

    <div class="admin-nav-menu">
        <input type="checkbox" id="admin-nav-toggle">

        <label for="admin-nav-toggle" class="admin-nav-toggle">
            ☰ Menu
        </label>

        <nav>
            @if (auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}"
                    class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>

                <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                    Users
                </a>

                <a href="{{ route('admin.advisors') }}" class="{{ request()->routeIs('admin.advisors') ? 'active' : '' }}">
                    Advisors
                </a>

                <a href="{{ route('admin.students') }}" class="{{ request()->routeIs('admin.students') ? 'active' : '' }}">
                    Students
                </a>

                <a href="{{ route('admin.classrooms.index') }}"
                    class="{{ request()->routeIs('admin.classrooms.*') ? 'active' : '' }}">
                    Classrooms
                </a>

                <a href="{{ route('admin.courses.index') }}"
                    class="{{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
                    Courses
                </a>

                <a href="{{ route('admin.plans') }}" class="{{ request()->routeIs('admin.plans') ? 'active' : '' }}">
                    Plans
                </a>

                <a href="{{ route('admin.prereqs.index') }}"
                    class="{{ request()->routeIs('admin.prereqs.*') ? 'active' : '' }}">
                    Prerequisites
                </a>

                <a href="{{ route('admin.programs.index') }}"
                    class="{{ request()->routeIs('admin.programs.*') ? 'active' : '' }}">
                    Programs
                </a>

                <a href="{{ route('admin.terms.index') }}"
                    class="{{ request()->routeIs('admin.terms.*') ? 'active' : '' }}">
                    Terms
                </a>

            @elseif (auth()->user()->role === 'advisor')
                @include('partials.advisor.navbar')
            @endif
        </nav>
    </div>
</aside>