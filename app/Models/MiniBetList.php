<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiniBetList extends Model
{
    use HasFactory;

    protected $table = 'sphinx.mn_bet_list';

    public function userinfo()
    {
        return $this->belongsTo(User::class, 'userid');
    }

    public function gameinfo()
    {
        return $this->belongsTo(MiniGameCategory::class, 'game_code', 'game_code');
    }
}
