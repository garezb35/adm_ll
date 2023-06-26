<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiniRateKSA extends Model
{
    use HasFactory;
    protected $table = 'sphinx.mn_rate_ksa';
    public $timestamps = false;
}
