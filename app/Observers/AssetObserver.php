<?php

namespace App\Observers;

use App\Models\Asset;
use App\Models\AssetLog;

class AssetObserver
{
    /**
     * Fires when a new Asset is created.
     * Replicates the "Created" branch of AssetAuditInterceptor.
     */
    public function created(Asset $asset): void
    {
        AssetLog::create([
            'asset_id'    => $asset->id,
            'action_type' => 'Created',
            'action_date' => now(),
            'user_name'   => auth()->check() ? auth()->user()->name : 'System',
            'description' => "Asset '{$asset->name}' ({$asset->tag_number}) was added to inventory.",
        ]);
    }

    /**
     * Fires when an Asset is updated.
     * Replicates the "StatusChanged" branch of AssetAuditInterceptor.
     */
    public function updated(Asset $asset): void
    {
        if ($asset->wasChanged('status')) {
            $old = $asset->getOriginal('status');
            $new = $asset->status;
            
            $actionType = 'Status Changed';
            if ($new === \App\Models\Asset::STATUS_ALLOCATED) {
                $actionType = 'Allocated';
            } elseif ($new === \App\Models\Asset::STATUS_IN_STOCK && $old === \App\Models\Asset::STATUS_ALLOCATED) {
                $actionType = 'Returned';
            }

            AssetLog::create([
                'asset_id'    => $asset->id,
                'action_type' => $actionType,
                'action_date' => now(),
                'user_name'   => auth()->check() ? auth()->user()->name : 'System',
                'description' => "Status changed from '{$old}' to '{$new}'.",
            ]);
        }
    }
}
