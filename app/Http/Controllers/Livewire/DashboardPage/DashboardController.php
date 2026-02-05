<?php

namespace App\Http\Controllers\Livewire\DashboardPage;

use Livewire\Component;
use App\Models\User;
use App\Models\Archive;
use App\Models\Attendance;
use App\Models\AdminHistory;
use App\Models\RoomReservation;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Component
{
    public $title = 'LISO - Dashboard';
    public $filterDays = 30; // Unified filter for all dashboard data
    public $customDays = ''; // Custom days input for filter

    public function mount()
    {
        session(['title' => $this->title]);
    }

    public function setFilterDays($days)
    {
        $this->filterDays = $days;
        $this->customDays = ''; // Clear custom input when preset is clicked

        // Get fresh data for charts
        $timelineData = $this->getTimelineData();
        $coursesByUsers = $this->getCoursesByUsers();
        $sexDistribution = $this->getsexDistribution();

        // Dispatch event to update charts with new data
        $this->dispatch(
            'charts-update',
            timelineData: $timelineData,
            coursesByUsers: $coursesByUsers,
            sexDistribution: $sexDistribution
        );
    }

    public function applyCustomDays()
    {
        if ($this->customDays && is_numeric($this->customDays) && $this->customDays > 0) {
            $this->filterDays = (int) $this->customDays;

            // Get fresh data for charts
            $timelineData = $this->getTimelineData();
            $coursesByUsers = $this->getCoursesByUsers();
            $sexDistribution = $this->getsexDistribution();

            // Dispatch event to update charts with new data
            $this->dispatch(
                'charts-update',
                timelineData: $timelineData,
                coursesByUsers: $coursesByUsers,
                sexDistribution: $sexDistribution
            );
        }
    }

    public function render()
    {
        $totalUsers = $this->getTotalUsers();
        $totalLogins = $this->getTotalLogins();
        $activeUsers = Attendance::whereNotNull('login_time')
            ->whereNull('logout_time')
            ->where('library_section', session('current_section', 'entrance'))
            ->count();
        $archivedUsers = Archive::count();

        $topCoursesByLogins = $this->getTopCoursesByLogins();
        $topStudentsByLogins = $this->getTopStudentsByLogins();
        $recentLogins = $this->getRecentLogins();
        $timelineData = $this->getTimelineData();
        $coursesByUsers = $this->getCoursesByUsers();
        $sexDistribution = $this->getsexDistribution();

        $reservedRooms = $this->getReservedRooms();
        return view('components.dashboardPage.dashboard', [
            'totalUsers' => $totalUsers,
            'totalLogins' => $totalLogins,
            'activeUsers' => $activeUsers,
            'archivedUsers' => $archivedUsers,
            'topCoursesByLogins' => $topCoursesByLogins,
            'topStudentsByLogins' => $topStudentsByLogins,
            'recentLogins' => $recentLogins,
            'timelineData' => $timelineData,
            'coursesByUsers' => $coursesByUsers,
            'sexDistribution' => $sexDistribution,
            'reservedRooms' => $reservedRooms,
        ]);
    }

    public function getTotalUsers()
    {
        $startDate = Carbon::now()->subDays($this->filterDays);
        return User::where('created_at', '>=', $startDate)->count();
    }

    public function getTotalLogins()
    {
        $startDate = Carbon::now()->subDays($this->filterDays);
        return Attendance::whereNotNull('login_time')
            ->where('login_time', '>=', $startDate)
            ->where('library_section', session('current_section', 'entrance'))
            ->count();
    }

    public function getTopCoursesByLogins()
    {
        $startDate = Carbon::now()->subDays($this->filterDays);

        $topCourses = Attendance::join('users', 'attendance.user_id', '=', 'users.id')
            ->select('users.course', DB::raw('COUNT(*) as login_count'))
            ->whereNotNull('users.course')
            ->where('attendance.login_time', '>=', $startDate)
            ->where('attendance.library_section', session('current_section', 'entrance'))
            ->groupBy('users.course')
            ->orderByDesc('login_count')
            ->limit(10)
            ->get();

        return $topCourses;
    }

    public function getTopStudentsByLogins()
    {
        $startDate = Carbon::now()->subDays($this->filterDays);

        $topStudents = Attendance::join('users', 'attendance.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.fname',
                'users.lname',
                'users.course',
                DB::raw('COUNT(*) as login_count')
            )
            ->where('attendance.login_time', '>=', $startDate)
            ->where('attendance.library_section', session('current_section', 'entrance'))
            ->groupBy('users.id', 'users.fname', 'users.lname', 'users.course')
            ->orderByDesc('login_count')
            ->limit(10)
            ->get();

        return $topStudents;
    }

    public function getRecentLogins()
    {
        $recentLogins = Attendance::join('users', 'attendance.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.fname',
                'users.lname',
                'users.mname',
                'users.course',
                'users.sex',
                'users.user_type',
                'attendance.login_time',
                'attendance.logout_time'
            )
            ->where('attendance.library_section', session('current_section', 'entrance'))
            ->orderByDesc('attendance.login_time')
            ->limit(5)
            ->get();

        return $recentLogins;
    }

    public function getAdminHistory()
    {
        $adminHistory = AdminHistory::with('admin')
            ->orderByDesc('date_time')
            ->limit(20)
            ->get();

        return $adminHistory;
    }
    public function getTimelineData()
    {
        $startDate = Carbon::now()->subDays($this->filterDays);
        $timelineData = Attendance::select([
            DB::raw('DATE(login_time) as date'),
            DB::raw('COUNT(*) as count')
        ])
            ->where('login_time', '>=', $startDate)
            ->where('library_section', session('current_section', 'entrance'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return $timelineData;
    }

    public function getCoursesByUsers()
    {
        $startDate = Carbon::now()->subDays($this->filterDays);

        $coursesByLogins = Attendance::join('users', 'attendance.user_id', '=', 'users.id')
            ->select('users.course', DB::raw('COUNT(*) as login_count'))
            ->whereNotNull('users.course')
            ->where('attendance.login_time', '>=', $startDate)
            ->where('attendance.library_section', session('current_section', 'entrance'))
            ->groupBy('users.course')
            ->orderByDesc('login_count')
            ->get();

        return $coursesByLogins;
    }

    public function getsexDistribution()
    {
        $startDate = Carbon::now()->subDays($this->filterDays);

        $sexDistribution = Attendance::join('users', 'attendance.user_id', '=', 'users.id')
            ->select('users.sex', DB::raw('COUNT(*) as login_count'))
            ->whereNotNull('users.sex')
            ->where('attendance.login_time', '>=', $startDate)
            ->where('attendance.library_section', session('current_section', 'entrance'))
            ->groupBy('users.sex')
            ->get();

        return $sexDistribution;
    }

    public function getReservedRooms()
    {
        // Get upcoming reservations (today and future) with pending or approved status
        $reservedRooms = RoomReservation::with(['room', 'user'])
            ->whereIn('status', ['pending', 'approved'])
            ->where('reservation_date', '>=', Carbon::today())
            ->orderBy('reservation_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->take(10) // Limit to 10 upcoming reservations
            ->get();

        return $reservedRooms;
    }
}
