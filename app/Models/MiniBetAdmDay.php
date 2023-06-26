<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiniBetAdmDay extends Model
{
    use HasFactory;

    protected $table = 'sphinx.mn_bet_adm_day';

    public function userinfo()
    {
        return $this->belongsTo(User::class, 'userid');
    }
}
