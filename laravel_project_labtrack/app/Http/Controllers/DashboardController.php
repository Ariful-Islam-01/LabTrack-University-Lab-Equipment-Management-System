<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard statistics.
     */
    public function index()
    {
        // 1. Total Equipment: Count all rows from equipment table
        $totalEquipment = DB::table('equipment')->count();

        // 2. Available Equipment: Calculate the SUM of available_quantity
        $availableEquipment = DB::table('equipment')->sum('available_quantity') ?? 0;

        // 3. Pending Booking Requests: Count booking_requests where status='PENDING'
        $pendingRequests = DB::table('booking_requests')
            ->where('status', 'PENDING')
            ->count();

        // 4. Currently Borrowed Equipment: Count borrow_records where borrow_status='BORROWED'
        $borrowedEquipment = DB::table('borrow_records')
            ->where('borrow_status', 'BORROWED')
            ->count();

        return view('dashboard.index', compact(
            'totalEquipment',
            'availableEquipment',
            'pendingRequests',
            'borrowedEquipment'
        ));
    }
}
