@extends('layouts.app')

@section('title', 'Edit Classroom - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Edit Classroom</h1>
            <p>Update classroom information.</p>

            <form action="{{ route('admin.classrooms.update', $classroom) }}" method="POST">
                @csrf
                @method('PUT')

                @include('admin.classrooms.classrooms-form')

                <button type="submit" class="button-links-primary">
                    Update Classroom
                </button>

                <a href="{{ route('admin.classrooms.index') }}" class="button-links-danger">
                    Cancel
                </a>
            </form>
        </main>
    </div>
@endsection
