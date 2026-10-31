{{-- Shared program form used by programs-create and programs-edit --}}
@if ($errors->any())
    <ul class="form-errors">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

@php
    $oldCourses = collect(old('courses', $current->keys()->all()))->map(fn ($id) => (int) $id);
@endphp

<form method="POST" action="{{ $action }}" class="program-form">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    <label for="name">Program Name</label>
    <input id="name" type="text" name="name" value="{{ old('name', $programName) }}" maxlength="255" required>

    <label for="description">Description</label>
    <textarea id="description" name="description" rows="4" maxlength="2000">{{ old('description', $description) }}</textarea>

    <h2>Required Courses</h2>
    <p>
        Check each course this program requires and give it a requirement group,
        such as Core or Electives. Leaving the group blank makes it Core.
    </p>

    <label for="course-filter">Filter courses</label>
    <input id="course-filter" type="search" placeholder="Course number or name">

    <datalist id="requirement-groups">
        @foreach ($groupOptions as $option)
            <option value="{{ $option }}"></option>
        @endforeach
    </datalist>

    <div style="overflow-x: auto;">
        <table class="program-courses">
            <thead>
                <tr>
                    <th>Required</th>
                    <th>Course Number</th>
                    <th>Course Name</th>
                    <th>Credits</th>
                    <th>Requirement Group</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($courses as $course)
                    <tr data-search="{{ strtolower($course->courseNumber . ' ' . $course->name) }}">
                        <td>
                            <input type="checkbox" name="courses[]" value="{{ $course->id }}"
                                id="course-{{ $course->id }}"
                                aria-label="Require {{ $course->courseNumber }}"
                                @checked($oldCourses->contains($course->id))>
                        </td>
                        <td><label for="course-{{ $course->id }}">{{ $course->courseNumber }}</label></td>
                        <td>{{ $course->name }}</td>
                        <td>{{ $course->credits }}</td>
                        <td>
                            <input type="text" name="groups[{{ $course->id }}]" list="requirement-groups"
                                maxlength="100" placeholder="Core"
                                aria-label="Requirement group for {{ $course->courseNumber }}"
                                value="{{ old('groups.' . $course->id, $current[$course->id] ?? '') }}">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <button type="submit" class="button-links">{{ $submitLabel }}</button>
</form>

<script>
    // Hide course rows that don't match the filter text.
    document.getElementById('course-filter').addEventListener('input', function () {
        const term = this.value.trim().toLowerCase();

        document.querySelectorAll('.program-courses tbody tr').forEach(function (row) {
            row.hidden = term !== '' && !row.dataset.search.includes(term);
        });
    });
</script>
