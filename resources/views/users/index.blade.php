@extends('layouts.app', ['title' => 'System Users'])

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="page-title text-2xl font-bold text-slate-800">System Users</h1>
        <p class="page-subtitle text-slate-500">Manage individuals who have access to the tracking system.</p>
    </div>
    <div>
        <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-medium py-2 px-4 rounded-lg shadow-md shadow-indigo-500/30 hover:shadow-lg hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all text-sm border-none">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New System User
        </a>
    </div>
</div>

<div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 transition-all duration-300 hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold">
                <tr>
                    <th class="px-6 py-4">Full Name</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Department</th>
                    <th class="px-6 py-4">Roles</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($users as $item)
                    <tr>
                        <td class="px-6 py-4 font-medium text-slate-900">{{ $item->full_name ?? $item->name }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $item->email }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $item->department ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            @foreach($item->roles as $role)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('users.changePassword', $item) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Change Password</a>
                            @if(auth()->id() !== $item->id)
                                <span class="text-slate-300">|</span>
                                <a href="{{ route('users.delete', $item) }}" class="text-rose-600 hover:text-rose-800 text-sm font-medium">Delete</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
