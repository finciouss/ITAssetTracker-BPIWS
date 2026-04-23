<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetAllocation extends Model
{
    protected $fillable = [
        'asset_id',
        'project_id',
        'employee_id',
        'quantity',
        'check_out_date',
        'expected_return_date',
        'actual_return_date',
        'notes',
        'is_transfer_out',
        'is_transfer_in',
    ];

    protected $casts = [
        'check_out_date'        => 'datetime',
        'expected_return_date'  => 'date',
        'actual_return_date'    => 'date',
        'is_transfer_out'       => 'boolean',
        'is_transfer_in'        => 'boolean',
        'quantity'              => 'integer',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function isActive(): bool
    {
        return is_null($this->actual_return_date);
    }
}
