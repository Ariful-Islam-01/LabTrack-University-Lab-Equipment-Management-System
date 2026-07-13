@extends('layouts.app')

@section('title', 'Add Teacher')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Header Section -->
            <div class="mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="bi bi-person-plus-fill me-2 text-primary"></i>Add New Teacher
                </h1>
            </div>

            <!-- Form Card -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="{{ route('teachers.store') }}" method="POST" novalidate>
                        @csrf

                        <!-- Teacher ID -->
                        <div class="mb-3">
                            <label for="user_id" class="form-label fw-semibold">
                                Teacher ID <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('user_id') is-invalid @enderror"
                                   id="user_id"
                                   name="user_id"
                                   placeholder="e.g. TCH2024001"
                                   value="{{ old('user_id') }}"
                                   maxlength="20">
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Full Name -->
                        <div class="mb-3">
                            <label for="full_name" class="form-label fw-semibold">
                                Full Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('full_name') is-invalid @enderror"
                                   id="full_name"
                                   name="full_name"
                                   placeholder="e.g. Dr. Jane Smith"
                                   value="{{ old('full_name') }}"
                                   maxlength="150">
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">
                                Email Address <span class="text-danger">*</span>
                            </label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   placeholder="e.g. teacher@university.edu"
                                   value="{{ old('email') }}"
                                   maxlength="100">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">
                                Password <span class="text-danger">*</span>
                            </label>
                            <input type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   id="password"
                                   name="password"
                                   placeholder="Minimum 6 characters"
                                   maxlength="100">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Department -->
                        <div class="mb-4">
                            <label for="department" class="form-label fw-semibold">
                                Department <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('department') is-invalid @enderror"
                                    id="department"
                                    name="department">
                                <option value="">-- Select Department --</option>
                                @foreach (['CSE', 'EEE', 'CE', 'ME', 'BBA'] as $dept)
                                    <option value="{{ $dept }}" {{ old('department') === $dept ? 'selected' : '' }}>
                                        {{ $dept }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-circle me-1"></i>Save Teacher
                            </button>
                            <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary px-4">
                                <i class="bi bi-x-circle me-1"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
