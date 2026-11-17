<div class="form-container">

    <div class="form-group">
        <label for="course">Course</label>

        <select name="course" id="course" required>
            <option value="">Select a course</option>

            @foreach ($courses as $courseOption)
                <option value="{{ $courseOption->id }}" {{ old('course', $classroom->course ?? '') == $courseOption->id ? 'selected' : '' }}>
                    {{ $courseOption->courseNumber }} - {{ $courseOption->name }}
                </option>
            @endforeach
        </select>

        @error('course')
            <div class="alert-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="teacher">Teacher</label>

        <select name="teacher" id="teacher" class="searchable-select">
            <option value="">Select a teacher</option>

            @foreach ($teachers as $teacherOption)
                <option value="{{ $teacherOption }}" {{ old('teacher', $classroom->teacher ?? '') == $teacherOption ? 'selected' : '' }}>
                    {{ $teacherOption }}
                </option>
            @endforeach
        </select>

        @error('teacher')
            <div class="alert-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="seats">Total Seats</label>

        <input type="number" name="seats" id="seats" min="0" value="{{ old('seats', $classroom->seats ?? 0) }}"
            required>

        @error('seats')
            <div class="alert-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="seatsOpen">Open Seats</label>

        <input type="number" name="seatsOpen" id="seatsOpen" min="0"
            value="{{ old('seatsOpen', $classroom->seatsOpen ?? 0) }}" required>

        @error('seatsOpen')
            <div class="alert-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="classroom-options">

        <fieldset>
            <legend>Terms Available</legend>

            @foreach (['fall' => 'Fall', 'winter' => 'Winter', 'spring' => 'Spring', 'summer' => 'Summer'] as $field => $label)
                <div class="form-group">
                    <input type="hidden" name="{{ $field }}" value="0">

                    <label>
                        <input type="checkbox" name="{{ $field }}" value="1" {{ old($field, $classroom->$field ?? 0) ? 'checked' : '' }}>

                        {{ $label }}
                    </label>
                </div>
            @endforeach
        </fieldset>

        <fieldset>
            <legend>Class Schedule</legend>

            <div class="form-group">
                <label for="timeStart">Start Time</label>

                <input type="time" name="timeStart" id="timeStart"
                    value="{{ old('timeStart', isset($classroom) && $classroom->timeStart ? \Carbon\Carbon::parse($classroom->timeStart)->format('H:i') : '') }}">

                @error('timeStart')
                    <div class="alert-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="timeEnd">End Time</label>

                <input type="time" name="timeEnd" id="timeEnd"
                    value="{{ old('timeEnd', isset($classroom) && $classroom->timeEnd ? \Carbon\Carbon::parse($classroom->timeEnd)->format('H:i') : '') }}">

                @error('timeEnd')
                    <div class="alert-error">{{ $message }}</div>
                @enderror
            </div>
        </fieldset>

    </div>