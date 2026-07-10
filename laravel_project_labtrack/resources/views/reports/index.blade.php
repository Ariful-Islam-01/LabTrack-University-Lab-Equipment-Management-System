@extends('layouts.app')

@section('title', 'Reports Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            @if ($role === 'STUDENT')
                My Reports & Statistics
            @else
                Reports Dashboard
            @endif
        </h1>
    </div>

    <!-- ========================================================================= -->
    <!-- 1. STUDENT VIEW                                                           -->
    <!-- ========================================================================= -->
    @if ($role === 'STUDENT')
        <!-- Personal Summary Cards -->
        <div class="row mb-5">
            <!-- My Total Bookings -->
            <div class="col-12 col-md-4 col-xl-2 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3 text-center">
                        <div class="bg-primary-subtle text-primary p-3 rounded-circle d-inline-flex mb-3">
                            <i class="bi bi-journal-plus fs-3"></i>
                        </div>
                        <span class="text-muted small text-uppercase fw-semibold d-block">My Bookings</span>
                        <h3 class="mb-0 fw-bold text-dark mt-1">{{ $myTotalBookings }}</h3>
                    </div>
                </div>
            </div>

            <!-- My Active Borrows -->
            <div class="col-12 col-md-4 col-xl-2 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3 text-center">
                        <div class="bg-info-subtle text-info p-3 rounded-circle d-inline-flex mb-3">
                            <i class="bi bi-box-arrow-right fs-3"></i>
                        </div>
                        <span class="text-muted small text-uppercase fw-semibold d-block">Active Borrows</span>
                        <h3 class="mb-0 fw-bold text-dark mt-1">{{ $myActiveBorrows }}</h3>
                    </div>
                </div>
            </div>

            <!-- My Returned Items -->
            <div class="col-12 col-md-4 col-xl-2 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3 text-center">
                        <div class="bg-success-subtle text-success p-3 rounded-circle d-inline-flex mb-3">
                            <i class="bi bi-box-arrow-in-left fs-3"></i>
                        </div>
                        <span class="text-muted small text-uppercase fw-semibold d-block">Returned Items</span>
                        <h3 class="mb-0 fw-bold text-dark mt-1">{{ $myReturnedItems }}</h3>
                    </div>
                </div>
            </div>

            <!-- My Total Fines -->
            <div class="col-12 col-md-6 col-xl-3 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3 text-center">
                        <div class="bg-warning-subtle text-warning p-3 rounded-circle d-inline-flex mb-3">
                            <i class="bi bi-currency-dollar fs-3"></i>
                        </div>
                        <span class="text-muted small text-uppercase fw-semibold d-block">My Total Fines</span>
                        <h3 class="mb-0 fw-bold text-dark mt-1">{{ $myTotalFines }}</h3>
                    </div>
                </div>
            </div>

            <!-- My Unpaid Fines -->
            <div class="col-12 col-md-6 col-xl-3 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3 text-center">
                        <div class="bg-danger-subtle text-danger p-3 rounded-circle d-inline-flex mb-3">
                            <i class="bi bi-cash-stack fs-3"></i>
                        </div>
                        <span class="text-muted small text-uppercase fw-semibold d-block">My Unpaid Fines</span>
                        <h3 class="mb-0 fw-bold text-dark mt-1">Tk. {{ number_format($myUnpaidFines, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Reports Section (Student-Specific) -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold text-dark mb-0">My History & Records</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('reports.my_borrows') }}" class="btn btn-outline-primary px-4 py-2">
                        <i class="bi bi-clock-history me-2"></i> My Borrow History
                    </a>
                    <a href="{{ route('reports.my_fines') }}" class="btn btn-outline-danger px-4 py-2">
                        <i class="bi bi-currency-dollar me-2"></i> My Fine History
                    </a>
                </div>
            </div>
        </div>

    <!-- ========================================================================= -->
    <!-- 2. TEACHER VIEW                                                           -->
    <!-- ========================================================================= -->
    @elseif ($role === 'TEACHER')
        <!-- Booking Summary Section -->
        <div class="mb-5">
            <h5 class="fw-bold text-secondary mb-3 text-uppercase small tracking-wider">Booking Summary</h5>
            <div class="row">
                <!-- Total Bookings -->
                <div class="col-12 col-sm-6 col-md-3 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex-shrink-0 bg-primary-subtle text-primary p-3 rounded-3 me-3">
                                <i class="bi bi-journal-plus fs-3"></i>
                            </div>
                            <div>
                                <span class="text-muted small text-uppercase fw-semibold d-block">Total Bookings</span>
                                <h3 class="mb-0 fw-bold text-dark">{{ $totalBookings }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Bookings -->
                <div class="col-12 col-sm-6 col-md-3 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex-shrink-0 bg-warning-subtle text-warning p-3 rounded-3 me-3">
                                <i class="bi bi-clock-history fs-3"></i>
                            </div>
                            <div>
                                <span class="text-muted small text-uppercase fw-semibold d-block">Pending Bookings</span>
                                <h3 class="mb-0 fw-bold text-dark">{{ $pendingBookings }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Approved Bookings -->
                <div class="col-12 col-sm-6 col-md-3 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex-shrink-0 bg-success-subtle text-success p-3 rounded-3 me-3">
                                <i class="bi bi-check2-circle fs-3"></i>
                            </div>
                            <div>
                                <span class="text-muted small text-uppercase fw-semibold d-block">Approved Bookings</span>
                                <h3 class="mb-0 fw-bold text-dark">{{ $approvedBookings }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rejected Bookings -->
                <div class="col-12 col-sm-6 col-md-3 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex-shrink-0 bg-danger-subtle text-danger p-3 rounded-3 me-3">
                                <i class="bi bi-x-circle fs-3"></i>
                            </div>
                            <div>
                                <span class="text-muted small text-uppercase fw-semibold d-block">Rejected Bookings</span>
                                <h3 class="mb-0 fw-bold text-dark">{{ $rejectedBookings }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Reports Section (Teacher-Specific) -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold text-dark mb-0">Detailed Reports</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('reports.bookings') }}" class="btn btn-outline-success px-4 py-2">
                        <i class="bi bi-journal-plus me-2"></i> Booking Report
                    </a>
                </div>
            </div>
        </div>

    <!-- ========================================================================= -->
    <!-- 3. LAB_ASSISTANT VIEW                                                     -->
    <!-- ========================================================================= -->
    @elseif ($role === 'LAB_ASSISTANT')
        <!-- Equipment Summary Section -->
        <div class="mb-5">
            <h5 class="fw-bold text-secondary mb-3 text-uppercase small tracking-wider">Equipment Summary</h5>
            <div class="row">
                <!-- Total Equipment -->
                <div class="col-12 col-md-4 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex-shrink-0 bg-primary-subtle text-primary p-3 rounded-3 me-3">
                                <i class="bi bi-cpu fs-3"></i>
                            </div>
                            <div>
                                <span class="text-muted small text-uppercase fw-semibold d-block">Total Equipment Types</span>
                                <h3 class="mb-0 fw-bold text-dark">{{ $totalEquipment }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Available Equipment -->
                <div class="col-12 col-md-4 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex-shrink-0 bg-success-subtle text-success p-3 rounded-3 me-3">
                                <i class="bi bi-check-circle fs-3"></i>
                            </div>
                            <div>
                                <span class="text-muted small text-uppercase fw-semibold d-block">Available Units</span>
                                <h3 class="mb-0 fw-bold text-dark">{{ $availableEquipment }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Out of Stock Equipment -->
                <div class="col-12 col-md-4 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex-shrink-0 bg-danger-subtle text-danger p-3 rounded-3 me-3">
                                <i class="bi bi-exclamation-triangle fs-3"></i>
                            </div>
                            <div>
                                <span class="text-muted small text-uppercase fw-semibold d-block">Out of Stock Items</span>
                                <h3 class="mb-0 fw-bold text-dark">{{ $outOfStockEquipment }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Summary Section -->
        <div class="mb-5">
            <h5 class="fw-bold text-secondary mb-3 text-uppercase small tracking-wider">Booking Summary</h5>
            <div class="row">
                <!-- Total Bookings -->
                <div class="col-12 col-sm-6 col-md-3 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex-shrink-0 bg-primary-subtle text-primary p-3 rounded-3 me-3">
                                <i class="bi bi-journal-plus fs-3"></i>
                            </div>
                            <div>
                                <span class="text-muted small text-uppercase fw-semibold d-block">Total Bookings</span>
                                <h3 class="mb-0 fw-bold text-dark">{{ $totalBookings }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Bookings -->
                <div class="col-12 col-sm-6 col-md-3 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex-shrink-0 bg-warning-subtle text-warning p-3 rounded-3 me-3">
                                <i class="bi bi-clock-history fs-3"></i>
                            </div>
                            <div>
                                <span class="text-muted small text-uppercase fw-semibold d-block">Pending Bookings</span>
                                <h3 class="mb-0 fw-bold text-dark">{{ $pendingBookings }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Approved Bookings -->
                <div class="col-12 col-sm-6 col-md-3 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex-shrink-0 bg-success-subtle text-success p-3 rounded-3 me-3">
                                <i class="bi bi-check2-circle fs-3"></i>
                            </div>
                            <div>
                                <span class="text-muted small text-uppercase fw-semibold d-block">Approved Bookings</span>
                                <h3 class="mb-0 fw-bold text-dark">{{ $approvedBookings }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rejected Bookings -->
                <div class="col-12 col-sm-6 col-md-3 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex-shrink-0 bg-danger-subtle text-danger p-3 rounded-3 me-3">
                                <i class="bi bi-x-circle fs-3"></i>
                            </div>
                            <div>
                                <span class="text-muted small text-uppercase fw-semibold d-block">Rejected Bookings</span>
                                <h3 class="mb-0 fw-bold text-dark">{{ $rejectedBookings }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Borrow Summary Section -->
        <div class="mb-5">
            <h5 class="fw-bold text-secondary mb-3 text-uppercase small tracking-wider">Borrow Summary</h5>
            <div class="row">
                <!-- Total Borrows -->
                <div class="col-12 col-md-4 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex-shrink-0 bg-primary-subtle text-primary p-3 rounded-3 me-3">
                                <i class="bi bi-arrow-left-right fs-3"></i>
                            </div>
                            <div>
                                <span class="text-muted small text-uppercase fw-semibold d-block">Total Borrows</span>
                                <h3 class="mb-0 fw-bold text-dark">{{ $totalBorrows }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Currently Borrowed -->
                <div class="col-12 col-md-4 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex-shrink-0 bg-info-subtle text-info p-3 rounded-3 me-3">
                                <i class="bi bi-box-arrow-right fs-3"></i>
                            </div>
                            <div>
                                <span class="text-muted small text-uppercase fw-semibold d-block">Currently Borrowed</span>
                                <h3 class="mb-0 fw-bold text-dark">{{ $currentlyBorrowed }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Returned Equipment -->
                <div class="col-12 col-md-4 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex-shrink-0 bg-success-subtle text-success p-3 rounded-3 me-3">
                                <i class="bi bi-box-arrow-in-left fs-3"></i>
                            </div>
                            <div>
                                <span class="text-muted small text-uppercase fw-semibold d-block">Returned Equipment</span>
                                <h3 class="mb-0 fw-bold text-dark">{{ $returnedEquipment }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fine Summary Section -->
        <div class="mb-5">
            <h5 class="fw-bold text-secondary mb-3 text-uppercase small tracking-wider">Fine Summary</h5>
            <div class="row">
                <!-- Total Fines -->
                <div class="col-12 col-md-4 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex-shrink-0 bg-primary-subtle text-primary p-3 rounded-3 me-3">
                                <i class="bi bi-currency-dollar fs-3"></i>
                            </div>
                            <div>
                                <span class="text-muted small text-uppercase fw-semibold d-block">Total Fines</span>
                                <h3 class="mb-0 fw-bold text-dark">{{ $totalFines }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Unpaid Amount -->
                <div class="col-12 col-md-4 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex-shrink-0 bg-danger-subtle text-danger p-3 rounded-3 me-3">
                                <i class="bi bi-cash-stack fs-3"></i>
                            </div>
                            <div>
                                <span class="text-muted small text-uppercase fw-semibold d-block">Total Unpaid Amount</span>
                                <h3 class="mb-0 fw-bold text-dark">Tk. {{ number_format($totalUnpaidFineAmount, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Paid Amount -->
                <div class="col-12 col-md-4 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex-shrink-0 bg-success-subtle text-success p-3 rounded-3 me-3">
                                <i class="bi bi-wallet2 fs-3"></i>
                            </div>
                            <div>
                                <span class="text-muted small text-uppercase fw-semibold d-block">Total Paid Amount</span>
                                <h3 class="mb-0 fw-bold text-dark">Tk. {{ number_format($totalPaidFineAmount, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Reports Section -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold text-dark mb-0">Detailed Reports</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('reports.equipment') }}" class="btn btn-outline-primary px-4 py-2">
                        <i class="bi bi-cpu me-2"></i> Equipment Report
                    </a>
                    <a href="{{ route('reports.bookings') }}" class="btn btn-outline-success px-4 py-2">
                        <i class="bi bi-journal-plus me-2"></i> Booking Report
                    </a>
                    <a href="{{ route('reports.borrows') }}" class="btn btn-outline-secondary px-4 py-2">
                        <i class="bi bi-arrow-left-right me-2"></i> Borrow Report
                    </a>
                    <a href="{{ route('reports.fines') }}" class="btn btn-outline-danger px-4 py-2">
                        <i class="bi bi-currency-dollar me-2"></i> Fine Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Advanced Database Reports Section -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold text-dark mb-0">Advanced Database Reports</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('reports.most_borrowed') }}" class="btn btn-outline-dark px-4 py-2">
                        <i class="bi bi-bar-chart-steps me-2"></i> Most Borrowed Equipment
                    </a>
                    <a href="{{ route('reports.top_borrowers') }}" class="btn btn-outline-dark px-4 py-2">
                        <i class="bi bi-people me-2"></i> Top Borrowers
                    </a>
                    <a href="{{ route('reports.category') }}" class="btn btn-outline-dark px-4 py-2">
                        <i class="bi bi-tags me-2"></i> Equipment By Category
                    </a>
                    <a href="{{ route('reports.recent_activities') }}" class="btn btn-outline-dark px-4 py-2">
                        <i class="bi bi-activity me-2"></i> Recent Activities
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
