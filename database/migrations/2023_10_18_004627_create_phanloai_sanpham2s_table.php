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
        Schema::dropIfExists('phanloai_sanpham2s');

        Schema::create('phanloai_sanpham2s', function (Blueprint $table) {
            $table->integer('MAPL2');
            $table->unsignedInteger('MAPL1');
            $table->text('TENPL2');

            $table->primary(['MAPL2', 'MAPL1']); 

            $table->foreign('MAPL1')->references('MAPL')->on('phanloai_sanphams');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phanloai_sanpham2s');
    }
};
