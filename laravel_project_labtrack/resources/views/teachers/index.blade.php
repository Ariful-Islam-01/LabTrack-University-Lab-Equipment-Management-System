@extends('layouts.app')

@section('title', 'Teacher Management')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-person-workspace me-2 text-primary"></i>Teacher Management
        </h1>
        <a href="{{ route('teachers.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Add Teacher
        </a>
    </div>

    <!-- Search Section -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('teachers.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-8">
                        <label for="search" class="form-label fw-semibold text-secondary">Search</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text"
                                   id="search"
                                   name="search"
                                   class="form-control border-start-0 ps-0"
                                   placeholder="Search by Teacher ID, Name, Email or Department..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-12 col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-search"></i> Search
                        </button>
                        <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary flex-grow-1 text-nowrap">
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
                            <th>Teacher ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th class="text-center">Department</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($teachers->count())
                            @foreach ($teachers as $teacher)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary fw-normal">{{ $teacher->user_id }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center"
                                                 style="width:36px;height:36px;font-size:0.85rem;flex-shrink:0;">
                                                {{ strtoupper(substr($teacher->full_name, 0, 1)) }}
                                            </div>
                                            <span class="fw-semibold">{{ $teacher->full_name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-muted">{{ $teacher->email }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-info text-dark fw-semibold">{{ $teacher->department }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex flex-column flex-md-row gap-2 justify-content-md-center align-items-stretch align-items-md-center">
                                            <a href="{{ route('teachers.edit', $teacher->user_id) }}"
                                               class="btn btn-sm btn-outline-primary text-nowrap">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                            <!-- Delete Trigger -->
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger text-nowrap"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal"
                                                    data-teacher-id="{{ $teacher->user_id }}"
                                                    data-teacher-name="{{ $teacher->full_name }}">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted mb-3">
                                        <i class="bi bi-person-workspace fs-1"></i>
                                    </div>
                                    <h5 class="fw-semibold text-secondary">No teachers found</h5>
                                    <p class="text-muted mb-0">No teachers match your search criteria.</p>
                                    @if(request()->filled('search'))
                                        <div class="mt-3">
                                            <a href="{{ route('teachers.index') }}" class="btn btn-sm btn-primary">
                                                Reset Search
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
        {{ $teachers->links() }}
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="mb-1">Are you sure you want to delete the teacher:</p>
                <p class="fw-bold fs-6 mb-0" id="deleteTeacherName"></p>
                <p class="text-muted small mt-2 mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    This action cannot be undone. Teachers with existing approved booking or equipment log records cannot be deleted.
                </p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Delete Teacher
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Populate delete modal with teacher data
    const deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const teacherId   = button.getAttribute('data-teacher-id');
        const teacherName = button.getAttribute('data-teacher-name');

        document.getElementById('deleteTeacherName').textContent = teacherName + ' (' + teacherId + ')';
        document.getElementById('deleteForm').action = '/teachers/' + teacherId;
    });
</script>
@endpush
