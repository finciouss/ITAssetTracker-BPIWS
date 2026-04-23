<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'name',
        'department',
        'user_id',
    ];

    public function getFirstNameAttribute()
    {
        if (!$this->name) return '';
        $parts = explode(' ', $this->name);
        return $parts[0];
    }

    public function getLastNameAttribute()
    {
        if (!$this->name) return '';
        $parts = explode(' ', $this->name);
        if (count($parts) > 1) {
            array_shift($parts);
            return implode(' ', $parts);
        }
        return '';
    }

    public function getFullNameAttribute()
    {
        return $this->name;
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(AssetAllocation::class);
    }
}
