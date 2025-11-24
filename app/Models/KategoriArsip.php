<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriArsip extends Model
{
    use HasFactory;

    protected $fillable = ['nama_kategori'];

    public function arsip()
    {
        return $this->hasMany(Arsip::class);
    }
}

