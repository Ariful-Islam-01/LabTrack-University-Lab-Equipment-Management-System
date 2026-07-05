@extends('layouts.app')

@section('title', 'Request Equipment')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Header Section -->
            <div class="mb-4">
                <h1 class="h3 mb-0 text-gray-800">Request Equipment</h1>
            </div>

            <!-- Form & Info Card -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <!-- Equipment Details -->
                    <h5 class="card-title fw-bold text-secondary mb-3">Equipment Information</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <span class="text-muted d-block small">Equipment ID</span>
                            <strong class="text-dark">{{ $equipment->equipment_id }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block small">Equipment Name</span>
                            <strong class="text-dark">{{ $equipment->equipment_name }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block small">Category</span>
                            <strong class="text-dark">{{ $equipment->category_name }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block small">Lab</span>
                            <strong class="text-dark">{{ $equipment->lab_name }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block small">Available Quantity</span>
                            <strong class="text-dark">{{ $equipment->available_quantity }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block small">Status</span>
                            <span class="badge {{ strtoupper($equipment->status) === 'AVAILABLE' ? 'bg-success' : 'bg-warning text-dark' }} fw-semibold">
                                {{ strtoupper($equipment->status) }}
                            </span>
                        </div>
                    </div>

                    <hr class="my-4 text-muted">

                    <!-- Request Form -->
                    <form action="{{ route('bookings.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="equipment_id" value="{{ $equipment->equipment_id }}">

                        <!-- Quantity -->
                        <div class="mb-4">
                            <label for="quantity" class="form-label fw-semibold">Quantity</label>
                            <input type="number" 
                                   min="1" 
                                   max="{{ $equipment->available_quantity }}"
                                   class="form-control" 
                                   id="quantity" 
                                   name="quantity" 
                                   required>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                Request Equipment
                            </button>
                            <a href="{{ route('equipment.index') }}" class="btn btn-outline-secondary px-4">
                                Back to Equipment
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
