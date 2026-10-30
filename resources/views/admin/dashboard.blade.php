@extends('layouts.admin-advisor-dashboards')

@section('title', 'Admin Dashboard - Degree Planner')
@section('heading', 'Admin Dashboard')

@section('dashboard')
    <p>Overview of the Degree Planner database.</p>

    <div class="dashboard-cards">
        @foreach ($counts as $label => $count)
            <div class="dashboard-card">
                <h3>{{ $label }}</h3>
                <p class="record-count">{{ $count }}</p>

                @php
                    $viewRoutes = [
                        'Advisors' => 'admin.advisors',
                        'Course Prerequisites' => 'admin.prereqs',
                        'Course Programs' => 'admin.data',
                        'Courses' => 'admin.courses.index',
                        'Plan Courses' => 'admin.data',
                        'Plans' => 'admin.plans',
                        'Programs' => 'admin.programs',
                        'Students' => 'admin.students',
                        'Terms' => 'admin.terms',
                        'Users' => 'admin.users',
                    ];
                @endphp

                <a href="{{ route($viewRoutes[$label] ?? 'admin.data') }}">
                    Manage {{ $label }}
                </a>
            </div>
        @endforeach
    </div>
@endsection