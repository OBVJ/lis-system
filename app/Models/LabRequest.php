<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\User;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * LabRequest Model
 *
 * @property int $id
 * @property-read int $id
 * @property int $patient_id
 * @property-read int $patient_id
 * @property string $status
 * @property-read string $status
 * @property float $total_price
 * @property-read float $total_price
 * @property string|null $priority
 * @property-read string|null $priority
 * @property string|null $notes
 * @property-read string|null $notes
 * @property int|null $created_by
 * @property-read int|null $created_by
 * @property \Carbon\Carbon|null $collected_at
 * @property-read \Carbon\Carbon|null $collected_at
 * @property int|null $collected_by
 * @property-read int|null $collected_by
 * @property \Carbon\Carbon|null $in_progress_at
 * @property-read \Carbon\Carbon|null $in_progress_at
 * @property int|null $in_progress_by
 * @property-read int|null $in_progress_by
 * @property \Carbon\Carbon|null $review_at
 * @property-read \Carbon\Carbon|null $review_at
 * @property int|null $review_by
 * @property-read int|null $review_by
 * @property \Carbon\Carbon|null $completed_at
 * @property-read \Carbon\Carbon|null $completed_at
 * @property int|null $completed_by
 * @property-read int|null $completed_by
 * @property \Carbon\Carbon|null $delivered_at
 * @property-read \Carbon\Carbon|null $delivered_at
 * @property int|null $delivered_by
 * @property-read int|null $delivered_by
 * @property \Carbon\Carbon $created_at
 * @property-read \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Carbon\Carbon $updated_at
 */
class LabRequest extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'patient_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'status',
        'total_price',
        'priority',
        'notes',
        'created_by',
        'collected_at',
        'collected_by',
        'in_progress_at',
        'in_progress_by',
        'review_at',
        'review_by',
        'completed_at',
        'completed_by',
        'delivered_at',
        'delivered_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'collected_at' => 'datetime',
        'in_progress_at' => 'datetime',
        'review_at' => 'datetime',
        'completed_at' => 'datetime',
        'delivered_at' => 'datetime',
        'total_price' => 'decimal:2',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(LabRequestItem::class, 'request_id');
    }

    public function samples(): HasMany
    {
        return $this->hasMany(Sample::class, 'request_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'request_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'request_id')->latestOfMany();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
