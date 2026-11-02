@extends('layouts.app')

@section('title', 'Create Term - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Create Term</h1>
            <p>Add a new term to the Degree Planner.</p>

            <form method="POST" action="{{ route('admin.terms.store') }}">
                @csrf

                @include('admin.terms-form')

                <div class="form-buttons">
                    <button type="submit" class="button-links">
                        Create Term
                    </button>

                    <a href="{{ route('admin.terms.index') }}" class="button-links-danger">
                        Cancel
                    </a>
                </div>
            </form>
        </main>
    </div>
@endsection