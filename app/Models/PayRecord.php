<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayRecord extends Model
{
    use HasFactory;
    protected $table= 'sphinx.getPayRecord';

    public function adminfo()
    {
        return $this->belongsTo(User::class, 'updateBy');
    }
}
