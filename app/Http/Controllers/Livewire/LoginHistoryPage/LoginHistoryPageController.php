<?php

namespace App\Http\Controllers\Livewire\LoginHistoryPage;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoginHistoryPageController extends Component
{
    use WithPagination;

    public $title = 'Login History';
    public $sex = '';
    public $course = '';
    public $userType = '';
    public $search = '';
    public $startDate = '';
    public $endDate = '';

    protected $listeners = ['loginDeleted' => '$refresh'];

    public function render()
    {
        $loginHistory = $this->getLoginHistory();
        $courses = $this->getAvailableCourses();

        return view('components.loginHistoryPage.loginHistoryTable', [
            'loginHistory' => $loginHistory,
            'courses' => $courses,
            'title' => $this->title,
        ]);
    }

    public function getLoginHistory()
    {
        $query = DB::table('attendance')
            ->leftJoin('users', 'attendance.user_id', '=', 'users.id')
            ->leftJoin('users_archive', 'attendance.user_id', '=', 'users_archive.id')
            ->select(
                'attendance.id as attendance_id',
                'attendance.login_time',
                'attendance.logout_time',
                DB::raw('COALESCE(users.id, users_archive.id, 0) as user_id'),
                DB::raw('COALESCE(users.lname, users_archive.lname, "Unknown") as lname'),
                DB::raw('COALESCE(users.fname, users_archive.fname, "User") as fname'),
                DB::raw('COALESCE(users.mname, users_archive.mname, "") as mname'),
                DB::raw('COALESCE(users.course, users_archive.course, "N/A") as course'),
                DB::raw('COALESCE(users.sex, users_archive.sex, "unknown") as sex')
            )
            ->where('attendance.library_section', session('current_section', 'entrance'));

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where(DB::raw('COALESCE(users.fname, users_archive.fname)'), 'like', '%' . $this->search . '%')
                    ->orWhere(DB::raw('COALESCE(users.lname, users_archive.lname)'), 'like', '%' . $this->search . '%')
                    ->orWhere(DB::raw('COALESCE(users.course, users_archive.course)'), 'like', '%' . $this->search . '%')
                    ->orWhere(DB::raw('COALESCE(users.id, users_archive.id)'), 'like', '%' . $this->search . '%');
            });
        }

        // Apply filters
        if (!empty($this->sex)) {
            $query->where(function ($q) {
                $q->where('users.sex', $this->sex)
                    ->orWhere('users_archive.sex', $this->sex);
            });
        }

        if (!empty($this->course)) {
            $query->where(function ($q) {
                $q->where('users.course', $this->course)
                    ->orWhere('users_archive.course', $this->course);
            });
        }

        if (!empty($this->userType)) {
            $query->where(function ($q) {
                $q->where('users.user_type', $this->userType)
                    ->orWhere('users_archive.user_type', $this->userType);
            });
        }

        // Apply date range filter
        if (!empty($this->startDate)) {
            $query->where('attendance.login_time', '>=', $this->startDate);
        }

        if (!empty($this->endDate)) {
            $endDateTime = Carbon::parse($this->endDate)->endOfDay();
            $query->where('attendance.login_time', '<=', $endDateTime);
        }

        return $query->orderBy('attendance.login_time', 'desc')->paginate(25);
    }

    public function getAvailableCourses()
    {
        return DB::table('users')
            ->whereNotNull('course')
            ->where('course', '!=', '')
            ->distinct()
            ->pluck('course')
            ->toArray();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteLogin($attendanceId)
    {
        try {
            DB::table('attendance')->where('id', $attendanceId)->delete();

            $this->dispatch(
                'show-toast',
                type: 'success',
                message: 'Login record deleted successfully'
            );

            $this->dispatch('loginDeleted');
        } catch (\Exception $e) {
            $this->dispatch(
                'show-toast',
                type: 'error',
                message: 'Failed to delete login record'
            );
        }
    }

    public function bulkDelete($attendanceIds)
    {
        try {
            $count = DB::table('attendance')->whereIn('id', $attendanceIds)->delete();

            $this->dispatch(
                'show-toast',
                type: 'success',
                message: "{$count} login record(s) deleted successfully"
            );

            $this->dispatch('loginDeleted');
        } catch (\Exception $e) {
            $this->dispatch(
                'show-toast',
                type: 'error',
                message: 'Failed to delete login records'
            );
        }
    }
}