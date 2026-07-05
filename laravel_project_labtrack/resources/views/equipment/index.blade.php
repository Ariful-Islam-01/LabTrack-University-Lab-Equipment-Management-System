@extends('layouts.app')

@section('title', 'Equipment')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Equipment List</h1>
        @if (session('role') === 'LAB_ASSISTANT')
            <a href="{{ route('equipment.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Add Equipment
            </a>
        @endif
    </div>

    <!-- Filters Section -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('equipment.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-4">
                        <label for="search" class="form-label fw-semibold text-secondary">Search</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" 
                                   id="search"
                                   name="search" 
                                   class="form-control border-start-0 ps-0" 
                                   placeholder="Search equipment by ID or name..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
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
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="status" class="form-label fw-semibold text-secondary">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="AVAILABLE" {{ request('status') == 'AVAILABLE' ? 'selected' : '' }}>AVAILABLE</option>
                            <option value="OUT_OF_STOCK" {{ request('status') == 'OUT_OF_STOCK' ? 'selected' : '' }}>OUT_OF_STOCK</option>
                            <option value="UNDER_MAINTENANCE" {{ request('status') == 'UNDER_MAINTENANCE' ? 'selected' : '' }}>UNDER_MAINTENANCE</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-search"></i> Search
                        </button>
                        <a href="{{ route('equipment.index') }}" class="btn btn-outline-secondary flex-grow-1 text-nowrap">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
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
                            <th class="text-center">Available Qty</th>
                            <th class="text-center">Total Qty</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Purchase Date</th>
                            @if (session('role') === 'LAB_ASSISTANT' || session('role') === 'STUDENT')
                                <th class="text-center">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if($equipment->count())
                            @foreach ($equipment as $item)
                                <tr>
                                    <td>{{ $item->equipment_id }}</td>
                                    <td>{{ $item->equipment_name }}</td>
                                    <td>{{ $item->category_name }}</td>
                                    <td>{{ $item->lab_name }}</td>
                                    <td class="text-center">{{ $item->available_quantity }}</td>
                                    <td class="text-center">{{ $item->total_quantity }}</td>
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
                                    <td class="text-center">{{ \Carbon\Carbon::parse($item->purchase_date)->format('d-M-Y H:i') }}</td>
                                    @if (session('role') === 'LAB_ASSISTANT' || session('role') === 'STUDENT')
                                        <td class="text-center">
                                            @if (session('role') === 'LAB_ASSISTANT')
                                                <div class="d-flex flex-column flex-md-row gap-2 justify-content-md-center align-items-stretch align-items-md-center">
                                                    <a href="{{ route('equipment.edit', $item->equipment_id) }}" class="btn btn-sm btn-outline-primary text-nowrap">
                                                        <i class="bi bi-pencil-square"></i> Edit
                                                    </a>
                                                    <form action="{{ route('equipment.destroy', $item->equipment_id) }}" method="POST" onsubmit="return confirm('Are you sure?');" class="d-grid d-md-block m-0">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger text-nowrap w-100">
                                                            <i class="bi bi-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            @elseif (session('role') === 'STUDENT')
                                                <a href="{{ route('bookings.create', $item->equipment_id) }}" class="btn btn-sm btn-success">
                                                    <i class="bi bi-journal-plus"></i> Request
                                                </a>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="{{ (session('role') === 'LAB_ASSISTANT' || session('role') === 'STUDENT') ? 9 : 8 }}" class="text-center py-5">
                                    <div class="text-muted mb-3">
                                        <i class="bi bi-inbox fs-1"></i>
                                    </div>
                                    <h5 class="fw-semibold text-secondary">No equipment found</h5>
                                    <p class="text-muted mb-0">No equipment matches your search or filter criteria.</p>
                                    @if (request()->filled('search') || request()->filled('category') || request()->filled('status'))
                                        <div class="mt-3">
                                            <a href="{{ route('equipment.index') }}" class="btn btn-sm btn-primary">
                                                Reset Filters
                                            </a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endif
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
