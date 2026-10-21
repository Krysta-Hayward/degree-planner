<div>
    <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Log out</button>
    </form>
    <!-- Very little is needed to make a happy life. - Marcus Aurelius -->
     <h1>Hello world from the advisor dashboard</h1>
     <p>Logged in as {{ auth()->user()->name }} ({{ auth()->user()->role }})</p>
     <a href="{{ route('advisor.students.index') }}">Go to students</a>
</div>
