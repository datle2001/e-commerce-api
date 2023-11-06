<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductOrder extends Pivot
{
    protected $table = 'product_orders';
    protected $fillable = ['order_id', 'product_id', 'quantity', 'can_fulfill'];
}
