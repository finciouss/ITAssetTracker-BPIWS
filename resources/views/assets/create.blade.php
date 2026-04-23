@extends('layouts.app', ['title' => 'Create Asset'])

@section('content')
<div class="mb-6">
    <h1 class="page-title text-2xl font-bold text-slate-800">Add New Asset</h1>
    <p class="page-subtitle text-slate-500">Register a new IT asset into the tracking system.</p>
</div>

<div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 transition-all duration-300 hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 p-6 max-w-2xl">
    <form method="post" action="{{ route('assets.store') }}">
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
                <input type="text" name="tag_number" id="tag_number" value="{{ old('tag_number') }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                @error('tag_number')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Name / Model</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                @error('name')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label for="purchase_date" class="block text-sm font-medium text-slate-700 mb-1">Purchase Date</label>
                <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date') }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                @error('purchase_date')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label for="stock" class="block text-sm font-medium text-slate-700 mb-1">Total Stock</label>
                <input type="number" name="stock" id="stock" value="{{ old('stock', 1) }}" min="1" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                <p class="text-xs text-slate-400 mt-1">How many units of this asset do you have?</p>
                @error('stock')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label for="category" class="block text-sm font-medium text-slate-700 mb-1">Category</label>
                <input type="text" name="category" id="category" value="{{ old('category') }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" placeholder="e.g. Network, Laptop, Radio..." />
                @error('category')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label for="cost" class="block text-sm font-medium text-slate-700 mb-1">Cost (IDR)</label>
                <input type="number" name="cost" id="cost" value="{{ old('cost') }}" min="0" step="1000" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                @error('cost')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div class="md:col-span-2">
                <label for="specifications" class="block text-sm font-medium text-slate-700 mb-1">Specifications & Serial Number</label>
                <textarea name="specifications" id="specifications" rows="4" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all">{{ old('specifications') }}</textarea>
                @error('specifications')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
            <button type="submit" class="bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-medium py-2 px-4 rounded-lg shadow-md shadow-indigo-500/30 hover:shadow-lg hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all text-sm border-none">Save Asset</button>
            <a href="{{ route('assets.index') }}" class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium py-2 px-4 rounded-md shadow-sm text-sm">Cancel</a>
        </div>
    </form>
</div>
@endsection
