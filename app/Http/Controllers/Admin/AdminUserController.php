<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $users = User::orderBy('name')->paginate(50);

        return view('admin.users.users', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.users-create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['student', 'advisor', 'admin'])],
        ]);

        User::create($validated);

        return redirect()
            ->route('admin.users')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.users-edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['student', 'advisor', 'admin'])],
        ]);

        if ($user->id === auth()->id() && $validated['role'] !== 'admin') {
            return back()
                ->withInput()
                ->with('error', 'You cannot remove your own admin role.');
        }

        if (
            $user->role === 'admin' &&
            $validated['role'] !== 'admin' &&
            User::where('role', 'admin')->count() <= 1
        ) {
            return back()
                ->withInput()
                ->with('error', 'You cannot remove the last administrator.');
        }

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('admin.users')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()
                ->with('error', 'You cannot delete your own account.');
        }

        if (
            $user->plans()->exists() ||
            $user->advisor()->exists() ||
            $user->students()->exists()
        ) {
            return back()
                ->with('error', 'This user has associated plans or advisor/student assignments. Remove those associations first.');
        }

        if (
            $user->role === 'admin' &&
            User::where('role', 'admin')->count() <= 1
        ) {
            return back()
                ->with('error', 'You cannot delete the last administrator.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users')
            ->with('success', 'User deleted successfully.');
    }
}
