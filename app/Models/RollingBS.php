<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RollingBS extends Model
{
    use HasFactory;

    protected $table = 'sphinx.rolling_bs';

    public function userInfo()
    {
        return $this->belongsTo(User::class, 'userid');
    }

    public $timestamps = false;
}
