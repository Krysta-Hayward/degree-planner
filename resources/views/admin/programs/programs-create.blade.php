@extends('layouts.app')

@section('title', 'Add Program - Degree Planner')

@section('content')
        <div class="admin-layout">
            @include('partials.sidebar')

            <main class="admin-content">
                <h1>Add Program</h1>

                @include('admin.programs.programs-form', [
        'action' => route('admin.programs.store'),
        'method' => 'POST',
        'programName' => '',
        'description' => '',
        'current' => collect(),
        'submitLabel' => 'Create Program',
    ])

                    <a href="{{ route('admin.programs.index') }}" class="button-links-danger">Cancel</a>
            </main>
        </div>
@endsection
