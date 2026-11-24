@extends('layouts.app')

@section('title', 'Edit ' . $course->courseNumber . ' - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Edit {{ $course->courseNumber }}</h1>

            <p>Update the course information below.</p>

            <form action="{{ route('admin.courses.update', $course) }}" method="POST">

                @csrf
                @method('PUT')

                @include('admin.courses.courses-form')
            </form>
        </main>
    </div>
@endsection
