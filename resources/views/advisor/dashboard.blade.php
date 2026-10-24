@extends('layouts.advisor')

@section('title', 'Advisor Dashboard')

@section('heading', 'Advisor Dashboard')

@section('content')

    <section class="stats">
        <div class="card">
            <p class="card-label">Assigned students</p>
            <p class="card-number">{{ $studentCount }}</p>
        </div>
        <div class="card">
            <p class="card-label">Plans awaiting review</p>
            <p class="card-number">{{ $awaitingReview->count() }}</p>
        </div>
        <div class="card">
            <p class="card-label">Needs attention</p>
            <p class="card-number">{{ $flaggedStudents->count() }}</p>
        </div>
    </section>

    <section class="sections">
        <div class="card">
            <h2>Plans awaiting review</h2>
            <p class="empty">No plans waiting for review.</p>
        </div>
        <div class="card">
            <h2>Students needing attention</h2>
            <p class="empty">No students flagged.</p>
        </div>
    </section>

    <section class="card schedule">
        <h2>Schedule</h2>
        <p class="empty">Schedule will go here.</p>
    </section>

@endsection