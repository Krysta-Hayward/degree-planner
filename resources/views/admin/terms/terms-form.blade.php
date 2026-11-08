{{-- Quarter / Year --}}
<div class="form-group">
    <label for="quarterYear">Quarter / Year</label>

    <input type="text" id="quarterYear" name="quarterYear" value="{{ old('quarterYear', $term->quarterYear ?? '') }}"
        placeholder="Example: Fall 2026" required>

    @error('quarterYear')
        <p class="error">{{ $message }}</p>
    @enderror
</div>

{{-- Course --}}
<div class="form-group">
    <label for="course">Course</label>

    <select id="course" name="course" required>
        <option value="">Select a course</option>

        @foreach ($courses as $course)
            <option value="{{ $course->id }}" @selected(old('course', $term->course ?? '') == $course->id)>
                {{ $course->courseNumber }}
            </option>
        @endforeach
    </select>

    @error('course')
        <p class="error">{{ $message }}</p>
    @enderror
</div>