<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayDaily extends Model
{
    use HasFactory;

    protected $table = 'sphinx.pay_daily';

    public $timestamps = false;

}
