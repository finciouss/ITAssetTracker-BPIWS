@extends('layouts.app', ['title' => 'Return Asset'])

@section('content')
<div class="mb-6">
    <h1 class="page-title text-2xl font-bold text-slate-800">Return Asset</h1>
    <p class="page-subtitle text-slate-500">Record an asset returning from allocation back to inventory.</p>
</div>

<div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 transition-all duration-300 hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 p-6 max-w-2xl">
    
    <div class="mb-6 bg-slate-50 p-4 rounded-lg border border-slate-100">
        <h3 class="text-sm font-semibold text-slate-900 mb-3">Allocation Details</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-slate-500 font-medium">Asset</dt>
                <dd class="text-slate-900 font-semibold mt-0.5">{{ $allocation->asset->tag_number }} - {{ $allocation->asset->name }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 font-medium">Currently Assigned To</dt>
                <dd class="text-slate-900 mt-0.5">
                    {{ $allocation->employee ? $allocation->employee->first_name . ' ' . $allocation->employee->last_name : 'No Employee' }} / 
                    {{ $allocation->project ? $allocation->project->project_name : 'No Project' }}
                </dd>
            </div>
            <div>
                <dt class="text-slate-500 font-medium">Check Out Date</dt>
                <dd class="text-slate-900 mt-0.5">{{ $allocation->check_out_date->format('m/d/Y') }}</dd>
            </div>
        </dl>
    </div>

    <form method="post" action="{{ route('allocations.processReturn', $allocation) }}">
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

        <div class="grid grid-cols-1 gap-6 mb-6">
            <div>
                <label for="actual_return_date" class="block text-sm font-medium text-slate-700 mb-1">Return Date</label>
                <input type="date" name="actual_return_date" id="actual_return_date" value="{{ old('actual_return_date', now()->format('Y-m-d')) }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                @error('actual_return_date')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-slate-700 mb-1">Return Condition / Notes</label>
                <textarea name="notes" id="notes" rows="4" placeholder="Note the condition of the asset upon return..." class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all">{{ old('notes') }}</textarea>
                @error('notes')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2 px-4 rounded-md shadow-sm text-sm">Process Return</button>
            <a href="{{ route('allocations.index') }}" class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium py-2 px-4 rounded-md shadow-sm text-sm">Cancel</a>
        </div>
    </form>
</div>
@endsection
