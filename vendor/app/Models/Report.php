<?php

namespace App\Models;

use App\Traits\RunCustomQueries;
use AppierSign\RealtimeModel\Traits\RealtimeModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Report extends Model
{
    use HasFactory, RunCustomQueries;

    protected $fillable = [
        'external_id',
        'reporter_id',
        'reportable_id',
        'reportable_type',
        'booking_id',
        'reason',
        'resolved_by',
        'resolved_on'
    ];

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

//    public function getSyncKey(): string
//    {
//        return 'external_id';
//    }
//
//    public function toRealtimeData(): array
//    {
//        return [
//            'id' => $this->id,
//            'userId' => $this->user_id,
//            'reportableId' => $this->reportable_id,
//            'reportableType' => $this->reportable_type
//        ];
//    }

}
