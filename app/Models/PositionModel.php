<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PositionModel extends Model
{
    use HasFactory;

    protected $table="Position";

    protected $fillable = [
        'position_id',
        'position_name',
    ];

    protected  $primaryKey = 'position_id';
}
