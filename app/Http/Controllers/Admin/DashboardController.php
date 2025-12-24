<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $adminCount = User::where('role', 'admin')->count();
        $lecturerCount = User::where('role', 'dosen')->count();
        $studentCount = User::where('role', 'mahasiswa')->count();
        $recentUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact('adminCount', 'lecturerCount', 'studentCount', 'recentUsers'));
    }
}