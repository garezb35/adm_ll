<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayExchange extends Model
{
    use HasFactory;

    protected $table = 'sphinx.pay_exchange';

    public $timestamps = false;

    public function userinfo()
    {
        return $this->belongsTo(User::class, 'userid');
    }
}
