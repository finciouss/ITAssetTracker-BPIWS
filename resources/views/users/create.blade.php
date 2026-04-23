@extends('layouts.app', ['title' => 'Create System User'])

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="page-title text-2xl font-bold text-slate-800">Add New System User</h1>
        <p class="page-subtitle text-slate-500">Create a new login account and assign their access role.</p>
    </div>
</div>

<div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 transition-all duration-300 hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 p-6 max-w-2xl">
    <form method="post" action="{{ route('users.store') }}">
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
            
            <div class="md:col-span-2">
                <label for="role" class="block text-sm font-medium text-slate-700 mb-1">System Role</label>
                <select name="role" id="role" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    <option value="" disabled selected>Select a role...</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                            {{ $role->name }} 
                            @if($role->name === 'Admin') (Full Access) @endif
                            @if($role->name === 'Staff') (Manage Tracker, Cannot Access Users) @endif
                            @if($role->name === 'Viewer') (Read-only Assets view) @endif
                        </option>
                    @endforeach
                </select>
                @error('role')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Username (Login ID)</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" required />
                @error('name')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>
            
            <div>
                <label for="full_name" class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" required />
                @error('full_name')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" required />
                @error('email')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label for="department" class="block text-sm font-medium text-slate-700 mb-1">Department</label>
                <input type="text" name="department" id="department" value="{{ old('department') }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                @error('department')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                <input type="password" name="password" id="password" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" required />
                @error('password')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" required />
            </div>

        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
            <button type="submit" class="bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-medium py-2 px-4 rounded-lg shadow-md shadow-indigo-500/30 hover:shadow-lg hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all text-sm border-none">Create System User</button>
            <a href="{{ route('users.index') }}" class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium py-2 px-4 rounded-md shadow-sm text-sm transition-all duration-200 hover:-translate-y-0.5">Cancel</a>
        </div>
    </form>
</div>
@endsection
