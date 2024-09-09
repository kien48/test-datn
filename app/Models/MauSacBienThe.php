<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MauSacBienThe extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ten_mau_sac',
        'ma_mau_sac',
    ];

    protected $table  = 'mau_sac_bien_the';

}
