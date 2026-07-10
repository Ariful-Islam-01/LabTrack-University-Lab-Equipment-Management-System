<!-- STUDENT Menu -->
<a href="{{ route('equipment.index') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('equipment.*') ? 'active' : '' }}">
    <i class="bi bi-pc-display-horizontal me-2"></i> Equipment
</a>
<a href="{{ route('bookings.index') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
    <i class="bi bi-journal-plus me-2"></i> My Requests
</a>
<a href="{{ route('borrows.index') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('borrows.*') ? 'active' : '' }}">
    <i class="bi bi-clock-history me-2"></i> My Borrow Records
</a>
<a href="{{ route('fines.index') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('fines.*') ? 'active' : '' }}">
    <i class="bi bi-currency-dollar me-2"></i> Fines
</a>

<!-- Reports Submenu -->
<a href="#reportsSubmenu" data-bs-toggle="collapse" class="list-group-item list-group-item-action border-0 rounded mb-1 d-flex justify-content-between align-items-center {{ request()->routeIs('reports.*') ? 'active' : '' }}">
    <span><i class="bi bi-file-earmark-bar-graph me-2"></i> Reports</span>
    <i class="bi bi-chevron-down small"></i>
</a>
<div class="collapse {{ request()->routeIs('reports.*') ? 'show' : '' }} ps-3" id="reportsSubmenu">
    <a href="{{ route('reports.index') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 py-1 small {{ request()->routeIs('reports.index') ? 'fw-bold text-primary' : '' }}">
        My Statistics
    </a>
    <a href="{{ route('reports.my_borrows') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 py-1 small {{ request()->routeIs('reports.my_borrows') ? 'fw-bold text-primary' : '' }}">
        My Borrow History
    </a>
    <a href="{{ route('reports.my_fines') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 py-1 small {{ request()->routeIs('reports.my_fines') ? 'fw-bold text-primary' : '' }}">
        My Fine History
    </a>
</div>
