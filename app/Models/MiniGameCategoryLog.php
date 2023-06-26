<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiniGameCategoryLog extends Model
{
    use HasFactory;

    protected $table = 'sphinx.mn_gm_category_log';

    public function userinfo()
    {
        return $this->belongsTo(User::class, 'adminid');
    }

    public function gameinfo()
    {
        return $this->belongsTo(MiniGameCategory::class, 'gameid', 'gameid');
    }

}
