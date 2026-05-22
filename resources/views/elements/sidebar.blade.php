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
            <li class="{{ request()->routeIs('attendance.*', 'schedules.*') ? 'mm-active' : '' }}">
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="bi bi-calendar-check"></i>
                    <span class="nav-text">Absensi</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('schedules.index') }}">Jadwal & Absen</a></li>
                    <li><a href="{{ route('attendance.recap', ['class' => 'today']) }}">Rekap Kelas</a></li>
                </ul>
            </li>

            {{-- Data Master (Admin+) --}}
            @can('manage-master-data')
            <li class="{{ request()->routeIs('students.*', 'classes.*', 'teachers.*', 'substitutions.*', 'akademik.mapel-jadwal') ? 'mm-active' : '' }}">
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="bi bi-people"></i>
                    <span class="nav-text">Data Master</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('students.index') }}">Santri</a></li>
                    <li><a href="{{ route('teachers.index') }}">Guru</a></li>
                    <li><a href="{{ route('classes.index') }}">Kelas</a></li>
                    <li><a href="{{ route('akademik.mapel-jadwal') }}">Mata Pelajaran & Jadwal</a></li>
                    <li><a href="{{ route('substitutions.index') }}">Guru Badal</a></li>
                </ul>
            </li>
            @endcan

            {{-- UKS & Notifikasi (Admin+) --}}
            @can('manage-leaves')
            <li class="{{ request()->routeIs('leaves.*') ? 'mm-active' : '' }}">
                <a href="{{ route('leaves.index') }}">
                    <i class="bi bi-heart-pulse"></i>
                    <span class="nav-text">UKS & Izin</span>
                </a>
            </li>
            @endcan

            @can('manage-notifications')
            <li class="{{ request()->routeIs('notifications.*') ? 'mm-active' : '' }}">
                <a href="{{ route('notifications.index') }}">
                    <i class="bi bi-bell"></i>
                    <span class="nav-text">Notifikasi Alpha</span>
                    {{-- Badge count jika ada pending --}}
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