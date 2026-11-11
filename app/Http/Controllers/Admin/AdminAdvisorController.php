<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class AdminAdvisorController extends Controller
{
    public function index(): View
    {
        $advisors = User::where('role', 'advisor')
            ->withCount('students')
            ->orderBy('name')
            ->paginate(25);

        return view('admin.advisors.advisors', compact('advisors'));
    }

    public function show(User $advisor): View
    {
        abort_unless($advisor->role === 'advisor', 404);

        $students = $advisor->students()
            ->orderBy('name')
            ->paginate(25);

        return view('admin.advisors.advisor-details', compact('advisor', 'students'));
    }
}
