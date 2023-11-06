<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\File;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = ['quantity_available', 'rating', 'num_rating', 'stripe_id'];
    protected $hidden = ['created_at', 'updated_at'];
    /**
     * The orders that contains the product
     */
    public function orders(): BelongsToMany
    {
        /**
         * Bypass Eloquent's default to combine 2 classes in alphabetical order 
         * (a.k.a 'order_product')
         */
        return $this->belongsToMany(Order::class, 'product_orders', 'product_id', 'order_id')->using(ProductOrder::class)->select('order_id', 'quantity');
    }
}
