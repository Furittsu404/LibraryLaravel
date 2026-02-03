<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Archive;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ScannerController extends Controller
{
    public function index()
    {
        // Get current section from session, default to 'entrance'
        $currentSection = session('scanner_section', 'entrance');
        $sectionName = $this->getSectionName($currentSection);

        // Get today's logins for current section
        $recentLogins = $this->getTodaysLogins($currentSection);

        return view('scanner.index', [
            'currentSection' => $currentSection,
            'sectionName' => $sectionName,
            'recentLogins' => $recentLogins
        ]);
    }

    public function scan(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string'
        ]);

        $barcode = $request->barcode;
        $currentSection = $request->input('section') ?? session('scanner_section', 'entrance');
        // Find user by barcode (student ID)
        $user = User::where('barcode', $barcode)->first();

        if (!$user) {
            // Check if user is in archive
            $archivedUser = Archive::where('barcode', $barcode)->first();

            if ($archivedUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account Archived!',
                    'description' => 'Please go to the Multimedia Section to re-activate your account.',
                    'type' => 'archived'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Student ID not found. Please register first.',
                'type' => 'error'
            ]);
        }

        // Check if user account is active
        if ($user->account_status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Account is inactive. Please contact the librarian.',
                'type' => 'error'
            ]);
        }

        // Check if user has expired
        if ($user->expiration_date && Carbon::parse($user->expiration_date)->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Account has expired. Please renew your library access.',
                'type' => 'error'
            ]);
        }

        // Check if user is already logged in today in this section
        $today = Carbon::today();
        if ($currentSection === 'exit') {
            // For exit section, check if user is logged in at the Entrance
            $existingLogin = Attendance::where('user_id', $user->id)
                ->where('library_section', 'entrance')
                ->whereDate('login_time', $today)
                ->whereNull('logout_time')
                ->first();
        } else {
            // For other sections, check specific section
            $existingLogin = Attendance::where('user_id', $user->id)
                ->where('library_section', $currentSection)
                ->whereDate('login_time', $today)
                ->whereNull('logout_time')
                ->first();
        }

        if ($existingLogin) {
            // Check if User is logging in at the Entrance or Exit section
            if ($currentSection === 'entrance') {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already logged in at the Entrance. Please log out first before logging in again.',
                    'type' => 'error'
                ]);
            } else {
                $user->user_status = 'outside';
                $user->save();
            }

            if ($currentSection === 'exit') {
                //Log out the user from all sections
                $loginsToLogout = Attendance::where('user_id', $user->id)
                    ->whereNull('logout_time')
                    ->get();
                foreach ($loginsToLogout as $login) {
                    $login->logout_time = now();
                    $login->save();
                }
                $loginCount = $loginsToLogout->count();
                return response()->json([
                    'success' => true,
                    'message' => 'Goodbye, ' . $user->fname . '! You have been logged out from ' . $loginCount . ' section(s).',
                    'type' => 'logout',
                    'user' => [
                        'name' => $user->fname . ' ' . $user->lname,
                        'course' => $user->course,
                        'user_type' => ucfirst($user->user_type),
                    ]
                ]);
            } else {
                // Log out the user
                $existingLogin->logout_time = now();
                $existingLogin->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Goodbye, ' . $user->fname . '! Logged out successfully.',
                'type' => 'logout',
                'user' => [
                    'name' => $user->fname . ' ' . $user->lname,
                    'course' => $user->course,
                    'user_type' => ucfirst($user->user_type),
                ]
            ]);
        } else {
            if ($currentSection === 'exit') {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not logged in at the Entrance. Please log in first before logging out.',
                    'type' => 'error'
                ]);
            }
            // Log in the user
            Attendance::create([
                'user_id' => $user->id,
                'library_section' => $currentSection,
                'login_time' => now(),
            ]);

            // Update user status
            $user->user_status = 'inside';
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Welcome, ' . $user->fname . '! Logged in successfully.',
                'type' => 'login',
                'user' => [
                    'name' => $user->fname . ' ' . $user->lname,
                    'course' => $user->course,
                    'user_type' => ucfirst($user->user_type),
                    'login_time' => now()->format('h:i A')
                ]
            ]);
        }
    }

    public function getTodaysLoginsApi(Request $request)
    {
        $currentSection = $request->query('section') ?? (session('scanner_section') === 'exit' ? 'entrance' : session('scanner_section', 'entrance'));
        $recentLogins = $this->getTodaysLogins($currentSection);

        return response()->json([
            'success' => true,
            'logins' => $recentLogins
        ]);
    }

    public function setSection(Request $request)
    {
        $request->validate([
            'section' => 'required|string'
        ]);

        session(['scanner_section' => $request->section]);

        return response()->json([
            'success' => true,
            'message' => 'Section changed successfully',
            'sectionName' => $this->getSectionName($request->section)
        ]);
    }

    private function getTodaysLogins($section)
    {
        $today = Carbon::today();
        $section = $section === 'exit' ? 'entrance' : $section;
        return DB::table('attendance')
            ->join('users', 'attendance.user_id', '=', 'users.id')
            ->select(
                'users.fname',
                'users.lname',
                'users.course',
                'users.user_type',
                'attendance.login_time',
                'attendance.logout_time'
            )
            ->where('attendance.library_section', $section)
            ->whereDate('attendance.login_time', $today)
            ->orderBy('attendance.login_time', 'desc')
            ->limit(15)
            ->get()
            ->map(function ($login) {
                return [
                    'name' => $login->fname . ' ' . $login->lname,
                    'course' => $login->course,
                    'user_type' => ucfirst($login->user_type),
                    'login_time' => Carbon::parse($login->login_time)->format('h:i A'),
                    'status' => $login->logout_time ? 'logged_out' : 'logged_in'
                ];
            });
    }

    private function getSectionName($code)
    {
        $sections = [
            'entrance' => 'Entrance',
            'exit' => 'Exit',
            'serials' => 'Serials & Reference',
            'humanities' => 'Humanities',
            'multimedia' => 'Multimedia',
            'filipiniana' => 'Filipiniana & Theses',
            'relegation' => 'Relegation',
            'science' => 'Science & Technology'
        ];

        return $sections[$code] ?? 'Unknown Section';
    }
}