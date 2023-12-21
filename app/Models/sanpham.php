<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sanpham extends Model
{
    use HasFactory; 
    public function sanpham_mausac_size(){
        return $this->hasMany(sanpham_mausac_size::class);
    }
}
