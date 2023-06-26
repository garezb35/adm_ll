<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SvcAsk extends Model
{
    use HasFactory;

    protected $table = 'sphinx.svc_ask';

    public function userinfo()
    {
        return $this->belongsTo(User::class, 'userid');
    }
}
