<!--**********************************
    Header start
***********************************-->
<div class="header">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="header-left">
                    <div class="dashboard_bar">
                        @yield('page-title', 'Dashboard')
                    </div>
                </div>
                <ul class="navbar-nav header-right">
                    <!-- Unit Badge -->
                    @auth
                    <li class="nav-item">
                        <span class="badge bg-primary fs-14 px-3 py-2">
                            <i class="bi bi-building me-1"></i>
                            @if(auth()->user()->role === 'superadmin')
                                Semua Unit
                            @else
                                {{ auth()->user()->unit?->name ?? 'Unit' }}
                            @endif
                        </span>
                    </li>
                    @endauth

                    <!-- User Dropdown -->
                    <li class="nav-item dropdown header-profile">
                        <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                            <div class="header-info">
                                <span class="fs-14 fw-semibold">{{ auth()->user()->name ?? 'User' }}</span>
                                <small class="text-muted">{{ ucfirst(auth()->user()->role ?? 'guest') }}</small>
                            </div>
                            <i class="bi bi-person-circle fs-24 text-primary"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="{{ route('profile.password') }}" class="dropdown-item ai-icon">
                                <i class="bi bi-key me-2"></i>
                                <span class="ms-2">Ganti Password</span>
                            </a>
                            <a href="{{ route('logout') }}" class="dropdown-item ai-icon">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                <span class="ms-2">Logout</span>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>
<!--**********************************
    Header end
***********************************-->
