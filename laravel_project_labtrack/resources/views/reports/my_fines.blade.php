@extends('layouts.app')

@section('title', 'My Fine History')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">My Fine History</h1>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Reports
        </a>
    </div>

    @if ($fines->isEmpty())
        <div class="alert alert-secondary text-center py-4" role="alert">
            You do not have any fine records.
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
                                    <td>{{ $fine->equipment_name }}</td>
                                    <td class="text-end fw-semibold text-danger">
                                        Tk. {{ number_format($fine->amount, 2) }}
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
            {{ $fines->links() }}
        </div>
    @endif
</div>
@endsection
