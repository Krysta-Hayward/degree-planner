<?php
/*
ADVISOR HOME PAGE
Stats
Plans
Awaiting Review
Flagged Students
*/
namespace App\Http\Controllers\Advisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index(){
        return view('advisor.dashboard');
    }
}
