<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiniGamePushTemp extends Model
{
    use HasFactory;

    protected $table = 'sphinx.mn_gm_push_temp';

    public function typeinfo()
    {
        return $this->hasOne(MiniGamePush::class, 'pushid', 'pushid');
    }
}
