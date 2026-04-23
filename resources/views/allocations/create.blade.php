@extends('layouts.app', ['title' => 'New Allocation'])

@section('content')
<div class="mb-6">
    <h1 class="page-title text-2xl font-bold text-slate-800">Allocate Asset</h1>
    <p class="page-subtitle text-slate-500">Assign available units of an asset to an employee or project.</p>
</div>

{{-- Hidden stock data for JS --}}
@php
    $stockData = $assets->mapWithKeys(fn($a) => [$a->id => $a->availableStock()])->toJson();
@endphp

<div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 transition-all duration-300 hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 p-6 max-w-2xl">
    <form method="post" action="{{ route('allocations.store') }}">
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

            {{-- Asset Select --}}
            <div class="md:col-span-2">
                <label for="asset_id" class="block text-sm font-medium text-slate-700 mb-1">Select Asset</label>
                <select name="asset_id" id="asset_id" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    <option value="">-- Choose Asset --</option>
                    @foreach($assets as $a)
                        <option value="{{ $a->id }}" data-available="{{ $a->availableStock() }}" {{ old('asset_id') == $a->id ? 'selected' : '' }}>
                            {{ $a->tag_number }} – {{ $a->name }} ({{ $a->availableStock() }} available)
                        </option>
                    @endforeach
                </select>
                @error('asset_id')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            {{-- Quantity --}}
            <div class="md:col-span-2">
                <label for="quantity" class="block text-sm font-medium text-slate-700 mb-1">Quantity to Allocate</label>
                <div class="flex items-center gap-4">
                    <input type="number" name="quantity" id="quantity"
                        value="{{ old('quantity', 1) }}"
                        min="1" max="9999"
                        class="w-40 rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                    <p id="stock_hint" class="text-sm text-slate-500">Select an asset to see available stock.</p>
                </div>
                @error('quantity')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            {{-- Employee --}}
            <div>
                <label for="employee_id" class="block text-sm font-medium text-slate-700 mb-1">Assign to Employee</label>
                <select name="employee_id" id="employee_id" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    <option value="">-- None (Project Only) --</option>
                    @foreach($employees as $e)
                        <option value="{{ $e->id }}" {{ old('employee_id') == $e->id ? 'selected' : '' }}>
                            {{ $e->first_name }} {{ $e->last_name }} ({{ $e->department }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Project --}}
            <div>
                <label for="project_id" class="block text-sm font-medium text-slate-700 mb-1">Assign to Project</label>
                <select name="project_id" id="project_id" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    <option value="">-- None (Employee Only) --</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->project_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Dates --}}
            <div>
                <label for="check_out_date" class="block text-sm font-medium text-slate-700 mb-1">Check Out Date</label>
                <input type="date" name="check_out_date" id="check_out_date" value="{{ old('check_out_date', now()->format('Y-m-d')) }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                @error('check_out_date')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label for="expected_return_date" class="block text-sm font-medium text-slate-700 mb-1">Expected Return Date (Optional)</label>
                <input type="date" name="expected_return_date" id="expected_return_date" value="{{ old('expected_return_date') }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                @error('expected_return_date')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div class="md:col-span-2">
                <label for="notes" class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                <textarea name="notes" id="notes" rows="3" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
            <button type="submit" class="bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-medium py-2 px-4 rounded-lg shadow-md shadow-indigo-500/30 hover:shadow-lg hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all text-sm border-none">Allocate Asset</button>
            <a href="{{ route('allocations.index') }}" class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium py-2 px-4 rounded-md shadow-sm text-sm">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const stockMap = {!! $stockData !!};

document.addEventListener('DOMContentLoaded', function() {
    new TomSelect('#asset_id', {
        placeholder: '-- Choose Asset --',
        allowEmptyOption: true,
        onChange: function(val) {
            const qty = document.getElementById('quantity');
            const hint = document.getElementById('stock_hint');
            if (val && stockMap[val] !== undefined) {
                const avail = stockMap[val];
                qty.max = avail;
                hint.textContent = `Available stock: ${avail} unit(s)`;
                hint.className = avail > 0
                    ? 'text-sm text-emerald-600 font-medium'
                    : 'text-sm text-rose-600 font-medium';
                if (parseInt(qty.value) > avail) qty.value = avail;
            } else {
                qty.max = 9999;
                hint.textContent = 'Select an asset to see available stock.';
                hint.className = 'text-sm text-slate-500';
            }
        }
    });
    new TomSelect('#employee_id', { placeholder: '-- None (Project Only) --', allowEmptyOption: true });
    new TomSelect('#project_id',  { placeholder: '-- None (Employee Only) --', allowEmptyOption: true });

    // Trigger on page load if asset_id pre-selected (old input)
    const sel = document.getElementById('asset_id');
    if (sel && sel.value) sel.dispatchEvent(new Event('change'));
});
</script>
@endpush
