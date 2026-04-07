<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sample extends Model
{
    protected $fillable = ['request_id', 'sample_type', 'collected_at', 'status', 'barcode', 'technician_name'];

    protected $casts = [
        'collected_at' => 'datetime',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(LabRequest::class, 'request_id');
    }
}
