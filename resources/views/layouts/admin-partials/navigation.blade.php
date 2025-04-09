<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
        <!--begin::Brand Link-->
        <a href="{{ route('admin.dashboard') }}" class="brand-link">
            <!--begin::Brand Image-->
           <!-- <span class="brand-text fw-light">ColdStorage</span> -->
           <img src="{{ asset('assets/images/brand-logo.png') }}" class="main-logo" alt="Ubrand-logo" />

        </a>
        <!--end::Brand Link-->
    </div>
    <!--end::Sidebar Brand-->
    <!--begin::Sidebar Wrapper-->
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul
                class="nav sidebar-menu flex-column"
                data-lte-toggle="treeview"
                role="menu"
                data-accordion="false"
            >
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <p>Users</p>
                    </a>
                </li>
                {{-- <li class="nav-item">
                    <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-lock"></i>
                        <p>Roles</p>
                    </a>
                </li> --}}
                <li class="nav-item">
                    <a href="{{ route('admin.farmers.index') }}" class="nav-link {{ request()->routeIs('admin.farmers.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-tie"></i>
                        <p>Farmers</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.companies.index') }}" class="nav-link {{ request()->routeIs('admin.companies.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-building"></i>
                        <p>Seeds Companies</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.seed-varieties.index') }}" class="nav-link {{ request()->routeIs('admin.seed-varieties.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-seedling"></i>
                        <p>Seed Varieties</p>
                    </a>
                </li>
                 <li class="nav-item">
                    <a href="{{ route('admin.agreements.index') }}" class="nav-link {{ request()->routeIs('admin.agreements.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-signature"></i>
                        <p>Potato Booking(Agreement)</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.seeds-booking.index') }}" class="nav-link {{ request()->routeIs('admin.seeds-booking.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-plus"></i>
                        <p>Seeds Booking</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.seed-distributions.index') }}" class="nav-link {{ request()->routeIs('admin.seed-distributions.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-seedling"></i>
                        <p>Seed Distributions</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.packaging-distributions.index') }}" class="nav-link {{ request()->routeIs('admin.packaging-distributions.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box-open"></i>
                        <p>Packaging Distributions</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.advance-payments.index') }}" class="nav-link {{ request()->routeIs('admin.advance-payments.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-money-check-alt"></i>
                        <p>Advance Payments</p> 
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.transporters.index') }}" class="nav-link {{ request()->routeIs('admin.transporters.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-truck"></i>
                        <p>Transporters</p> 
                    </a>
                </li>
                {{-- <li class="nav-item">
                    <a href="{{ route('admin.vehicles.index') }}" class="nav-link {{ request()->routeIs('admin.vehicles.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-truck-loading"></i>
                        <p>Vehicles</p> 
                    </a>
                </li> --}}
                <li class="nav-item">
                    <a href="{{ route('admin.cold-storages.index') }}" class="nav-link {{ request()->routeIs('admin.cold-storages.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-warehouse"></i>
                        <p>Cold Storages</p> 
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.storage-loadings.index') }}" class="nav-link {{ request()->routeIs('admin.storage-loadings.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-dolly"></i>
                        <p>Storage Loading</p> 
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.challans.index') }}" class="nav-link {{ request()->routeIs('admin.challans.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>Challans</p> 
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.unloading-companies.index') }}" class="nav-link {{ request()->routeIs('admin.unloading-companies.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-industry"></i>
                        <p>Unloading Companies</p> 
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.storage-unloadings.index') }}" class="nav-link {{ request()->routeIs('admin.storage-unloadings.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-dolly-flatbed"></i>
                        <p>Storage Unloading</p> 
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Reports</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.sms.test') ? 'active' : '' }}" 
                    href="{{ route('admin.sms.test') }}">
                        <i class="nav-icon fas fa-sms"></i>
                        <span>SMS Test</span>
                    </a>
                </li> 
            </ul>
            <!--end::Sidebar Menu-->
        </nav>
    </div>
    <!--end::Sidebar Wrapper-->
</aside>