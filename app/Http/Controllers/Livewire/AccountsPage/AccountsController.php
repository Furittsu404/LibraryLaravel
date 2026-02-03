<?php

namespace App\Http\Controllers\Livewire\AccountsPage;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Archive;
use App\Models\Setting;

class AccountsController extends Controller
{
    public function edit(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:users,id',
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
            'user_type' => 'required|in:student,visitor,faculty,staff',
            'account_status' => 'required|in:active,inactive'
        ]);

        $user = User::findOrFail($validated['id']);
        $conflictUser = User::where('barcode', $validated['barcode'])->where('id', '!=', $validated['id'])->first();
        if ($conflictUser) {
            return response()->json(['error' => true, 'message' => 'Error! Account with ID ' . $validated['barcode'] . ' already exists.']);
        }
        $user->update($validated);

        return response()->json(['success' => true, 'message' => 'User updated successfully']);
    }
    public function create(Request $request)
    {
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
            'user_type' => 'required|in:student,visitor,faculty,staff',
            'account_status' => 'required|in:active,inactive'
        ]);

        $user = User::where('barcode', $validated['barcode'])->first();
        if ($user) {
            return response()->json(['error' => true, 'message' => 'Error! Account with ID ' . $validated['barcode'] . ' already exists.']);
        }

        // Automatically set expiration_date from settings
        $validated['expiration_date'] = Setting::get('default_expiration_date', now()->addYear()->format('Y-m-d'));

        User::create($validated);
        return response()->json(['success' => true, 'message' => 'Account created successfully']);
    }
    public function delete(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:users,id',
        ]);

        DB::beginTransaction();

        try {
            $user = User::findOrFail($validated['id']);

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
                'created_at' => $user->created_at ?? null,
                'updated_at' => $user->updated_at ?? null,
            ]);
            $archive->timestamps = false;
            $archive->save();
            $user->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Account archived successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Archive failed: ' . $e->getMessage()], 500);
        }
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        try {
            $file = $request->file('file');
            $path = $file->getRealPath();
            $data = array_map('str_getcsv', file($path));

            $headers = array_map('trim', $data[0]);
            unset($data[0]);

            $imported = 0;
            $skipped = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($data as $index => $row) {
                if (count($row) !== count($headers)) {
                    $skipped++;
                    continue;
                }

                $userData = array_combine($headers, array_map('trim', $row));

                if (User::where('barcode', $userData['barcode'] ?? '')->exists()) {
                    // Update user information
                    foreach ($userData as $key => $value) {
                        if (is_null($value) || $value === '') {
                            unset($userData[$key]);
                        }
                    }
                    User::where('barcode', $userData['barcode'])->update($userData);
                    $imported++;
                    continue;
                }

                if (empty($userData['lname']) || empty($userData['fname']) || empty($userData['barcode'])) {
                    $skipped++;
                    $errors[] = "Row " . ($index + 1) . ": Missing required fields: " . implode(', ', array_filter([
                        empty($userData['lname']) ? 'lname' : null,
                        empty($userData['fname']) ? 'fname' : null,
                        empty($userData['barcode']) ? 'barcode' : null,
                    ]));
                    continue;
                }

                try {
                    User::create([
                        'lname' => $userData['lname'] ?? '',
                        'fname' => $userData['fname'] ?? '',
                        'mname' => $userData['mname'] ?? null,
                        'email' => $userData['email'] ?? null,
                        'sex' => $userData['sex'] ?? null,
                        'phonenumber' => $userData['phonenumber'] ?? null,
                        'address' => $userData['address'] ?? null,
                        'course' => $userData['course'] ?? '',
                        'section' => $userData['section'] ?? null,
                        'barcode' => $userData['barcode'] ?? '',
                        'user_type' => $userData['user_type'] ?? 'student',
                        'account_status' => $userData['account_status'] ?? 'active',
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $skipped++;
                    $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Successfully imported {$imported} records.";
            if ($skipped > 0) {
                $message .= " Skipped {$skipped} records.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'imported' => $imported,
                'skipped' => $skipped,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }
    public function updateAllExpirationDates(Request $request)
    {
        $validated = $request->validate([
            'expiration_date' => 'required|date',
        ]);

        try {
            $count = User::query()->update([
                'expiration_date' => $validated['expiration_date']
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully updated expiration date for all {$count} users."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update expiration dates: ' . $e->getMessage()
            ], 500);
        }
    }
}
