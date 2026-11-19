@extends('layouts.app')

@section('title', 'Add Program - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Add Program</h1>

                @include('admin.programs.programs-form')
            </form>

        </main>
    </div>
@endsection
