<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    /**
     * Display a paginated list of teachers with optional search.
     */
    public function index()
    {
        $search = request('search');

        $query = DB::table('users')
            ->where('role', 'TEACHER')
            ->select(
                'user_id',
                'full_name',
                'email',
                'department',
                'role'
            );

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('user_id',    'like', '%' . $search . '%')
                  ->orWhere('full_name',  'like', '%' . $search . '%')
                  ->orWhere('email',      'like', '%' . $search . '%')
                  ->orWhere('department', 'like', '%' . $search . '%');
            });
        }

        $teachers = $query
            ->orderBy('full_name', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('teachers.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new teacher.
     */
    public function create()
    {
        return view('teachers.create');
    }

    /**
     * Store a newly created teacher in the users table.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'    => 'required|string|max:20|unique:users,user_id',
            'full_name'  => 'required|string|max:150',
            'email'      => 'required|email|max:100|unique:users,email',
            'password'   => 'required|string|min:6|max:100',
            'department' => 'required|string|in:CSE,EEE,CE,ME,BBA',
        ]);

        try {
            DB::table('users')->insert([
                'user_id'    => $validated['user_id'],
                'full_name'  => $validated['full_name'],
                'email'      => $validated['email'],
                'password'   => Hash::make($validated['password']),
                'role'       => 'TEACHER',
                'department' => $validated['department'],
            ]);

            return redirect()->route('teachers.index')
                ->with('success', 'Teacher added successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to add teacher: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing an existing teacher.
     */
    public function edit(string $id)
    {
        $teacher = DB::table('users')
            ->where('user_id', $id)
            ->where('role', 'TEACHER')
            ->first();

        if (!$teacher) {
            abort(404, 'Teacher not found.');
        }

        return view('teachers.edit', compact('teacher'));
    }

    /**
     * Update the specified teacher in the users table.
     */
    public function update(Request $request, string $id)
    {
        $teacher = DB::table('users')
            ->where('user_id', $id)
            ->where('role', 'TEACHER')
            ->first();

        if (!$teacher) {
            abort(404, 'Teacher not found.');
        }

        $validated = $request->validate([
            'full_name'  => 'required|string|max:150',
            'email'      => 'required|email|max:100|unique:users,email,' . $id . ',user_id',
            'department' => 'required|string|in:CSE,EEE,CE,ME,BBA',
            'password'   => 'nullable|string|min:6|max:100',
        ]);

        try {
            $updateData = [
                'full_name'  => $validated['full_name'],
                'email'      => $validated['email'],
                'department' => $validated['department'],
            ];

            // Only update password if a new one was provided
            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            DB::table('users')
                ->where('user_id', $id)
                ->where('role', 'TEACHER')
                ->update($updateData);

            return redirect()->route('teachers.index')
                ->with('success', 'Teacher updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update teacher: ' . $e->getMessage());
        }
    }

    /**
     * Delete a teacher after checking for related records.
     */
    public function destroy(string $id)
    {
        $teacher = DB::table('users')
            ->where('user_id', $id)
            ->where('role', 'TEACHER')
            ->first();

        if (!$teacher) {
            return back()->with('error', 'Teacher not found.');
        }

        // Check for related records in booking_requests.approved_by
        $hasApprovedBookings = DB::table('booking_requests')
            ->where('approved_by', $id)
            ->exists();

        if ($hasApprovedBookings) {
            return back()->with(
                'error',
                'Cannot delete teacher "' . $teacher->full_name . '". They have approved booking records.'
            );
        }

        // Check for related records in equipment_logs.user_id
        $hasEquipmentLogs = DB::table('equipment_logs')
            ->where('user_id', $id)
            ->exists();

        if ($hasEquipmentLogs) {
            return back()->with(
                'error',
                'Cannot delete teacher "' . $teacher->full_name . '". They have existing equipment log records.'
            );
        }

        try {
            DB::table('users')
                ->where('user_id', $id)
                ->where('role', 'TEACHER')
                ->delete();

            return redirect()->route('teachers.index')
                ->with('success', 'Teacher "' . $teacher->full_name . '" deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete teacher: ' . $e->getMessage());
        }
    }
}
