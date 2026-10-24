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
<li><a href="#">Student Planner</a></li>
<li><a href="#">Notifications</a></li>
<li><a href="#">Calendar</a></li>
<li><a href="#">Inbox</a></li>
