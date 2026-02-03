<?php

namespace App\Http\Controllers\Livewire\ArchivePage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Archive;
use App\Models\User;
use App\Models\Setting;

class ArchiveController extends Controller
{
    /**
     * Edit archived user - automatically activates and moves to users table
     */
    public function edit(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'lname' => 'required|string|max:255',
            'fname' => 'required|string|max:255',
            'mname' => 'nullable|string|max:255',
            'sex' => 'required|in:male,female',
            'email' => 'nullable|email|max:255',
            'phonenumber' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'course' => 'required|string|max:100',
            'section' => 'nullable|string|max:50',
            'barcode' => 'required|string|max:255',
            'user_type' => 'required|in:student,visitor,faculty,staff'
        ]);

        DB::beginTransaction();

        try {
            $archivedUser = Archive::find($request->id);

            if (!$archivedUser) {
                return response()->json(['success' => false, 'message' => 'Archived user not found'], 404);
            }

            // Automatically set expiration_date from settings
            $expirationDate = Setting::get('default_expiration_date', now()->addYear()->format('Y-m-d'));

            // Create active user with updated data
            $user = new User([
                'lname' => $validated['lname'],
                'fname' => $validated['fname'],
                'mname' => $validated['mname'],
                'address' => $validated['address'],
                'email' => $validated['email'],
                'sex' => $validated['sex'],
                'course' => $validated['course'],
                'section' => $validated['section'],
                'phonenumber' => $validated['phonenumber'],
                'barcode' => $validated['barcode'],
                'user_status' => 'outside',
                'user_type' => $validated['user_type'],
                'account_status' => 'active', // Automatically activated
                'expiration_date' => $expirationDate,
            ]);
            $user->save();

            // Delete from archive
            $archivedUser->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Account updated and activated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update and activate: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Permanently delete archived user
     */
    public function delete(Request $request)
    {
        try {
            $archivedUser = Archive::find($request->id);

            if (!$archivedUser) {
                return response()->json(['success' => false, 'message' => 'Archived user not found'], 404);
            }

            $archivedUser->delete();

            return response()->json([
                'success' => true,
                'message' => 'Archived account permanently deleted'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete archived users
     */
    public function bulkDelete(Request $request)
    {
        try {
            $count = Archive::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => "{$count} archived account(s) permanently deleted"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk delete failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
