@extends('layouts.app')

@section('title', 'Booking Requests')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Booking Requests</h1>
    </div>

    <!-- Filters Section -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('bookings.index') }}" method="GET">
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
                                   placeholder="Search equipment or category..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="status" class="form-label fw-semibold text-secondary">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="">All</option>
                            <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>Pending</option>
                            <option value="APPROVED" {{ request('status') == 'APPROVED' ? 'selected' : '' }}>Approved</option>
                            <option value="REJECTED" {{ request('status') == 'REJECTED' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-search"></i> Search
                        </button>
                        <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary flex-grow-1 text-nowrap">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($bookings->isEmpty())
        <div class="alert alert-secondary text-center py-4" role="alert">
            No booking requests found.
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
                                <th>Equipment</th>
                                <th>Category</th>
                                <th class="text-center">Quantity</th>
                                <th>Request Date</th>
                                <th class="text-center">Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $booking)
                                <tr>
                                    <td>{{ $booking->booking_id }}</td>
                                    <td>{{ $booking->equipment_name }}</td>
                                    <td>{{ $booking->category_name }}</td>
                                    <td class="text-center">{{ $booking->quantity }}</td>
                                    <td>{{ \Carbon\Carbon::parse($booking->request_date)->format('d M Y h:i A') }}</td>
                                    <td class="text-center">
                                        @php
                                            $statusUpper = strtoupper($booking->status);
                                            $badgeColor = match ($statusUpper) {
                                                'PENDING' => 'bg-warning text-dark',
                                                'APPROVED' => 'bg-success',
                                                'REJECTED' => 'bg-danger',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeColor }} fw-semibold">{{ $statusUpper }}</span>
                                    </td>
                                    <td>{{ $booking->remarks ?? '-' }}</td>
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
