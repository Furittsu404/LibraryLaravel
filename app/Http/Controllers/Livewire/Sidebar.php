<?php

namespace App\Http\Controllers\Livewire;

use Livewire\Component;

class Sidebar extends Component
{
    public $collapsed = true;
    public $activePage = '';

    public function mount()
    {
        $routeName = request()->route() ? request()->route()->getName() : null;
        if ($routeName) {
            if (str_starts_with($routeName, 'admin.dashboard')) {
                $this->activePage = 'dashboard';
            } elseif (str_starts_with($routeName, 'admin.accounts')) {
                $this->activePage = 'accounts';
            } elseif (str_contains($routeName, 'admin.reports')) {
                $this->activePage = 'reports';
            } elseif (str_contains($routeName, 'admin.login-history')) {
                $this->activePage = 'login-history';
            } elseif (str_contains($routeName, 'admin.archive')) {
                $this->activePage = 'archive';
            } elseif (str_contains($routeName, 'admin.settings')) {
                $this->activePage = 'settings';
            }
        }
    }

    public function toggleSidebar()
    {
        $this->collapsed = !$this->collapsed;
    }

    public function render()
    {
        return view('components.sidebar', ['activePage' => $this->activePage, 'collapsed' => $this->collapsed]);
    }
}
