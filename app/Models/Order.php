<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    // Returns the order items
    public function items(){
        return $this->hasMany(OrderItem::class);
    }
}
