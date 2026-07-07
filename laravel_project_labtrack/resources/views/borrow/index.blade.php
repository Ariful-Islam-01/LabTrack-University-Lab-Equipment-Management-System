@extends('layouts.app')

@section('title', 'Borrow Records')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Borrow Records</h1>
    </div>

    <!-- Filters Section -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('borrows.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-6">
                        <label for="search" class="form-label fw-semibold text-secondary">Search</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" 
                                   id="search"
                                   name="search" 
                                   class="form-control border-start-0 ps-0" 
                                   placeholder="{{ session('role') === 'LAB_ASSISTANT' ? 'Search borrow ID, student, or equipment...' : 'Search borrow ID or equipment...' }}" 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="status" class="form-label fw-semibold text-secondary">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="">All</option>
                            <option value="BORROWED" {{ request('status') == 'BORROWED' ? 'selected' : '' }}>Borrowed</option>
                            <option value="RETURNED" {{ request('status') == 'RETURNED' ? 'selected' : '' }}>Returned</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-search"></i> Search
                        </button>
                        <a href="{{ route('borrows.index') }}" class="btn btn-outline-secondary flex-grow-1 text-nowrap">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($borrows->isEmpty())
        <div class="alert alert-secondary text-center py-4" role="alert">
            No borrow records found.
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
                                @if (session('role') === 'LAB_ASSISTANT')
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                @endif
                                <th>Equipment Name</th>
                                <th>Category</th>
                                <th class="text-center">Quantity</th>
                                <th>Borrow Date</th>
                                <th>Expected Return Date</th>
                                <th>Actual Return Date</th>
                                <th class="text-center">Borrow Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($borrows as $borrow)
                                <tr>
                                    <td>{{ $borrow->borrow_id }}</td>
                                    @if (session('role') === 'LAB_ASSISTANT')
                                        <td>{{ $borrow->student_id }}</td>
                                        <td>{{ $borrow->student_name }}</td>
                                    @endif
                                    <td>{{ $borrow->equipment_name }}</td>
                                    <td>{{ $borrow->category_name }}</td>
                                    <td class="text-center">{{ $borrow->quantity }}</td>
                                    <td>{{ \Carbon\Carbon::parse($borrow->borrow_date)->format('d M Y h:i A') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($borrow->expected_return_date)->format('d M Y h:i A') }}</td>
                                    <td>
                                        @if ($borrow->actual_return_date)
                                            {{ \Carbon\Carbon::parse($borrow->actual_return_date)->format('d M Y h:i A') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $statusUpper = strtoupper($borrow->borrow_status);
                                            $badgeColor = match ($statusUpper) {
                                                'BORROWED' => 'bg-warning text-dark',
                                                'RETURNED' => 'bg-success text-white',
                                                'OVERDUE'  => 'bg-danger text-white',
                                                default    => 'bg-secondary text-white',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeColor }} fw-semibold">{{ $statusUpper }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if (strtoupper($borrow->borrow_status) === 'BORROWED' || strtoupper($borrow->borrow_status) === 'OVERDUE')
                                            @if (session('role') === 'LAB_ASSISTANT')
                                                <form action="{{ route('borrows.return', $borrow->borrow_id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to return this equipment?')">
                                                        Return
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" class="btn btn-secondary btn-sm" disabled>Return</button>
                                            @endif
                                        @endif
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
