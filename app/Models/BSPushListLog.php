<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BSPushListLog extends Model
{
    use HasFactory;

    protected $table = 'sphinx.bs_ps_list_log';

    public function gameinfo()
    {
        return $this->belongsTo(MiniGameCategory::class, 'gameid', 'gameid');
    }

    public function userinfo()
    {
        return $this->belongsTo(User::class, 'adminid');
    }
}
