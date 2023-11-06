<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = ['user_id'];
    /**
     * The products that belong to the order
     */
    public function products()
    {
        /**
         * Bypass Eloquent's default to combine 2 classes in alphabetical order 
         * (a.k.a 'order_product')
         */ 
        return $this->belongsToMany(Product::class, 'product_orders')->where('can_fulfill', '=', true)->select('quantity', 'can_fulfill')->getResults();
    }

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }


}
