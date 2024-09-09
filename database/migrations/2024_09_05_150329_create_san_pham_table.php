<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('san_pham', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_danh_muc')->nullable()->constrained('danh_muc')->onDelete('set null');
            $table->string('ten_san_pham')->unique();
            $table->string('duong_dan');
            $table->string('ma_san_pham')->unique();
            $table->string('anh_san_pham');
            $table->string('mo_ta_ngan');
            $table->text('noi_dung');
            $table->integer('luot_xem')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('san_pham'); // Tên bảng phải trùng khớp
    }
};
