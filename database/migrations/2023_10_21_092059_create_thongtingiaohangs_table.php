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
        Schema::dropIfExists('thongtingiaohangs');

        Schema::create('thongtingiaohangs', function (Blueprint $table) {
            $table->increments('MATTGH'); 
            $table->unsignedInteger('MATK');
            $table->string('TEN');
            $table->string('SDT');
            $table->string('DIACHI');
            $table->string('TINH_TP');
            $table->string('QUAN_HUYEN');
            $table->string('PHUONG_XA');
            $table->integer('DANGSUDUNG');

            $table->foreign('MATK')->references('MATK')->on('taikhoans');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thongtingiaohangs');
    }
};
