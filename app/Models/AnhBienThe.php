<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnhBienThe extends Model
{
    use HasFactory,softDeletes;

    protected $table = 'anh_bien_the';

    protected $fillable = ['id_bien_the', 'duong_dan_anh'];
}
