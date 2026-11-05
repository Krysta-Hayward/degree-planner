@php
$selectedCourse = old('course', $prereq->course ?? '');

$selectedPrereqs = old('prereqs', $selectedPrereqs ?? []);
$selectedPrereqs = array_map('strval', $selectedPrereqs);
@endphp

<div class="form-group">
    @if (isset($prereq))
        <input type="hidden" name="course" value="{{ $prereq->course }}">
    @else
        <select name="course" id="course" required>
            <option value="">Select a course</option>

            @foreach ($courses as $course)
                <option value="{{ $course->id }}" {{ (string) $selectedCourse === (string) $course->id ? 'selected' : '' }}>
                    {{ $course->courseNumber }} - {{ $course->name }}
                </option>
            @endforeach
        </select>
    @endif

    @error('course')
        <p class="error-message">{{ $message }}</p>
    @enderror
</div>

<div class="prereq-selector">
    <div class="prereq-list">
        <label for="available-prereqs">Available Courses</label>

        <select id="available-prereqs" multiple size="12"></select>
    </div>

    <div class="prereq-arrows">
        <button type="button" id="add-prereq" title="Add selected prerequisites">
            &gt;
        </button>
    </div>

    <div class="prereq-list">
        <label for="selected-prereqs">Required Prerequisites</label>

        <select name="prereqs[]" id="selected-prereqs" multiple size="12" required></select>
    </div>
</div>

@error('prereqs')
    <p class="error-message">{{ $message }}</p>
@enderror

@error('prereqs.*')
    <p class="error-message">{{ $message }}</p>
@enderror

<div class="form-actions">
    <button type="submit" class="button-links">
        {{ isset($prereq) ? 'Update Prerequisites' : 'Create Prerequisites' }}
    </button>

    <a href="{{ route('admin.prereqs.index') }}" class="button-links-danger">Cancel</a>
</div>

<script>
    const allCourses = @json($courses->map(fn($course) => [
    'id' => (string) $course->id,
    'label' => $course->courseNumber . ' - ' . $course->name,
])->values());

    const courseSelect = document.getElementById('course');
    const availableList = document.getElementById('available-prereqs');
    const selectedList = document.getElementById('selected-prereqs');

    let selectedIds = @json($selectedPrereqs);

    function renderLists() {
        const mainCourse = courseSelect
            ? courseSelect.value
            : @json((string) ($prereq->course ?? ''));

        availableList.innerHTML = '';
        selectedList.innerHTML = '';

        allCourses.forEach(course => {
            // A course cannot be its own prerequisite.
            if (course.id === mainCourse) {
                return;
            }

            const option = new Option(course.label, course.id);

            if (selectedIds.includes(course.id)) {
                selectedList.add(option);
            } else {
                availableList.add(option);
            }
        });
    }

    function moveSelected(source, destination) {
        Array.from(source.selectedOptions).forEach(option => {
            const movedOption = new Option(option.text, option.value);
            movedOption.selected = true;

            destination.add(movedOption);
        });

        updateSelectedIds();
    }

    function updateSelectedIds() {
        selectedIds = Array.from(selectedList.options).map(option => option.value);
    }

    document.getElementById('add-prereq').addEventListener('click', () => {
        moveSelected(availableList, selectedList);
    });

    if (courseSelect) {
        courseSelect.addEventListener('change', () => {
            updateSelectedIds();
            renderLists();
        });
    }

    // Make sure all courses on the right are submitted with the form.
    selectedList.form.addEventListener('submit', () => {
        Array.from(selectedList.options).forEach(option => {
            option.selected = true;
        });
    });

    renderLists();
</script>
