<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierModel extends Model
{
    use HasFactory;

    protected $table="supplier";

    protected $fillable = [
        'supplier_name',
        'supplier_email',
        'phone_number',
        'address',
        'photo',
    ];

    protected  $primaryKey = 'supplier_id';

}
