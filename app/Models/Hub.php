<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hub extends Model
{
    use HasFactory;

    protected $table = 'iot.hubs';

    protected $fillable = [
        'serial_number',
        'mac_address',
        'company_id',
        'name',
        'api_token',
        'ip_address',
        'status',
        'last_ping_at',
    ];

    protected function casts(): array
    {
        return [
            'last_ping_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class, 'hub_id');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class, 'hub_id');
    }
}
