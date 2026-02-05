<?php

namespace App\Http\Controllers\Livewire\StudentRegistrationPage;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

class StudentRegistrationController extends Controller
{
    /**
     * Show the student registration page
     */
    public function index()
    {
        return view('student.registration');
    }

    /**
     * Handle the registration form submission
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'barcode' => 'required|string|unique:users,barcode|unique:users_archive,barcode',
            'fname' => 'required|string|max:255',
            'mname' => 'nullable|string|max:255',
            'lname' => 'required|string|max:255',
            'course' => 'required|string|max:255',
            'section' => 'nullable|string|max:255',
            'sex' => 'required|in:male,female',
            'user_type' => 'required|in:student,faculty,visitor,staff',
            'email' => 'required|email|unique:users,email',
            'phonenumber' => 'required|string|max:20',
        ]);

        try {
            // Get expiration date from settings
            $expirationDate = Setting::get('default_expiration_date');

            // Create the user
            $user = User::create([
                'barcode' => $validated['barcode'],
                'fname' => $validated['fname'],
                'mname' => $validated['mname'] ?? null,
                'lname' => $validated['lname'],
                'course' => $validated['course'],
                'section' => $validated['section'] ?? null,
                'sex' => $validated['sex'],
                'user_type' => $validated['user_type'],
                'email' => $validated['email'],
                'phonenumber' => $validated['phonenumber'],
                'user_status' => 'outside',
                'expiration_date' => $expirationDate ? Carbon::parse($expirationDate) : null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registration successful! You can now use your ID to access the library.',
                'user' => [
                    'name' => $user->fname . ' ' . $user->lname,
                    'barcode' => $user->barcode,
                    'user_type' => $user->user_type,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
