<?php

namespace App\Exports;

use App\Models\AssetAllocation;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AllocationExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $search;
    protected $status;

    public function __construct($search, $status)
    {
        $this->search = $search;
        $this->status = $status;
    }

    public function query()
    {
        $query = AssetAllocation::with(['asset', 'employee', 'project']);

        if (!empty($this->search)) {
            $query->whereHas('asset', function($q) {
                $q->where('name', 'ilike', '%' . $this->search . '%')
                  ->orWhere('tag_number', 'ilike', '%' . $this->search . '%');
            })->orWhereHas('employee', function($q) {
                $q->where('first_name', 'ilike', '%' . $this->search . '%')
                  ->orWhere('last_name', 'ilike', '%' . $this->search . '%');
            })->orWhereHas('project', function($q) {
                $q->where('project_name', 'ilike', '%' . $this->search . '%');
            });
        }

        if (!empty($this->status)) {
            if ($this->status === 'active') {
                $query->whereNull('actual_return_date');
            } elseif ($this->status === 'closed') {
                $query->whereNotNull('actual_return_date');
            } elseif ($this->status === 'returned') {
                $query->whereNotNull('actual_return_date')->where('is_transfer_out', false);
            } elseif ($this->status === 'transferred') {
                $query->where('is_transfer_out', true);
            }
        }

        return $query->orderBy('check_out_date', 'desc')->orderBy('id', 'desc');
    }

    public function headings(): array
    {
        return [
            'Record ID',
            'Asset Tag',
            'Asset Name',
            'Assigned To',
            'Check Out Date',
            'Expected Return',
            'Actual Return',
            'Status'
        ];
    }

    public function map($allocation): array
    {
        $assignedTo = [];
        if ($allocation->project) $assignedTo[] = 'Project: ' . $allocation->project->project_name;
        if ($allocation->employee) $assignedTo[] = 'Employee: ' . $allocation->employee->full_name;
        $assignedToStr = empty($assignedTo) ? 'Unassigned' : implode(' | ', $assignedTo);

        $statusStr = 'Active';
        if ($allocation->actual_return_date) {
            $statusStr = $allocation->is_transfer_out ? 'Transferred' : 'Returned';
        }

        return [
            $allocation->id,
            $allocation->asset->tag_number ?? 'N/A',
            $allocation->asset->name ?? 'Deleted',
            $assignedToStr,
            $allocation->check_out_date->format('Y-m-d'),
            $allocation->expected_return_date ? $allocation->expected_return_date->format('Y-m-d') : 'N/A',
            $allocation->actual_return_date ? $allocation->actual_return_date->format('Y-m-d') : 'N/A',
            $statusStr
        ];
    }
}
