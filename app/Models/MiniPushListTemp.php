<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiniPushListTemp extends Model
{
    use HasFactory;

    protected $table = 'sphinx.mn_ps_list_temp';

    public function typeinfo()
    {
        return $this->hasOne(MiniPushTypes::class, 'pushid', 'pushid');
    }

}
