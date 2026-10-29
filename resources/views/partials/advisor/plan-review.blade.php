<h2>{{ $programMap->owner->name }}'s program map</h2>
<p>Status: {{ $programMap->status }}</p>

@foreach ($coursesByQuarter as $quarter => $courses)
    <h3>{{ $quarter }}</h3>
    @foreach ($courses as $course)
        <p>{{ $course->courseNumber }}: {{ $course->name }} ({{ $course->credits }} credits)</p>
    @endforeach
@endforeach

@can('review', $programMap)
    <form method="POST" action="{{ route('advisor.plans.approve', $programMap) }}">
        @csrf
        <button type="submit">Approve</button>
    </form>

    <form method="POST" action="{{ route('advisor.plans.changes', $programMap) }}">
        @csrf
        <button type="submit">Request changes</button>
    </form>
@endcan

<a href="{{ route('advisor.plans.index') }}">Back to plans</a>