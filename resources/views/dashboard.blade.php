@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
<div class="mb-6">
    <h1 class="page-title text-2xl font-bold text-slate-800">Dashboard</h1>
    <p class="page-subtitle text-slate-500">Overview of IT asset metrics and recent activity.</p>
</div>

<!-- Key Metrics Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <a href="{{ route('assets.index') }}" class="block bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 transition-all duration-300 hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 p-6 flex items-start gap-4 hover:shadow-md transition-shadow">
        <div class="p-3 bg-gradient-to-br from-indigo-500 to-indigo-600 text-white shadow-lg shadow-indigo-500/30 rounded-xl">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
        </div>
        <div>
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Total Assets</p>
            <h3 class="text-2xl font-bold text-slate-900">{{ $totalAssets }}</h3>
        </div>
    </a>

    <a href="{{ route('assets.index', ['StatusFilter' => 'InStock']) }}" class="block bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 transition-all duration-300 hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 p-6 flex items-start gap-4 hover:shadow-md transition-shadow">
        <div class="p-3 bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-lg shadow-emerald-500/30 rounded-xl">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">In Stock</p>
            <h3 class="text-2xl font-bold text-slate-900">{{ $inStockAssets }}</h3>
        </div>
    </a>

    <a href="{{ route('projects.index') }}" class="block bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 transition-all duration-300 hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 p-6 flex items-start gap-4 hover:shadow-md transition-shadow">
        <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-lg shadow-blue-500/30 rounded-xl">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        </div>
        <div>
           <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Active Projects</p>
           <h3 class="text-2xl font-bold text-slate-900">{{ $activeProjects }}</h3>
        </div>
    </a>

    @hasrole('Admin|Staff')
    <a href="{{ route('allocations.index', ['StatusFilter' => 'active']) }}" class="block bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 transition-all duration-300 hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 p-6 flex items-start gap-4 hover:shadow-md transition-shadow">
        <div class="p-3 bg-gradient-to-br from-amber-500 to-amber-600 text-white shadow-lg shadow-amber-500/30 rounded-xl">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Overdue Alerts</p>
            <h3 class="text-2xl font-bold text-slate-900">{{ $overdueAllocations }}</h3>
        </div>
    </a>
    @endhasrole
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Log Widget -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 transition-all duration-300 hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-900">Recent Activity</h3>
                <a href="{{ route('system.audit') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">View All</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($recentLogs as $log)
                    <div class="p-6 flex items-start gap-4">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-slate-900">{{ $log->action_type === 'StatusChanged' ? 'Status Changed' : $log->action_type }} <span class="text-slate-400 font-normal">on</span> {{ $log->asset->name ?? 'Unknown Asset' }} <span class="text-slate-400 font-normal">by</span> <span class="text-indigo-600 font-semibold">{{ $log->user_name ?? 'an admin' }}</span></p>
                            <p class="text-sm text-slate-500 mt-1">{{ $log->description }}</p>
                            <p class="text-xs text-slate-400 mt-2">{{ $log->action_date->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-500 text-sm">
                        No recent activity recorded.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Links Widget -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 transition-all duration-300 hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 overflow-hidden">
             <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="text-base font-semibold text-slate-900">Quick Actions</h3>
            </div>
            <div class="p-4 flex flex-col gap-2">
                @hasrole('Admin|Staff')
                <a href="{{ route('assets.create') }}" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white hover:from-indigo-500 hover:to-indigo-600 font-medium py-2.5 px-4 rounded-xl shadow-md shadow-indigo-500/20 hover:shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-0.5 transition-all outline-none border-none">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Register Asset
                </a>
                <a href="{{ route('allocations.create') }}" class="w-full flex items-center justify-center gap-2 bg-white text-slate-700 hover:bg-slate-50 hover:text-slate-900 font-medium py-2.5 px-4 rounded-xl shadow-[0_2px_8px_-2px_rgba(0,0,0,0.05)] border border-slate-200/60 hover:-translate-y-0.5 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                    Allocate Hardware
                </a>
                @endhasrole
            </div>
        </div>
    </div>
</div>
@endsection
