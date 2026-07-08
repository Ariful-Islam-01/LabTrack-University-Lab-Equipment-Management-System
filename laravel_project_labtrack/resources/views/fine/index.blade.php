@extends('layouts.app')

@section('title', 'Fines')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Fine Records</h1>
    </div>
    <!-- Filters Section -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('fines.index') }}" method="GET">
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
                                   placeholder="{{ session('role') === 'LAB_ASSISTANT' ? 'Search fine ID, borrow ID, student, or equipment...' : 'Search fine ID, borrow ID, or equipment...' }}" 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="status" class="form-label fw-semibold text-secondary">Payment Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="">All</option>
                            <option value="UNPAID" {{ request('status') == 'UNPAID' ? 'selected' : '' }}>Unpaid</option>
                            <option value="PAID" {{ request('status') == 'PAID' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-search"></i> Search
                        </button>
                        <a href="{{ route('fines.index') }}" class="btn btn-outline-secondary flex-grow-1 text-nowrap">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($fines->isEmpty())
        <div class="alert alert-secondary text-center py-4" role="alert">
            No fine records found.
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
                                @if (session('role') === 'LAB_ASSISTANT')
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                @endif
                                <th>Equipment Name</th>
                                <th class="text-end">Fine Amount</th>
                                <th>Reason</th>
                                <th class="text-center">Payment Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($fines as $fine)
                                <tr>
                                    <td>{{ $fine->fine_id ?? '-' }}</td>
                                    <td>{{ $fine->borrow_id }}</td>
                                    @if (session('role') === 'LAB_ASSISTANT')
                                        <td>{{ $fine->student_id }}</td>
                                        <td>{{ $fine->student_name }}</td>
                                    @endif
                                    <td>{{ $fine->equipment_name }}</td>
                                    <td class="text-end">
                                        @if ($fine->fine_amount !== null)
                                            Tk. {{ number_format($fine->fine_amount, 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $fine->reason ?? '-' }}</td>
                                    <td class="text-center">
                                        @if ($fine->payment_status)
                                            @php
                                                $statusUpper = strtoupper($fine->payment_status);
                                                $badgeColor = match ($statusUpper) {
                                                    'UNPAID' => 'bg-danger text-white',
                                                    'PAID'   => 'bg-success text-white',
                                                    default  => 'bg-secondary text-white',
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeColor }} fw-semibold">{{ $statusUpper }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($fine->fine_id === null)
                                            @if (strtoupper($fine->borrow_status) === 'RETURNED')
                                                <form action="{{ route('fines.generate', $fine->borrow_id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-warning btn-sm">Generate Fine</button>
                                                </form>
                                            @endif
                                        @elseif (strtoupper($fine->payment_status) === 'UNPAID')
                                            @if (session('role') === 'LAB_ASSISTANT')
                                                <form action="{{ route('fines.markPaid', $fine->fine_id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to mark this fine as paid?')">Mark Paid</button>
                                                </form>
                                            @else
                                                <button type="button" class="btn btn-secondary btn-sm" disabled>Mark Paid</button>
                                            @endif
                                        @elseif (strtoupper($fine->payment_status) === 'PAID')
                                            <span class="badge bg-secondary">Paid</span>
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
            {{ $fines->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
