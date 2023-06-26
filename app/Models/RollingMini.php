<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RollingMini extends Model
{
    use HasFactory;

    protected $table = 'sphinx.rolling_mini';

    public $timestamps = false;
}
