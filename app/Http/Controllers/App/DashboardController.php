<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard
     */
    public function index()
    {
        $tenant = app('tenant');

        $stats = [
            'total_users' => User::count(),
            'active_users' => User::whereNotNull('email_verified_at')->count(),
        ];

        $recentUsers = User::latest()->take(5)->get();

        return view('app.dashboard', compact('stats', 'recentUsers', 'tenant'));
    }
}
