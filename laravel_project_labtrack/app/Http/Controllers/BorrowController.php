<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BorrowController extends Controller
{
    /**
     * Display a listing of borrows.
     *
     * @return void
     */
    public function index()
    {
        $role = session('role');
        $userId = session('user_id');
        $search = request('search');
        $status = request('status');

        $query = DB::table('borrow_records')
            ->join('users', 'borrow_records.user_id', '=', 'users.user_id')
            ->join('equipment', 'borrow_records.equipment_id', '=', 'equipment.equipment_id')
            ->join('categories', 'equipment.category_id', '=', 'categories.category_id')
            ->select(
                'borrow_records.borrow_id',
                'borrow_records.user_id as student_id',
                'users.full_name as student_name',
                'equipment.equipment_name',
                'categories.category_name',
                'borrow_records.quantity',
                'borrow_records.borrow_date',
                'borrow_records.expected_return_date',
                'borrow_records.actual_return_date',
                'borrow_records.borrow_status'
            );

        if ($role === 'STUDENT') {
            $query->where('borrow_records.user_id', $userId);
        }

        if (!empty($status)) {
            $query->where('borrow_records.borrow_status', strtoupper($status));
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($role, $search) {
                if ($role === 'LAB_ASSISTANT') {
                    $q->where('borrow_records.borrow_id', 'like', '%' . $search . '%')
                      ->orWhere('users.full_name', 'like', '%' . $search . '%')
                      ->orWhere('equipment.equipment_name', 'like', '%' . $search . '%');
                } else {
                    $q->where('borrow_records.borrow_id', 'like', '%' . $search . '%')
                      ->orWhere('equipment.equipment_name', 'like', '%' . $search . '%');
                }
            });
        }

        $borrows = $query->orderBy('borrow_records.borrow_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('borrow.index', compact('borrows'));
    }

    /**
     * Issue borrowed equipment from a booking request.
     *
     * @param  string  $booking
     * @return void
     */
    public function issue(string $booking)
    {
        $bookingData = DB::table('booking_requests')
            ->where('booking_id', $booking)
            ->first();

        if (!$bookingData) {
            abort(404);
        }

        try {
            $borrowId = (DB::table('borrow_records')->max('borrow_id') ?? 7000) + 1;

            DB::statement('BEGIN borrow_equipment(:borrow_id, :booking_id, :user_id, :equipment_id, :quantity); END;', [
                'borrow_id'    => $borrowId,
                'booking_id'   => $bookingData->booking_id,
                'user_id'      => $bookingData->user_id,
                'equipment_id' => $bookingData->equipment_id,
                'quantity'     => $bookingData->quantity,
            ]);

            return back()->with('success', 'Equipment issued successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function returnEquipment(string $borrow)
    {
        try {
            DB::statement('BEGIN return_equipment(:borrow_id); END;', [
                'borrow_id' => $borrow,
            ]);

            return back()->with('success', 'Equipment returned successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
