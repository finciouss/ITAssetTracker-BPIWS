<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\AssetAllocation;
use App\Helpers\ExcelTemplate;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::all();
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:100',
            'location'     => 'nullable|string|max:200',
            'start_date'   => 'nullable|date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
            'status'       => 'required|in:' . implode(',', array_keys(Project::statusOptions())),
        ]);

        Project::create($validated);
        
        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $project->load(['allocations.asset', 'allocations.employee']);
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:100',
            'location'     => 'nullable|string|max:200',
            'start_date'   => 'nullable|date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
            'status'       => 'required|in:' . implode(',', array_keys(Project::statusOptions())),
        ]);

        $project->update($validated);
        
        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    public function delete(Project $project)
    {
        return view('projects.delete', compact('project'));
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }

    public function exportPdf(Project $project)
    {
        $project->load(['allocations.asset', 'allocations.employee']);
        
        $activeAllocations = $project->allocations->filter(function ($alloc) {
            return is_null($alloc->actual_return_date);
        });

        $transferredAllocations = $project->allocations->filter(function ($alloc) {
            return !is_null($alloc->actual_return_date) && $alloc->is_transfer_out;
        })->map(function ($alloc) {
            $nextAlloc = \App\Models\AssetAllocation::with(['employee', 'project'])
                ->where('asset_id', $alloc->asset_id)
                ->where('is_transfer_in', true)
                ->where('id', '>', $alloc->id)
                ->orderBy('id', 'asc')
                ->first();
                
            if ($nextAlloc) {
                $parts = [];
                if ($nextAlloc->employee) {
                    $parts[] = $nextAlloc->employee->full_name ?? $nextAlloc->employee->first_name;
                }
                if ($nextAlloc->project) {
                    $parts[] = $nextAlloc->project->project_name;
                }
                $alloc->transferred_to_name = empty($parts) ? 'Unknown' : implode(' - ', $parts);
            } else {
                $alloc->transferred_to_name = 'N/A';
            }
            
            return $alloc;
        });

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('projects.export', compact('project', 'activeAllocations', 'transferredAllocations'));
        
        return $pdf->stream("Project_{$project->project_name}_IT_Asset_Report.pdf");
    }

    public function exportExcel(Project $project)
    {
        $project->load(['allocations.asset', 'allocations.employee']);

        $excel = new ExcelTemplate();
        $row = $excel->writeCompanyHeader('IT ASSET REPORT');

        // Metadata
        $row = $excel->writeMetadataBlock($row, [
            'PROJECT' => $project->project_name,
            'DATE'    => now()->timezone('Asia/Jakarta')->format('d F Y'),
            'STATUS'  => $project->status ?? 'Ongoing',
        ]);

        // ── Active Allocations ──
        $activeAllocations = $project->allocations->filter(fn($a) => is_null($a->actual_return_date));

        $row = $excel->writeSectionTitle($row, 'ACTIVELY ALLOCATED ASSETS ');
        $dataRow = $excel->writeTableHeader($row, [
            'B' => 'No.',
            'C' => 'Tag Number',
            'E' => 'Asset Name',
            'F' => 'Category',
            'H' => 'Employee',
        ]);

        if ($activeAllocations->isEmpty()) {
            $dataRow = $excel->writeEmptyPlaceholder($dataRow, '(No active allocations)');
        } else {
            $no = 1;
            foreach ($activeAllocations as $alloc) {
                $excel->writeDataRow($dataRow, [
                    'B' => $no,
                    'C' => $alloc->asset->tag_number ?? 'N/A',
                    'E' => $alloc->asset->name ?? 'Deleted',
                    'F' => $alloc->asset->category ?? '',
                    'H' => $alloc->employee ? $alloc->employee->full_name : '-',
                ]);
                $dataRow++;
                $no++;
            }
        }

        // ── Transferred Allocations ──
        $transferredAllocations = $project->allocations->filter(fn($a) => !is_null($a->actual_return_date) && $a->is_transfer_out);
        $transferredAllocations = $transferredAllocations->map(function ($alloc) {
            $nextAlloc = AssetAllocation::with(['employee', 'project'])
                ->where('asset_id', $alloc->asset_id)
                ->where('is_transfer_in', true)
                ->where('id', '>', $alloc->id)
                ->orderBy('id', 'asc')
                ->first();
            if ($nextAlloc) {
                $parts = [];
                if ($nextAlloc->employee) $parts[] = $nextAlloc->employee->full_name ?? $nextAlloc->employee->first_name;
                if ($nextAlloc->project) $parts[] = $nextAlloc->project->project_name;
                $alloc->transferred_to_name = empty($parts) ? 'Unknown' : implode(' - ', $parts);
            } else {
                $alloc->transferred_to_name = 'N/A';
            }
            return $alloc;
        });

        $row = $excel->writeSectionTitle($dataRow, 'TRANSFERRED ASSETS HISTORY');
        $dataRow = $excel->writeTableHeader($row, [
            'B' => 'No.',
            'C' => 'Tag Number',
            'E' => 'Asset Name',
            'F' => 'Previous Emp.',
            'H' => 'Transferred To',
        ]);

        if ($transferredAllocations->isEmpty()) {
            $dataRow = $excel->writeEmptyPlaceholder($dataRow, '(No transferred assets)');
        } else {
            $no = 1;
            foreach ($transferredAllocations as $alloc) {
                $excel->writeDataRow($dataRow, [
                    'B' => $no,
                    'C' => $alloc->asset->tag_number ?? 'N/A',
                    'E' => $alloc->asset->name ?? 'Deleted',
                    'F' => $alloc->employee ? $alloc->employee->full_name : '-',
                    'H' => $alloc->transferred_to_name ?? 'N/A',
                ]);
                $dataRow++;
                $no++;
            }
        }

        // Signature block
        $excel->writeSignatureBlock($dataRow + 3);

        return $excel->download("Project_{$project->project_name}_IT_Asset_Report.xlsx");
    }
}
