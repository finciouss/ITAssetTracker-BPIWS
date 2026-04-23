<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — IT Asset Tracker</title>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Tom Select CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .ts-control { border-radius: 0.5rem; border-color: #e2e8f0; padding: 0.5rem 0.75rem; font-family: 'Inter', sans-serif; font-size: 0.85rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
        .ts-control.focus { border-color: #6366f1; box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2); }
        .ts-wrapper.multi .ts-control > div { background: #e0e7ff; color: #4f46e5; border-radius: 6px; border: 0; padding: 4px 8px; font-weight: 500; font-size: 0.8rem; margin: 2px; }
        .ts-control input { font-family: 'Inter', sans-serif; font-size: 0.85rem; }
    </style>
</head>
<body class="h-full bg-slate-50 font-sans antialiased">

<div class="flex h-full relative overflow-hidden">
    
    <!-- Mobile Backdrop -->
    <div id="mobile-overlay" class="fixed inset-0 bg-slate-900/40 z-40 hidden md:hidden backdrop-blur-sm transition-opacity duration-300 opacity-0"></div>

    <!-- ── Sidebar ───────────────────────────────────────────────────────── -->
    <aside id="sidebar" class="w-64 bg-white/90 backdrop-blur-2xl flex flex-col shrink-0 fixed inset-y-0 left-0 z-50 border-r border-slate-200/60 shadow-xl md:shadow-sm transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">

        <!-- Brand -->
        <div class="flex flex-col items-center justify-center px-6 py-8 border-b border-slate-100">
             <img src="{{ asset('img/bauer-logo.jpeg') }}" alt="BAUER Logo" class="h-12 rounded-md object-contain mb-2 shadow-sm bg-white">
            <p class="text-slate-400 tracking-widest text-[10px] mt-1 font-medium text-center">IT ASSET TRACKER</p>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

            <p class="px-4 pt-2 pb-1 text-xs font-semibold text-slate-500 uppercase tracking-wider">Overview</p>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <p class="px-4 pt-4 pb-1 text-xs font-semibold text-slate-500 uppercase tracking-wider">Assets</p>
            <a href="{{ route('assets.index') }}" class="nav-link {{ request()->routeIs('assets.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                All Assets
            </a>

            @hasrole('Admin|Staff')
            <a href="{{ route('allocations.index') }}" class="nav-link {{ request()->routeIs('allocations.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                Allocations
            </a>

            <p class="px-4 pt-4 pb-1 text-xs font-semibold text-slate-500 uppercase tracking-wider">Management</p>
            <a href="{{ route('projects.index') }}" class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Projects
            </a>
            <a href="{{ route('employees.index') }}" class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Employees
            </a>
            @endhasrole

            @hasrole('Admin')
            <p class="px-4 pt-4 pb-1 text-xs font-semibold text-slate-500 uppercase tracking-wider">Settings</p>
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                System Users
            </a>
            <a href="{{ route('system.audit') }}" class="nav-link {{ request()->routeIs('system.audit') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                Audit Trails
            </a>
            <a href="{{ route('reports.monthly') }}" class="nav-link {{ request()->routeIs('reports.monthly') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Monthly Reports
            </a>
            @endhasrole
        </nav>

        <!-- User footer -->
        <div class="p-4 border-t border-slate-200/60 bg-slate-50/30">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-xs font-bold shrink-0 border border-indigo-200">
                    {{ strtoupper(substr(auth()->user()->full_name ?? auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 truncate">{{ auth()->user()->full_name ?? auth()->user()->name }}</p>
                    <p class="text-[10px] uppercase tracking-wider text-slate-500">{{ auth()->user()->getRoleNames()->first() ?? 'IT Department' }}</p>
                </div>
                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Sign out"
                            class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- ── Main content ──────────────────────────────────────────────────── -->
    <div id="main-content" class="flex-1 ml-0 md:ml-64 flex flex-col min-h-screen max-w-full overflow-x-hidden transition-all duration-300 relative z-0">

        <!-- Top bar -->
        <header class="bg-white/80 backdrop-blur-xl border-b border-slate-200/60 px-4 md:px-8 py-4 flex items-center justify-between sticky top-0 z-10">
            <div class="flex items-center gap-2 md:gap-3">
                <button id="mobile-menu-btn" class="md:hidden text-slate-500 hover:text-slate-800 p-1 -ml-1 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    <svg class="w-6 h-6 leading-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h2 class="text-base font-semibold text-slate-900 truncate max-w-[150px] sm:max-w-xs">{{ $title ?? 'Dashboard' }}</h2>
            </div>
            <div class="flex items-center gap-4">
                @if(session('success'))
                <div class="text-emerald-600 text-sm font-medium flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
                @endif
                @if(session('error'))
                <div class="text-rose-600 text-sm font-medium flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') }}
                </div>
                @endif
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ now()->format('l, d F Y') }}
                </div>
            </div>
        </header>

        <!-- Page content -->
        <main class="flex-1 p-8">
            @yield('content')
        </main>

        <footer class="px-8 py-4 text-xs text-slate-400 border-t border-slate-200">
            &copy; {{ now()->year }} IT Asset Tracker — IT Department Internal System
        </footer>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobile-overlay');
        const menuBtn = document.getElementById('mobile-menu-btn');

        function toggleMenu() {
            sidebar.classList.toggle('-translate-x-full');
            if (overlay.classList.contains('hidden')) {
                overlay.classList.remove('hidden');
                // slight delay to allow display:block to apply before animating opacity
                setTimeout(() => overlay.classList.remove('opacity-0'), 10);
            } else {
                overlay.classList.add('opacity-0');
                setTimeout(() => overlay.classList.add('hidden'), 300);
            }
        }

        if(menuBtn) menuBtn.addEventListener('click', toggleMenu);
        if(overlay) overlay.addEventListener('click', toggleMenu);
    });
</script>

@stack('scripts')
</body>
</html>
