@php
$courseNumber = old('courseNumber', $course->courseNumber ?? '');
$courseName = old('name', $course->name ?? '');
$credits = old('credits', $course->credits ?? '');
$description = old('description', $course->description ?? '');
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

    <input type="text" id="courseNumber" name="courseNumber" value="{{ $courseNumber }}" placeholder="CIS 272">
</div>

<div class="form-group">
    <label for="name">Course Name</label>

    <input type="text" id="name" name="name" value="{{ $courseName }}" required>
</div>

<div class="form-group">
    <label for="credits">Credits</label>

    <input type="number" id="credits" name="credits" value="{{ $credits }}" min="0" step="any" required>
</div>

<div class="form-group">
    <label for="description">Description</label>
    
    <textarea id="description" name="description" rows="4" maxlength="2000">{{ $description }}</textarea>
</div>

<div class="form-group">
    <label>Terms Offered</label>

    @php
        $selectedTerms = old('quarterYears', $selectedQuarterYears ?? []);
        $termsByYear = collect($availableQuarterYears)->groupBy(function ($term) {
            return substr($term, -4);
        });
    @endphp

    <div class="terms-compact">
        @foreach ($termsByYear as $year => $terms)
            <div class="terms-year">
                <strong>{{ $year }}</strong>

                <div class="terms-options">
                    @foreach ($terms as $quarterYear)
                        <label>
                            <input type="checkbox" name="quarterYears[]" value="{{ $quarterYear }}"
                                @checked(in_array($quarterYear, $selectedTerms))>
                            {{ str_replace(' ' . $year, '', $quarterYear) }}
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="form-actions">
    <button type="submit" class="button-links-primary">
        {{ isset($course) ? 'Update Course' : 'Create Course' }}
    </button>

    <a href="{{ route('admin.courses.index') }}" class="button-links-danger">
        Cancel
    </a>
</div>