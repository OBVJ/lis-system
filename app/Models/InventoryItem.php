<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'name', 'sku', 'category', 'unit', 'min_level', 'current_stock', 'expiry_date'
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('current_stock <= min_level');
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays($days));
    }
}
