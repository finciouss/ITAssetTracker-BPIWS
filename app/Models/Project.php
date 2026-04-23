<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'project_name',
        'location',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    const STATUS_ONGOING   = 'Ongoing';
    const STATUS_COMPLETED = 'Completed';

    public static function statusOptions(): array
    {
        return [
            self::STATUS_ONGOING   => 'Ongoing',
            self::STATUS_COMPLETED => 'Completed',
        ];
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(AssetAllocation::class);
    }
}
