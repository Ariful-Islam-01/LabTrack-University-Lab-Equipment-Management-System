@extends('layouts.app')

@section('title', 'Top Borrowers')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Top Student Borrowers</h1>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Reports
        </a>
    </div>

    @if ($students->isEmpty())
        <div class="alert alert-secondary text-center py-4" role="alert">
            No borrowers found.
        </div>
    @else
        <!-- Table Section -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Student ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th class="text-center">Total Borrow Records (Times)</th>
                                <th class="text-center">Total Quantity Borrowed (Units)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $student)
                                <tr>
                                    <td>{{ $student->user_id }}</td>
                                    <td>{{ $student->full_name }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td class="text-center fw-bold text-primary">{{ $student->total_borrows }}</td>
                                    <td class="text-center">{{ $student->total_quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination Section -->
        <div class="d-flex justify-content-center mt-3">
            {{ $students->links() }}
        </div>
    @endif
</div>
@endsection
