@extends('layouts.app')

@section('title', 'Create Classroom - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Create Classroom</h1>
            <p>Add a new classroom section to a course.</p>

            <form action="{{ route('admin.classrooms.store') }}" method="POST">
                @csrf

                @include('admin.classrooms.classrooms-form')

                <button type="submit" class="button-links-primary">
                    Create Classroom
                </button>

                <a href="{{ route('admin.classrooms.index') }}" class="button-links-danger">
                    Cancel
                </a>
            </form>
        </main>
    </div>
@endsection
