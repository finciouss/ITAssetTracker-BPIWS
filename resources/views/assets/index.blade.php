@extends('layouts.app', ['title' => 'Assets'])

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="page-title text-2xl font-bold text-slate-800">IT Assets</h1>
        <p class="page-subtitle text-slate-500">Manage all hardware and software computing assets.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('assets.export', request()->all()) }}" target="_blank" class="bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 hover:border-rose-300 font-medium py-2 px-4 rounded-md shadow-sm text-sm flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            Export PDF
        </a>
        <a href="{{ route('assets.export_excel', request()->all()) }}" class="bg-white border border-emerald-200 text-emerald-600 hover:bg-emerald-50 hover:border-emerald-300 font-medium py-2 px-4 rounded-md shadow-sm text-sm flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            Export Excel
        </a>
        @hasrole('Admin|Staff')
        <a href="{{ route('assets.create') }}" class="bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-medium py-2 px-4 rounded-lg shadow-md shadow-indigo-500/30 hover:shadow-lg hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all text-sm border-none inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add New Asset
        </a>
        @endhasrole
    </div>
</div>

<!-- Filters -->
<div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 mb-6">
    <form method="get" class="flex flex-col sm:flex-row gap-4 items-end">
        <div class="flex-1 w-full">
            <label for="SearchString" class="block text-sm font-medium text-slate-700 mb-1">Search Tag or Name</label>
            <input type="text" name="SearchString" id="SearchString" value="{{ request('SearchString') }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" placeholder="Search..." />
        </div>
        <div class="w-full sm:w-48">
            <label for="StatusFilter" class="block text-sm font-medium text-slate-700 mb-1">Filter by Status</label>
            <select name="StatusFilter" id="StatusFilter" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all">
                <option value="">All Statuses</option>
                @foreach(\App\Models\Asset::statusOptions() as $key => $label)
                    <option value="{{ $key }}" {{ request('StatusFilter') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-medium py-2 px-4 rounded-lg shadow-md shadow-indigo-500/30 hover:shadow-lg hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all text-sm border-none">Filter</button>
            <a href="{{ route('assets.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700 py-2 px-3">Clear</a>
        </div>
    </form>
</div>

<!-- Data Table -->
<div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 transition-all duration-300 hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold">
                <tr>
                    <th class="px-6 py-4">Tag Number</th>
                    <th class="px-6 py-4">Name</th>
                    <th class="px-6 py-4 text-center">
                        @php
                            $currentSortDate = request('sort_date');
                            $nextSortDate = 'oldest';
                            if ($currentSortDate === 'oldest' || (!request('sort_date') && !request('sort_status'))) {
                                $nextSortDate = 'newest';
                            }
                        @endphp
                        <a href="{{ request()->fullUrlWithQuery(['sort_date' => $nextSortDate, 'sort_status' => null]) }}" class="inline-flex items-center justify-center gap-1 hover:text-slate-800 transition-colors w-full">
                            Purchase Date
                            @if($currentSortDate === 'oldest')
                                <svg class="w-3 h-3 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" title="Oldest First"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" /></svg>
                            @elseif($currentSortDate === 'newest' || (!request('sort_date') && !request('sort_status')))
                                <svg class="w-3 h-3 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" title="Newest First"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                            @else
                                <svg class="w-3 h-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" /></svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-4 text-center">
                        @php
                            $currentSortStatus = request('sort_status');
                            $nextSortStatus = 'asc';
                            if ($currentSortStatus === 'asc') {
                                $nextSortStatus = 'desc';
                            } elseif ($currentSortStatus === 'desc') {
                                $nextSortStatus = null;
                            }
                        @endphp
                        <a href="{{ request()->fullUrlWithQuery(['sort_status' => $nextSortStatus, 'sort_date' => null]) }}" class="inline-flex items-center justify-center gap-1 hover:text-slate-800 transition-colors w-full">
                            Status
                            @if($currentSortStatus === 'asc')
                                <svg class="w-3 h-3 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" /></svg>
                            @elseif($currentSortStatus === 'desc')
                                <svg class="w-3 h-3 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                            @else
                                <svg class="w-3 h-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" /></svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($assets as $item)
                    <tr>
                        <td class="px-6 py-4 font-medium text-slate-900">{{ $item->tag_number }}</td>
                        <td class="px-6 py-4">{{ $item->name }}</td>
                        <td class="px-6 py-4 text-center">{{ $item->purchase_date->format('m/d/Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $badgeClass = match($item->status) {
                                    \App\Models\Asset::STATUS_IN_STOCK => "bg-emerald-100 text-emerald-800",
                                    \App\Models\Asset::STATUS_ALLOCATED => "bg-blue-100 text-blue-800",
                                    \App\Models\Asset::STATUS_MAINTENANCE => "bg-amber-100 text-amber-800",
                                    \App\Models\Asset::STATUS_RETIRED => "bg-rose-100 text-rose-800",
                                    default => "bg-slate-100 text-slate-800"
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center space-x-2">
                            <a href="{{ route('assets.show', $item) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Details</a>
                            @hasrole('Admin|Staff')
                                <span class="text-slate-300">|</span>
                                <a href="{{ route('assets.edit', $item) }}" class="text-slate-600 hover:text-slate-900 text-sm font-medium">Edit</a>
                                <span class="text-slate-300">|</span>
                                <a href="{{ route('assets.delete', $item) }}" class="text-rose-600 hover:text-rose-800 text-sm font-medium">Delete</a>
                            @endhasrole
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                            No assets found matching the criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
