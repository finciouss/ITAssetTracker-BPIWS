@extends('layouts.app', ['title' => 'Delete User'])

@section('content')
<div class="mb-6">
    <h1 class="page-title text-2xl font-bold text-slate-800">Delete User</h1>
</div>

<div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 transition-all duration-300 hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 max-w-2xl overflow-hidden">
    <div class="p-6 bg-rose-50 border-b border-rose-100 flex items-start gap-4">
        <div class="p-2 bg-rose-100 text-rose-600 rounded-full shrink-0">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <div>
            <h3 class="text-lg font-semibold text-rose-900">Are you absolutely sure?</h3>
            <p class="text-sm text-rose-700 mt-1">This will permanently delete the user <strong>{{ $user->email }}</strong> and revoke their access to the system entirely.</p>
        </div>
    </div>
    
    <div class="p-6">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
            <div>
                <dt class="text-sm font-medium text-slate-500">Name</dt>
                <dd class="mt-1 text-sm text-slate-900 font-semibold">{{ $user->full_name ?? $user->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-slate-500">Email Address</dt>
                <dd class="mt-1 text-sm text-slate-900">{{ $user->email }}</dd>
            </div>
        </dl>

        <form method="post" action="{{ route('users.destroy', $user) }}" class="mt-8 flex items-center gap-3 pt-4 border-t border-slate-100">
            @csrf
            <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-medium py-2 px-4 rounded-md shadow-sm text-sm">Yes, Delete User</button>
            <a href="{{ route('users.index') }}" class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium py-2 px-4 rounded-md shadow-sm text-sm">Cancel</a>
        </form>
    </div>
</div>
@endsection
