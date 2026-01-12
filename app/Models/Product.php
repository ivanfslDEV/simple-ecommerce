<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image_url',
        'price',
        'stock_quantity',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}
