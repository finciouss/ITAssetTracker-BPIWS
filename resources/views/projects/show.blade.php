@extends('layouts.app', ['title' => 'Project Details'])

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="page-title text-2xl font-bold text-slate-800">Project Details</h1>
        <p class="page-subtitle text-slate-500">View project information and allocated assets.</p>
    </div>
    <div class="space-x-2 flex items-center">
        <a href="{{ route('projects.export', $project) }}" target="_blank" class="bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 hover:border-rose-300 font-medium py-2 px-4 rounded-md shadow-sm text-sm flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            Export PDF
        </a>
        <a href="{{ route('projects.export_excel', $project) }}" class="bg-white border border-emerald-200 text-emerald-600 hover:bg-emerald-50 hover:border-emerald-300 font-medium py-2 px-4 rounded-md shadow-sm text-sm flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            Export Excel
        </a>
        @hasrole('Admin|Staff')
            <a href="{{ route('projects.edit', $project) }}" class="bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-medium py-2 px-4 rounded-lg shadow-md shadow-indigo-500/30 hover:shadow-lg hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all text-sm border-none">Edit Project</a>
        @endhasrole
        <a href="{{ route('projects.index') }}" class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium py-2 px-4 rounded-md shadow-sm text-sm">Back to List</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Info -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 transition-all duration-300 hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="text-base font-semibold text-slate-900">Project Information</h3>
            </div>
            <div class="p-6">
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-slate-500">Project Name</dt>
                        <dd class="mt-1 text-sm text-slate-900 font-semibold">{{ $project->project_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-slate-500">Location / Site</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $project->location ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-slate-500">Duration</dt>
                        <dd class="mt-1 text-sm text-slate-900">
                            {{ $project->start_date ? $project->start_date->format('m/d/Y') : '-' }} to 
                            {{ $project->end_date ? $project->end_date->format('m/d/Y') : '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-slate-500">Status</dt>
                        <dd class="mt-1">
                            @if ($project->status === \App\Models\Project::STATUS_COMPLETED)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Completed</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Ongoing</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- Allocations -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 transition-all duration-300 hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="text-base font-semibold text-slate-900">Assets Allocated to Project</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold">
                        <tr>
                            <th class="px-6 py-4">Asset</th>
                            <th class="px-6 py-4">Employee</th>
                            <th class="px-6 py-4">Check Out</th>
                            <th class="px-6 py-4">Return Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($project->allocations as $alloc)
                            <tr>
                                <td class="px-6 py-4">
                                    <a href="{{ route('assets.show', $alloc->asset_id) }}" class="font-medium text-indigo-600 hover:text-indigo-800">
                                        {{ $alloc->asset->name }}
                                    </a>
                                    <div class="text-xs text-slate-500">{{ $alloc->asset->tag_number }}</div>
                                </td>
                                <td class="px-6 py-4">{{ $alloc->employee->full_name ?? ($alloc->employee->first_name ?? "-") }}</td>
                                <td class="px-6 py-4">{{ $alloc->check_out_date->format('m/d/Y') }}</td>
                                <td class="px-6 py-4">
                                    @if($alloc->actual_return_date)
                                         <span class="text-emerald-600 font-medium">Returned {{ $alloc->actual_return_date->format('m/d/Y') }}</span>
                                    @else
                                         <span class="text-slate-500">Active</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-slate-500">No assets allocated to this project.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
