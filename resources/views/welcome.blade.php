@extends('layouts.app')

@section('title', 'Degree Planner')

@section('content')
    <section>
        <h1>Plan Your Degree </h1>

        <p>
            Organize your courses, track your progress, and plan
            future terms.
        </p>

        <div>
            <button>My Degree Plan</button>
            <button>View Programs</button>
        </div>
    </section>

    <section>
        <div>
            <h2>Plan Your Courses</h2>
            <p>
                Build a course plan for upcoming terms and keep
                track of the classes you need to complete.
            </p>
        </div>

        <div>
            <h2>Track Prerequisites</h2>
            <p>
                See which courses are required before you can
                enroll in future classes.
            </p>
        </div>

        <div>
            <h2>Stay on Track</h2>
            <p>
                Keep your degree progress organized and make
                planning your education easier.
            </p>
        </div>
    </section>
@endsection