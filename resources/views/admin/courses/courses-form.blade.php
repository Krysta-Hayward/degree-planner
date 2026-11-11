@php
    $courseNumber = old('courseNumber', $course->courseNumber ?? '');
    $courseName = old('name', $course->name ?? '');
    $credits = old('credits', $course->credits ?? '');
@endphp

@if ($errors->any())
    <div class="alert-error">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="form-group">
    <label for="courseNumber">Course Number</label>

    <input type="text" id="courseNumber" name="courseNumber" value="{{ $courseNumber }}" placeholder="CIS 272"
        maxlength="7" required>
</div>

<div class="form-group">
    <label for="name">Course Name</label>

    <input type="text" id="name" name="name" value="{{ $courseName }}" required>
</div>

<div class="form-group">
    <label for="credits">Credits</label>

    <input type="number" id="credits" name="credits" value="{{ $credits }}" min="0" step="any" required>
</div>

<div class="form-actions">
    <button type="submit" class="button-links">
        {{ isset($course) ? 'Update Course' : 'Create Course' }}
    </button>

    <a href="{{ route('admin.courses.index') }}" class="button-links-danger">
        Cancel
    </a>
</div>