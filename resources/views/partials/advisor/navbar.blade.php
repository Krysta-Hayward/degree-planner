<ul class="advisor-nav">
    <li>
        <a href="{{ route('advisor.dashboard') }}"
        class="{{ request()->routeIs('advisor.dashboard') ? 'active' : '' }}">
        Dashboard
        </a>
    </li>
    <li>
        <a href="{{ route('advisor.students.index') }}"
        class="{{ request()->routeIs('advisor.students.*') ? 'active' : '' }}">
        My Students
        </a>
    </li>
    <li>
        <a href="{{ route('advisor.plans.index') }}"
        class="{{ request()->routeIs('advisor.plans.*') ? 'active' : '' }}">
        Plan Reviews
        </a>
    </li>
</ul>


