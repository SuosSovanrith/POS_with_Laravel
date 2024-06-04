<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderModel extends Model
{
    use HasFactory;

    protected $table = "orders";

    protected $fillable = [
        'customer_id',
        'user_id',
    ];

    protected  $primaryKey = 'order_id';
}
