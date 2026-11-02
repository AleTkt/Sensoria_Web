<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelemetryReading extends Model
{
    use HasFactory;

    protected $table = 'iot.telemetry_readings';

    protected $fillable = [
        'device_id',
        'value',
        'unit',
        'payload',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'payload' => 'array',
            'recorded_at' => 'datetime',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id');
    }
}
