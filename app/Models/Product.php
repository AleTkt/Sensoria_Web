<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $table = 'commerce.products';

    protected $fillable = [
        'sku',
        'name',
        'description',
        'image_path',
        'price',
        'stock',
        'category',
        'is_active',
    ];

    /**
     * Kits en los que está incluido este producto.
     */
    public function kits(): BelongsToMany
    {
        return $this->belongsToMany(Kit::class, 'commerce.kit_product', 'product_id', 'kit_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
