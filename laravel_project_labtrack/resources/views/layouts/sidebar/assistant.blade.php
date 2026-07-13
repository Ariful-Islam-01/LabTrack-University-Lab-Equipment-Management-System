<!-- LAB_ASSISTANT Menu -->
<a href="{{ route('equipment.index') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('equipment.*') ? 'active' : '' }}">
    <i class="bi bi-pc-display-horizontal me-2"></i> Equipment
</a>
<a href="{{ route('bookings.index') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
    <i class="bi bi-check2-square me-2"></i> Booking Requests
</a>
<a href="{{ route('borrows.index') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('borrows.*') ? 'active' : '' }}">
    <i class="bi bi-arrow-left-right me-2"></i> Borrow & Return
</a>
<a href="{{ route('fines.index') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('fines.*') ? 'active' : '' }}">
    <i class="bi bi-currency-dollar me-2"></i> Fines
</a>
<a href="{{ route('students.index') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('students.*') ? 'active' : '' }}">
    <i class="bi bi-people-fill me-2"></i> Students
</a>
<a href="{{ route('teachers.index') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('teachers.*') ? 'active' : '' }}">
    <i class="bi bi-person-workspace me-2"></i> Teachers
</a>

<!-- Reports Submenu -->
<a href="#reportsSubmenu" data-bs-toggle="collapse" class="list-group-item list-group-item-action border-0 rounded mb-1 d-flex justify-content-between align-items-center {{ request()->routeIs('reports.*') ? 'active' : '' }}">
    <span><i class="bi bi-file-earmark-bar-graph me-2"></i> Reports</span>
    <i class="bi bi-chevron-down small"></i>
</a>
<div class="collapse {{ request()->routeIs('reports.*') ? 'show' : '' }} ps-3" id="reportsSubmenu">
    <a href="{{ route('reports.index') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 py-1 small {{ request()->routeIs('reports.index') ? 'fw-bold text-primary' : '' }}">
        Dashboard
    </a>
    <a href="{{ route('reports.equipment') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 py-1 small {{ request()->routeIs('reports.equipment') ? 'fw-bold text-primary' : '' }}">
        Equipment Report
    </a>
    <a href="{{ route('reports.bookings') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 py-1 small {{ request()->routeIs('reports.bookings') ? 'fw-bold text-primary' : '' }}">
        Booking Report
    </a>
    <a href="{{ route('reports.borrows') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 py-1 small {{ request()->routeIs('reports.borrows') ? 'fw-bold text-primary' : '' }}">
        Borrow Report
    </a>
    <a href="{{ route('reports.fines') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 py-1 small {{ request()->routeIs('reports.fines') ? 'fw-bold text-primary' : '' }}">
        Fine Report
    </a>
    
    <!-- Advanced Reports Divider & Subitems -->
    <div class="text-uppercase text-muted fw-bold px-2 py-1 mt-2" style="font-size: 0.7rem;">Advanced Reports</div>
    <a href="{{ route('reports.most_borrowed') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 py-1 small {{ request()->routeIs('reports.most_borrowed') ? 'fw-bold text-primary' : '' }}">
        Most Borrowed
    </a>
    <a href="{{ route('reports.top_borrowers') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 py-1 small {{ request()->routeIs('reports.top_borrowers') ? 'fw-bold text-primary' : '' }}">
        Top Borrowers
    </a>
    <a href="{{ route('reports.category') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 py-1 small {{ request()->routeIs('reports.category') ? 'fw-bold text-primary' : '' }}">
        By Category
    </a>
    <a href="{{ route('reports.recent_activities') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 py-1 small {{ request()->routeIs('reports.recent_activities') ? 'fw-bold text-primary' : '' }}">
        Recent Activities
    </a>
</div>
