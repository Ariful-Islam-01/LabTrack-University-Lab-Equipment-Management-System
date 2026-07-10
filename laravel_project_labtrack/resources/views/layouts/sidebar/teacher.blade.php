<!-- TEACHER Menu -->
<a href="{{ route('equipment.index') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('equipment.*') ? 'active' : '' }}">
    <i class="bi bi-pc-display-horizontal me-2"></i> Equipment
</a>
<a href="{{ route('bookings.index') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
    <i class="bi bi-check2-square me-2"></i> Booking Requests
</a>

<!-- Reports Submenu -->
<a href="#reportsSubmenu" data-bs-toggle="collapse" class="list-group-item list-group-item-action border-0 rounded mb-1 d-flex justify-content-between align-items-center {{ request()->routeIs('reports.*') ? 'active' : '' }}">
    <span><i class="bi bi-file-earmark-bar-graph me-2"></i> Reports</span>
    <i class="bi bi-chevron-down small"></i>
</a>
<div class="collapse {{ request()->routeIs('reports.*') ? 'show' : '' }} ps-3" id="reportsSubmenu">
    <a href="{{ route('reports.bookings') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 py-1 small {{ request()->routeIs('reports.bookings') ? 'fw-bold text-primary' : '' }}">
        Booking Report
    </a>
    <a href="{{ route('reports.index') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 py-1 small {{ request()->routeIs('reports.index') ? 'fw-bold text-primary' : '' }}">
        Booking Statistics
    </a>
</div>
