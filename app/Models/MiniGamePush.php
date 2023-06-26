<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 유저 누르기 리스트
 */
class MiniGamePush extends Model
{
    use HasFactory;

    protected $table = 'sphinx.mn_gm_push';

    public $timestamps = false;
}
