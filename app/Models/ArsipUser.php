<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ArsipUser extends Pivot
{
    protected $table = 'arsip_user'; // Nama tabel pivot
    
    protected $fillable = [
        'arsip_id',
        'user_id',
    ];
}
