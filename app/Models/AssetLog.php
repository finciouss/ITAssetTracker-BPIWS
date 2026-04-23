<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetLog extends Model
{
    protected $fillable = [
        'asset_id',
        'action_type',
        'action_date',
        'description',
        'user_name',
    ];

    protected $casts = [
        'action_date' => 'datetime',
    ];

    // Action type constants
    const ACTION_CREATED       = 'Created';
    const ACTION_STATUS_CHANGED = 'StatusChanged';
    const ACTION_ALLOCATED     = 'Allocated';
    const ACTION_RETURNED      = 'Returned';
    const ACTION_UPDATED       = 'Updated';
    const ACTION_TRANSFERRED   = 'Transferred';

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
