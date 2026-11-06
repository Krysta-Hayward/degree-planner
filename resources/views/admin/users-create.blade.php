@extends('layouts.app')

@section('title', 'Create User - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Create User</h1>

            <p>Enter the information for the new user account.</p>

            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                @include('admin.users-form')
            </form>
        </main>
    </div>
@endsection
