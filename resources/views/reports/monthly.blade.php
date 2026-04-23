@extends('layouts.app', ['title' => 'Monthly Report'])

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="page-title text-2xl font-bold text-slate-800">Monthly Metrics</h1>
        <p class="page-subtitle text-slate-500">Comprehensive overview of IT asset movements and expenditure.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('reports.monthly.export_excel', request()->all()) }}" class="bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-500 hover:to-emerald-600 text-white font-medium py-2 px-6 rounded-lg shadow-md shadow-emerald-500/30 hover:shadow-lg hover:shadow-emerald-500/40 hover:-translate-y-0.5 transition-all text-sm border-none inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            Export Corporate Sheet (.xlsx)
        </a>
    </div>
</div>

<div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 mb-8 max-w-2xl bg-gradient-to-br from-white to-slate-50">
    <form method="get" action="{{ route('reports.monthly') }}" class="flex flex-col sm:flex-row items-end gap-4">
        <div class="flex-1 w-full">
            <label class="block text-sm font-semibold text-slate-700 mb-2">Target Month</label>
            <select name="month" class="w-full rounded-lg border border-slate-200 px-4 py-2.5 bg-white text-slate-700 focus:ring-2 focus:ring-indigo-500/50 shadow-sm transition">
                @for($m=1; $m<=12; $m++)
                    <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="flex-1 w-full">
            <label class="block text-sm font-semibold text-slate-700 mb-2">Target Year</label>
            <select name="year" class="w-full rounded-lg border border-slate-200 px-4 py-2.5 bg-white text-slate-700 focus:ring-2 focus:ring-indigo-500/50 shadow-sm transition">
                @for($y=date('Y')+1; $y>=2020; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <button type="submit" class="w-full sm:w-auto bg-slate-800 hover:bg-slate-700 text-white font-medium py-2.5 px-8 rounded-lg shadow-sm hover:shadow-md transition">Generate</button>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-[0_2px_12px_-4px_rgba(0,0,0,0.05)] border-l-4 border-l-blue-500 transition hover:-translate-y-1">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">New Procurement</p>
        <p class="text-4xl font-black text-slate-800 tracking-tight">{{ number_format($metrics['new_assets_count']) }} <span class="text-sm font-medium text-slate-400 tracking-normal">units</span></p>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-[0_2px_12px_-4px_rgba(0,0,0,0.05)] border-l-4 border-l-indigo-500 transition hover:-translate-y-1">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Check-outs</p>
        <p class="text-4xl font-black text-slate-800 tracking-tight">{{ number_format($metrics['allocations_count']) }} <span class="text-sm font-medium text-slate-400 tracking-normal">movements</span></p>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-[0_2px_12px_-4px_rgba(0,0,0,0.05)] border-l-4 border-l-purple-500 transition hover:-translate-y-1">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Returned</p>
        <p class="text-4xl font-black text-slate-800 tracking-tight">{{ number_format($metrics['returns_count']) }} <span class="text-sm font-medium text-slate-400 tracking-normal">recoveries</span></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
    <!-- New Assets Table -->
    <div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-800">New Hardware Acquired</h3>
            <p class="text-xs text-slate-500 mt-1">Found {{ $newAssets->count() }} records for {{ $selectedDate->format('F Y') }}</p>
        </div>
        <div class="overflow-x-auto max-h-[400px]">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-white sticky top-0 border-b border-slate-200 text-slate-500 font-semibold z-10">
                    <tr>
                        <th class="px-6 py-3">Asset</th>
                        <th class="px-6 py-3">Cost</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600">
                    @forelse($newAssets as $asset)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3">
                                <span class="block font-medium text-slate-900">{{ $asset->name }}</span>
                                <span class="block text-xs text-slate-400">#{{ $asset->tag_number }}</span>
                            </td>
                            <td class="px-6 py-3 font-medium text-emerald-600">
                                @if($asset->cost)
                                    Rp {{ number_format($asset->cost, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="px-6 py-8 text-center text-slate-400 italic">No new hardware procured.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Returns Table -->
    <div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-800">Inventory Returns</h3>
            <p class="text-xs text-slate-500 mt-1">Found {{ $returns->count() }} records for {{ $selectedDate->format('F Y') }}</p>
        </div>
        <div class="overflow-x-auto max-h-[400px]">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-white sticky top-0 border-b border-slate-200 text-slate-500 font-semibold z-10">
                    <tr>
                        <th class="px-6 py-3">Asset</th>
                        <th class="px-6 py-3">Returned From</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600">
                    @forelse($returns as $alloc)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3">
                                <span class="block font-medium text-slate-900">{{ $alloc->asset->name ?? 'Deleted' }}</span>
                                <span class="block text-xs text-slate-400">#{{ $alloc->asset->tag_number ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-3">
                                @if($alloc->project)
                                    <span class="block text-slate-900">{{ $alloc->project->project_name }} <span class="text-xs text-slate-400">(Project)</span></span>
                                @endif
                                @if($alloc->employee)
                                    <span class="block text-slate-900">{{ $alloc->employee->full_name }} <span class="text-xs text-slate-400">(Emp)</span></span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="px-6 py-8 text-center text-slate-400 italic">No assets returned.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
