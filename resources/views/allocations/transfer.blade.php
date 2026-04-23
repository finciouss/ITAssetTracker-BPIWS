@extends('layouts.app', ['title' => 'Transfer Asset'])

@section('content')
<div class="mb-6">
    <h1 class="page-title text-2xl font-bold text-slate-800">Transfer Asset</h1>
    <p class="page-subtitle text-slate-500">Reassign an already allocated asset directly to a new user or project.</p>
</div>

<div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 transition-all duration-300 hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 p-6 max-w-2xl">
    
    <div class="mb-6 bg-slate-50 p-4 rounded-lg border border-slate-100">
        <h3 class="text-sm font-semibold text-slate-900 mb-3">Current Allocation</h3>
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
        </dl>
    </div>

    <form method="post" action="{{ route('allocations.processTransfer', $allocation) }}">
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

        <h3 class="text-sm font-semibold text-slate-900 mb-4 border-b border-slate-200 pb-2">New Binding</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="employee_id" class="block text-sm font-medium text-slate-700 mb-1">Transfer to Employee</label>
                <select name="employee_id" id="employee_id" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    <option value="">-- None (Project Only) --</option>
                    @foreach($employees as $e)
                        <option value="{{ $e->id }}" {{ old('employee_id') == $e->id ? 'selected' : '' }}>
                            {{ $e->first_name }} {{ $e->last_name }} ({{ $e->department }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="project_id" class="block text-sm font-medium text-slate-700 mb-1">Transfer to Project</label>
                <select name="project_id" id="project_id" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    <option value="">-- None (Employee Only) --</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->project_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="transfer_date" class="block text-sm font-medium text-slate-700 mb-1">Transfer Date</label>
                <input type="date" name="transfer_date" id="transfer_date" value="{{ old('transfer_date', now()->format('Y-m-d')) }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                @error('transfer_date')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label for="expected_return_date" class="block text-sm font-medium text-slate-700 mb-1">New Expected Return (Optional)</label>
                <input type="date" name="expected_return_date" id="expected_return_date" value="{{ old('expected_return_date') }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                @error('expected_return_date')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div class="md:col-span-2">
                <label for="notes" class="block text-sm font-medium text-slate-700 mb-1">Transfer Notes</label>
                <textarea name="notes" id="notes" rows="3" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all">{{ old('notes') }}</textarea>
                @error('notes')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
            <button type="submit" class="bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-medium py-2 px-4 rounded-lg shadow-md shadow-indigo-500/30 hover:shadow-lg hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all text-sm border-none">Transfer Asset</button>
            <a href="{{ route('allocations.index') }}" class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium py-2 px-4 rounded-md shadow-sm text-sm">Cancel</a>
        </div>
    </form>
</div>
@endsection
