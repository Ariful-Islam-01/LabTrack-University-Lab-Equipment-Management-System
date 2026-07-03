@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header / Welcome Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-12">
            <h1 class="h3 text-dark fw-bold mb-0">Dashboard</h1>
            <p class="text-secondary mt-1 mb-0">
                Welcome, <span class="fw-semibold text-dark">{{ session('full_name') }}</span>
                @php
                    $role = session('role');
                    $badgeColor = match(strtoupper($role)) {
                        'STUDENT' => 'bg-primary',
                        'TEACHER' => 'bg-success',
                        'LAB_ASSISTANT' => 'bg-secondary',
                        default => 'bg-light text-dark'
                    };
                @endphp
                <span class="badge {{ $badgeColor }} ms-2">{{ $role }}</span>
            </p>
        </div>
    </div>

    <!-- Cards Section -->
    <div class="row mb-4">
        <!-- Card 1: Total Equipment -->
        <div class="col-12 col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 bg-primary-subtle text-primary p-3 rounded-3 me-3">
                        <i class="bi bi-cpu fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold d-block">Total Equipment</span>
                        <h3 class="mb-0 fw-bold text-dark">{{ $totalEquipment }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Available Equipment -->
        <div class="col-12 col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 bg-success-subtle text-success p-3 rounded-3 me-3">
                        <i class="bi bi-check-circle fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold d-block">Available Equipment</span>
                        <h3 class="mb-0 fw-bold text-dark">{{ $availableEquipment }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Pending Requests -->
        <div class="col-12 col-sm-6 col-xl-3 mb-3 mb-sm-0">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 bg-warning-subtle text-warning p-3 rounded-3 me-3">
                        <i class="bi bi-clock-history fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold d-block">Pending Requests</span>
                        <h3 class="mb-0 fw-bold text-dark">{{ $pendingRequests }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Active Borrows -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 bg-info-subtle text-info p-3 rounded-3 me-3">
                        <i class="bi bi-box-arrow-right fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold d-block">Active Borrows</span>
                        <h3 class="mb-0 fw-bold text-dark">{{ $borrowedEquipment }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0">Quick Access</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('equipment.index') }}" class="btn btn-outline-primary px-4 py-2">
                            <i class="bi bi-pc-display-horizontal me-2"></i> Equipment
                        </a>
                        <a href="{{ route('bookings.index') }}" class="btn btn-outline-success px-4 py-2">
                            <i class="bi bi-journal-plus me-2"></i> Booking
                        </a>
                        <a href="{{ route('borrows.index') }}" class="btn btn-outline-secondary px-4 py-2">
                            <i class="bi bi-arrow-left-right me-2"></i> Borrow
                        </a>
                        <a href="{{ route('reports.index') }}" class="btn btn-outline-dark px-4 py-2">
                            <i class="bi bi-file-earmark-bar-graph me-2"></i> Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
