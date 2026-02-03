<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Section;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function render()
    {
        return view('layouts.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            // Redirect to section selector instead of dashboard
            return redirect()->route('select-section');
        }
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function selectSection()
    {
        $sections = Section::where('is_active', true)->get();
        return view('layouts.select-section', compact('sections'));
    }

    public function setSection(Request $request)
    {
        $validated = $request->validate([
            'section' => 'required|exists:sections,code'
        ]);

        // Get section details
        $section = Section::where('code', $validated['section'])->first();

        // Store in session
        session([
            'current_section' => $section->code,
            'current_section_name' => $section->name,
            'current_section_icon' => $section->icon,
        ]);

        return redirect()->route('admin.dashboard.index');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function debugCreateAdmin()
    {
        $name = 'admin';
        $email = 'admin';
        Admin::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt('admin'),
            'role' => 'super_admin'
        ]);
        return 'Admin created';
    }
}
