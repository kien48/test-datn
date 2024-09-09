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
        Schema::create('danh_muc', function (Blueprint $table) {
            $table->id();
            $table->string('ten_danh_muc')->unique();
            $table->foreignId('id_cha')->nullable()->constrained('danh_muc');
            $table->string('duong_dan');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danh_muc');
    }
};
