@extends('layouts.app')

@section('title', 'Most Borrowed Equipment')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Most Borrowed Equipment</h1>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Reports
        </a>
    </div>

    @if ($equipments->isEmpty())
        <div class="alert alert-secondary text-center py-4" role="alert">
            No borrow activity recorded yet.
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
                                <th class="text-center">Borrow Frequencies (Times)</th>
                                <th class="text-center">Total Quantity Borrowed (Units)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($equipments as $item)
                                <tr>
                                    <td>{{ $item->equipment_id }}</td>
                                    <td>{{ $item->equipment_name }}</td>
                                    <td>{{ $item->category_name }}</td>
                                    <td class="text-center fw-bold text-primary">{{ $item->total_borrowed_times }}</td>
                                    <td class="text-center">{{ $item->total_borrowed_quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination Section -->
        <div class="d-flex justify-content-center mt-3">
            {{ $equipments->links() }}
        </div>
    @endif
</div>
@endsection
