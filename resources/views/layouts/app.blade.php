<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Aplikasi' }}</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        @php($setting = \App\Models\AppSetting::first())
        <style>
            :root { --sidebar-w: {{ ($setting?->sidebar_width ?? 260) }}px; --primary: {{ $setting?->theme_primary ?? '#0b3a82' }}; }
            .sidebar { width: var(--sidebar-w); background: linear-gradient(180deg, var(--primary), #072a5e); position: fixed; top: 0; bottom: 0; left: 0; transition: transform .2s ease; transform: translateX(0); }
            .sidebar .nav-link.active { background-color: rgba(255,255,255,.22); border-radius: .5rem; }
            .content { margin-left: var(--sidebar-w); min-height: 100vh; overflow-y: auto; transition: margin-left .2s ease; }
            body.sidebar-collapsed .sidebar { transform: translateX(-100%); }
            body.sidebar-collapsed .content { margin-left: 0; }
            @media (max-width: 992px) {
                .sidebar { position: static; width: 100%; transform: none; }
                .content { margin-left: 0; min-height: auto; overflow: visible; }
            }
            .topbar { background: linear-gradient(180deg, #ffffff, #f8fafc); border: 1px solid #e9ecef; border-left: 6px solid var(--primary); border-radius: .75rem; padding: .5rem .75rem; box-shadow: 0 6px 16px rgba(0,0,0,.06); }
            .topbar .btn { border-color: #e9ecef; }
            .topbar .btn:hover { color: var(--primary); border-color: var(--primary); }
            .role-badge { background: var(--primary); color: #fff; border-radius: .5rem; font-weight: 600; }
            .role-badge.staff { background: transparent; color: var(--primary); border: 1px solid var(--primary); }
            .user-info .name { line-height: 1; }
            .user-info .desc { font-size: .85rem; color: #6c757d; margin-top: 2px; }
        </style>
    </head>
    <body class="bg-light">
        <div class="d-flex">
            <aside class="sidebar text-white d-none d-lg-flex flex-column p-3">
                <div class="text-center mb-3">
                    <img src="{{ $setting?->logo_path ? asset($setting->logo_path) : asset('img/logo.png') }}" alt="Logo" class="rounded-circle" style="width:72px;height:72px;">
                    <div class="fw-semibold mt-2">{{ $setting?->school_name ?? 'SMK Nasional Dawarblandong' }}</div>
                    <div class="text-white-50 small">Sistem Peminjaman Alat</div>
                    <span class="badge bg-light text-dark mt-2">{{ $setting?->department_name ?? 'TEKNIK KOMPUTER & JARINGAN' }}</span>
                </div>
                <nav class="nav flex-column gap-1 flex-grow-1">
                    <a class="nav-link text-white {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class='bx bxs-dashboard me-2'></i>Dashboard</a>
                    @if(auth()->user()?->role !== 'admin')
                    <a class="nav-link text-white {{ request()->routeIs('loans') ? 'active' : '' }}" href="{{ route('loans') }}"><i class='bx bx-calendar me-2'></i>Peminjaman</a>
                    <a class="nav-link text-white {{ request()->routeIs('returns') ? 'active' : '' }}" href="{{ route('returns') }}"><i class='bx bx-undo me-2'></i>Pengembalian</a>
                    @endif
                    @if(auth()->user()?->role === 'admin')
                    <a class="nav-link text-white {{ request()->routeIs('equipment') ? 'active' : '' }}" href="{{ route('equipment') }}"><i class='bx bx-wrench me-2'></i>Data Peralatan</a>
                    @endif
                    @if(auth()->user()?->role !== 'admin')
                    <a class="nav-link text-white {{ request()->routeIs('reports') ? 'active' : '' }}" href="{{ route('reports') }}"><i class='bx bx-file me-2'></i>Laporan</a>
                    @endif
                    @if(auth()->user()?->role === 'admin')
                        <a class="nav-link text-white {{ request()->routeIs('settings') ? 'active' : '' }}" href="{{ route('settings') }}"><i class='bx bx-cog me-2'></i>Pengaturan</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="mt-auto">
                        @csrf
                        <button class="btn btn-danger w-100"><i class='bx bx-log-out me-1'></i> Logout</button>
                    </form>
                </nav>
                <div class="small text-white-50 mt-2">{{ $setting?->footer_text ?? '© Tim IT SMK Nasional Dawarblandong' }}</div>
            </aside>
            <main class="content flex-grow-1 p-3 p-lg-4">
                <div class="topbar d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <button id="btnToggleSidebar" class="btn btn-light btn-sm"><i class='bx bx-menu'></i></button>
                    </div>
                    @if(auth()->check())
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge role-badge {{ auth()->user()->role === 'admin' ? 'admin' : 'staff' }}">{{ auth()->user()->role === 'admin' ? 'Admin' : 'Petugas' }}</span>
                        <div class="user-info text-end">
                            <div class="name">Selamat datang, <strong>{{ auth()->user()->name }}</strong></div>
                            @if(auth()->user()->role === 'admin')
                                <div class="desc">Admin (Kepala Jurusan): tambah alat & konfigurasi aplikasi</div>
                            @else
                                <div class="desc">Petugas: peminjaman, pengembalian, laporan</div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
                @yield('content')
            </main>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            (function(){
                var key='sidebarCollapsed';
                var body=document.body;
                var saved=localStorage.getItem(key);
                if(saved==='1'){ body.classList.add('sidebar-collapsed'); }
                var btn=document.getElementById('btnToggleSidebar');
                if(btn){ btn.addEventListener('click', function(){ body.classList.toggle('sidebar-collapsed'); localStorage.setItem(key, body.classList.contains('sidebar-collapsed')?'1':'0'); }); }
            })();
        </script>
    </body>
</html>