<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SvcMsg extends Model
{
    use HasFactory;

    protected $table = 'sphinx.svc_msg';

    public function userinfo()
    {
        return $this->belongsTo(User::class, 'userid');
    }
}
