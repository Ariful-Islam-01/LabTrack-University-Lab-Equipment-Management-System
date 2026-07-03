<nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom">
    <div class="container-fluid">
        <!-- Brand -->
        <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
            LabTrack
        </a>

        <!-- Responsive toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Content -->
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto align-items-center gap-3 mt-2 mt-lg-0">
                @if(session()->has('user_id'))
                    <!-- User Info -->
                    <li class="nav-item text-white">
                        <span class="me-2">{{ session('full_name') }}</span>
                        
                        @php
                            $role = session('role');
                            $badgeClass = match(strtoupper($role)) {
                                'STUDENT' => 'bg-primary',
                                'TEACHER' => 'bg-success',
                                'LAB_ASSISTANT' => 'bg-secondary',
                                default => 'bg-light text-dark'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $role }}</span>
                    </li>

                    <!-- Logout Button -->
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                Logout
                            </button>
                        </form>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
