@extends('layouts.app', ['title' => 'Create Employee'])

@section('content')
<div class="mb-6">
    <h1 class="page-title text-2xl font-bold text-slate-800">Add New Employee</h1>
    <p class="page-subtitle text-slate-500">Register a new employee into the system.</p>
</div>

<div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 transition-all duration-300 hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 p-6 max-w-2xl">
    <form method="post" action="{{ route('employees.store') }}">
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
                <label for="employee_number" class="block text-sm font-medium text-slate-700 mb-1">Employee Number</label>
                <input type="text" name="employee_number" id="employee_number" value="{{ old('employee_number') }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                @error('employee_number')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>
            
            <div>
                <label for="department" class="block text-sm font-medium text-slate-700 mb-1">Department</label>
                <input type="text" name="department" id="department" value="{{ old('department') }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                @error('department')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label for="first_name" class="block text-sm font-medium text-slate-700 mb-1">First Name</label>
                <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                @error('first_name')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label for="last_name" class="block text-sm font-medium text-slate-700 mb-1">Last Name</label>
                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                @error('last_name')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div class="md:col-span-2">
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                @error('email')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
            <button type="submit" class="bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-medium py-2 px-4 rounded-lg shadow-md shadow-indigo-500/30 hover:shadow-lg hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all text-sm border-none">Save Employee</button>
            <a href="{{ route('employees.index') }}" class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium py-2 px-4 rounded-md shadow-sm text-sm">Cancel</a>
        </div>
    </form>
</div>
@endsection
