@extends('layouts.app')

@section('title', 'Terms | Degree Planner')

@section('content')
    <div>
        <h1>Available Terms</h1>

        <p>
            Browse the academic terms available through Degree Planner.
        </p>

        @if ($terms->count())
            <table>
                <thead>
                    <tr>
                        <th>Quarter / Year</th>
                        <th>Details</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($terms as $term)
                        <tr>
                            <td>{{ $term->quarterYear }}</td>
                            <td>
                                <a href="{{ route('public.terms.show', $term->id) }}">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $terms->links() }}
        @else
            <p>No terms are currently available.</p>
        @endif
    </div>
@endsection