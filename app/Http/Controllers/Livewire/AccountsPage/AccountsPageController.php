<?php

namespace App\Http\Controllers\Livewire\AccountsPage;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

use App\Models\User;
use App\Models\Archive;

class AccountsPageController extends Component
{
    use WithPagination;
    public $title = 'LISO - Accounts';
    protected $users = [];
    protected $courses = [];
    public $sex;
    public $course;
    public $status;
    public $type;
    public $search;

    protected $listeners = ['userUpdated' => '$refresh', 'userCreated' => '$refresh', 'userDeleted' => '$refresh'];

    public function mount()
    {
        session(['title' => $this->title]);
    }

    public function render()
    {
        $this->getUsers();
        $this->getCourses();
        return view('components.accountsPage.accountsTable', [
            'users' => $this->users,
            'courses' => $this->courses,
            'title' => $this->title,
        ]);
    }
    public function getUsers()
    {
        if (isset($this->sex) || isset($this->course) || isset($this->status) || isset($this->type) || isset($this->search)) {
            $query = User::query();
            if (isset($this->search)) {
                $query->where(function ($q) {
                    $q->where('fname', 'like', '%' . $this->search . '%')
                        ->orWhere('lname', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('barcode', 'like', '%' . $this->search . '%');
                });
            }
            if (isset($this->sex) && $this->sex !== '') {
                $query->where('sex', $this->sex);
            }
            if (isset($this->course) && $this->course !== '') {
                $query->where('course', $this->course);
            }
            if (isset($this->status) && $this->status !== '') {
                $query->where('user_status', $this->status);
            }
            if (isset($this->type) && $this->type !== '') {
                $query->where('user_type', $this->type);
            }
            $this->users = $query->orderBy('id', 'desc')->paginate(10);
        } else {
            $this->users = User::orderBy('id', 'desc')->paginate(10);
        }
    }
    public function getCourses()
    {
        $this->courses = User::select('course')->distinct()->pluck('course');
    }
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updateUserStatus($userId, $newStatus)
    {
        DB::beginTransaction();

        try {
            $user = User::find($userId);
            if (!$user) {
                throw new \Exception('User not found');
            }

            // Handle attendance tracking
            if ($newStatus === 'inside') {
                // Check if user already has an open attendance record (already inside)
                $existingAttendance = DB::table('attendance')
                    ->where('user_id', $userId)
                    ->whereNull('logout_time')
                    ->exists();

                if ($existingAttendance) {
                    $this->dispatch(
                        'show-toast',
                        type: 'error',
                        message: 'User is already inside the library'
                    );
                    DB::rollBack();
                    return;
                }

                // Create new attendance record with login time
                DB::table('attendance')->insert([
                    'user_id' => $userId,
                    'library_section' => 'entrance',
                    'login_time' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            } elseif ($newStatus === 'outside') {
                // Check if user has an open attendance record
                $hasOpenRecord = DB::table('attendance')
                    ->where('user_id', $userId)
                    ->whereNull('logout_time')
                    ->exists();

                if (!$hasOpenRecord) {
                    $this->dispatch(
                        'show-toast',
                        type: 'error',
                        message: 'User has no active attendance record to close'
                    );
                    DB::rollBack();
                    return;
                }

                // Update the most recent attendance record with logout time
                DB::table('attendance')
                    ->where('user_id', $userId)
                    ->whereNull('logout_time')
                    ->orderBy('login_time', 'desc')
                    ->limit(1)
                    ->update([
                        'logout_time' => now(),
                        'updated_at' => now(),
                    ]);
            }

            // Update user status only after attendance is handled successfully
            $user->user_status = $newStatus;
            $user->save();

            DB::commit();

            $this->dispatch(
                'show-toast',
                type: 'success',
                message: 'User status updated successfully'
            );

        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch(
                'show-toast',
                type: 'error',
                message: 'Failed to update user status: ' . $e->getMessage()
            );
        }
    }

    public function bulkUpdateStatus($userIds, $newStatus)
    {
        DB::beginTransaction();

        try {
            // Handle attendance tracking for bulk updates
            if ($newStatus === 'inside') {
                // Get users who don't already have open attendance records
                $usersWithOpenRecords = DB::table('attendance')
                    ->whereIn('user_id', $userIds)
                    ->whereNull('logout_time')
                    ->pluck('user_id')
                    ->toArray();

                $usersToUpdate = array_diff($userIds, $usersWithOpenRecords);

                if (empty($usersToUpdate)) {
                    $this->dispatch(
                        'show-toast',
                        type: 'error',
                        message: 'All selected users are already inside'
                    );
                    DB::rollBack();
                    return false;
                }

                // Create attendance records for users who can be moved inside
                $attendanceRecords = [];
                foreach ($usersToUpdate as $userId) {
                    $attendanceRecords[] = [
                        'user_id' => $userId,
                        'library_section' => 'entrance',
                        'login_time' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                DB::table('attendance')->insert($attendanceRecords);

                // Update only users who were successfully processed
                $count = User::whereIn('id', $usersToUpdate)->update(['user_status' => $newStatus]);

                $skippedCount = count($usersWithOpenRecords);
                $message = "{$count} user(s) moved inside successfully";
                if ($skippedCount > 0) {
                    $message .= ". {$skippedCount} user(s) skipped (already inside)";
                }

            } elseif ($newStatus === 'outside') {
                // Get users who have open attendance records
                $usersWithOpenRecords = DB::table('attendance')
                    ->whereIn('user_id', $userIds)
                    ->whereNull('logout_time')
                    ->pluck('user_id')
                    ->toArray();

                if (empty($usersWithOpenRecords)) {
                    $this->dispatch(
                        'show-toast',
                        type: 'error',
                        message: 'No users have active attendance records to close'
                    );
                    DB::rollBack();
                    return false;
                }

                // Update logout time for users with open records
                DB::table('attendance')
                    ->whereIn('user_id', $usersWithOpenRecords)
                    ->whereNull('logout_time')
                    ->update([
                        'logout_time' => now(),
                        'updated_at' => now(),
                    ]);

                // Update only users who were successfully processed
                $count = User::whereIn('id', $usersWithOpenRecords)->update(['user_status' => $newStatus]);

                $skippedCount = count($userIds) - count($usersWithOpenRecords);
                $message = "{$count} user(s) moved outside successfully";
                if ($skippedCount > 0) {
                    $message .= ". {$skippedCount} user(s) skipped (no active record)";
                }
            }

            DB::commit();

            $this->dispatch(
                'show-toast',
                type: 'success',
                message: $message
            );

            return true;

        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch(
                'show-toast',
                type: 'error',
                message: 'Failed to update users: ' . $e->getMessage()
            );
            return false;
        }
    }

    public function bulkArchive($userIds)
    {
        DB::beginTransaction();

        try {
            $users = User::whereIn('id', $userIds)->get();
            $count = 0;

            foreach ($users as $user) {
                $archive = new Archive([
                    'id' => $user->id,
                    'lname' => $user->lname,
                    'fname' => $user->fname,
                    'mname' => $user->mname,
                    'address' => $user->address,
                    'email' => $user->email,
                    'sex' => $user->sex,
                    'course' => $user->course,
                    'section' => $user->section,
                    'phonenumber' => $user->phonenumber,
                    'barcode' => $user->barcode,
                    'user_status' => 'outside',
                    'user_type' => $user->user_type,
                    'account_status' => 'inactive',
                    'expiration_date' => $user->expiration_date ?? null,
                    'archived_at' => now(),
                ]);
                $archive->timestamps = false;
                $archive->save();

                $user->delete();
                $count++;
            }

            DB::commit();

            $this->dispatch(
                'show-toast',
                type: 'success',
                message: "{$count} user(s) archived successfully"
            );

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch(
                'show-toast',
                type: 'error',
                message: 'Archive failed: ' . $e->getMessage()
            );

            return false;
        }
    }

    public function downloadAllUsers()
    {
        try {
            $users = User::orderBy('id', 'desc')->get();

            $filename = 'all_users_' . date('Y-m-d_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($users) {
                $file = fopen('php://output', 'w');

                // Add CSV headers
                fputcsv($file, [
                    'id',
                    'barcode',
                    'lname',
                    'fname',
                    'mname',
                    'email',
                    'sex',
                    'phonenumber',
                    'address',
                    'course',
                    'section',
                    'user_type',
                    'user_status',
                    'account_status',
                    'expiration_date',
                    'created_at',
                    'updated_at'
                ]);

                // Add user data
                foreach ($users as $user) {
                    fputcsv($file, [
                        $user->id,
                        $user->barcode,
                        $user->lname,
                        $user->fname,
                        $user->mname,
                        $user->email,
                        $user->sex,
                        $user->phonenumber,
                        $user->address,
                        $user->course,
                        $user->section,
                        $user->user_type,
                        $user->user_status,
                        $user->account_status,
                        $user->expiration_date,
                        $user->created_at,
                        $user->updated_at
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            $this->dispatch(
                'show-toast',
                type: 'error',
                message: 'Failed to download CSV: ' . $e->getMessage()
            );
            return false;
        }
    }

    public function changeAllStatus($newStatus)
    {
        DB::beginTransaction();

        try {
            $allUserIds = User::pluck('id')->toArray();

            // Handle attendance tracking
            if ($newStatus === 'inside') {
                // Get users who don't already have open attendance records
                $usersWithOpenRecords = DB::table('attendance')
                    ->whereIn('user_id', $allUserIds)
                    ->whereNull('logout_time')
                    ->pluck('user_id')
                    ->toArray();

                $usersToUpdate = array_diff($allUserIds, $usersWithOpenRecords);

                if (empty($usersToUpdate)) {
                    $this->dispatch(
                        'show-toast',
                        type: 'error',
                        message: 'All users are already inside'
                    );
                    DB::rollBack();
                    return false;
                }

                // Create attendance records
                $attendanceRecords = [];
                foreach ($usersToUpdate as $userId) {
                    $attendanceRecords[] = [
                        'user_id' => $userId,
                        'library_section' => 'entrance',
                        'login_time' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                DB::table('attendance')->insert($attendanceRecords);

                $count = User::whereIn('id', $usersToUpdate)->update(['user_status' => $newStatus]);

                $skippedCount = count($usersWithOpenRecords);
                $message = "Successfully changed {$count} user(s) status to INSIDE";
                if ($skippedCount > 0) {
                    $message .= ". {$skippedCount} user(s) already inside";
                }

            } elseif ($newStatus === 'outside') {
                $usersWithOpenRecords = DB::table('attendance')
                    ->whereIn('user_id', $allUserIds)
                    ->whereNull('logout_time')
                    ->pluck('user_id')
                    ->toArray();

                if (empty($usersWithOpenRecords)) {
                    $this->dispatch(
                        'show-toast',
                        type: 'error',
                        message: 'No users have active attendance records to close'
                    );
                    DB::rollBack();
                    return false;
                }

                DB::table('attendance')
                    ->whereIn('user_id', $usersWithOpenRecords)
                    ->whereNull('logout_time')
                    ->update([
                        'logout_time' => now(),
                        'updated_at' => now(),
                    ]);

                $count = User::whereIn('id', $usersWithOpenRecords)->update(['user_status' => $newStatus]);

                $skippedCount = count($allUserIds) - count($usersWithOpenRecords);
                $message = "Successfully changed {$count} user(s) status to OUTSIDE";
                if ($skippedCount > 0) {
                    $message .= ". {$skippedCount} user(s) had no active record";
                }
            }

            DB::commit();

            $this->dispatch(
                'show-toast',
                type: 'success',
                message: $message
            );

            return true;

        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch(
                'show-toast',
                type: 'error',
                message: 'Failed to update all users: ' . $e->getMessage()
            );
            return false;
        }
    }
}
