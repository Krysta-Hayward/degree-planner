@extends('layouts.admin-advisor-dashboards')

@section('heading', 'Advisor Dashboard')

@section('dashboard')
    <p>Welcome, {{ auth()->user()->name }}.</p>

    <div class="dashboard-cards">
        <div class="dashboard-card">
            <h3>Assigned Students</h3>
            <p class="record-count">{{ $studentCount }}</p>
            <a href="{{ route('advisor.students.index') }}">View students</a>
        </div>

        <div class="dashboard-card">
            <h3>Plans Awaiting Review</h3>
            <p class="record-count">{{ $awaitingReview }}</p>
            <a href="{{ route('advisor.plans.index') }}">Review plans</a>
        </div>

        <div class="dashboard-card">
            <h3>Needs Attention</h3>
            <p class="record-count">{{ $flaggedStudents }}</p>
        </div>
    </div>
@endsection