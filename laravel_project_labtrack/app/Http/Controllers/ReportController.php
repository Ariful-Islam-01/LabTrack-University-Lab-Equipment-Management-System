<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display the reports dashboard with summary statistics.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $role = strtoupper(session('role'));
        $userId = session('user_id');

        if ($role === 'LAB_ASSISTANT') {
            // Lab Assistant Stats
            $totalEquipment = DB::table('equipment')->count();
            $availableEquipment = DB::table('equipment')->sum('available_quantity') ?? 0;
            $outOfStockEquipment = DB::table('equipment')->where('status', 'OUT_OF_STOCK')->count();

            $bookingStats = $this->getBookingStats();

            $totalBorrows = DB::table('borrow_records')->count();
            $currentlyBorrowed = DB::table('borrow_records')->where('borrow_status', 'BORROWED')->count();
            $returnedEquipment = DB::table('borrow_records')->where('borrow_status', 'RETURNED')->count();

            $totalFines = DB::table('fines')->count();
            $totalUnpaidFineAmount = DB::table('fines')->where('payment_status', 'UNPAID')->sum('amount') ?? 0;
            $totalPaidFineAmount = DB::table('fines')->where('payment_status', 'PAID')->sum('amount') ?? 0;

            return view('reports.index', array_merge([
                'role' => $role,
                'totalEquipment' => $totalEquipment,
                'availableEquipment' => $availableEquipment,
                'outOfStockEquipment' => $outOfStockEquipment,
                'totalBorrows' => $totalBorrows,
                'currentlyBorrowed' => $currentlyBorrowed,
                'returnedEquipment' => $returnedEquipment,
                'totalFines' => $totalFines,
                'totalUnpaidFineAmount' => $totalUnpaidFineAmount,
                'totalPaidFineAmount' => $totalPaidFineAmount
            ], $bookingStats));

        } elseif ($role === 'TEACHER') {
            // Teacher Stats (booking related only)
            $bookingStats = $this->getBookingStats();

            return view('reports.index', array_merge([
                'role' => $role
            ], $bookingStats));

        } elseif ($role === 'STUDENT') {
            // Student Stats (personal only)
            $myTotalBookings = DB::table('booking_requests')->where('user_id', $userId)->count();
            $myActiveBorrows = DB::table('borrow_records')
                ->where('user_id', $userId)
                ->whereIn('borrow_status', ['BORROWED', 'OVERDUE'])
                ->count();
            $myReturnedItems = DB::table('borrow_records')
                ->where('user_id', $userId)
                ->where('borrow_status', 'RETURNED')
                ->count();
            $myTotalFines = DB::table('fines')
                ->join('borrow_records', 'fines.borrow_id', '=', 'borrow_records.borrow_id')
                ->where('borrow_records.user_id', $userId)
                ->count();
            $myUnpaidFines = DB::table('fines')
                ->join('borrow_records', 'fines.borrow_id', '=', 'borrow_records.borrow_id')
                ->where('borrow_records.user_id', $userId)
                ->where('fines.payment_status', 'UNPAID')
                ->sum('fines.amount') ?? 0;

            return view('reports.index', compact(
                'role',
                'myTotalBookings',
                'myActiveBorrows',
                'myReturnedItems',
                'myTotalFines',
                'myUnpaidFines'
            ));
        }

        abort(403, 'Unauthorized role.');
    }

    /**
     * Display the equipment report with search, filter, and sorting options.
     *
     * @return \Illuminate\View\View
     */
    public function equipmentReport()
    {
        if (!$this->isAuthorized(['LAB_ASSISTANT'])) {
            return redirect()->route('dashboard')->with('error', 'Access Denied');
        }
        $search = request('search');
        $category = request('category');
        $status = request('status');
        $sort = request('sort', 'newest');

        $query = DB::table('equipment')
            ->join('categories', 'equipment.category_id', '=', 'categories.category_id')
            ->join('labs', 'equipment.lab_id', '=', 'labs.lab_id')
            ->select(
                'equipment.equipment_id',
                'equipment.equipment_name',
                'categories.category_name',
                'labs.lab_name',
                'equipment.total_quantity',
                DB::raw('get_available_stock(equipment.equipment_id) as available_stock'),
                'equipment.status',
                'equipment.purchase_date'
            );

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('equipment.equipment_id', 'like', '%' . $search . '%')
                  ->orWhere('equipment.equipment_name', 'like', '%' . $search . '%');
            });
        }

        if (!empty($category)) {
            $query->where('equipment.category_id', $category);
        }

        if (!empty($status)) {
            $query->where('equipment.status', $status);
        }

        if ($sort === 'oldest') {
            $query->orderBy('equipment.purchase_date', 'asc');
        } else {
            $query->orderBy('equipment.purchase_date', 'desc');
        }

        $equipments = $query->paginate(10)->withQueryString();

        $categories = $this->getCategories();

        return view('reports.equipment', compact('equipments', 'categories'));
    }

    /**
     * Display the booking request report with search, filter, and sorting options.
     *
     * @return \Illuminate\View\View
     */
    public function bookingReport()
    {
        if (!$this->isAuthorized(['LAB_ASSISTANT', 'TEACHER'])) {
            return redirect()->route('dashboard')->with('error', 'Access Denied');
        }
        // 1. Gather global stats for the cards
        $bookingStats = $this->getBookingStats();
        extract($bookingStats);

        // 2. Build filterable query
        $search = request('search');
        $status = request('status');
        $category = request('category');
        $sort = request('sort', 'newest');

        $query = DB::table('booking_requests')
            ->join('users as student', 'booking_requests.user_id', '=', 'student.user_id')
            ->leftJoin('users as teacher', 'booking_requests.approved_by', '=', 'teacher.user_id')
            ->join('equipment', 'booking_requests.equipment_id', '=', 'equipment.equipment_id')
            ->join('categories', 'equipment.category_id', '=', 'categories.category_id')
            ->select(
                'booking_requests.booking_id',
                'booking_requests.user_id as student_id',
                'student.full_name as student_name',
                'equipment.equipment_name',
                'categories.category_name',
                'booking_requests.quantity',
                'booking_requests.request_date',
                'booking_requests.status',
                'teacher.full_name as approved_by',
                'booking_requests.approval_date'
            );

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('booking_requests.booking_id', 'like', '%' . $search . '%')
                  ->orWhere('booking_requests.user_id', 'like', '%' . $search . '%')
                  ->orWhere('student.full_name', 'like', '%' . $search . '%')
                  ->orWhere('equipment.equipment_name', 'like', '%' . $search . '%');
            });
        }

        if (!empty($status)) {
            $query->where('booking_requests.status', strtoupper($status));
        }

        if (!empty($category)) {
            $query->where('equipment.category_id', $category);
        }

        if ($sort === 'oldest') {
            $query->orderBy('booking_requests.request_date', 'asc');
        } else {
            $query->orderBy('booking_requests.request_date', 'desc');
        }

        $bookings = $query->paginate(10)->withQueryString();

        $categories = $this->getCategories();

        return view('reports.bookings', compact(
            'totalBookings',
            'pendingBookings',
            'approvedBookings',
            'rejectedBookings',
            'bookings',
            'categories'
        ));
    }

    /**
     * Display the borrow report with search, filter, and sorting options.
     *
     * @return \Illuminate\View\View
     */
    public function borrowReport()
    {
        if (!$this->isAuthorized(['LAB_ASSISTANT'])) {
            return redirect()->route('dashboard')->with('error', 'Access Denied');
        }
        // 1. Gather global stats for the cards
        $totalBorrows = DB::table('borrow_records')->count();
        $currentlyBorrowed = DB::table('borrow_records')->where('borrow_status', 'BORROWED')->count();
        $returnedEquipment = DB::table('borrow_records')->where('borrow_status', 'RETURNED')->count();

        // 2. Build filterable query
        $search = request('search');
        $status = request('status');
        $category = request('category');
        $sort = request('sort', 'newest');

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
                'borrow_records.borrow_status',
                DB::raw('get_borrow_count(borrow_records.user_id) as total_borrow_count')
            );

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('borrow_records.borrow_id', 'like', '%' . $search . '%')
                  ->orWhere('borrow_records.user_id', 'like', '%' . $search . '%')
                  ->orWhere('users.full_name', 'like', '%' . $search . '%')
                  ->orWhere('equipment.equipment_name', 'like', '%' . $search . '%');
            });
        }

        if (!empty($status)) {
            $query->where('borrow_records.borrow_status', strtoupper($status));
        }

        if (!empty($category)) {
            $query->where('equipment.category_id', $category);
        }

        if ($sort === 'oldest') {
            $query->orderBy('borrow_records.borrow_date', 'asc');
        } else {
            $query->orderBy('borrow_records.borrow_date', 'desc');
        }

        $borrows = $query->paginate(10)->withQueryString();

        $categories = $this->getCategories();

        return view('reports.borrows', compact(
            'totalBorrows',
            'currentlyBorrowed',
            'returnedEquipment',
            'borrows',
            'categories'
        ));
    }

    /**
     * Display the fine report with search, filter, and sorting options.
     *
     * @return \Illuminate\View\View
     */
    public function fineReport()
    {
        if (!$this->isAuthorized(['LAB_ASSISTANT'])) {
            return redirect()->route('dashboard')->with('error', 'Access Denied');
        }
        // 1. Gather global stats for the cards
        $totalFines = DB::table('fines')->count();
        $totalPaidAmount = DB::table('fines')->where('payment_status', 'PAID')->sum('amount') ?? 0;
        $totalUnpaidAmount = DB::table('fines')->where('payment_status', 'UNPAID')->sum('amount') ?? 0;
        $paidFineCount = DB::table('fines')->where('payment_status', 'PAID')->count();
        $unpaidFineCount = DB::table('fines')->where('payment_status', 'UNPAID')->count();

        // 2. Build filterable query
        $search = request('search');
        $status = request('status');
        $reason = request('reason');
        $sort = request('sort', 'return_desc');

        $query = DB::table('fines')
            ->join('borrow_records', 'fines.borrow_id', '=', 'borrow_records.borrow_id')
            ->join('users', 'borrow_records.user_id', '=', 'users.user_id')
            ->join('equipment', 'borrow_records.equipment_id', '=', 'equipment.equipment_id')
            ->select(
                'fines.fine_id',
                'fines.borrow_id',
                'borrow_records.user_id as student_id',
                'users.full_name as student_name',
                'equipment.equipment_name',
                'fines.amount as fine_amount',
                'fines.reason',
                'fines.payment_status',
                'borrow_records.borrow_date',
                'borrow_records.actual_return_date'
            );

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('fines.fine_id', 'like', '%' . $search . '%')
                  ->orWhere('fines.borrow_id', 'like', '%' . $search . '%')
                  ->orWhere('borrow_records.user_id', 'like', '%' . $search . '%')
                  ->orWhere('users.full_name', 'like', '%' . $search . '%')
                  ->orWhere('equipment.equipment_name', 'like', '%' . $search . '%');
            });
        }

        if (!empty($status)) {
            $query->where('fines.payment_status', strtoupper($status));
        }

        if (!empty($reason)) {
            $query->where('fines.reason', $reason);
        }

        if ($sort === 'amount_desc') {
            $query->orderBy('fines.amount', 'desc');
        } elseif ($sort === 'amount_asc') {
            $query->orderBy('fines.amount', 'asc');
        } elseif ($sort === 'return_asc') {
            $query->orderBy('borrow_records.actual_return_date', 'asc');
        } else {
            $query->orderBy('borrow_records.actual_return_date', 'desc');
        }

        $fines = $query->paginate(10)->withQueryString();

        $reasons = DB::table('fines')
            ->select('reason')
            ->distinct()
            ->whereNotNull('reason')
            ->orderBy('reason', 'asc')
            ->get();

        return view('reports.fines', compact(
            'totalFines',
            'totalPaidAmount',
            'totalUnpaidAmount',
            'paidFineCount',
            'unpaidFineCount',
            'fines',
            'reasons'
        ));
    }

    /**
     * Ensure the vw_borrow_details view exists in the Oracle database.
     */
    private function ensureViewExists()
    {
        try {
            DB::statement("
                CREATE OR REPLACE VIEW vw_borrow_details AS
                SELECT 
                    b.borrow_id,
                    u.full_name AS student_name,
                    e.equipment_name,
                    c.category_name AS category,
                    b.quantity,
                    b.borrow_date,
                    b.expected_return_date,
                    b.actual_return_date,
                    b.borrow_status
                FROM borrow_records b
                JOIN users u ON b.user_id = u.user_id
                JOIN equipment e ON b.equipment_id = e.equipment_id
                JOIN categories c ON e.category_id = c.category_id
            ");
        } catch (\Exception $e) {
            logger()->error('Failed to auto-create vw_borrow_details view: ' . $e->getMessage());
        }
    }

    /**
     * Display the most borrowed equipment report (GROUP BY).
     *
     * @return \Illuminate\View\View
     */
    public function mostBorrowed()
    {
        if (!$this->isAuthorized(['LAB_ASSISTANT'])) {
            return redirect()->route('dashboard')->with('error', 'Access Denied');
        }
        $equipments = DB::table('borrow_records')
            ->join('equipment', 'borrow_records.equipment_id', '=', 'equipment.equipment_id')
            ->join('categories', 'equipment.category_id', '=', 'categories.category_id')
            ->select(
                'equipment.equipment_id',
                'equipment.equipment_name',
                'categories.category_name',
                DB::raw('COUNT(borrow_records.borrow_id) as total_borrowed_times'),
                DB::raw('SUM(borrow_records.quantity) as total_borrowed_quantity')
            )
            ->groupBy('equipment.equipment_id', 'equipment.equipment_name', 'categories.category_name')
            ->orderBy('total_borrowed_times', 'desc')
            ->paginate(10);

        return view('reports.most_borrowed', compact('equipments'));
    }

    /**
     * Display the top borrowers report (Subquery).
     *
     * @return \Illuminate\View\View
     */
    public function topBorrowers()
    {
        if (!$this->isAuthorized(['LAB_ASSISTANT'])) {
            return redirect()->route('dashboard')->with('error', 'Access Denied');
        }
        // Select students and subquery total borrow records count/quantity
        $students = DB::table('users')
            ->where('role', 'STUDENT')
            ->select(
                'users.user_id',
                'users.full_name',
                'users.email',
                DB::raw('(SELECT COUNT(*) FROM borrow_records WHERE borrow_records.user_id = users.user_id) as total_borrows'),
                DB::raw('(SELECT SUM(quantity) FROM borrow_records WHERE borrow_records.user_id = users.user_id) as total_quantity')
            )
            ->whereRaw('(SELECT COUNT(*) FROM borrow_records WHERE borrow_records.user_id = users.user_id) > 0')
            ->orderBy('total_borrows', 'desc')
            ->paginate(10);

        return view('reports.top_borrowers', compact('students'));
    }

    /**
     * Display the equipment borrow count by category (VIEW).
     *
     * @return \Illuminate\View\View
     */
    public function categoryReport()
    {
        if (!$this->isAuthorized(['LAB_ASSISTANT'])) {
            return redirect()->route('dashboard')->with('error', 'Access Denied');
        }
        // 1. Ensure view exists in the DB
        $this->ensureViewExists();

        // 2. Query category borrow metrics from the SQL VIEW vw_borrow_details
        $categoriesReport = DB::table('vw_borrow_details')
            ->select(
                'category',
                'equipment_name',
                DB::raw('COUNT(borrow_id) as total_borrows'),
                DB::raw('SUM(quantity) as total_quantity_borrowed')
            )
            ->groupBy('category', 'equipment_name')
            ->orderBy('category', 'asc')
            ->orderBy('total_borrows', 'desc')
            ->paginate(10);

        return view('reports.category', compact('categoriesReport'));
    }

    /**
     * Display recent unified activities (UNION).
     *
     * @return \Illuminate\View\View
     */
    public function recentActivities()
    {
        if (!$this->isAuthorized(['LAB_ASSISTANT'])) {
            return redirect()->route('dashboard')->with('error', 'Access Denied');
        }
        // Query approved bookings
        $bookingsQuery = DB::table('booking_requests')
            ->join('users', 'booking_requests.user_id', '=', 'users.user_id')
            ->join('equipment', 'booking_requests.equipment_id', '=', 'equipment.equipment_id')
            ->where('booking_requests.status', 'APPROVED')
            ->select(
                'booking_requests.booking_id as activity_id',
                DB::raw("'Booking Approved' as activity_type"),
                'users.full_name as student_name',
                'equipment.equipment_name',
                'booking_requests.quantity',
                'booking_requests.approval_date as activity_date'
            );

        // Query borrow activities
        $borrowsQuery = DB::table('borrow_records')
            ->join('users', 'borrow_records.user_id', '=', 'users.user_id')
            ->join('equipment', 'borrow_records.equipment_id', '=', 'equipment.equipment_id')
            ->select(
                'borrow_records.borrow_id as activity_id',
                DB::raw("CASE WHEN borrow_records.borrow_status = 'RETURNED' THEN 'Equipment Returned' ELSE 'Equipment Borrowed' END as activity_type"),
                'users.full_name as student_name',
                'equipment.equipment_name',
                'borrow_records.quantity',
                'borrow_records.borrow_date as activity_date'
            );

        // Perform union, order by activity_date desc, and paginate
        // Wrapping in a subquery is the safest way to execute union queries across multiple drivers
        $activities = DB::table(DB::raw("({$bookingsQuery->toSql()} UNION ALL {$borrowsQuery->toSql()}) temp"))
            ->mergeBindings($bookingsQuery)
            ->mergeBindings($borrowsQuery)
            ->orderBy('activity_date', 'desc')
            ->paginate(10);

        return view('reports.recent_activities', compact('activities'));
    }

    /**
     * Display the student's personal borrow history report.
     *
     * @return \Illuminate\View\View
     */
    public function myBorrows()
    {
        if (!$this->isAuthorized(['STUDENT'])) {
            return redirect()->route('dashboard')->with('error', 'Access Denied');
        }

        $userId = session('user_id');

        $borrows = DB::table('borrow_records')
            ->join('equipment', 'borrow_records.equipment_id', '=', 'equipment.equipment_id')
            ->join('categories', 'equipment.category_id', '=', 'categories.category_id')
            ->where('borrow_records.user_id', $userId)
            ->select(
                'borrow_records.borrow_id',
                'equipment.equipment_name',
                'categories.category_name',
                'borrow_records.quantity',
                'borrow_records.borrow_date',
                'borrow_records.expected_return_date',
                'borrow_records.actual_return_date',
                'borrow_records.borrow_status'
            )
            ->orderBy('borrow_records.borrow_date', 'desc')
            ->paginate(10);

        return view('reports.my_borrows', compact('borrows'));
    }

    /**
     * Display the student's personal fine history report.
     *
     * @return \Illuminate\View\View
     */
    public function myFines()
    {
        if (!$this->isAuthorized(['STUDENT'])) {
            return redirect()->route('dashboard')->with('error', 'Access Denied');
        }

        $userId = session('user_id');

        $fines = DB::table('fines')
            ->join('borrow_records', 'fines.borrow_id', '=', 'borrow_records.borrow_id')
            ->join('equipment', 'borrow_records.equipment_id', '=', 'equipment.equipment_id')
            ->where('borrow_records.user_id', $userId)
            ->select(
                'fines.fine_id',
                'fines.borrow_id',
                'equipment.equipment_name',
                'fines.amount',
                'fines.reason',
                'fines.payment_status',
                'borrow_records.borrow_date',
                'borrow_records.actual_return_date'
            )
            ->orderBy('borrow_records.actual_return_date', 'desc')
            ->paginate(10);

        return view('reports.my_fines', compact('fines'));
    }

    /**
     * Check if the logged-in user has one of the allowed roles.
     *
     * @param array $allowedRoles
     * @return bool
     */
    private function isAuthorized(array $allowedRoles)
    {
        return in_array(strtoupper(session('role')), $allowedRoles);
    }

    /**
     * Fetch global booking request statistics.
     *
     * @return array
     */
    private function getBookingStats()
    {
        return [
            'totalBookings' => DB::table('booking_requests')->count(),
            'pendingBookings' => DB::table('booking_requests')->where('status', 'PENDING')->count(),
            'approvedBookings' => DB::table('booking_requests')->where('status', 'APPROVED')->count(),
            'rejectedBookings' => DB::table('booking_requests')->where('status', 'REJECTED')->count(),
        ];
    }

    /**
     * Get list of equipment categories sorted alphabetically.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getCategories()
    {
        return DB::table('categories')
            ->orderBy('category_name', 'asc')
            ->get();
    }
}
