<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCartModel extends Model
{
    use HasFactory;

    protected $table = "user_cart";

    protected $fillable = [
        'user_id',
        'product_id',
        'cart_quantity',
    ];

    protected  $primaryKey = 'cart_id';

}
