<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyActivityLog extends Model
{
    protected $table = 'identity.company_activity_logs';

    protected $fillable = [
        'company_id', 'user_id', 'category', 'action', 'description'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
