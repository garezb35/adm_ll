<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserIpblock extends Model
{
    use HasFactory;

    protected $table = 'sphinx.user_ipblock';

}
