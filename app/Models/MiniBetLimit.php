<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiniBetLimit extends Model
{
    use HasFactory;

    protected $table = 'sphinx.mn_bet_limit';

    public $timestamps = false;
}
