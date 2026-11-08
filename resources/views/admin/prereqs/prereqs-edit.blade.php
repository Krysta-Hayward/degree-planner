@extends('layouts.app')

@section('title', 'Edit Prerequisite - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Edit Prerequisites for: {{ $prereq->courseDetails->courseNumber }}
            - {{ $prereq->courseDetails->name }}</h1>

            <p>Update the prerequisite requirement for this course.</p>

            <form action="{{ route('admin.prereqs.update', $prereq) }}" method="POST">

                @csrf
                @method('PUT')

                @include('admin.prereqs-form')
            </form>
        </main>
    </div>
@endsection
