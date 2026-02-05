<?php

namespace App\Http\Controllers\Livewire\SettingsPage;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

class SettingsPageController extends Component
{
    public $title = 'LISO - Settings';
    public $defaultExpirationDate;
    public $autoLogoutTime;

    // Admin account fields
    public $adminName;
    public $adminEmail;
    public $currentPassword;
    public $newPassword;
    public $newPasswordConfirmation;

    public function mount()
    {
        session(['title' => $this->title]);

        // Load current settings from database
        $this->loadSettings();

        // Load current admin data
        $admin = Auth::guard('admin')->user();
        $this->adminName = $admin->name;
        $this->adminEmail = $admin->email;
    }

    public function loadSettings()
    {
        // Get default expiration date
        $this->defaultExpirationDate = Setting::get('default_expiration_date', now()->addYear()->format('Y-m-d'));

        // Get auto logout time
        $this->autoLogoutTime = Setting::get('auto_logout_time', '17:00');
    }

    #[On('settingsUpdated')]
    public function refreshSettings()
    {
        $this->loadSettings();
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('components.settingsPage.settingsPage', [
            'activePage' => 'settings'
        ]);
    }
}
