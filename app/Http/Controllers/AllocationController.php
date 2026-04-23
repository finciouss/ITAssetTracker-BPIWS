<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetAllocation;
use App\Models\Employee;
use App\Models\Project;
use App\Helpers\ExcelTemplate;
use Illuminate\Http\Request;

class AllocationController extends Controller
{
    public function index(Request $request)
    {
        $query = AssetAllocation::with(['asset', 'employee', 'project']);

        if ($request->filled('SearchString')) {
            $search = $request->input('SearchString');
            $query->where(function($q) use ($search) {
                $q->whereHas('asset', function ($sq) use ($search) {
                    $sq->where('tag_number', 'like', "%{$search}%")
                       ->orWhere('name', 'like', "%{$search}%");
                })->orWhereHas('employee', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })->orWhereHas('project', function ($sq) use ($search) {
                    $sq->where('project_name', 'like', "%{$search}%");
                });
            });
        }

        $status = $request->input('StatusFilter', 'active');
        if ($status === 'active') {
            $query->whereNull('actual_return_date');
        } elseif ($status === 'returned') {
            $query->whereNotNull('actual_return_date')->where('is_transfer_out', false);
        } elseif ($status === 'transferred') {
            $query->whereNotNull('actual_return_date')->where('is_transfer_out', true);
        } elseif ($status === 'closed') {
            $query->whereNotNull('actual_return_date');
        }

        $sortStatus = $request->input('sort_status');
        if ($sortStatus === 'returned_first') {
            $query->orderByRaw('CASE WHEN actual_return_date IS NOT NULL AND is_transfer_out = false THEN 0 ELSE 1 END')
                  ->orderBy('check_out_date', 'desc')
                  ->orderBy('id', 'desc');
        } elseif ($sortStatus === 'active_first') {
            $query->orderByRaw('CASE WHEN actual_return_date IS NULL THEN 0 ELSE 1 END')
                  ->orderBy('check_out_date', 'desc')
                  ->orderBy('id', 'desc');
        } else {
            $query->orderBy('check_out_date', 'desc')
                  ->orderBy('id', 'desc');
        }

        $allocations = $query->get();
        return view('allocations.index', compact('allocations'));
    }

    public function exportPdf(Request $request)
    {
        $query = AssetAllocation::with(['asset', 'employee', 'project']);

        if ($request->filled('SearchString')) {
            $search = $request->input('SearchString');
            $query->where(function($q) use ($search) {
                $q->whereHas('asset', function ($sq) use ($search) {
                    $sq->where('tag_number', 'like', "%{$search}%")
                       ->orWhere('name', 'like', "%{$search}%");
                })->orWhereHas('employee', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })->orWhereHas('project', function ($sq) use ($search) {
                    $sq->where('project_name', 'like', "%{$search}%");
                });
            });
        }

        $status = $request->input('StatusFilter', 'active');
        if ($status === 'active') {
            $query->whereNull('actual_return_date');
        } elseif ($status === 'returned') {
            $query->whereNotNull('actual_return_date')->where('is_transfer_out', false);
        } elseif ($status === 'transferred') {
            $query->whereNotNull('actual_return_date')->where('is_transfer_out', true);
        } elseif ($status === 'closed') {
            $query->whereNotNull('actual_return_date');
        }

        $sortStatus = $request->input('sort_status');
        if ($sortStatus === 'returned_first') {
            $query->orderByRaw('CASE WHEN actual_return_date IS NOT NULL AND is_transfer_out = false THEN 0 ELSE 1 END')
                  ->orderBy('check_out_date', 'desc')
                  ->orderBy('id', 'desc');
        } elseif ($sortStatus === 'active_first') {
            $query->orderByRaw('CASE WHEN actual_return_date IS NULL THEN 0 ELSE 1 END')
                  ->orderBy('check_out_date', 'desc')
                  ->orderBy('id', 'desc');
        } else {
            $query->orderBy('check_out_date', 'desc')
                  ->orderBy('id', 'desc');
        }

        $allocations = $query->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('allocations.export', compact('allocations'))
                    ->setPaper('a4', 'landscape'); // Allocations might need landscape
        
        return $pdf->stream("IT_Asset_Allocations_Report.pdf");
    }

    public function exportExcel(Request $request)
    {
        $query = AssetAllocation::with(['asset', 'employee', 'project']);

        if ($request->filled('SearchString')) {
            $search = $request->input('SearchString');
            $query->where(function($q) use ($search) {
                $q->whereHas('asset', function ($sq) use ($search) {
                    $sq->where('tag_number', 'like', "%{$search}%")
                       ->orWhere('name', 'like', "%{$search}%");
                })->orWhereHas('employee', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })->orWhereHas('project', function ($sq) use ($search) {
                    $sq->where('project_name', 'like', "%{$search}%");
                });
            });
        }

        $status = $request->input('StatusFilter', 'active');
        if ($status === 'active') {
            $query->whereNull('actual_return_date');
        } elseif ($status === 'returned') {
            $query->whereNotNull('actual_return_date')->where('is_transfer_out', false);
        } elseif ($status === 'transferred') {
            $query->whereNotNull('actual_return_date')->where('is_transfer_out', true);
        } elseif ($status === 'closed') {
            $query->whereNotNull('actual_return_date');
        }

        $allocations = $query->orderBy('check_out_date', 'desc')->orderBy('id', 'desc')->get();

        $excel = new ExcelTemplate();
        $row = $excel->writeCompanyHeader('IT ASSET ALLOCATIONS REPORT');

        $row = $excel->writeMetadataBlock($row, [
            'DATE'  => now()->timezone('Asia/Jakarta')->format('d F Y'),
            'TOTAL' => $allocations->count() . ' records',
        ]);

        $row = $excel->writeSectionTitle($row, 'ALLOCATION RECORDS');
        $dataRow = $excel->writeTableHeader($row, [
            'B' => 'No.',
            'C' => 'Tag Number',
            'E' => 'Asset Name',
            'F' => 'Assigned To',
            'H' => 'Status',
        ]);

        $no = 1;
        foreach ($allocations as $alloc) {
            $assignedTo = [];
            if ($alloc->project) $assignedTo[] = $alloc->project->project_name;
            if ($alloc->employee) $assignedTo[] = $alloc->employee->full_name;
            $assignedStr = empty($assignedTo) ? '-' : implode(' | ', $assignedTo);

            $statusStr = 'Active';
            if ($alloc->actual_return_date) {
                $statusStr = $alloc->is_transfer_out ? 'Transferred' : 'Returned';
            }

            $excel->writeDataRow($dataRow, [
                'B' => $no,
                'C' => $alloc->asset->tag_number ?? 'N/A',
                'E' => $alloc->asset->name ?? 'Deleted',
                'F' => $assignedStr,
                'H' => $statusStr,
            ]);
            $dataRow++;
            $no++;
        }

        $excel->writeSignatureBlock($dataRow + 3);

        return $excel->download('IT_Asset_Allocations_Report.xlsx');
    }

    public function create()
    {
        // Show assets that have available stock (not fully allocated/retired)
        $assets = Asset::where('status', '!=', Asset::STATUS_RETIRED)
            ->get()
            ->filter(fn($a) => $a->availableStock() > 0)
            ->values();
        $employees = Employee::orderBy('name')->get();
        $projects = Project::where('status', Project::STATUS_ONGOING)->orderBy('project_name')->get();

        return view('allocations.create', compact('assets', 'employees', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_id'             => 'required|exists:assets,id',
            'quantity'             => 'required|integer|min:1',
            'employee_id'          => 'nullable|exists:employees,id',
            'project_id'           => 'nullable|exists:projects,id',
            'check_out_date'       => 'required|date',
            'expected_return_date' => 'nullable|date|after_or_equal:check_out_date',
            'notes'                => 'nullable|string|max:1000',
        ]);

        if (empty($validated['employee_id']) && empty($validated['project_id'])) {
            return back()->withInput()->withErrors(['general' => 'Must select an Employee, a Project, or both.']);
        }

        $asset = Asset::findOrFail($validated['asset_id']);
        $available = $asset->availableStock();

        if ($validated['quantity'] > $available) {
            return back()->withInput()->withErrors([
                'quantity' => "Only {$available} unit(s) of '{$asset->name}' are available."
            ]);
        }

        $allocation = AssetAllocation::create([
            'asset_id'             => $asset->id,
            'quantity'             => $validated['quantity'],
            'employee_id'          => $validated['employee_id'],
            'project_id'           => $validated['project_id'],
            'check_out_date'       => $validated['check_out_date'],
            'expected_return_date' => $validated['expected_return_date'] ?? null,
            'notes'                => $validated['notes'] ?? null,
        ]);

        $target = [];
        if ($validated['project_id']) {
            $prj = \App\Models\Project::find($validated['project_id']);
            if ($prj) $target[] = 'Project ' . $prj->project_name;
        }
        if ($validated['employee_id']) {
            $emp = \App\Models\Employee::find($validated['employee_id']);
            if ($emp) $target[] = 'Employee ' . $emp->first_name;
        }
        $targetStr = implode(' and ', $target);

        \App\Models\AssetLog::create([
            'asset_id'    => $asset->id,
            'action_type' => 'Allocated',
            'action_date' => now(),
            'user_name'   => auth()->user() ? (auth()->user()->full_name ?? auth()->user()->name) : 'System',
            'description' => "Allocated {$validated['quantity']} unit(s) to {$targetStr}"
        ]);

        $asset->autoUpdateStatus();

        return redirect()->route('allocations.index')->with('success', "Allocated {$validated['quantity']} unit(s) of {$asset->name} successfully.");
    }

    public function return(AssetAllocation $allocation)
    {
        $allocation->load(['asset', 'employee', 'project']);
        return view('allocations.return', compact('allocation'));
    }

    public function processReturn(Request $request, AssetAllocation $allocation)
    {
        $validated = $request->validate([
            'actual_return_date' => 'required|date|after_or_equal:' . $allocation->check_out_date->format('Y-m-d'),
            'notes'              => 'nullable|string|max:1000',
        ]);

        $allocation->update([
            'actual_return_date' => $validated['actual_return_date'],
            'notes' => $allocation->notes ? $allocation->notes . "\nReturn Notes: " . $validated['notes'] : $validated['notes']
        ]);

        \App\Models\AssetLog::create([
            'asset_id'    => $allocation->asset_id,
            'action_type' => 'Returned',
            'action_date' => now(),
            'user_name'   => auth()->user() ? (auth()->user()->full_name ?? auth()->user()->name) : 'System',
            'description' => "Returned {$allocation->quantity} unit(s) to inventory."
        ]);

        // Auto-recalculate status after return
        $allocation->asset->autoUpdateStatus();

        return redirect()->route('allocations.index')->with('success', 'Asset returned successfully.');
    }

    public function transfer(AssetAllocation $allocation)
    {
        $allocation->load(['asset', 'employee', 'project']);
        $employees = Employee::orderBy('name')->get();
        $projects = Project::where('status', Project::STATUS_ONGOING)->orderBy('project_name')->get();
        return view('allocations.transfer', compact('allocation', 'employees', 'projects'));
    }

    public function processTransfer(Request $request, AssetAllocation $allocation)
    {
        $validated = $request->validate([
            'employee_id'          => 'nullable|exists:employees,id',
            'project_id'           => 'nullable|exists:projects,id',
            'transfer_date'        => 'required|date|after_or_equal:' . $allocation->check_out_date->format('Y-m-d'),
            'expected_return_date' => 'nullable|date|after_or_equal:transfer_date',
            'notes'                => 'nullable|string|max:1000',
        ]);

        if (empty($validated['employee_id']) && empty($validated['project_id'])) {
            return back()->withInput()->withErrors(['general' => 'Must select a new Employee, a Project, or both for transfer.']);
        }

        // Close current allocation as transferred out
        $allocation->update([
            'actual_return_date' => $validated['transfer_date'],
            'is_transfer_out'    => true,
            'notes'              => $allocation->notes ? $allocation->notes . "\nTransferred Out." : "Transferred Out."
        ]);

        // Create new allocation (carry same quantity)
        AssetAllocation::create([
            'asset_id'             => $allocation->asset_id,
            'quantity'             => $allocation->quantity,
            'employee_id'          => $validated['employee_id'],
            'project_id'           => $validated['project_id'],
            'check_out_date'       => $validated['transfer_date'],
            'expected_return_date' => $validated['expected_return_date'],
            'notes'                => $validated['notes'],
            'is_transfer_in'       => true,
        ]);

        $target = [];
        if ($validated['project_id']) {
            $prj = \App\Models\Project::find($validated['project_id']);
            if ($prj) $target[] = 'Project ' . $prj->project_name;
        }
        if ($validated['employee_id']) {
            $emp = \App\Models\Employee::find($validated['employee_id']);
            if ($emp) $target[] = 'Employee ' . $emp->first_name;
        }
        $targetStr = implode(' and ', $target);

        \App\Models\AssetLog::create([
            'asset_id'    => $allocation->asset_id,
            'action_type' => 'Transferred',
            'action_date' => now(),
            'user_name'   => auth()->user() ? (auth()->user()->full_name ?? auth()->user()->name) : 'System',
            'description' => "Transferred to {$targetStr}"
        ]);

        // autoUpdateStatus – transfer keeps qty the same so status shouldn't change
        $allocation->asset->refresh()->autoUpdateStatus();

        return redirect()->route('allocations.index')->with('success', 'Asset transferred successfully.');
    }
}
