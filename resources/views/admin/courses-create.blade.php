@extends('layouts.app')

@section('title', 'Add Course - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Add Course</h1>

            @if (session('success'))
                <p>{{ session('success') }}</p>
            @endif

            @if ($errors->any())
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <form method="POST" action="{{ route('admin.courses.store') }}">
                @csrf

                <label for="courseNumber">Course Number</label>
                <input id="courseNumber" type="text" name="courseNumber" value="{{ old('courseNumber') }}" required>

                <label for="name">Course Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required>

                <label for="credits">Credits</label>
                <input id="credits" type="number" name="credits" min="0" step="any" value="{{ old('credits') }}" required>

                <button type="submit" class="button-links">
                    Create Course
                </button>
            </form>

            <button class="button-links">
                <a href="{{ route('admin.courses.index') }}">Cancel</a>
            </button>
        </main>
    </div>
@endsection