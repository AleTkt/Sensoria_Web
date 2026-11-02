<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Kit extends Model
{
    use HasFactory;

    protected $table = 'commerce.kits';

    protected $fillable = [
        'sku',
        'name',
        'description',
        'target_sector',
        'price',
        'is_active',
    ];

    /**
     * Productos que forman parte de este Kit.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'commerce.kit_product', 'kit_id', 'product_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
