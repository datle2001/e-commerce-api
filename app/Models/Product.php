<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    public function orders():BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'product_order');
    }
    protected $fillable = [
        'quantity_available',
    ];
}
