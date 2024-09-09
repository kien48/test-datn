<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KichThuocBienThe extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ten_kich_thuoc',
    ];

    protected $table  = 'kich_thuoc_bien_the';
}
