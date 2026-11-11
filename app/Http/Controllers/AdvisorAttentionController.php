<?php

namespace App\Http\Controllers;

use App\Services\NeedsAttention;

class AdvisorAttentionController extends Controller
{
    // Students who need attention, with the reasons
    public function index()
    {
        return view('advisor.attention.index', [
            'flaggedStudents' => NeedsAttention::forAdvisor(auth()->id()),
        ]);
    }
}