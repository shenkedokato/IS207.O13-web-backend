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
        Schema::dropIfExists('donhangs');

        Schema::create('donhangs', function (Blueprint $table) {
            $table->increments('MADH');
            $table->unsignedInteger('MATK');
            $table->date('NGAYORDER');
            $table->date('NGAYGIAOHANG')->nullable();
            $table->integer('TONGTIEN_SP');  
            $table->integer('VOUCHERGIAM');
            $table->integer('TONGTIENDONHANG');
            $table->integer('PHIVANCHUYEN');

            $table->string('HINHTHUC_THANHTOAN');
            $table->string('TRANGTHAI_THANHTOAN');
            $table->string('TRANGTHAI_DONHANG');
            $table->unsignedInteger('MATTGH');
            $table->longText('GHICHU');

            $table->foreign('MATK')->references('MATK')->on('taikhoans');
            $table->foreign('MATTGH')->references('MATTGH')->on('thongtingiaohangs');
            
            $table->timestamp('updated_at')->nullable(); 
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donhangs');
    }
};
