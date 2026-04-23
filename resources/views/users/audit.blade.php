@extends('layouts.app', ['title' => 'System Audit Logs'])

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="page-title text-2xl font-bold text-slate-800">System Audit Trails</h1>
        <p class="page-subtitle text-slate-500">Global ledger of all system modifications and user activities.</p>
    </div>
</div>

<div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 transition-all duration-300 hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold">
                <tr>
                    <th class="px-6 py-4 w-48">Date & Time</th>
                    <th class="px-6 py-4">User</th>
                    <th class="px-6 py-4 w-32">Action</th>
                    <th class="px-6 py-4">Description</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-600">
                @forelse ($logs as $log)
                    <tr>
                        <td class="px-6 py-4 text-slate-900 font-medium">{{ \Carbon\Carbon::parse($log->timestamp)->timezone('Asia/Jakarta')->format('Y-m-d H:i:s') }} WIB</td>
                        <td class="px-6 py-4 font-medium">{{ $log->user ?? 'System' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">{{ $log->action_type }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-normal">
                            @if($log->log_type === 'asset')
                                @if($log->asset_name)
                                    Action performed on <a href="{{ route('assets.show', $log->entity_id) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">[{{ $log->asset_tag }}] {{ $log->asset_name }}</a> - {{ $log->description }}
                                @else
                                    Action performed on deleted or unknown asset (ID: {{ $log->entity_id }}) - {{ $log->description }}
                                @endif
                            @else
                                {{ $log->description }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                            No logs found in the system.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($logs->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
