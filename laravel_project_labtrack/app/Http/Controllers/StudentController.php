<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    /**
     * Display a paginated list of students with optional search.
     */
    public function index()
    {
        $search = request('search');

        $query = DB::table('users')
            ->where('role', 'STUDENT')
            ->select(
                'user_id',
                'full_name',
                'email',
                'department',
                'role'
            );

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('user_id',   'like', '%' . $search . '%')
                  ->orWhere('full_name', 'like', '%' . $search . '%')
                  ->orWhere('email',     'like', '%' . $search . '%')
                  ->orWhere('department','like', '%' . $search . '%');
            });
        }

        $students = $query
            ->orderBy('full_name', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('students.index', compact('students'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        return view('students.create');
    }

    /**
     * Store a newly created student in the users table.
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
                'role'       => 'STUDENT',
                'department' => $validated['department'],
            ]);

            return redirect()->route('students.index')
                ->with('success', 'Student added successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to add student: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing an existing student.
     */
    public function edit(string $id)
    {
        $student = DB::table('users')
            ->where('user_id', $id)
            ->where('role', 'STUDENT')
            ->first();

        if (!$student) {
            abort(404, 'Student not found.');
        }

        return view('students.edit', compact('student'));
    }

    /**
     * Update the specified student in the users table.
     */
    public function update(Request $request, string $id)
    {
        $student = DB::table('users')
            ->where('user_id', $id)
            ->where('role', 'STUDENT')
            ->first();

        if (!$student) {
            abort(404, 'Student not found.');
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
                ->where('role', 'STUDENT')
                ->update($updateData);

            return redirect()->route('students.index')
                ->with('success', 'Student updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update student: ' . $e->getMessage());
        }
    }

    /**
     * Delete a student after checking for related records.
     */
    public function destroy(string $id)
    {
        $student = DB::table('users')
            ->where('user_id', $id)
            ->where('role', 'STUDENT')
            ->first();

        if (!$student) {
            return back()->with('error', 'Student not found.');
        }

        // Check for related records in booking_requests
        $hasBookings = DB::table('booking_requests')
            ->where('user_id', $id)
            ->exists();

        if ($hasBookings) {
            return back()->with(
                'error',
                'Cannot delete student "' . $student->full_name . '". They have existing booking records.'
            );
        }

        // Check for related records in borrow_records
        $hasBorrows = DB::table('borrow_records')
            ->where('user_id', $id)
            ->exists();

        if ($hasBorrows) {
            return back()->with(
                'error',
                'Cannot delete student "' . $student->full_name . '". They have existing borrow records.'
            );
        }

        // Check for related records in equipment_logs
        $hasLogs = DB::table('equipment_logs')
            ->where('user_id', $id)
            ->exists();

        if ($hasLogs) {
            return back()->with(
                'error',
                'Cannot delete student "' . $student->full_name . '". They have existing equipment log records.'
            );
        }

        try {
            DB::table('users')
                ->where('user_id', $id)
                ->where('role', 'STUDENT')
                ->delete();

            return redirect()->route('students.index')
                ->with('success', 'Student "' . $student->full_name . '" deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete student: ' . $e->getMessage());
        }
    }
}
