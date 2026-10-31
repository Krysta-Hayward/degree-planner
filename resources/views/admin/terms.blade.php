@extends('layouts.app')

@section('title', 'Manage Terms - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Manage Terms</h1>
            <p>View terms available in the Degree Planner.</p>

            <p>
                <strong>Total terms:</strong> {{ $terms->count() }}
            </p>

            @if ($terms->isEmpty())
                <p>No terms found.</p>
            @else
                <div class="pagination">
                    {{ $terms->links() }}
                </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Term ID</th>
                                <th>Quarter / Year</th>
                                <th>Course ID</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($terms as $term)
                                <tr>
                                    <td>{{ $term->id }}</td>
                                    <td>{{ $term->quarterYear }}</td>
                                    <td>{{ $term->course }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
            @endif
            <div class="pagination">
                {{ $terms->links() }}
            </div>
        </main>
    </div>
@endsection
