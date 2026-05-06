<!--**********************************
    Sidebar start
***********************************-->
<div class="dlabnav">
    <div class="dlabnav-scroll">
        <ul class="metismenu" id="menu">

            {{-- Dashboard --}}
            <li class="{{ request()->routeIs('dashboard') ? 'mm-active' : '' }}">
                <a href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            {{-- Absensi --}}
            <li class="{{ request()->routeIs('schedules.*', 'attendance.*') ? 'mm-active' : '' }}">
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="bi bi-calendar-check"></i>
                    <span class="nav-text">Absensi</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('schedules.index') }}">Jadwal & Absen</a></li>
                    <li><a href="{{ route('attendance.recap') }}">Rekap Kelas</a></li>
                </ul>
            </li>

            {{-- Mata Pelajaran (semua role) --}}
            <li class="{{ request()->routeIs('subjects.*') ? 'mm-active' : '' }}">
                <a href="{{ route('subjects.index') }}">
                    <i class="bi bi-book"></i>
                    <span class="nav-text">Mata Pelajaran</span>
                </a>
            </li>

            {{-- Data Master (Admin+) --}}
            @can('manage-master-data')
            <li class="{{ request()->routeIs('students.*', 'classes.*', 'substitutions.*') ? 'mm-active' : '' }}">
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="bi bi-people"></i>
                    <span class="nav-text">Data Master</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('students.index') }}">Santri</a></li>
                    <li><a href="{{ route('classes.index') }}">Kelas</a></li>
                    <li><a href="{{ route('substitutions.index') }}">Guru Badal</a></li>
                </ul>
            </li>

            {{-- Export Excel --}}
            <li class="{{ request()->is('export*') ? 'mm-active' : '' }}">
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="bi bi-file-earmark-excel"></i>
                    <span class="nav-text">Export Excel</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('export.students') }}">Data Santri</a></li>
                    <li><a href="{{ route('export.teachers') }}">Data Guru</a></li>
                    <li><a href="{{ route('export.schedules') }}">Jadwal Pelajaran</a></li>
                </ul>
            </li>
            @endcan

            {{-- UKS & Izin (Admin+) --}}
            @can('manage-leaves')
            <li class="{{ request()->routeIs('leaves.*') ? 'mm-active' : '' }}">
                <a href="{{ route('leaves.index') }}">
                    <i class="bi bi-heart-pulse"></i>
                    <span class="nav-text">UKS & Izin</span>
                </a>
            </li>
            @endcan

            {{-- Notifikasi Alpha (Admin+) --}}
            @can('manage-notifications')
            <li class="{{ request()->routeIs('notifications.*') ? 'mm-active' : '' }}">
                <a href="{{ route('notifications.index') }}">
                    <i class="bi bi-bell"></i>
                    <span class="nav-text">Notifikasi Alpha</span>
                </a>
            </li>
            @endcan

            {{-- Sistem (Superadmin) --}}
            @can('manage-system')
            <li class="{{ request()->routeIs('system.*') ? 'mm-active' : '' }}">
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="bi bi-gear"></i>
                    <span class="nav-text">Sistem</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('system.calendar') }}">Kalender Libur</a></li>
                    <li><a href="{{ route('system.settings') }}">Pengaturan Alpha</a></li>
                </ul>
            </li>
            @endcan

        </ul>

        <div class="copyright">
            <p><strong>PPNI Smart Attendance</strong></p>
            <p class="fs-12">Unit Banin — MTs & MA</p>
        </div>
    </div>
</div>
<!--**********************************
    Sidebar end
***********************************-->
