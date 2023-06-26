<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiniGameBetLimit extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'sphinx.mn_gm_betlimit';

}
