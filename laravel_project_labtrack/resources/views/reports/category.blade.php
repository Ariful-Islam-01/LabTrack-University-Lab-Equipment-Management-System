@extends('layouts.app')

@section('title', 'Equipment By Category')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Equipment By Category</h1>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Reports
        </a>
    </div>

    @if ($categoriesReport->isEmpty())
        <div class="alert alert-secondary text-center py-4" role="alert">
            No category statistics found.
        </div>
    @else
        <!-- Table Section -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Category</th>
                                <th>Equipment Name</th>
                                <th class="text-center">Total Borrow Records (Times)</th>
                                <th class="text-center">Total Quantity Borrowed (Units)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categoriesReport as $row)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary fw-semibold">{{ $row->category }}</span>
                                    </td>
                                    <td>{{ $row->equipment_name }}</td>
                                    <td class="text-center fw-bold text-primary">{{ $row->total_borrows }}</td>
                                    <td class="text-center">{{ $row->total_quantity_borrowed }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination Section -->
        <div class="d-flex justify-content-center mt-3">
            {{ $categoriesReport->links() }}
        </div>
    @endif
</div>
@endsection
