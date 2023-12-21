<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class chitiet_giohang extends Model
{
    use HasFactory;
    public $timestamps = false; 
    protected $fillable = [
        'MATK',
        'MASP',
        'SOLUONG',
        'TONGGIA',
        'SELECTED',
        'MAMAU',
        'MASIZE',
    ];
}
