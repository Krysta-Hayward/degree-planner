<div class="form-group">
    <label for="name">Name</label>

    <input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}" required>

    @error('name')
        <p class="error-message">{{ $message }}</p>
    @enderror
</div>

<div class="form-group">
    <label for="email">Email</label>

    <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" required>

    @error('email')
        <p class="error-message">{{ $message }}</p>
    @enderror
</div>

<div class="form-group">
    <label for="role">Role</label>

    <select name="role" id="role" required>
        <option value="">Select a role</option>

        @foreach (['student', 'advisor', 'admin'] as $role)
            <option value="{{ $role }}" {{ old('role', $user->role ?? 'student') === $role ? 'selected' : '' }}>
                {{ ucfirst($role) }}
            </option>
        @endforeach
    </select>

    @error('role')
        <p class="error-message">{{ $message }}</p>
    @enderror
</div>

<div class="form-group">
    <label for="password">
        Password
        @if (isset($user))
            <span>(Leave blank to keep current password)</span>
        @endif
    </label>

    <input type="password" name="password" id="password" {{ isset($user) ? '' : 'required' }}
        autocomplete="new-password">

    @error('password')
        <p class="error-message">{{ $message }}</p>
    @enderror
</div>

<div class="form-group">
    <label for="password_confirmation">Confirm Password</label>

    <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password">
</div>

<div class="form-actions">
    <button type="submit" class="button-links-primary">
        {{ isset($user) ? 'Update User' : 'Create User' }}
    </button>

    <a href="{{ route('admin.users') }}" class="button-links-danger">Cancel</a>
</div>
