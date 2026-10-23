@extends('layouts.app')

@section('title', ucfirst(auth()->user()->role) . ' Dashboard - Degree Planner')

@section('content')
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <h2>Degree Planner</h2>
            <p>{{ ucfirst(auth()->user()->role) }} Panel</p>

            <nav>
                @if (auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="active">
                        Dashboard
                    </a>

                    <a href="{{ route('admin.data') }}">
                        Database Overview
                    </a>
                @else
                    <a href="{{ route('advisor.dashboard') }}" class="active">
                        Dashboard
                    </a>

                    <a href="{{ route('advisor.students.index') }}">
                        View Students
                    </a>
                @endif

                <a href="/">Home</a>
            </nav>
        </aside>

        <main class="admin-content">
            <h1>{{ ucfirst(auth()->user()->role) }} Dashboard</h1>

            <p>
                Welcome, {{ auth()->user()->name }}.
            </p>

            @if (auth()->user()->role === 'admin')
                <p>Overview of the Degree Planner database.</p>

                <div class="dashboard-cards">
                    @foreach ($counts as $label => $count)
                        <div class="dashboard-card">
                            <h3>{{ $label }}</h3>

                            <p class="record-count">{{ $count }}</p>

                            <a href="{{ route('admin.data') }}">
                                View Database
                            </a>
                        </div>
                    @endforeach
                </div>
            @elseif (auth()->user()->role === 'advisor')
                <div class="dashboard-cards">
                    <div class="dashboard-card">
                        <h3>Assigned Students</h3>
                        <p class="record-count">{{ $studentCount }}</p>

                        <a href="{{ route('advisor.students.index') }}">
                            View Students
                        </a>
                    </div>

                    <div class="dashboard-card">
                        <h3>Plans Awaiting Review</h3>
                        <p class="record-count">0</p>
                    </div>

                    <div class="dashboard-card">
                        <h3>Needs Attention</h3>
                        <p class="record-count">0</p>
                    </div>
                </div>

                <div class="dashboard-cards">
                    <div class="dashboard-card">
                        <h3>Plans Awaiting Review</h3>
                        <p>No plans waiting for review.</p>
                    </div>

                    <div class="dashboard-card">
                        <h3>Students Needing Attention</h3>
                        <p>No students flagged.</p>
                    </div>
                </div>
            @endif
        </main>
    </div>
@endsection