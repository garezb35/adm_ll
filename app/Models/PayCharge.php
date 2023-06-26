<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayCharge extends Model
{
    use HasFactory;

    protected $table = 'sphinx.pay_charge';

    public function userinfo()
    {
        return $this->belongsTo(User::class, 'userid');
    }

    public function adminfo()
    {
        return $this->belongsTo(User::class, 'updateBy');
    }
}
