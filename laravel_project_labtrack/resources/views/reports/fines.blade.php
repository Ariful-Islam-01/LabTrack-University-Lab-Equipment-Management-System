@extends('layouts.app')

@section('title', 'Fine Report')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Fine Report</h1>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Reports
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="mb-4">
        <!-- Row 1: Count Statistics -->
        <div class="row mb-3">
            <!-- Total Fines -->
            <div class="col-12 col-md-4 mb-3 mb-md-0">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary-subtle text-primary p-3 rounded-3 me-3">
                            <i class="bi bi-currency-dollar fs-3"></i>
                        </div>
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold d-block">Total Fine Records</span>
                            <h3 class="mb-0 fw-bold text-dark">{{ $totalFines }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unpaid Fine Count -->
            <div class="col-12 col-md-4 mb-3 mb-md-0">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning-subtle text-warning p-3 rounded-3 me-3">
                            <i class="bi bi-exclamation-circle fs-3"></i>
                        </div>
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold d-block">Unpaid Fines Count</span>
                            <h3 class="mb-0 fw-bold text-dark">{{ $unpaidFineCount }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paid Fine Count -->
            <div class="col-12 col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success-subtle text-success p-3 rounded-3 me-3">
                            <i class="bi bi-check-circle fs-3"></i>
                        </div>
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold d-block">Paid Fines Count</span>
                            <h3 class="mb-0 fw-bold text-dark">{{ $paidFineCount }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Amount Statistics -->
        <div class="row">
            <!-- Total Unpaid Amount -->
            <div class="col-12 col-md-6 mb-3 mb-md-0">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-shrink-0 bg-danger-subtle text-danger p-3 rounded-3 me-3">
                            <i class="bi bi-cash-stack fs-3"></i>
                        </div>
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold d-block">Total Unpaid Amount</span>
                            <h3 class="mb-0 fw-bold text-dark">Tk. {{ number_format($totalUnpaidAmount, 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Paid Amount -->
            <div class="col-12 col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success-subtle text-success p-3 rounded-3 me-3">
                            <i class="bi bi-wallet2 fs-3"></i>
                        </div>
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold d-block">Total Paid Amount</span>
                            <h3 class="mb-0 fw-bold text-dark">Tk. {{ number_format($totalPaidAmount, 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('reports.fines') }}" method="GET">
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
                                   placeholder="Search fine ID, student, or equipment..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-12 col-sm-6 col-md-2">
                        <label for="status" class="form-label fw-semibold text-secondary">Payment Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="UNPAID" {{ request('status') == 'UNPAID' ? 'selected' : '' }}>UNPAID</option>
                            <option value="PAID" {{ request('status') == 'PAID' ? 'selected' : '' }}>PAID</option>
                        </select>
                    </div>

                    <!-- Fine Reason Filter -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="reason" class="form-label fw-semibold text-secondary">Reason</label>
                        <select id="reason" name="reason" class="form-select">
                            <option value="">All Reasons</option>
                            @foreach ($reasons as $reasonItem)
                                <option value="{{ $reasonItem->reason }}" 
                                        {{ request('reason') == $reasonItem->reason ? 'selected' : '' }}>
                                    {{ $reasonItem->reason }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort Options -->
                    <div class="col-12 col-sm-6 col-md-2">
                        <label for="sort" class="form-label fw-semibold text-secondary">Sort By</label>
                        <select id="sort" name="sort" class="form-select">
                            <option value="return_desc" {{ request('sort') == 'return_desc' ? 'selected' : '' }}>Return Date: Newest</option>
                            <option value="return_asc" {{ request('sort') == 'return_asc' ? 'selected' : '' }}>Return Date: Oldest</option>
                            <option value="amount_desc" {{ request('sort') == 'amount_desc' ? 'selected' : '' }}>Fine Amount: High to Low</option>
                            <option value="amount_asc" {{ request('sort') == 'amount_asc' ? 'selected' : '' }}>Fine Amount: Low to High</option>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="col-12 col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-search"></i> Search
                        </button>
                        <a href="{{ route('reports.fines') }}" class="btn btn-outline-secondary flex-grow-1 text-nowrap">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($fines->isEmpty())
        <div class="alert alert-secondary text-center py-4" role="alert">
            No fine records found matching the report criteria.
        </div>
    @else
        <!-- Table Section -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Fine ID</th>
                                <th>Borrow ID</th>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Equipment Name</th>
                                <th class="text-end">Fine Amount</th>
                                <th>Reason</th>
                                <th class="text-center">Payment Status</th>
                                <th>Borrow Date</th>
                                <th>Actual Return Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($fines as $fine)
                                <tr>
                                    <td>{{ $fine->fine_id }}</td>
                                    <td>{{ $fine->borrow_id }}</td>
                                    <td>{{ $fine->student_id }}</td>
                                    <td>{{ $fine->student_name }}</td>
                                    <td>{{ $fine->equipment_name }}</td>
                                    <td class="text-end fw-semibold text-danger">
                                        Tk. {{ number_format($fine->fine_amount, 2) }}
                                    </td>
                                    <td>{{ $fine->reason ?? '-' }}</td>
                                    <td class="text-center">
                                        @php
                                            $statusUpper = strtoupper($fine->payment_status);
                                            $badgeColor = match ($statusUpper) {
                                                'PAID'   => 'bg-success',
                                                'UNPAID' => 'bg-danger',
                                                default  => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeColor }} fw-semibold">{{ $statusUpper }}</span>
                                    </td>
                                    <td>
                                        {{ $fine->borrow_date ? \Carbon\Carbon::parse($fine->borrow_date)->format('d-M-Y h:i A') : '-' }}
                                    </td>
                                    <td>
                                        {{ $fine->actual_return_date ? \Carbon\Carbon::parse($fine->actual_return_date)->format('d-M-Y h:i A') : '-' }}
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
            {{ $fines->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
