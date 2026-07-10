@extends('layouts.app')

@section('title', 'Booking Report')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Booking Report</h1>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Reports
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <!-- Total Bookings -->
        <div class="col-12 col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 bg-primary-subtle text-primary p-3 rounded-3 me-3">
                        <i class="bi bi-journal-plus fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold d-block">Total Requests</span>
                        <h3 class="mb-0 fw-bold text-dark">{{ $totalBookings }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Bookings -->
        <div class="col-12 col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 bg-warning-subtle text-warning p-3 rounded-3 me-3">
                        <i class="bi bi-clock-history fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold d-block">Pending Requests</span>
                        <h3 class="mb-0 fw-bold text-dark">{{ $pendingBookings }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approved Bookings -->
        <div class="col-12 col-sm-6 col-xl-3 mb-3 mb-sm-0">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 bg-success-subtle text-success p-3 rounded-3 me-3">
                        <i class="bi bi-check2-circle fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold d-block">Approved Requests</span>
                        <h3 class="mb-0 fw-bold text-dark">{{ $approvedBookings }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rejected Bookings -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 bg-danger-subtle text-danger p-3 rounded-3 me-3">
                        <i class="bi bi-x-circle fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold d-block">Rejected Requests</span>
                        <h3 class="mb-0 fw-bold text-dark">{{ $rejectedBookings }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('reports.bookings') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <!-- Search Input -->
                    <div class="col-12 col-md-3">
                        <label for="search" class="form-label fw-semibold text-secondary">Search</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" 
                                   id="search"
                                   name="search" 
                                   class="form-control border-start-0 ps-0" 
                                   placeholder="Search booking ID, student, or equipment..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-12 col-sm-6 col-md-2">
                        <label for="status" class="form-label fw-semibold text-secondary">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>PENDING</option>
                            <option value="APPROVED" {{ request('status') == 'APPROVED' ? 'selected' : '' }}>APPROVED</option>
                            <option value="REJECTED" {{ request('status') == 'REJECTED' ? 'selected' : '' }}>REJECTED</option>
                        </select>
                    </div>

                    <!-- Category Filter -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="category" class="form-label fw-semibold text-secondary">Category</label>
                        <select id="category" name="category" class="form-select">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->category_id }}" 
                                        {{ request('category') == $category->category_id ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort Options -->
                    <div class="col-12 col-sm-6 col-md-2">
                        <label for="sort" class="form-label fw-semibold text-secondary">Sort Request Date</label>
                        <select id="sort" name="sort" class="form-select">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="col-12 col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-search"></i> Search
                        </button>
                        <a href="{{ route('reports.bookings') }}" class="btn btn-outline-secondary flex-grow-1 text-nowrap">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($bookings->isEmpty())
        <div class="alert alert-secondary text-center py-4" role="alert">
            No booking requests found matching the report criteria.
        </div>
    @else
        <!-- Table Section -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Booking ID</th>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Equipment Name</th>
                                <th>Category</th>
                                <th class="text-center">Quantity</th>
                                <th>Request Date</th>
                                <th class="text-center">Status</th>
                                <th>Approved By</th>
                                <th>Approval Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $booking)
                                <tr>
                                    <td>{{ $booking->booking_id }}</td>
                                    <td>{{ $booking->student_id }}</td>
                                    <td>{{ $booking->student_name }}</td>
                                    <td>{{ $booking->equipment_name }}</td>
                                    <td>{{ $booking->category_name }}</td>
                                    <td class="text-center">{{ $booking->quantity }}</td>
                                    <td>
                                        {{ $booking->request_date ? \Carbon\Carbon::parse($booking->request_date)->format('d-M-Y h:i A') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $statusUpper = strtoupper($booking->status);
                                            $badgeColor = match ($statusUpper) {
                                                'APPROVED' => 'bg-success',
                                                'PENDING'  => 'bg-warning text-dark',
                                                'REJECTED' => 'bg-danger',
                                                default    => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeColor }} fw-semibold">{{ $statusUpper }}</span>
                                    </td>
                                    <td>{{ $booking->approved_by ?? '-' }}</td>
                                    <td>
                                        {{ $booking->approval_date ? \Carbon\Carbon::parse($booking->approval_date)->format('d-M-Y h:i A') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination Section -->
        <div class="d-flex justify-content-center mt-3">
            {{ $bookings->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
