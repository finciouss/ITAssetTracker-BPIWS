@extends('layouts.app', ['title' => 'Allocations'])

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="page-title text-2xl font-bold text-slate-800">Asset Allocations</h1>
        <p class="page-subtitle text-slate-500">Track which assets are assigned to whom or what project.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('allocations.export', request()->all()) }}" target="_blank" class="bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 hover:border-rose-300 font-medium py-2 px-4 rounded-md shadow-sm text-sm flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            Export PDF
        </a>
        <a href="{{ route('allocations.export_excel', request()->all()) }}" class="bg-white border border-emerald-200 text-emerald-600 hover:bg-emerald-50 hover:border-emerald-300 font-medium py-2 px-4 rounded-md shadow-sm text-sm flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            Export Excel
        </a>
        <a href="{{ route('allocations.create') }}" class="bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-medium py-2 px-4 rounded-lg shadow-md shadow-indigo-500/30 hover:shadow-lg hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all text-sm border-none inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Allocation
        </a>
    </div>
</div>

<!-- Status Filter Buttons -->
<div class="flex flex-wrap gap-2 mb-4">
    <a href="{{ route('allocations.index', ['StatusFilter' => 'active', 'SearchString' => request('SearchString')]) }}" 
       class="px-4 py-2 rounded-full text-sm font-medium transition-colors shadow-sm {{ request('StatusFilter', 'active') === 'active' ? 'bg-indigo-600 text-white border-transparent' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
        Active Allocations
    </a>
    <a href="{{ route('allocations.index', ['StatusFilter' => 'closed', 'SearchString' => request('SearchString')]) }}" 
       class="px-4 py-2 rounded-full text-sm font-medium transition-colors shadow-sm {{ request('StatusFilter') === 'closed' ? 'bg-indigo-600 text-white border-transparent' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
        Past Allocations
    </a>
    <a href="{{ route('allocations.index', ['StatusFilter' => 'all', 'SearchString' => request('SearchString')]) }}" 
       class="px-4 py-2 rounded-full text-sm font-medium transition-colors shadow-sm {{ request('StatusFilter') === 'all' ? 'bg-indigo-600 text-white border-transparent' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
        All Records
    </a>
</div>

<!-- Filters -->
<div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 mb-6">
    <form method="get" class="flex flex-col sm:flex-row gap-4 items-end">
        <div class="flex-1 w-full">
            <label for="SearchString" class="block text-sm font-medium text-slate-700 mb-1">Search Asset, Employee, or Project</label>
            <input type="text" name="SearchString" id="SearchString" value="{{ request('SearchString') }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" placeholder="Search..." />
        </div>
        <div class="w-full sm:w-48">
            <label for="StatusFilter" class="block text-sm font-medium text-slate-700 mb-1">Filter by Status</label>
            <select name="StatusFilter" id="StatusFilter" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all">
                <option value="all" {{ request('StatusFilter') === 'all' ? 'selected' : '' }}>All Statuses</option>
                <option value="active" {{ request('StatusFilter', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="closed" {{ request('StatusFilter') === 'closed' ? 'selected' : '' }}>Closed (All)</option>
                <option value="returned" {{ request('StatusFilter') === 'returned' ? 'selected' : '' }}>Returned</option>
                <option value="transferred" {{ request('StatusFilter') === 'transferred' ? 'selected' : '' }}>Transferred</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-medium py-2 px-4 rounded-lg shadow-md shadow-indigo-500/30 hover:shadow-lg hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all text-sm border-none">Filter</button>
            <a href="{{ route('allocations.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700 py-2 px-3">Clear</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 transition-all duration-300 hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold">
                <tr>
                    <th class="px-6 py-4 w-12 text-center">#</th>
                    <th class="px-6 py-4">Asset</th>
                    <th class="px-6 py-4">Assigned To</th>
                    <th class="px-6 py-4">Check Out</th>
                    <th class="px-6 py-4 text-center">
                        @php
                            $currentSort = request('sort_status');
                            $nextSort = 'returned_first';
                            if ($currentSort === 'returned_first') {
                                $nextSort = 'active_first';
                            } elseif ($currentSort === 'active_first') {
                                $nextSort = null;
                            }
                        @endphp
                        <a href="{{ request()->fullUrlWithQuery(['sort_status' => $nextSort]) }}" class="inline-flex items-center justify-center gap-1 hover:text-slate-800 transition-colors w-full">
                            Status
                            @if($currentSort === 'returned_first')
                                <svg class="w-3 h-3 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" title="Returned First"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" /></svg>
                            @elseif($currentSort === 'active_first')
                                <svg class="w-3 h-3 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" title="Active First"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" /></svg>
                            @else
                                <svg class="w-3 h-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" /></svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($allocations as $item)
                    <tr>
                        <td class="px-6 py-4 text-center font-medium text-slate-400">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('assets.show', $item->asset_id) }}" class="font-medium text-indigo-600 hover:text-indigo-800">
                                {{ $item->asset->name }}{{ $item->quantity > 1 ? ' x' . $item->quantity : '' }}
                            </a>
                            <div class="text-xs text-slate-500">{{ $item->asset->tag_number }}</div>
                            @if($item->quantity > 1)
                            <div class="mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-700">
                                    Qty: {{ $item->quantity }}
                                </span>
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($item->employee && $item->project)
                                <div class="font-medium text-slate-900">{{ $item->employee->full_name }}</div>
                                <div class="text-xs text-slate-500">Project: {{ $item->project->project_name }}</div>
                            @elseif($item->employee)
                                <div class="font-medium text-slate-900">{{ $item->employee->full_name }}</div>
                                <div class="text-xs text-slate-500">Employee</div>
                            @elseif($item->project)
                                <div class="font-medium text-slate-900">{{ $item->project->project_name }}</div>
                                <div class="text-xs text-slate-500">Project Only</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-slate-900">{{ $item->check_out_date->format('m/d/Y') }}</div>
                            @if($item->expected_return_date)
                                <div class="text-xs text-slate-500">Expected: {{ $item->expected_return_date->format('m/d/Y') }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if ($item->isActive())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Active</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                    {{ $item->is_transfer_out ? 'Transferred' : 'Returned' }}
                                </span>
                                <div class="text-xs text-slate-500 mt-1">{{ $item->actual_return_date->format('m/d/Y') }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            @if ($item->isActive())
                                <a href="{{ route('allocations.return', $item) }}" class="text-emerald-600 hover:text-emerald-800 text-sm font-medium">Return</a>
                                <span class="text-slate-300">|</span>
                                <a href="{{ route('allocations.transfer', $item) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Transfer</a>
                            @else
                                <span class="text-slate-400 text-sm italic">Closed</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                            No allocations found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
