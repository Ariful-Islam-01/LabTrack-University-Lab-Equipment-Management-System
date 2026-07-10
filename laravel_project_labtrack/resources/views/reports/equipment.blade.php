@extends('layouts.app')

@section('title', 'Equipment Report')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Equipment Report</h1>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Reports
        </a>
    </div>

    <!-- Filters Section -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('reports.equipment') }}" method="GET">
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
                                   placeholder="Search ID or name..." 
                                   value="{{ request('search') }}">
                        </div>
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

                    <!-- Status Filter -->
                    <div class="col-12 col-sm-6 col-md-2">
                        <label for="status" class="form-label fw-semibold text-secondary">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="AVAILABLE" {{ request('status') == 'AVAILABLE' ? 'selected' : '' }}>AVAILABLE</option>
                            <option value="OUT_OF_STOCK" {{ request('status') == 'OUT_OF_STOCK' ? 'selected' : '' }}>OUT_OF_STOCK</option>
                            <option value="UNDER_MAINTENANCE" {{ request('status') == 'UNDER_MAINTENANCE' ? 'selected' : '' }}>UNDER_MAINTENANCE</option>
                        </select>
                    </div>

                    <!-- Sort Options -->
                    <div class="col-12 col-sm-6 col-md-2">
                        <label for="sort" class="form-label fw-semibold text-secondary">Sort Purchase Date</label>
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
                        <a href="{{ route('reports.equipment') }}" class="btn btn-outline-secondary flex-grow-1 text-nowrap">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($equipments->isEmpty())
        <div class="alert alert-secondary text-center py-4" role="alert">
            No equipment found matching the report criteria.
        </div>
    @else
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
                                <th class="text-center">Total Quantity</th>
                                <th class="text-center">Available Stock</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Purchase Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($equipments as $item)
                                <tr>
                                    <td>{{ $item->equipment_id }}</td>
                                    <td>{{ $item->equipment_name }}</td>
                                    <td>{{ $item->category_name }}</td>
                                    <td>{{ $item->lab_name }}</td>
                                    <td class="text-center">{{ $item->total_quantity }}</td>
                                    <td class="text-center fw-semibold text-primary">{{ $item->available_stock ?? 0 }}</td>
                                    <td class="text-center">
                                        @php
                                            $statusUpper = strtoupper($item->status);
                                            $badgeColor = match ($statusUpper) {
                                                'AVAILABLE' => 'bg-success',
                                                'UNDER_MAINTENANCE' => 'bg-warning text-dark',
                                                'OUT_OF_STOCK' => 'bg-danger',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeColor }} fw-semibold">{{ $statusUpper }}</span>
                                    </td>
                                    <td class="text-center">
                                        {{ $item->purchase_date ? \Carbon\Carbon::parse($item->purchase_date)->format('d-M-Y') : '-' }}
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
            {{ $equipments->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
