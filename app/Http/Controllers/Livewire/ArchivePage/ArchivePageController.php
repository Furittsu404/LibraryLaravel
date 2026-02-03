<?php

namespace App\Http\Controllers\Livewire\ArchivePage;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

use App\Models\Archive;
use App\Models\User;

class ArchivePageController extends Component
{
    use WithPagination;

    public $title = 'Archive Management';
    protected $users = [];
    protected $courses = [];
    public $sex;
    public $course;
    public $type;
    public $search;

    protected $listeners = ['userUpdated' => '$refresh', 'userActivated' => '$refresh', 'userDeleted' => '$refresh'];

    public function render()
    {
        $this->getUsers();
        $this->getCourses();
        return view('components.archivePage.archiveTable', [
            'users' => $this->users,
            'courses' => $this->courses,
            'title' => $this->title,
        ]);
    }

    public function getUsers()
    {
        $query = Archive::query();

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

        if (isset($this->type) && $this->type !== '') {
            $query->where('user_type', $this->type);
        }

        $this->users = $query->orderBy('archived_at', 'desc')->paginate(10);
    }

    public function getCourses()
    {
        $this->courses = Archive::select('course')->distinct()->pluck('course');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function activateUser($userId)
    {
        DB::beginTransaction();

        try {
            $archivedUser = Archive::find($userId);

            if (!$archivedUser) {
                throw new \Exception('Archived user not found');
            }

            // Create active user from archived data
            $user = new User([
                'id' => $archivedUser->id,
                'lname' => $archivedUser->lname,
                'fname' => $archivedUser->fname,
                'mname' => $archivedUser->mname,
                'address' => $archivedUser->address,
                'email' => $archivedUser->email,
                'sex' => $archivedUser->sex,
                'course' => $archivedUser->course,
                'section' => $archivedUser->section,
                'phonenumber' => $archivedUser->phonenumber,
                'barcode' => $archivedUser->barcode,
                'user_status' => 'outside',
                'user_type' => $archivedUser->user_type,
                'account_status' => 'active',
                'expiration_date' => $archivedUser->expiration_date ?? null,
                'created_at' => $archivedUser->created_at ?? null,
                'updated_at' => $archivedUser->updated_at ?? null,
            ]);
            $user->save();

            // Delete from archive
            $archivedUser->delete();

            DB::commit();

            $this->dispatch(
                'show-toast',
                type: 'success',
                message: 'User activated successfully'
            );

            $this->dispatch('userActivated');

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch(
                'show-toast',
                type: 'error',
                message: 'Activation failed: ' . $e->getMessage()
            );

            return false;
        }
    }

    public function bulkActivate($userIds)
    {
        DB::beginTransaction();

        try {
            $archivedUsers = Archive::whereIn('id', $userIds)->get();
            $count = 0;

            foreach ($archivedUsers as $archivedUser) {
                $user = new User([
                    'id' => $archivedUser->id,
                    'lname' => $archivedUser->lname,
                    'fname' => $archivedUser->fname,
                    'mname' => $archivedUser->mname,
                    'address' => $archivedUser->address,
                    'email' => $archivedUser->email,
                    'sex' => $archivedUser->sex,
                    'course' => $archivedUser->course,
                    'section' => $archivedUser->section,
                    'phonenumber' => $archivedUser->phonenumber,
                    'barcode' => $archivedUser->barcode,
                    'user_status' => 'outside',
                    'user_type' => $archivedUser->user_type,
                    'account_status' => 'active',
                    'expiration_date' => $archivedUser->expiration_date ?? null,
                ]);
                $user->save();

                $archivedUser->delete();
                $count++;
            }

            DB::commit();

            $this->dispatch(
                'show-toast',
                type: 'success',
                message: "{$count} user(s) activated successfully"
            );

            $this->dispatch('userActivated');

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch(
                'show-toast',
                type: 'error',
                message: 'Bulk activation failed: ' . $e->getMessage()
            );

            return false;
        }
    }
}
