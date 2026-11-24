@extends('layouts.app')

@section('title', 'Create Course - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Create Course</h1>

            <p>Enter the course number, name, and credits.</p>

            <form action="{{ route('admin.courses.store') }}" method="POST">

                @csrf

                @include('admin.courses.courses-form')
            </form>
        </main>
    </div>
@endsection
