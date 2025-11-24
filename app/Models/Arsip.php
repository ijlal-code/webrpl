<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arsip extends Model
{
    use HasFactory;

    protected $fillable = ['judul_arsip', 'file_arsip', 'tanggal_upload', 'kategori_id', 'user_id'];

   
    public function kategori()
    {
        return $this->belongsTo(KategoriArsip::class, 'kategori_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function aksesUsers() {
        return $this->belongsToMany(User::class, 'arsip_user')
        ->using(ArsipUser::class) // Tambahkan ini
        ->withTimestamps();
    }
}
