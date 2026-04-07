<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'normal_min',
        'normal_max',
        'unit',
        'price'
    ];

    public function category()
    {
        return $this->belongsTo(TestCategory::class, 'category_id');
    }
}
