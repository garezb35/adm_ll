<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SvcBoards extends Model
{
    use HasFactory;

    protected $table = 'sphinx.svc_boards';

    public function info()
    {
        return $this->hasOne(SiteDomain::class, 'id', 'domain');
    }
}
