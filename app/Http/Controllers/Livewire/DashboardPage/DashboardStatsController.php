<?php

namespace App\Http\Controllers\Livewire\DashboardPage;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardStatsController
{
    public function index(Request $request)
    {
        $days = (int) $request->query('days', 30);

        $since = Carbon::now()->subDays($days);

        $totalUsers = DB::table('users')->where('created_at', '>=', $since)->count();
        $archivedUsers = DB::table('users_archive')->count();
        $totalLogins = DB::table('attendance')->where('login_time', '>=', $since)->count();
        $activeUsers = DB::table('attendance')->whereNull('logout_time')->where('library_section', session('current_section', 'entrance'))->count();

        // Top students by login count in timeframe
        $topStudents = DB::table('attendance')
            ->select('users.id', 'users.fname', 'users.lname', 'users.course', DB::raw('COUNT(attendance.id) as login_count'))
            ->join('users', 'attendance.user_id', '=', 'users.id')
            ->where('attendance.login_time', '>=', $since)
            ->groupBy('users.id', 'users.fname', 'users.lname', 'users.course')
            ->orderByDesc('login_count')
            ->limit(10)
            ->get();

        // Top courses by login count
        $topCourses = DB::table('attendance')
            ->select('users.course', DB::raw('COUNT(attendance.id) as login_count'))
            ->join('users', 'attendance.user_id', '=', 'users.id')
            ->where('attendance.login_time', '>=', $since)
            ->groupBy('users.course')
            ->orderByDesc('login_count')
            ->limit(10)
            ->get();

        // Recent logins (most recent 10)
        $recentLogins = DB::table('attendance')
            ->select('attendance.id', 'users.fname', 'users.lname', 'users.mname', 'users.course', 'users.sex', 'users.user_type', 'attendance.login_time')
            ->join('users', 'attendance.user_id', '=', 'users.id')
            ->where('attendance.login_time', '>=', $since)
            ->where('attendance.library_section', session('current_section', 'entrance'))
            ->orderByDesc('attendance.login_time')
            ->limit(10)
            ->get()
            ->map(function ($r) {
                $r->login_time_human = Carbon::parse($r->login_time)->diffForHumans();
                return $r;
            });

        return response()->json([
            'success' => true,
            'totalUsers' => $totalUsers,
            'archivedUsers' => $archivedUsers,
            'totalLogins' => $totalLogins,
            'activeUsers' => $activeUsers,
            'topStudents' => $topStudents,
            'topCourses' => $topCourses,
            'recentLogins' => $recentLogins,
        ]);
    }
}
