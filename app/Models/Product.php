<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'img', 'price', 'buy_count'];

    public function order()
    {
        return $this->belongsToMany(Order::class, 'product_orders');
    }

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }
}
