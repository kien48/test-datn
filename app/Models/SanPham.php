<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SanPham extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'san_pham';
    protected $fillable = [
        'id_danh_muc',
        'ten_san_pham',
        'anh_san_pham',
        'ma_san_pham',
        'duong_dan',
        'mo_ta_ngan',
        'noi_dung',
    ];

    public function danhMuc()
    {
        return $this->belongsTo(DanhMuc::class, 'id_danh_muc', 'id');
    }

    public function bienTheSanPham()
    {
        return $this->hasMany(BienTheSanPham::class, 'id_san_pham', 'id');
    }

    public function theSanPham()
    {
        return $this->belongsToMany(The::class, 'the_san_pham', 'id_san_pham', 'id_the');
    }
}
