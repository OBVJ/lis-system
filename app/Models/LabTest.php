<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabTest extends Model
{
    use HasFactory;
    protected $table = 'tests';
    protected $fillable = ['name', 'category_id', 'normal_min', 'normal_max', 'unit', 'price'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(TestCategory::class, 'category_id');
    }

    public function materials()
    {
        return $this->belongsToMany(InventoryItem::class, 'test_materials', 'test_id', 'inventory_item_id')
                    ->withPivot('quantity_required')
                    ->withTimestamps();
    }
}
