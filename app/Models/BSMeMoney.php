<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BSMeMoney extends Model
{
    use HasFactory;

    protected $table = 'sphinx.bs_me_money';

    public function userinfo()
    {
        return $this->belongsTo(User::class, 'userid');
    }
}
