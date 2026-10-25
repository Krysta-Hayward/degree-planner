<h2>Plan Reviews</h2>

<h3>List of student plans will goe here</h3>

@if (session('success'))
    <p>{{ session('success') }}</p>
@endif


<p>Will have Status of:</p>
<li>Draft</li>
<li>Processed</li>
<li>Apporved</li>

@isset($planId)
    <p>Plan ID: {{ $planId }}</p>

    <form method="POST" action="{{ route('advisor.plans.approve', $planId) }}">
        @csrf
        <button type="submit">Approve</button>
    </form>

    <form method="POST" action="{{ route('advisor.plans.changes', $planId) }}">
        @csrf
        <button type="submit">Request changes</button>
    </form>
@endisset
