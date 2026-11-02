<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    use HasFactory;

    protected $table = 'iot.devices';

    protected $fillable = [
        'hub_id',
        'product_id',
        'device_code',
        'name',
        'type',
        'location',
        'is_active',
    ];

    public function hub(): BelongsTo
    {
        return $this->belongsTo(Hub::class, 'hub_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function telemetryReadings(): HasMany
    {
        return $this->hasMany(TelemetryReading::class, 'device_id');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class, 'device_id');
    }
}
