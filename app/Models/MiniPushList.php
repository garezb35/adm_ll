<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 보험 리스트
 */
class MiniPushList extends Model
{
    use HasFactory;

    protected $table = 'sphinx.mn_ps_list';
    public $timestamps = false;

    public function typeinfo()
    {
        return $this->hasOne(MiniPushTypes::class, 'pushid', 'pushid');
    }
}
