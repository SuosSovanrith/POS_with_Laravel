<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerModel extends Model
{
    use HasFactory;
    
    protected $table="customer";

    protected $fillable = [
        'customer_name',
        'customer_email',
        'phone_number',
        'address',
        'photo',
    ];

    protected  $primaryKey = 'customer_id';
}
