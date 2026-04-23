@extends('layouts.app', ['title' => 'Asset Details'])

@section('content')
@php
    $allocated  = $asset->allocatedQuantity();
    $available  = $asset->availableStock();
    $inMaint    = $asset->maintenance_quantity;

    $badgeClass = match($asset->status) {
        \App\Models\Asset::STATUS_IN_STOCK    => "bg-emerald-100 text-emerald-800",
        \App\Models\Asset::STATUS_ALLOCATED   => "bg-blue-100 text-blue-800",
        \App\Models\Asset::STATUS_MAINTENANCE => "bg-amber-100 text-amber-800",
        \App\Models\Asset::STATUS_RETIRED     => "bg-rose-100 text-rose-800",
        default                               => "bg-slate-100 text-slate-800"
    };
@endphp

{{-- Header --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="page-title text-2xl font-bold text-slate-800">{{ $asset->name }}</h1>
        <p class="page-subtitle text-slate-500 font-mono text-sm">{{ $asset->tag_number }}</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        @hasrole('Admin|Staff')
            <a href="{{ route('assets.edit', $asset) }}" class="bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-medium py-2 px-4 rounded-lg shadow-md shadow-indigo-500/30 hover:shadow-lg hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all text-sm border-none">Edit Asset</a>
            @if($available > 0)
            <a href="{{ route('allocations.create', ['asset_id' => $asset->id]) }}" class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium py-2 px-4 rounded-md shadow-sm text-sm">Allocate</a>
            @endif
        @endhasrole
        <a href="{{ route('assets.index') }}" class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium py-2 px-4 rounded-md shadow-sm text-sm">Back to List</a>
    </div>
</div>

{{-- Stock Overview Cards (dashboard style) --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 p-5 flex items-start gap-4">
        <div class="p-3 bg-gradient-to-br from-slate-500 to-slate-600 text-white shadow-lg shadow-slate-500/30 rounded-xl shrink-0">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
        </div>
        <div>
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Total Stock</p>
            <h3 class="text-2xl font-bold text-slate-900">{{ $asset->stock }}</h3>
        </div>
    </div>

    <div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 p-5 flex items-start gap-4">
        <div class="p-3 bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-lg shadow-emerald-500/30 rounded-xl shrink-0">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Available</p>
            <h3 class="text-2xl font-bold text-slate-900">{{ $available }}</h3>
        </div>
    </div>

    <div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 p-5 flex items-start gap-4">
        <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-lg shadow-blue-500/30 rounded-xl shrink-0">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
        </div>
        <div>
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Allocated</p>
            <h3 class="text-2xl font-bold text-slate-900">{{ $allocated }}</h3>
        </div>
    </div>

    <div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 p-5 flex items-start gap-4">
        <div class="p-3 bg-gradient-to-br from-amber-500 to-amber-600 text-white shadow-lg shadow-amber-500/30 rounded-xl shrink-0">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div>
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Maintenance</p>
            <h3 class="text-2xl font-bold text-slate-900">{{ $inMaint }}</h3>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left: Asset Info --}}
    <div class="lg:col-span-1 space-y-6">

        {{-- Asset Information --}}
        <div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-900 uppercase tracking-wide">Asset Information</h3>
            </div>
            <div class="p-6">
                <dl class="space-y-4">
                    <div>
                        <dt class="text-xs font-medium text-slate-400 uppercase tracking-wide">Tag Number</dt>
                        <dd class="mt-1 text-sm text-slate-900 font-mono font-semibold">{{ $asset->tag_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-400 uppercase tracking-wide">Name / Model</dt>
                        <dd class="mt-1 text-sm text-slate-900 font-medium">{{ $asset->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-400 uppercase tracking-wide">Category</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $asset->category ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-400 uppercase tracking-wide">Purchase Date</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $asset->purchase_date->format('d F Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-400 uppercase tracking-wide">Cost (per unit)</dt>
                        <dd class="mt-1 text-sm text-slate-900">
                            @if($asset->cost)
                                Rp {{ number_format($asset->cost, 0, ',', '.') }}
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    @if($asset->cost && $asset->stock > 1)
                    <div>
                        <dt class="text-xs font-medium text-slate-400 uppercase tracking-wide">Total Value ({{ $asset->stock }} units)</dt>
                        <dd class="mt-1 text-sm font-semibold text-indigo-700">
                            Rp {{ number_format($asset->cost * $asset->stock, 0, ',', '.') }}
                        </dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-xs font-medium text-slate-400 uppercase tracking-wide">Status</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">{{ $asset->status }}</span>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Specifications --}}
        <div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-900 uppercase tracking-wide">Specifications</h3>
            </div>
            <div class="p-6">
                <p class="text-sm text-slate-700 whitespace-pre-wrap leading-relaxed">{{ empty($asset->specifications) ? 'None provided.' : $asset->specifications }}</p>
            </div>
        </div>

    </div>

    {{-- Right: History --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Active Allocations --}}
        <div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-900 uppercase tracking-wide">Active Allocations</h3>
                @if($allocated > 0)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ $allocated }} unit(s) out</span>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold">
                        <tr>
                            <th class="px-6 py-3">Qty</th>
                            <th class="px-6 py-3">Check Out</th>
                            <th class="px-6 py-3">Expected Return</th>
                            <th class="px-6 py-3">Assigned To</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php $activeAllocs = $asset->allocations->filter(fn($a) => $a->isActive()); @endphp
                        @forelse ($activeAllocs as $alloc)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-indigo-100 text-indigo-700">
                                        x{{ $alloc->quantity }}
                                    </span>
                                </td>
                                <td class="px-6 py-3">{{ $alloc->check_out_date->format('d/m/Y') }}</td>
                                <td class="px-6 py-3">
                                    @if($alloc->expected_return_date)
                                        <span class="text-slate-500">{{ $alloc->expected_return_date->format('d/m/Y') }}</span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    @if($alloc->project && $alloc->employee)
                                        <div>{{ $alloc->project->project_name }}</div>
                                        <div class="text-xs text-slate-400">{{ $alloc->employee->full_name }}</div>
                                    @elseif($alloc->project)
                                        {{ $alloc->project->project_name }}
                                    @elseif($alloc->employee)
                                        {{ $alloc->employee->full_name }}
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-slate-400 text-sm">No active allocations.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Full Allocation Trail --}}
        <div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-900 uppercase tracking-wide">Full Allocation History</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold">
                        <tr>
                            <th class="px-6 py-3">Qty</th>
                            <th class="px-6 py-3">Check Out</th>
                            <th class="px-6 py-3">Return / Status</th>
                            <th class="px-6 py-3">Project</th>
                            <th class="px-6 py-3">Employee</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($asset->allocations->sortByDesc('check_out_date') as $alloc)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-indigo-50 text-indigo-600">x{{ $alloc->quantity }}</span>
                                </td>
                                <td class="px-6 py-3">{{ $alloc->check_out_date->format('d/m/Y') }}</td>
                                <td class="px-6 py-3">
                                    @if($alloc->actual_return_date)
                                        <span class="text-emerald-600 font-medium">{{ $alloc->actual_return_date->format('d/m/Y') }}</span>
                                        @if($alloc->is_transfer_out)
                                            <span class="ml-1 text-xs text-indigo-500">(Transfer)</span>
                                        @endif
                                    @elseif($alloc->expected_return_date)
                                        <span class="text-slate-400">({{ $alloc->expected_return_date->format('d/m/Y') }})</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Active</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3">{{ $alloc->project->project_name ?? '—' }}</td>
                                <td class="px-6 py-3">{{ $alloc->employee->full_name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-slate-400 text-sm">No allocations recorded.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Audit Logs --}}
        <div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-900 uppercase tracking-wide">Audit Logs</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold">
                        <tr>
                            <th class="w-44 px-6 py-3">Date & Time</th>
                            <th class="w-32 px-6 py-3">Action</th>
                            <th class="px-6 py-3">Description</th>
                            <th class="px-6 py-3">By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($asset->logs->sortByDesc('created_at') as $log)
                           <tr class="hover:bg-slate-50">
                                <td class="px-6 py-3 text-slate-500">{{ $log->created_at->format('d M Y H:i') }}</td>
                                <td class="px-6 py-3">
                                    @php
                                        $actionBadgeClass = match($log->action_type) {
                                            'Created'       => "bg-emerald-100 text-emerald-800",
                                            'Allocated'     => "bg-blue-100 text-blue-800",
                                            'Returned'      => "bg-purple-100 text-purple-800",
                                            'Transferred'   => "bg-indigo-100 text-indigo-800",
                                            'StatusChanged' => "bg-amber-100 text-amber-800",
                                            default         => "bg-slate-100 text-slate-800"
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $actionBadgeClass }}">{{ $log->action_type }}</span>
                                </td>
                                <td class="px-6 py-3 text-slate-600 whitespace-normal max-w-xs">{{ $log->description }}</td>
                                <td class="px-6 py-3 text-slate-500">{{ $log->user_name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-slate-400 text-sm">No logs recorded.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
