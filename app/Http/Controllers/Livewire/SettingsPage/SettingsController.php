<?php

namespace App\Http\Controllers\Livewire\SettingsPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\Admin;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function updateExpirationDate(Request $request)
    {
        $validated = $request->validate([
            'expiration_date' => 'required|date|after:today'
        ]);

        Setting::set('default_expiration_date', $validated['expiration_date']);

        return response()->json([
            'success' => true,
            'message' => 'Default expiration date updated to ' . $validated['expiration_date']
        ]);
    }

    public function updateAutoLogout(Request $request)
    {
        $validated = $request->validate([
            'logout_time' => 'required|date_format:H:i'
        ]);

        Setting::set('auto_logout_time', $validated['logout_time']);

        return response()->json([
            'success' => true,
            'message' => 'Auto-logout time updated to ' . $validated['logout_time']
        ]);
    }

    public function updateAdminAccount(Request $request)
    {
        try {
            $admin = Auth::guard('admin')->user();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|max:255|unique:admins,email,' . $admin->id,
                'current_password' => 'nullable|required_with:new_password',
                'new_password' => 'nullable|confirmed'
            ]);

            // If changing password, verify current password
            if ($request->filled('new_password')) {
                if (!Hash::check($validated['current_password'], $admin->password)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Current password is incorrect'
                    ]);
                }

                $admin->password = Hash::make($validated['new_password']);
            }

            $admin->name = $validated['name'];
            $admin->email = $validated['email'];
            $admin->save();

            return response()->json([
                'success' => true,
                'message' => 'Admin account updated successfully'
            ]);
        } catch (ValidationException $e) {
            // Get the first validation error message
            $errors = $e->errors();
            $firstError = reset($errors);
            $message = is_array($firstError) ? $firstError[0] : $firstError;

            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $errors
            ]);
        }
    }
}