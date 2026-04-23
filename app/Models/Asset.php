<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    protected $fillable = [
        'tag_number',
        'name',
        'specifications',
        'purchase_date',
        'status',
        'cost',
        'category',
        'stock',
        'maintenance_quantity',
    ];

    protected $casts = [
        'purchase_date'        => 'date',
        'stock'                => 'integer',
        'maintenance_quantity' => 'integer',
    ];

    // ── Status constants ───────────────────────────────────────────────────────
    const STATUS_IN_STOCK    = 'InStock';
    const STATUS_ALLOCATED   = 'Allocated';
    const STATUS_MAINTENANCE = 'Maintenance';
    const STATUS_RETIRED     = 'Retired';

    public static function statusOptions(): array
    {
        return [
            self::STATUS_IN_STOCK    => 'In Stock',
            self::STATUS_ALLOCATED   => 'Allocated',
            self::STATUS_MAINTENANCE => 'Maintenance',
            self::STATUS_RETIRED     => 'Retired',
        ];
    }

    // ── Relations ──────────────────────────────────────────────────────────────
    public function allocations(): HasMany
    {
        return $this->hasMany(AssetAllocation::class);
    }

    public function activeAllocations(): HasMany
    {
        return $this->hasMany(AssetAllocation::class)->whereNull('actual_return_date');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AssetLog::class);
    }

    // ── Stock helpers ──────────────────────────────────────────────────────────

    /**
     * Total units currently in active allocations.
     */
    public function allocatedQuantity(): int
    {
        return (int) $this->activeAllocations()->sum('quantity');
    }

    /**
     * Units available to be allocated right now.
     */
    public function availableStock(): int
    {
        return max(0, $this->stock - $this->allocatedQuantity() - $this->maintenance_quantity);
    }

    /**
     * Auto-recalculate and save status based on current stock state.
     * Priority: Retired (manual) > Maintenance > Allocated > InStock
     */
    public function autoUpdateStatus(): void
    {
        // Never override a Retired status automatically
        if ($this->status === self::STATUS_RETIRED) {
            return;
        }

        $available = $this->availableStock();

        if ($this->maintenance_quantity > 0) {
            $this->status = self::STATUS_MAINTENANCE;
        } elseif ($available <= 0) {
            $this->status = self::STATUS_ALLOCATED;
        } else {
            $this->status = self::STATUS_IN_STOCK;
        }

        $this->saveQuietly();
    }
}
