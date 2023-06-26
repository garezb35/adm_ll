<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BSBetCtl extends Model
{
    use HasFactory;

    protected $table = 'sphinx.bs_bet_ctl';

    public function gameInfo()
    {
        return $this->belongsTo(BSGameCategory::class, 'thirdParty', 'thirdPartyCode');
    }


}
