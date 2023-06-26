<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiniBetAdmAll extends Model
{
    use HasFactory;

    protected $table = 'sphinx.getMiniBetAdm';

    public function userinfo()
    {
        return $this->belongsTo(User::class, 'userid');
    }
}
