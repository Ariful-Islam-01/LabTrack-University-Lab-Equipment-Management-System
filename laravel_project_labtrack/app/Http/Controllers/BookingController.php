<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $userId = session('user_id');
        $role = session('role');
        $search = request('search');
        $status = request('status');

        if ($role === 'TEACHER') {
            $query = DB::table('booking_requests')
                ->join('users', 'booking_requests.user_id', '=', 'users.user_id')
                ->join('equipment', 'booking_requests.equipment_id', '=', 'equipment.equipment_id')
                ->join('categories', 'equipment.category_id', '=', 'categories.category_id')
                ->select(
                    'booking_requests.booking_id',
                    'booking_requests.user_id as student_id',
                    'users.full_name as student_name',
                    'equipment.equipment_name',
                    'categories.category_name',
                    'booking_requests.quantity',
                    'booking_requests.request_date',
                    'booking_requests.status',
                    'booking_requests.remarks'
                );

            if (!empty($status)) {
                $query->where('booking_requests.status', strtoupper($status));
            } else {
                $query->where('booking_requests.status', 'PENDING');
            }
        } else {
            $query = DB::table('booking_requests')
                ->join('equipment', 'booking_requests.equipment_id', '=', 'equipment.equipment_id')
                ->join('categories', 'equipment.category_id', '=', 'categories.category_id')
                ->select(
                    'booking_requests.booking_id',
                    'equipment.equipment_name',
                    'categories.category_name',
                    'booking_requests.quantity',
                    'booking_requests.request_date',
                    'booking_requests.status',
                    'booking_requests.remarks'
                )
                ->where('booking_requests.user_id', $userId);

            if (!empty($status)) {
                $query->where('booking_requests.status', strtoupper($status));
            }
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('equipment.equipment_name', 'like', '%' . $search . '%')
                  ->orWhere('categories.category_name', 'like', '%' . $search . '%');
            });
        }

        $bookings = $query->orderBy('booking_requests.request_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('booking.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new booking.
     *
     * @param  string  $equipment
     * @return \Illuminate\View\View
     */
    public function create(string $equipment)
    {
        $equipmentData = DB::table('equipment')
            ->join('categories', 'equipment.category_id', '=', 'categories.category_id')
            ->join('labs', 'equipment.lab_id', '=', 'labs.lab_id')
            ->select(
                'equipment.equipment_id',
                'equipment.equipment_name',
                'categories.category_name',
                'labs.lab_name',
                'equipment.available_quantity',
                'equipment.status'
            )
            ->where('equipment.equipment_id', $equipment)
            ->first();

        if (!$equipmentData) {
            abort(404);
        }

        $equipment = $equipmentData;

        return view('booking.create', compact('equipment'));
    }

    /**
     * Store a newly created booking in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => 'required',
            'quantity'     => 'required|integer|min:1',
        ]);

        $userId = session('user_id');

        try {
            DB::statement('BEGIN add_booking_request(:user_id, :equipment_id, :quantity); END;', [
                'user_id'      => $userId,
                'equipment_id' => $validated['equipment_id'],
                'quantity'     => $validated['quantity'],
            ]);

            return redirect()->route('bookings.index')
                ->with('success', 'Booking request submitted successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Approve the specified booking.
     *
     * @param  string  $booking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve($booking)
    {
        return $this->updateStatus($booking, 'APPROVED', 'Approved');
    }

    /**
     * Reject the specified booking.
     *
     * @param  string  $booking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject($booking)
    {
        return $this->updateStatus($booking, 'REJECTED', 'Rejected');
    }

    /**
     * Helper to update booking status using Oracle procedure.
     *
     * @param  string  $bookingId
     * @param  string  $status
     * @param  string  $remarks
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function updateStatus($bookingId, $status, $remarks)
    {
        $teacherId = session('user_id');

        try {
            DB::statement('BEGIN update_booking_status(:booking_id, :teacher_id, :status, :remarks); END;', [
                'booking_id' => $bookingId,
                'teacher_id' => $teacherId,
                'status'     => $status,
                'remarks'    => $remarks,
            ]);

            return back()->with('success', "Booking request " . strtolower($status) . " successfully.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
