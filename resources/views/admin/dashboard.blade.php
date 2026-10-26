@extends('layouts.app')

@section('title', 'Admin Dashboard - Degree Planner')

@section('content')
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <h2>Degree Planner</h2>
            <p>Admin Panel</p>

            <nav>
                <a href="{{ route('admin.dashboard') }}" class="active">Dashboard</a>
                <a href="{{ route('admin.data') }}">Database Overview</a>
                <a href="/">Home</a>
            </nav>
        </aside>

        <main class="admin-content">
            <h1>Admin Dashboard</h1>
            <p>Overview of the Degree Planner database.</p>

            <div class="dashboard-cards">
                @foreach ($counts as $label => $count)
                    <div class="dashboard-card">
                        <h3>{{ $label }}</h3>
                        <p class="record-count">{{ $count }}</p>
                        @php
                            $viewRoutes = [
                                'Users' => 'admin.users',
                                'Advisors' => 'admin.advisors',
                                'Students' => 'admin.students',
                                'Courses' => 'admin.courses',
                                'Programs' => 'admin.programs',
                                'Terms' => 'admin.terms',
                                'Plans' => 'admin.plans',
                                'Plan Courses' => 'admin.data',
                                'Course Programs' => 'admin.data',
                                'Course Prerequisites' => 'admin.prereqs',
                            ];
                        @endphp

                        <a href="{{ route($viewRoutes[$label] ?? 'admin.data') }}">
                            Manage {{ $label }}
                        </a>
                    </div>
                @endforeach
            </div>
        </main>
    </div>
@endsection