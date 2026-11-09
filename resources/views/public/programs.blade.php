@extends('layouts.app')

@section('title', 'Programs | Degree Planner')

@section('content')
    <div class="public-catalog">
        <h1>Degree Programs</h1>

        <p class="catalog-description">
            Browse the degree programs available through Degree Planner.
        </p>

        <table>
            <thead>
                <tr>
                    <th>Program Name</th>
                    <th>Details</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($programGroups as $programName => $programRows)
                    <tr>
                        <td>{{ $programName }}</td>
                        <td>
                            <a href="{{ route('public.programs.show', $programRows->first()->id) }}">
                                View Details
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2">No programs are currently available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
