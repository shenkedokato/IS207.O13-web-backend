<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class phanloai_sanpham extends Model
{
    use HasFactory;
    public function sanphams(){
        return $this->hasMany(sanpham::class);
    }
}
