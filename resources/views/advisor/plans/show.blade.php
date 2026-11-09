@extends('layouts.admin-advisor-dashboards')

@section('heading', 'Review Program Map')

@section('dashboard')
    <h2>{{ $programMap->owner->name }}</h2>
    <p>Status: {{ ucfirst(str_replace('_', ' ', $programMap->status)) }}</p>

    @if ($coursesByQuarter->isEmpty())
        <p>This student hasn't planned any courses yet.</p>
    @else
        @foreach ($coursesByQuarter as $quarter => $courses)
            <h3>{{ $quarter }}</h3>

            <table class="student-table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Name</th>
                        <th>Credits</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($courses as $course)
                        <tr>
                            <td>{{ $course->courseNumber }}</td>
                            <td>{{ $course->name }}</td>
                            <td>{{ $course->credits }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <p>Total credits: {{ $courses->sum('credits') }}</p>
        @endforeach
    @endif

    <details>
        <summary>Comments ({{ $programMap->comments->count() }})</summary>

         @forelse ($programMap->comments as $comment)
        <div>
            <p><strong>{{ $comment->author->name }}</strong> {{ $comment->created_at->format('M j, Y g:i A') }}</p>
            <p>{{ $comment->body }}</p>
        </div>
        @empty
            <p>No comments.</p>
        @endforelse

        @if (session('success'))
            <p>{{ session('success') }}</p>
        @endif

        @can('comment', $programMap)
            <form method="POST" action="{{ route('advisor.plans.comments.store', $programMap) }}">
                @csrf
                <label for="body">Add a comment</label>
                <br>
                <textarea name="body" id="body" rows="3">{{ old('body') }}</textarea>

                @error('body')
                    <p class="plan-status plan-status--missing">{{ $message }}</p>
                @enderror
    
                <button type="submit" name="post-comment">Post Comment</button>
            </form>
        @endcan
    </details>

    @can('review', $programMap)
        <div class="review-actions">
            <!-- Approve button -->
            <form method="POST" action="{{ route('advisor.plans.approve', $programMap) }}">
                @csrf
                <button type="submit" class="button-links">Approve</button>
            </form>

            <!-- Request Change button -->
            <form method="POST" action="{{ route('advisor.plans.changes', $programMap) }}">
                @csrf
                <button type="submit" class="button-links">Request changes</button>
            </form>
        </div>
    @endcan

    <a href="{{ route('advisor.plans.index') }}" class="button-links">Back to plan reviews</a>
@endsection