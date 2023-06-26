<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiniGameCategory extends Model
{
    use HasFactory;

    protected $table = 'sphinx.mn_gm_category';

    public $timestamps = false;

    public function pickinfo()
    {
        return $this->hasMany(MiniGamePick::class, 'gameid', 'gameid');
    }

    public function limitinfo()
    {
        return $this->hasMany(MiniGameBetLimit::class, 'gameid', 'gameid');
    }
}
