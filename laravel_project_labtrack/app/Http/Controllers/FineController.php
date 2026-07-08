<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FineController extends Controller
{
    /**
     * Display a listing of fines.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $role = session('role');
        $userId = session('user_id');
        $search = request('search');
        $status = request('status');

        $query = DB::table('borrow_records')
            ->leftJoin('fines', 'borrow_records.borrow_id', '=', 'fines.borrow_id')
            ->join('users', 'borrow_records.user_id', '=', 'users.user_id')
            ->join('equipment', 'borrow_records.equipment_id', '=', 'equipment.equipment_id')
            ->select(
                'fines.fine_id',
                'borrow_records.borrow_id',
                'borrow_records.user_id as student_id',
                'users.full_name as student_name',
                'equipment.equipment_name',
                'fines.amount as fine_amount',
                'fines.reason',
                'fines.payment_status',
                'borrow_records.borrow_status'
            );

        // Role-based records visibility
        if ($role === 'STUDENT') {
            $query->whereNotNull('fines.fine_id')
                  ->where('borrow_records.user_id', $userId);
        } else {
            $query->where(function ($q) {
                $q->whereNotNull('fines.fine_id')
                  ->orWhere('borrow_records.borrow_status', 'RETURNED');
            });
        }

        // Status Filter
        if (!empty($status)) {
            $query->where('fines.payment_status', strtoupper($status));
        }

        // Search Filter
        if (!empty($search)) {
            $query->where(function ($q) use ($role, $search) {
                if ($role === 'LAB_ASSISTANT') {
                    $q->where('fines.fine_id', 'like', '%' . $search . '%')
                      ->orWhere('borrow_records.borrow_id', 'like', '%' . $search . '%')
                      ->orWhere('borrow_records.user_id', 'like', '%' . $search . '%')
                      ->orWhere('users.full_name', 'like', '%' . $search . '%')
                      ->orWhere('equipment.equipment_name', 'like', '%' . $search . '%');
                } else {
                    $q->where('fines.fine_id', 'like', '%' . $search . '%')
                      ->orWhere('borrow_records.borrow_id', 'like', '%' . $search . '%')
                      ->orWhere('equipment.equipment_name', 'like', '%' . $search . '%');
                }
            });
        }

        $fines = $query->orderBy('fines.fine_id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('fine.index', compact('fines'));
    }

    /**
     * Generate a fine for a borrow record.
     *
     * @param  string  $borrow
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generate(string $borrow)
    {
        try {
            // Verify borrow record exists
            $borrowRecord = DB::table('borrow_records')
                ->where('borrow_id', $borrow)
                ->first();

            if (!$borrowRecord) {
                return back()->with('error', 'Borrow record not found.');
            }

            // Verify borrow_status = RETURNED
            if (strtoupper($borrowRecord->borrow_status) !== 'RETURNED') {
                return back()->with('error', 'Fines can only be generated for returned equipment.');
            }

            // Verify that no fine already exists for this borrow_id
            $fineExists = DB::table('fines')
                ->where('borrow_id', $borrow)
                ->exists();

            if ($fineExists) {
                return back()->with('error', 'A fine has already been generated for this borrow record.');
            }

            // Generate next fine_id safely
            $maxFineId = DB::table('fines')->max('fine_id');
            $fineId = ($maxFineId ?? 8000) + 1;

            // Call the Oracle procedure
            DB::statement('BEGIN generate_fine(:fine_id, :borrow_id); END;', [
                'fine_id'   => $fineId,
                'borrow_id' => $borrow,
            ]);

            return back()->with('success', 'Fine process completed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Mark a fine as paid.
     *
     * @param  string  $fine
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markPaid(string $fine)
    {
        try {
            DB::statement('BEGIN mark_fine_paid(:fine_id); END;', [
                'fine_id' => $fine,
            ]);

            return back()->with('success', 'Fine marked as paid successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
