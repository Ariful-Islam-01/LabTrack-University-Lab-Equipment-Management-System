<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - LabTrack</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        body {
            background-color: #f4f6f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 15px;
        }
        .card {
            border: none;
            border-radius: 12px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="card shadow-lg p-3">
        <div class="card-body">
            <!-- Project Header -->
            <div class="text-center mb-4">
                <h2 class="fw-bold text-dark mb-1">LabTrack</h2>
                <span class="text-muted small">Laboratory Equipment Management System</span>
            </div>

            <!-- Bootstrap Alert for Login Errors -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Input -->
                <div class="mb-3">
                    <label for="email" class="form-label text-secondary fw-semibold">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control form-control-lg @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus autocomplete="email">
                </div>

                <!-- Password Input -->
                <div class="mb-4">
                    <label for="password" class="form-label text-secondary fw-semibold">Password</label>
                    <input type="password" name="password" id="password" class="form-control form-control-lg @error('password') is-invalid @enderror" required autocomplete="current-password">
                </div>

                <!-- Submit Button -->
                <div class="d-grid mb-2">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold">Login</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Small Footer -->
    <div class="text-center mt-4 text-secondary small">
        <p>&copy; 2026 LabTrack</p>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>
