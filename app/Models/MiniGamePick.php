<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiniGamePick extends Model
{
    use HasFactory;

    protected $table = 'sphinx.mn_gm_pick';

    public $timestamps = false;
}
