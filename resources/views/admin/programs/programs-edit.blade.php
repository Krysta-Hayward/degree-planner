@extends('layouts.app')

@section('title', 'Edit ' . $programName . ' - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Edit {{ $programName }}</h1>

            @include('admin.programs.programs-form', [
                'action' => route('admin.programs.update', ['programName' => $programName]),
                'method' => 'PUT',
                'submitLabel' => 'Save Changes',
            ])

        </main>
    </div>
@endsection
