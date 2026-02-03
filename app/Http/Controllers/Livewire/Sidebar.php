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
                session(['title' => 'Dashboard']);
            } elseif (str_starts_with($routeName, 'admin.accounts')) {
                $this->activePage = 'accounts';
                session(['title' => 'Accounts']);
            } elseif (str_contains($routeName, 'admin.reports')) {
                $this->activePage = 'reports';
                session(['title' => 'Reports']);
            } elseif (str_contains($routeName, 'admin.login-history')) {
                $this->activePage = 'login-history';
                session(['title' => 'Login History']);
            } elseif (str_contains($routeName, 'admin.archive')) {
                $this->activePage = 'archive';
                session(['title' => 'Archive']);
            } elseif (str_contains($routeName, 'admin.reserved-rooms')) {
                $this->activePage = 'reserved-rooms';
                session(['title' => 'Reserved Rooms']);
            } elseif (str_contains($routeName, 'admin.settings')) {
                $this->activePage = 'settings';
                session(['title' => 'Settings']);
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