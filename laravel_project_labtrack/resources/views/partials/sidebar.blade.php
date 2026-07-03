<div class="d-flex flex-column p-3 bg-white h-100 border-end" style="min-height: 100%;">
    <!-- Navigation Heading -->
    <span class="fs-6 fw-bold text-uppercase text-muted px-2 mb-3">Navigation</span>
    
    <!-- List Group -->
    <div class="list-group list-group-flush flex-grow-1">
        @php
            $role = strtoupper(session('role'));
        @endphp

        <!-- Common Link: Dashboard -->
        <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>

        <!-- STUDENT Menu -->
        @if($role === 'STUDENT')
            <a href="{{ route('equipment.index') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('equipment.*') ? 'active' : '' }}">
                <i class="bi bi-pc-display-horizontal me-2"></i> Equipment
            </a>
            <a href="{{ route('bookings.index') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                <i class="bi bi-journal-plus me-2"></i> My Requests
            </a>
            <a href="{{ route('borrows.index') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('borrows.*') ? 'active' : '' }}">
                <i class="bi bi-clock-history me-2"></i> My Borrow Records
            </a>
        @endif

        <!-- TEACHER Menu -->
        @if($role === 'TEACHER')
            <a href="{{ route('bookings.index') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                <i class="bi bi-check2-square me-2"></i> Booking Requests
            </a>
            <a href="{{ route('reports.index') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph me-2"></i> Reports
            </a>
        @endif

        <!-- LAB_ASSISTANT Menu -->
        @if($role === 'LAB_ASSISTANT')
            <a href="{{ route('equipment.index') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('equipment.*') ? 'active' : '' }}">
                <i class="bi bi-pc-display-horizontal me-2"></i> Equipment
            </a>
            <a href="{{ route('borrows.index') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('borrows.*') ? 'active' : '' }}">
                <i class="bi bi-arrow-left-right me-2"></i> Borrow & Return
            </a>
            <a href="{{ route('fines.index') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('fines.*') ? 'active' : '' }}">
                <i class="bi bi-currency-dollar me-2"></i> Fines
            </a>
            <a href="{{ route('reports.index') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph me-2"></i> Reports
            </a>
        @endif
    </div>
</div>
