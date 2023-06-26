<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteBlackNum extends Model
{
    use HasFactory;

    protected $table = 'sphinx.site_black_num';

    public function adminfo()
    {
        return $this->hasOne(User::class, 'adminid');
    }
}
