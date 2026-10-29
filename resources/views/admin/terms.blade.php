@extends('layouts.app')

@section('title', 'Manage Terms - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('shared.sidebar')

        <main class="admin-content">
            <h1>Manage Terms</h1>
            <p>View terms available in the Degree Planner.</p>

            <p>
                <strong>Total terms:</strong> {{ $terms->count() }}
            </p>

            @if ($terms->isEmpty())
                <p>No terms found.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Term Number</th>
                            <th>Term Name</th>
                            <th>Credits</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($terms as $term)
                            <tr>
                                <td>{{ $term->termNumber }}</td>
                                <td>{{ $term->name }}</td>
                                <td>{{ $term->credits }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </main>
    </div>
@endsection