<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiniRateKeno extends Model
{
    use HasFactory;
    protected $table = 'sphinx.mn_rate_keno';
    public $timestamps = false;
}
