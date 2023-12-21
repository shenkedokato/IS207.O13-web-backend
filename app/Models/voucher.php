<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class voucher extends Model
{
    use HasFactory;
    public $timestamps = false;

    public function donhangs(){
        return $this->hasMany(donhang::class);
    }
}
