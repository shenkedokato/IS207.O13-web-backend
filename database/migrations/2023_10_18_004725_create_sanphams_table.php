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
        Schema::dropIfExists('sanphams');

        Schema::create('sanphams', function (Blueprint $table) {
            $table->increments('MASP');
            $table->longText('TENSP');
            $table->integer('GIAGOC');
            $table->integer('GIABAN');
            $table->unsignedInteger('MAPL_SP');
            $table->integer('MAPL_SP2');
            $table->longText('MOTA');     
            $table->foreign('MAPL_SP')->references('MAPL')->on('phanloai_sanphams');
            
            // $table->foreign('MAPL_SP2')->references('MAPL2')->on('phanloai_sanpham2s');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sanphams');
    }
};
