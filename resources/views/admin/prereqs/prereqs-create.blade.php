@extends('layouts.app')

@section('title', 'Create Prerequisite - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Create Prerequisite</h1>

            <p>Select the course and the course that must be completed first.</p>

            <form action="{{ route('admin.prereqs.store') }}" method="POST">

                @csrf

                @include('admin.prereqs.prereqs-form')
            </form>
        </main>
    </div>
@endsection
