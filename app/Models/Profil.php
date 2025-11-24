<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profil extends Model
{
    protected $fillable = ['user_id', 'alamat', 'nomor_hp', 'jabatan'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
