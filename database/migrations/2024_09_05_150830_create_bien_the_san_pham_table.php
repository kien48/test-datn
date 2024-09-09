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
        Schema::create('bien_the_san_pham', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_san_pham')->constrained('san_pham');
            $table->foreignId('id_mau_sac_bien_the')->constrained('mau_sac_bien_the');
            $table->foreignId('id_kich_thuoc_bien_the')->constrained('kich_thuoc_bien_the');
            $table->integer('so_luong')->default(0);
            $table->decimal('gia_ban');
            $table->decimal('gia_khuyen_mai')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bien_the_san_pham');
    }
};
