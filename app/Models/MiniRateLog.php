<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiniRateLog extends Model
{
    use HasFactory;

    protected $table = 'sphinx.mn_rate_log';
    public $timestamps = false;

    public function userinfo()
    {
        return $this->belongsTo(User::class, 'userid');
    }

    public function adminfo()
    {
        return $this->belongsTo(User::class, 'adminid');
    }
}
