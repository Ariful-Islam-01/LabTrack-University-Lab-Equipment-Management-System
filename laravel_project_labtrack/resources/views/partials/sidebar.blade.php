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

        <!-- Include Role-Specific Sidebars -->
        @if($role === 'STUDENT')
            @include('layouts.sidebar.student')
        @elseif($role === 'TEACHER')
            @include('layouts.sidebar.teacher')
        @elseif($role === 'LAB_ASSISTANT')
            @include('layouts.sidebar.assistant')
        @endif
    </div>
</div>
