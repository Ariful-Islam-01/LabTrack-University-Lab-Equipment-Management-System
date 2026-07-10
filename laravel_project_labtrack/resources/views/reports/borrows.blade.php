@extends('layouts.app')

@section('title', 'Borrow Report')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Borrow Report</h1>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Reports
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <!-- Total Borrows -->
        <div class="col-12 col-md-4 mb-3 mb-md-0">
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
        <div class="col-12 col-md-4 mb-3 mb-md-0">
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
        <div class="col-12 col-md-4">
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

    <!-- Filters Section -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('reports.borrows') }}" method="GET">
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
                                   placeholder="Search ID, student, or equipment..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-12 col-sm-6 col-md-2">
                        <label for="status" class="form-label fw-semibold text-secondary">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="BORROWED" {{ request('status') == 'BORROWED' ? 'selected' : '' }}>BORROWED</option>
                            <option value="RETURNED" {{ request('status') == 'RETURNED' ? 'selected' : '' }}>RETURNED</option>
                            <option value="OVERDUE" {{ request('status') == 'OVERDUE' ? 'selected' : '' }}>OVERDUE</option>
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
                        <label for="sort" class="form-label fw-semibold text-secondary">Sort Borrow Date</label>
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
                        <a href="{{ route('reports.borrows') }}" class="btn btn-outline-secondary flex-grow-1 text-nowrap">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($borrows->isEmpty())
        <div class="alert alert-secondary text-center py-4" role="alert">
            No borrow records found matching the report criteria.
        </div>
    @else
        <!-- Table Section -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Borrow ID</th>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Equipment Name</th>
                                <th>Category</th>
                                <th class="text-center">Quantity</th>
                                <th>Borrow Date</th>
                                <th>Expected Return</th>
                                <th>Actual Return</th>
                                <th class="text-center">Borrow Status</th>
                                <th class="text-center">Total Student Borrows</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($borrows as $borrow)
                                <tr>
                                    <td>{{ $borrow->borrow_id }}</td>
                                    <td>{{ $borrow->student_id }}</td>
                                    <td>{{ $borrow->student_name }}</td>
                                    <td>{{ $borrow->equipment_name }}</td>
                                    <td>{{ $borrow->category_name }}</td>
                                    <td class="text-center">{{ $borrow->quantity }}</td>
                                    <td>
                                        {{ $borrow->borrow_date ? \Carbon\Carbon::parse($borrow->borrow_date)->format('d-M-Y h:i A') : '-' }}
                                    </td>
                                    <td>
                                        {{ $borrow->expected_return_date ? \Carbon\Carbon::parse($borrow->expected_return_date)->format('d-M-Y h:i A') : '-' }}
                                    </td>
                                    <td>
                                        {{ $borrow->actual_return_date ? \Carbon\Carbon::parse($borrow->actual_return_date)->format('d-M-Y h:i A') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $statusUpper = strtoupper($borrow->borrow_status);
                                            $badgeColor = match ($statusUpper) {
                                                'RETURNED' => 'bg-success',
                                                'BORROWED' => 'bg-warning text-dark',
                                                'OVERDUE'  => 'bg-danger',
                                                default    => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeColor }} fw-semibold">{{ $statusUpper }}</span>
                                    </td>
                                    <td class="text-center fw-semibold text-primary">
                                        {{ $borrow->total_borrow_count ?? 0 }}
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
            {{ $borrows->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
