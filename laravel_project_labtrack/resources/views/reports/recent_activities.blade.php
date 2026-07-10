@extends('layouts.app')

@section('title', 'Recent Activities')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Recent Activities</h1>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Reports
        </a>
    </div>

    @if ($activities->isEmpty())
        <div class="alert alert-secondary text-center py-4" role="alert">
            No recent activities recorded.
        </div>
    @else
        <!-- Table Section -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date & Time</th>
                                <th>Activity Type</th>
                                <th>Activity/Record ID</th>
                                <th>Student Name</th>
                                <th>Equipment Name</th>
                                <th class="text-center">Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activities as $activity)
                                <tr>
                                    <td>
                                        {{ $activity->activity_date ? \Carbon\Carbon::parse($activity->activity_date)->format('d-M-Y h:i A') : '-' }}
                                    </td>
                                    <td>
                                        @php
                                            $type = $activity->activity_type;
                                            $badgeColor = match ($type) {
                                                'Booking Approved'   => 'bg-success',
                                                'Equipment Borrowed' => 'bg-primary',
                                                'Equipment Returned' => 'bg-info text-dark',
                                                default              => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeColor }} fw-semibold">{{ $type }}</span>
                                    </td>
                                    <td>{{ $activity->activity_id }}</td>
                                    <td>{{ $activity->student_name }}</td>
                                    <td>{{ $activity->equipment_name }}</td>
                                    <td class="text-center">{{ $activity->quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination Section -->
        <div class="d-flex justify-content-center mt-3">
            {{ $activities->links() }}
        </div>
    @endif
</div>
@endsection
