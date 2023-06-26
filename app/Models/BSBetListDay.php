<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BSBetListDay extends Model
{
    use HasFactory;

    protected $table = 'sphinx.bs_bet_list_day';
    public $timestamps = false;

    public function betinfo()
    {
        return $this->belongsTo(BSGameCategory::class, 'thirdParty', 'thirdPartyCode');
    }

    public function userInfo()
    {
        return $this->belongsTo(User::class, 'userno');
    }

    public function pushInfo()
    {
        return $this->belongsTo(BSGamePush::class, 'userno', 'userid');
    }

}
