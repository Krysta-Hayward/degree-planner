@extends('layouts.admin-advisor-dashboards')

@section('title', 'Advisor Dashboard - Degree Planner')
@section('heading', 'Advisor Dashboard')

@section('dashboard')
    <p>Welcome, {{ auth()->user()->name }}.</p>
    <div class="dashboard-cards">
        <div class="dashboard-card">
            <h3>Assigned students</h3>
            <p class="record-count">{{ $studentCount }}</p>
            <a href="{{ route('advisor.students.index') }}">View students</a>
        </div>
        <div class="dashboard-card">
            <h3>Plans awaiting review</h3>
            <p class="record-count">{{ $awaitingReview }}</p>
        </div>
        <div class="dashboard-card">
            <h3>Needs attention</h3>
            <p class="record-count">{{ $flaggedStudents }}</p>
        </div>
        @include($advisorPage ?? 'partials.advisor-main')
    </div>
@endsection