<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BSGamePushLog extends Model
{
    use HasFactory;

    protected $table = 'sphinx.bs_gm_push_log';

    public function userinfo()
    {
        return $this->belongsTo(User::class, 'userid');
    }

    public function adminfo()
    {
        return $this->belongsTo(User::class, 'adminid');
    }

}
