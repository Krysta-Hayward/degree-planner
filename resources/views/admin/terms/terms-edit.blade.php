@extends('layouts.app')

@section('title', 'Edit Term - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Edit Term</h1>
            <p>Update the term information below.</p>

            <form method="POST" action="{{ route('admin.terms.update', $term) }}">
                @csrf
                @method('PUT')

                @include('admin.terms.terms-form')

                <div class="form-buttons">
                    <button type="submit" class="button-links-primary">
                        Update Term
                    </button>

                    <a href="{{ route('admin.terms.index') }}" class="button-links-danger">
                        Cancel
                    </a>
                </div>
            </form>
        </main>
    </div>
@endsection