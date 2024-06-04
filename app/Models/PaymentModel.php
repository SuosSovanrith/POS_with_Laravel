<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentModel extends Model
{
    use HasFactory;
    
    protected $table = "payment";

    protected $fillable = [
        'amount',
        'user_id',
        'order_id',
    ];

    protected  $primaryKey = 'payment_id';
}
