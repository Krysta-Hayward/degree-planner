@extends('layouts.app')

@section('title', 'Add Program - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Add Program</h1>

            @include('admin.programs-form', [
                'action' => route('admin.programs.store'),
                'method' => 'POST',
                'programName' => '',
                'description' => '',
                'current' => collect(),
                'submitLabel' => 'Create Program',
            ])

            <button class="button-links">
                <a href="{{ route('admin.programs') }}">Cancel</a>
            </button>
        </main>
    </div>
@endsection
