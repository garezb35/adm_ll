<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiniRateBBS extends Model
{
    use HasFactory;
    protected $table = 'sphinx.mn_rate_bbs';
    public $timestamps = false;
}
