<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SvcAskTemp extends Model
{
    use HasFactory;

    protected $table = 'sphinx.svc_ask_temp';

    public $timestamps = false;

}
