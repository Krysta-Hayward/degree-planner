@extends('layouts.app')

@section('title', 'Manage Students - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('shared.sidebar')

        <main class="admin-content">
            <h1>Manage Students</h1>
            <p>Assign advisors to student accounts.</p>

            <p>
                <strong>Total students:</strong> {{ $students->count() }}
                <strong>Available advisors:</strong> {{ $advisors->count() }}
            </p>

            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Email</th>
                        <th>Current Advisor</th>
                        <th>Assign Advisor</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($students as $student)
                        <tr>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->email }}</td>
                            <td>
                                {{ $student->advisor?->name ?? 'Not assigned' }}
                            </td>
                            <td>
                                <form action="{{ route('admin.students.advisor.update', $student) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <select name="advisor_id">
                                        <option value="">No advisor</option>

                                        @foreach ($advisors as $advisor)
                                            <option value="{{ $advisor->id }}" @selected(
                                                old('advisor_id', $student->advisor_id)
                                                == $advisor->id
                                            )>
                                                {{ $advisor->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <button type="submit">
                                        Save Assignment
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </main>
    </div>
@endsection