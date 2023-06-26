<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BSBetFeeAll extends Model
{
    use HasFactory;

    protected $table = 'sphinx.getBSBetFee';

    public function gameinfo()
    {
        return $this->belongsTo(BSGameCategory::class, 'thirdParty', 'thirdPartyCode');
    }

    public function userInfo()
    {
        return $this->belongsTo(User::class, 'bet_userid');
    }
}
