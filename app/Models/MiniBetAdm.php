<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiniBetAdm extends Model
{
    use HasFactory;

    protected $table = 'sphinx.mn_bet_adm';

    public function userinfo()
    {
        return $this->belongsTo(User::class, 'userid');
    }
}
