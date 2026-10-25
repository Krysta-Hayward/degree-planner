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
                    @include('partials.advisor.navbar')
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
                @include($advisorPage ?? 'partials.advisor.student-list')
            @endif

        </main>
    </div>
@endsection