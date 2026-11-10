<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $table = 'commerce.orders';

    protected $fillable = [
        'order_number',
        'company_id',
        'total_amount',
        'status',
        'payment_method_id',
        'paid_at',
    ];

    /**
     * Empresa dueña del pedido.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Detalles/ítems del pedido.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /**
     * El código de activación generado para esta orden.
     */
    public function activationCode(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ActivationCode::class, 'order_id');
    }
}
