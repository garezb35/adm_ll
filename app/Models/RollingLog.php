<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RollingLog extends Model
{
    use HasFactory;

    protected $table = 'sphinx.rolling_log';

    public function userinfo()
    {
        return $this->belongsTo(User::class, 'userid');
    }

    public function adminfo()
    {
        return $this->belongsTo(User::class, 'adminid');
    }
}
