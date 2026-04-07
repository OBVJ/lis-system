<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LabRequestItem extends Model
{
    protected $fillable = ['request_id', 'test_id', 'status'];

    public function request(): BelongsTo
    {
        return $this->belongsTo(LabRequest::class, 'request_id');
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(LabTest::class, 'test_id');
    }

    public function result(): HasOne
    {
        return $this->hasOne(TestResult::class, 'request_item_id');
    }
}
