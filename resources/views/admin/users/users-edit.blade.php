@extends('layouts.app')

@section('title', 'Edit User - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Edit User: {{ $user->name }}</h1>

            <p>Update the user's account information.</p>

            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                @include('admin.users.users-form')
            </form>
        </main>
    </div>
@endsection
