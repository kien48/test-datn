<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class TheSanPham extends Pivot
{
    use SoftDeletes;

    protected $table = 'the_san_pham'; // Tên bảng pivot

    protected $fillable = [
        'id_san_pham',
        'id_the',
    ];
}
