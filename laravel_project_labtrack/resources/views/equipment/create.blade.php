@extends('layouts.app')

@section('title', 'Add Equipment')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Header Section -->
            <div class="mb-4">
                <h1 class="h3 mb-0 text-gray-800">Add New Equipment</h1>
            </div>

            <!-- Form Card -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="{{ route('equipment.store') }}" method="POST">
                        @csrf

                        <!-- Equipment ID -->
                        <div class="mb-3">
                            <label for="equipment_id" class="form-label fw-semibold">Equipment ID</label>
                            <input type="text" 
                                   class="form-control @error('equipment_id') is-invalid @enderror" 
                                   id="equipment_id" 
                                   name="equipment_id" 
                                   value="{{ old('equipment_id') }}">
                            @error('equipment_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Equipment Name -->
                        <div class="mb-3">
                            <label for="equipment_name" class="form-label fw-semibold">Equipment Name</label>
                            <input type="text" 
                                   class="form-control @error('equipment_name') is-invalid @enderror" 
                                   id="equipment_name" 
                                   name="equipment_name" 
                                   value="{{ old('equipment_name') }}">
                            @error('equipment_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label for="category_id" class="form-label fw-semibold">Category</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                    id="category_id" 
                                    name="category_id">
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->category_id }}" 
                                            {{ old('category_id') == $category->category_id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Lab -->
                        <div class="mb-3">
                            <label for="lab_id" class="form-label fw-semibold">Lab</label>
                            <select class="form-select @error('lab_id') is-invalid @enderror" 
                                    id="lab_id" 
                                    name="lab_id">
                                <option value="">Select Lab</option>
                                @foreach ($labs as $lab)
                                    <option value="{{ $lab->lab_id }}" 
                                            {{ old('lab_id') == $lab->lab_id ? 'selected' : '' }}>
                                        {{ $lab->lab_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('lab_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Total Quantity -->
                        <div class="mb-4">
                            <label for="quantity" class="form-label fw-semibold">Total Quantity</label>
                            <input type="number" 
                                   min="1" 
                                   class="form-control @error('quantity') is-invalid @enderror" 
                                   id="quantity" 
                                   name="quantity" 
                                   value="{{ old('quantity') }}">
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                Save Equipment
                            </button>
                            <a href="{{ route('equipment.index') }}" class="btn btn-outline-secondary px-4">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
