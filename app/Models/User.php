<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Nombre de la tabla explícitamente cualificada con su esquema en PostgreSQL.olp   k
     */
    protected $table = 'identity.users';

    /**
     * Los atributos asignables de forma masiva.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'global_role',
        'is_active',
    ];

    /**
     * Los atributos que deben ocultarse en la serialización (JSON/API).
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Los atributos que deben ser casteados.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relación Muchos a Muchos con Empresas.
     */
    public function companies(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'identity.company_user', 'user_id', 'company_id')
            ->withPivot('company_role', 'is_active')
            ->withTimestamps();
    }

    /**
     * Verifica si el usuario tiene uno o varios roles específicos en la empresa activa.
     */
    public function hasCompanyRole($roles,$companyId = null): bool
    {
        $companyId =$companyId ?? session('current_company_id');

        if (!$companyId) {
            return false;
        }

        // Obtenemos directamente el rol desde la tabla pivote para optimizar la consulta
        $userRole =$this->companies()
            ->where('identity.companies.id', $companyId)
            ->value('identity.company_user.company_role');

        if (!$userRole) {
            return false;
        }

        // Convertimos a mayúsculas para evitar errores de tipeo y verificamos
        $roles = is_array($roles) ? array_map('strtoupper', $roles) : [strtoupper($roles)];

        return in_array(strtoupper($userRole),$roles);
    }

    /**
     * Métodos de pago del usuario.
     */
    public function paymentMethods(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PaymentMethod::class, 'user_id');
    }
}
