@extends('layouts.app')

@section('title', 'Manage Terms - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Manage Terms</h1>
            <p>View and manage terms available in the Degree Planner.</p>

            {{-- Success message --}}
            @if (session('success'))
                <div class="alert-success">
        {{ session('success') }}
    </div>
            @endif

            {{-- Add Term button --}}
            <a href="{{ route('admin.terms.create') }}" class="button-links">
                Add Term
            </a>

            <p>
                <strong>Total terms:</strong> {{ $terms->total() }}
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
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($terms as $term)
                            <tr>
                                <td>{{ $term->id }}</td>
                                <td>{{ $term->quarterYear }}</td>
                                <td>{{ $term->course }}</td>
                                <td>
                                    <a href="{{ route('admin.terms.edit', $term) }}" class="button-links">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('admin.terms.destroy', $term) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this term?');"
                                        style="display: inline;">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="button-links-danger">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="pagination">
                    {{ $terms->links() }}
                </div>
            @endif
        </main>
    </div>
@endsection