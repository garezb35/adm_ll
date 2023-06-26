<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BSBetPush extends Model
{
    use HasFactory;

    protected $table = 'sphinx.bs_bet_push';

    public function siteInfo()
    {
        return $this->belongsTo(BSPushList::class, 'siteid');
    }
}
