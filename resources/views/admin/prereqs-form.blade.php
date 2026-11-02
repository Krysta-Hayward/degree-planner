<div class="form-group">
    <label for="course">Course</label>

    <select name="course" id="course" required>
        <option value="">Select a course</option>

        @foreach ($courses as $course)
            <option value="{{ $course->id }}" {{ old('course', $prereq->course ?? '') == $course->id ? 'selected' : '' }}>
                {{ $course->courseNumber }} - {{ $course->name }}
            </option>
        @endforeach
    </select>

    @error('course')
        <p class="error">{{ $message }}</p>
    @enderror
</div>

<div class="form-group">
    <label for="prereq">Required Prerequisite</label>

    <select name="prereq" id="prereq" required>
        <option value="">Select a prerequisite</option>

        @foreach ($courses as $course)
            <option value="{{ $course->id }}" {{ old('prereq', $prereq->prereq ?? '') == $course->id ? 'selected' : '' }}>
                {{ $course->courseNumber }} - {{ $course->name }}
            </option>
        @endforeach
    </select>

    @error('prereq')
        <p class="error">{{ $message }}</p>
    @enderror
</div>

<div class="form-buttons">
    <button type="submit" class="button-links">
        {{ isset($prereq) ? 'Update Prerequisite' : 'Create Prerequisite' }}
    </button>

    <a href="{{ route('admin.prereqs.index') }}" class="button-links">
        Cancel
    </a>
</div>
