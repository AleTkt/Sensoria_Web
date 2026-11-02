<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Company extends Model
{
    use HasFactory;

    /**
     * Tabla explícita en PostgreSQL.
     */
    protected $table = 'identity.companies';

    /**
     * Atributos asignables de forma masiva.
     */
    protected $fillable = [
        'tax_id',
        'legal_name',
        'trade_name',
        'sector',
        'email',
        'phone',
        'address',
        'is_active',
    ];

    /**
     * Relación Muchos a Muchos con Usuarios.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'identity.company_user', 'company_id', 'user_id')
            ->withPivot('company_role', 'is_active')
            ->withTimestamps();
    }
}
