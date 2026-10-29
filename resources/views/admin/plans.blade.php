@extends('layouts.app')

@section('title', 'Manage Plans - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('shared.sidebar')

        <main class="admin-content">
            <h1>Manage Plans</h1>
            <p>View plans available in the Degree Planner.</p>

            <p>
                <strong>Total plans:</strong> {{ $plans->count() }}
            </p>

            @if ($plans->isEmpty())
                <p>No plans found.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Plan Number</th>
                            <th>Plan Name</th>
                            <th>Credits</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($plans as $plan)
                            <tr>
                                <td>{{ $plan->planNumber }}</td>
                                <td>{{ $plan->name }}</td>
                                <td>{{ $plan->credits }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </main>
    </div>
@endsection