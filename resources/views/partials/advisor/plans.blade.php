<h2>Plans awaiting review</h2>

@if (session('success'))
    <p>{{ session('success') }}</p>
@endif

@if ($maps->isEmpty())
    <p>No plans waiting for review.</p>
@else
    <ul>
        @foreach ($maps as $map)
            <li>
                {{ $map->owner->name }}
                <a href="{{ route('advisor.plans.show', $map) }}">Review</a>
            </li>
        @endforeach
    </ul>
@endif