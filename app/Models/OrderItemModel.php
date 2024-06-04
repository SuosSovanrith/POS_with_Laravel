<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItemModel extends Model
{
    use HasFactory;

    protected $table = "orderitem";

    protected $fillable = [
        'order_price',
        'order_quantity',
        'order_id',
        'product_id',
    ];

    protected  $primaryKey = 'orderitem_id';
}
