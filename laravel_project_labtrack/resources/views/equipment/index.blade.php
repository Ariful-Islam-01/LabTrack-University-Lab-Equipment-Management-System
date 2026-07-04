@extends('layouts.app')

@section('title', 'Equipment')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Equipment List</h1>
        @if (session('role') === 'LAB_ASSISTANT')
            <a href="{{ route('equipment.create') }}" class="btn btn-primary">
                Add Equipment
            </a>
        @endif
    </div>

    <!-- Filters Section (UI only) -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <input type="text" class="form-control" placeholder="Search..." readonly>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <select class="form-select" disabled>
                        <option value="">Category</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <select class="form-select" disabled>
                        <option value="">Status</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-grid">
                    <button type="button" class="btn btn-outline-secondary" disabled>Reset</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Equipment ID</th>
                            <th>Equipment Name</th>
                            <th>Category</th>
                            <th>Lab</th>
                            <th>Available Qty</th>
                            <th>Total Qty</th>
                            <th>Status</th>
                            <th>Purchase Date</th>
                            @if (session('role') === 'LAB_ASSISTANT')
                                <th class="text-center">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($equipment as $item)
                            <tr>
                                <td>{{ $item->equipment_id }}</td>
                                <td>{{ $item->equipment_name }}</td>
                                <td>{{ $item->category_name }}</td>
                                <td>{{ $item->lab_name }}</td>
                                <td>{{ $item->available_quantity }}</td>
                                <td>{{ $item->total_quantity }}</td>
                                <td>
                                    @php
                                        $statusUpper = strtoupper($item->status);
                                        $badgeColor = match ($statusUpper) {
                                            'AVAILABLE' => 'bg-success',
                                            'UNDER_MAINTENANCE' => 'bg-warning text-dark',
                                            'OUT_OF_STOCK' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeColor }}">{{ $statusUpper }}</span>
                                </td>
                                <td>{{ $item->purchase_date }}</td>
                                @if (session('role') === 'LAB_ASSISTANT')
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-2">
                                            <a href="{{ route('equipment.edit', $item->equipment_id) }}" class="btn btn-sm btn-outline-primary">
                                                Edit
                                            </a>
                                            <form action="{{ route('equipment.destroy', $item->equipment_id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination Section -->
    <div class="d-flex justify-content-center">
        {{ $equipment->links() }}
    </div>
</div>
@endsection
