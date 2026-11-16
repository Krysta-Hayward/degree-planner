@extends('layouts.app')

@section('title', 'Edit ' . $programName . ' - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Edit {{ $programName }}</h1>

            <form method="POST" action="{{ route('admin.programs.update', $programName) }}" class="program-form">
                @csrf
                @method('PUT')

                @include('admin.programs.programs-form')
            </form>

        </main>
    </div>
@endsection
