<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLoginLog extends Model
{
    use HasFactory;

    protected $table = 'sphinx.user_login_log';

    public function userinfo()
    {
        return $this->belongsTo(User::class, 'userId');
    }

}
