@extends('layouts.app', ['title' => 'Edit Asset'])

@section('content')
@php
    $allocated  = $asset->allocatedQuantity();
    $available  = $asset->availableStock();
    $inMaint    = $asset->maintenance_quantity;
@endphp

<div class="mb-6">
    <h1 class="page-title text-2xl font-bold text-slate-800">Edit Asset</h1>
    <p class="page-subtitle text-slate-500">Update information for {{ $asset->tag_number }}</p>
</div>

{{-- Stock Overview Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6 max-w-2xl">
    <div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 p-5 flex items-start gap-3">
        <div class="p-2.5 bg-gradient-to-br from-slate-500 to-slate-600 text-white shadow-lg shadow-slate-500/30 rounded-xl shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
        </div>
        <div>
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-0.5">Total</p>
            <h3 class="text-xl font-bold text-slate-900">{{ $asset->stock }}</h3>
        </div>
    </div>
    <div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 p-5 flex items-start gap-3">
        <div class="p-2.5 bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-lg shadow-emerald-500/30 rounded-xl shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-0.5">Available</p>
            <h3 class="text-xl font-bold text-slate-900">{{ $available }}</h3>
        </div>
    </div>
    <div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 p-5 flex items-start gap-3">
        <div class="p-2.5 bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-lg shadow-blue-500/30 rounded-xl shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
        </div>
        <div>
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-0.5">Allocated</p>
            <h3 class="text-xl font-bold text-slate-900">{{ $allocated }}</h3>
        </div>
    </div>
    <div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 p-5 flex items-start gap-3">
        <div class="p-2.5 bg-gradient-to-br from-amber-500 to-amber-600 text-white shadow-lg shadow-amber-500/30 rounded-xl shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div>
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-0.5">Maint.</p>
            <h3 class="text-xl font-bold text-slate-900">{{ $inMaint }}</h3>
        </div>
    </div>
</div>

{{-- Edit Form --}}
<div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 p-6 max-w-2xl mb-6">
    <form method="post" action="{{ route('assets.update', $asset) }}">
        @csrf

        @if ($errors->any())
        <div class="text-sm text-rose-600 bg-rose-50 border border-rose-200 rounded-lg px-4 py-3 mb-6">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="tag_number" class="block text-sm font-medium text-slate-700 mb-1">Tag Number</label>
                <input type="text" name="tag_number" id="tag_number" value="{{ old('tag_number', $asset->tag_number) }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                @error('tag_number')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Name / Model</label>
                <input type="text" name="name" id="name" value="{{ old('name', $asset->name) }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                @error('name')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label for="purchase_date" class="block text-sm font-medium text-slate-700 mb-1">Purchase Date</label>
                <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date', $asset->purchase_date->format('Y-m-d')) }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                @error('purchase_date')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label for="stock" class="block text-sm font-medium text-slate-700 mb-1">Total Stock</label>
                <input type="number" name="stock" id="stock" value="{{ old('stock', $asset->stock) }}" min="{{ $allocated + $inMaint }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                <p class="text-xs text-slate-400 mt-1">Minimum: {{ $allocated + $inMaint }} (currently committed)</p>
                @error('stock')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label for="category" class="block text-sm font-medium text-slate-700 mb-1">Category</label>
                <input type="text" name="category" id="category" value="{{ old('category', $asset->category ?? '') }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" placeholder="e.g. Network, Laptop, Radio..." />
                @error('category')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label for="cost" class="block text-sm font-medium text-slate-700 mb-1">Cost (IDR)</label>
                <input type="number" name="cost" id="cost" value="{{ old('cost', $asset->cost ?? '') }}" min="0" step="1000" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                @error('cost')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div class="md:col-span-2">
                <label for="specifications" class="block text-sm font-medium text-slate-700 mb-1">Specifications & Serial Number</label>
                <textarea name="specifications" id="specifications" rows="4" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all">{{ old('specifications', $asset->specifications) }}</textarea>
                @error('specifications')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            {{-- Retire option --}}
            @if($asset->status !== \App\Models\Asset::STATUS_RETIRED)
            <div class="md:col-span-2 flex items-center gap-3 p-3 bg-rose-50 rounded-lg border border-rose-200">
                <input type="checkbox" name="retire" id="retire" value="1" class="rounded border-rose-300 text-rose-600" {{ old('retire') ? 'checked' : '' }}>
                <label for="retire" class="text-sm font-medium text-rose-700">Mark all units as Retired (permanent — cannot be reversed automatically)</label>
            </div>
            @endif
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
            <button type="submit" class="bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-medium py-2 px-4 rounded-lg shadow-md shadow-indigo-500/30 hover:shadow-lg hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all text-sm border-none">Save Changes</button>
            <a href="{{ route('assets.show', $asset) }}" class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium py-2 px-4 rounded-md shadow-sm text-sm">Cancel</a>
        </div>
    </form>
</div>

{{-- Maintenance Stock Panel --}}
@if($asset->status !== \App\Models\Asset::STATUS_RETIRED)
<div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-amber-200 ring-1 ring-amber-100 p-6 max-w-2xl">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-slate-800">Maintenance Stock</h3>
            <p class="text-xs text-slate-500">Set how many units are currently under maintenance. Max: <strong>{{ $available + $inMaint }}</strong> (available + current maintenance)</p>
        </div>
    </div>

    @if(session('success'))
    <div class="text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
    @endif

    @error('maintenance_quantity')
    <div class="text-sm text-rose-600 bg-rose-50 border border-rose-200 rounded-lg px-4 py-3 mb-4">{{ $message }}</div>
    @enderror

    <form method="post" action="{{ route('assets.maintenance', $asset) }}" class="flex items-end gap-4">
        @csrf
        <div class="flex-1">
            <label for="maintenance_quantity" class="block text-sm font-medium text-slate-700 mb-1">
                Units in Maintenance <span class="text-slate-400">(currently: {{ $inMaint }})</span>
            </label>
            <input type="number" name="maintenance_quantity" id="maintenance_quantity"
                value="{{ old('maintenance_quantity', $inMaint) }}"
                min="0" max="{{ $available + $inMaint }}"
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-amber-500 focus:bg-white focus:ring-2 focus:ring-amber-500/20 transition-all" />
        </div>
        <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-medium py-2.5 px-5 rounded-lg text-sm border-none transition-all">
            Update Maintenance
        </button>
    </form>
</div>
@endif

@endsection
