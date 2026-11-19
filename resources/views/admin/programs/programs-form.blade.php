@if ($errors->any())
    <ul class="form-errors">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

@php
$oldCourses = collect(old('courses', $current->keys()->all()))->map(fn($id) => (int) $id);
@endphp

    @if ($method === 'PUT')
        @method('PUT')
    @endif

    <div class="form-buttons">
        <button type="submit" class="button-links-primary">
            {{ $submitLabel }}
        </button>

        <a href="{{ route('admin.programs.index') }}" class="button-links-danger">
            Cancel
        </a>
    </div>

    <div class="form-group">
        <label for="name">Program Name</label>
        <input id="name" type="text" name="name" value="{{ old('name', $programName ?? '') }}" maxlength="255" required>
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4"
            maxlength="2000">{{ old('description', $description ?? '') }}</textarea>
    </div>

    <h2>Required Courses</h2>
    <p>
        Check each course this program requires and give it a requirement group,
        such as Core or Electives. Leaving the group blank makes it Core.
    </p>

    <table class="programs-form-top-table">
        <td><label for="course-filter">Filter courses</label>
    <input id="course-filter" type="search" class="course-filter " placeholder="Course number or name"></td>
        <td>{{ $courses->links() }}</td>
    </table>

    <div>
        <table class="programs-form-bottom-table">
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
                            <input type="checkbox" name="courses[]" value="{{ $course->id }}" id="course-{{ $course->id }}"
                                aria-label="Require {{ $course->courseNumber }}"
                                @checked($oldCourses->contains($course->id))>
                        </td>
                        <td><label for="course-{{ $course->id }}">{{ $course->courseNumber }}</label></td>
                        <td>{{ $course->name }}</td>
                        <td>{{ $course->credits }}</td>
                        <td>
                            <select name="groups[{{ $course->id }}]"
                                aria-label="Requirement group for {{ $course->courseNumber }}">

                                @foreach ($groupOptions as $option)
                                    <option value="{{ $option }}" @selected(old('groups.' . $course->id, $current[$course->id] ?? '') === $option)>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="form-buttons">
        <button type="submit" class="button-links-primary">{{ $submitLabel }}</button>
        <a href="{{ route('admin.programs.index') }}" class="button-links-danger">Cancel</a>
    </div>

<script>
    document.getElementById('course-filter').addEventListener('input', function () {
        const term = this.value.trim().toLowerCase();

        document.querySelectorAll('.programs-form-bottom-table tbody tr').forEach(function (row) {
            row.hidden = term !== '' && !row.dataset.search.includes(term);
        });
    });
</script>