<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestResult extends Model
{
    protected $fillable = ['request_item_id', 'result_value', 'flag', 'notes'];

    public function requestItem(): BelongsTo
    {
        return $this->belongsTo(LabRequestItem::class, 'request_item_id');
    }
}
