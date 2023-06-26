<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiniBetFeeAll extends Model
{
    use HasFactory;

    protected $table = 'sphinx.getMiniFeeList';

    public function userinfo()
    {
        return $this->belongsTo(User::class, 'bet_userid');
    }

    public function gameinfo()
    {
        return $this->belongsTo(MiniGameCategory::class, 'game_code', 'game_code');
    }
}
