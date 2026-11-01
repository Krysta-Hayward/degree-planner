@extends('layouts.app')

@section('title', 'Edit ' . $programName . ' - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Edit {{ $programName }}</h1>

            @include('admin.programs-form', [
                'action' => route('admin.programs.update', ['programName' => $programName]),
                'method' => 'PUT',
                'submitLabel' => 'Save Changes',
            ])

            <button class="button-links">
                <a href="{{ route('admin.programs.show', ['programName' => $programName]) }}">Cancel</a>
            </button>
        </main>
    </div>
@endsection
