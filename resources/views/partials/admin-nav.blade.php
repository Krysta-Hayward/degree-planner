<a href="{{ route('admin.dashboard') }}"
   class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    Dashboard
</a>
<a href="{{ route('admin.data') }}"
   class="{{ request()->routeIs('admin.data') ? 'active' : '' }}">
    Database Overview
</a>