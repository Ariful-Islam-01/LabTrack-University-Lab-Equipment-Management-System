@extends('layouts.app')

@section('title', 'My Borrow History')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">My Borrow History</h1>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Reports
        </a>
    </div>

    @if ($borrows->isEmpty())
        <div class="alert alert-secondary text-center py-4" role="alert">
            You do not have any borrow records.
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
                                <th>Equipment Name</th>
                                <th>Category</th>
                                <th class="text-center">Quantity</th>
                                <th>Borrow Date</th>
                                <th>Expected Return</th>
                                <th>Actual Return</th>
                                <th class="text-center">Borrow Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($borrows as $borrow)
                                <tr>
                                    <td>{{ $borrow->borrow_id }}</td>
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
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination Section -->
        <div class="d-flex justify-content-center mt-3">
            {{ $borrows->links() }}
        </div>
    @endif
</div>
@endsection
