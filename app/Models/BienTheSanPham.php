<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BienTheSanPham extends Model
{
    use HasFactory,softDeletes;

    protected $table = 'bien_the_san_pham';

    protected $fillable = [
        'id_san_pham',
        'id_mau_sac_bien_the',
        'id_kich_thuoc_bien_the',
        'so_luong',
        'gia_ban',
        'gia_khuyen_mai',
    ];

    public function anhBienThe()
    {
        return $this->hasMany(AnhBienThe::class, 'id_bien_the', 'id');
    }

    public function mauBienThe()
    {
        return $this->belongsTo(MauSacBienThe::class, 'id_mau_sac_bien_the', 'id');
    }

    public function kichThuocBienThe()
    {
        return $this->belongsTo(KichThuocBienThe::class, 'id_kich_thuoc_bien_the', 'id');
    }

}
