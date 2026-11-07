<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    /**
     * Tabla explícita en PostgreSQL.
     */
    protected $table = 'identity.invitations';

    protected $fillable = [
        'email',
        'token',
        'company_id',
        'role'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
