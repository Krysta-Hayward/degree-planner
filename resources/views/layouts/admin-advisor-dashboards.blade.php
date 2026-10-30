@extends('layouts.app')

@section('title', ucfirst(auth()->user()->role) . ' Dashboard - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>@yield('heading', ucfirst(auth()->user()->role) . ' Dashboard')</h1>

            @yield('dashboard')
        </main>
    </div>
@endsection