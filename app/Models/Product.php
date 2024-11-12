<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = ['quantity', 'rating', 'num_rating', 'stripe_id', 'photo_key'];
    protected $hidden = ['created_at', 'updated_at'];
    /**
     * The orders that contain the product
     */
    public function orders(): BelongsToMany
    {
        /**
         * Bypass Eloquent's default to combine 2 classes in alphabetical order 
         * (a.k.a 'order_product')
         */
        return $this->belongsToMany(Order::class)->using(ProductOrder::class);
    }
}
